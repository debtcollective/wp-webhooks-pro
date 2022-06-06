<?php

/**
 * WP_Webhooks_Pro_Wizard Class
 *
 * This class contains all of the available api functions
 *
 * @since 5.0
 */

/**
 * The api class of the plugin.
 *
 * @since 5.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Wizard {

	/**
	 * Tthe wizard configuration
	 *
	 * @var boolean
	 */
	public $wizard = null;

	/**
	 * Whether we should show the migration tab
	 *
	 * @var boolean
	 */
	private $requires_action_50_migration = null;

	function __construct(){
		$this->page_name    = WPWHPRO()->settings->get_page_name();
	}

	/**
	 * Execute feature related hooks and logic to get 
	 * everything running
	 *
	 * @since 5.0
	 * @return void
	 */
	public function execute(){

		add_filter( 'wpwhpro/admin/page_template_file', array( $this, 'maybe_launch_wizard' ), 20 );
		add_action( 'wpwhpro/admin/settings/wizard/place_content', array( $this, 'display_wizard_step' ), 20 );

		// Validate settings
		add_action( 'admin_init',  array( $this, 'validate_wizard_step_data' ) );

		//Register the step actions
		add_filter( 'wpwhpro/admin/settings/wizard/process_step_data', array( $this, 'register_step_action_callback' ), 10, 2 );

	}

	public function maybe_launch_wizard( $wpwh_page ){

		if( $this->needs_wizard() ){
			$wpwh_page = WPWHPRO_PLUGIN_DIR . 'core/includes/partials/wpwhpro-wizard-display.php';
		}

		return $wpwh_page;
	}

	public function display_wizard_step( $wpwh_page ){

		$file = WPWHPRO_PLUGIN_DIR . 'core/includes/partials/wizard/' . sanitize_title( $wpwh_page ) . '.php';

		if( file_exists( $file ) ){
			include( $file );
		}
		
	}

	public function validate_wizard_step_data(){

        if( ! is_admin() || ! WPWHPRO()->helpers->is_page( $this->page_name ) ){
			return;
		}

		//Reset the wizard
		if( isset( $_POST['wpwhpro_tools_relaunch_wizard'] ) ){
			$wizard_nonce_data = WPWHPRO()->settings->get_wizard_nonce();

			if ( check_admin_referer( $wizard_nonce_data['action'], $wizard_nonce_data['arg'] ) ){
				if( current_user_can( apply_filters( 'wpwhpro/admin/settings/wizard', WPWHPRO()->settings->get_admin_cap( 'wpwhpro-wizard-reset' ) ) ) ){
					
					$this->reset_wizard();
		
					do_action( 'wpwhpro/admin/settings/wizard/reset_triggered' );
				}
			}
		}

		//Save the wizard settings
		if( isset( $_POST['wpwh_wizard_submit'] ) && isset( $_POST['wpwh_wizard_step'] ) ){
			$current_step = sanitize_title( $_GET['wpwhwizard'] );
			$previous_step = sanitize_title( $_POST['wpwh_wizard_step'] );

			$wizard_nonce_data = WPWHPRO()->settings->get_wizard_nonce();

			if ( check_admin_referer( $wizard_nonce_data['action'], $wizard_nonce_data['arg'] ) ){
				if( current_user_can( apply_filters( 'wpwhpro/admin/settings/wizard', WPWHPRO()->settings->get_admin_cap( 'wpwhpro-wizard-settings' ), $current_step ) ) ){
					
					//Allow hooks to change the current step based on a specific feedback along with saving the actual settings
					$current_step = apply_filters( 'wpwhpro/admin/settings/wizard/process_step_data', $current_step, $previous_step );

					//Update the current step
					if( $current_step ){
						WPWHPRO()->wizard->set_current_step( $current_step );
					}
		
					
				}
			}
		} elseif( isset( $_GET['wpwhwizard'] ) ){
			//Return to the previous wizard step
			$current_step = sanitize_title( $_GET['wpwhwizard'] );

			if( current_user_can( apply_filters( 'wpwhpro/admin/settings/wizard', WPWHPRO()->settings->get_admin_cap( 'wpwhpro-wizard-settings' ), $current_step ) ) ){
					
				$current_step = apply_filters( 'wpwhpro/admin/settings/wizard/previous_step', $current_step );

				//Update the current step
				if( $current_step ){
					WPWHPRO()->wizard->set_current_step( $current_step );
				}
			}
		}
    }

	public function register_step_action_callback( $current_step, $previous_step ){

		switch( $previous_step ){
			case 'settings':

				//Update the autoclean log setting
				if( isset( $_POST['wpwh_wizard_log_autoclean'] ) && ! empty( $_POST['wpwh_wizard_log_autoclean'] ) ){
					WPWHPRO()->settings->save_settings( array( 'wpwhpro_autoclean_logs' => 1 ) );
				}

				break;
			case 'license':

				//Update the autoclean log setting
				if( isset( $_POST['wpwh_wizard_license'] ) && ! empty( $_POST['wpwh_wizard_license'] ) ){
					$license_key = esc_html( $_POST['wpwh_wizard_license'] );
					
					$response = WPWHPRO()->license->activate( array( 'license' => $license_key	) );
					$reload_step = true;
	
					if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {

						$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		
						if( ! empty( $license_data ) && $license_data->license == 'valid' ){
							WPWHPRO()->license->update( 'status', $license_data->license );
							WPWHPRO()->license->update( 'expires', $license_data->expires );
							$reload_step = false;
						}
					}

					//If an error occured, handle the same step again
					if( $reload_step ){
						$current_step = $previous_step;
					}
				}

				break;
		}

		return $current_step;
	}

	/**
	 * Check if the whitelist is active
	 *
	 * @return bool - true if active, false if not
	 */
	public function needs_wizard(){

		if( $this->get_wizard() !== 'complete' && ! isset( $_GET['wpwhbypasswizard'] ) ){
			return true;
		}
		
		return false;
	}

	/**
	 * Check if the whitelist is active
	 *
	 * @return bool - true if active, false if not
	 */
	public function get_wizard(){

		if( $this->wizard !== null ){
			return $this->wizard;
		}

		$wizard = get_option( 'wpwhpro_wizard' );
		if( empty( $wizard ) ){
			$wizard = $this->get_wizard_steps()[0];
		}

		$this->wizard = $wizard;

		return $wizard;
	}

	/**
	 * Check if the whitelist is active
	 *
	 * @return bool - true if active, false if not
	 */
	public function reset_wizard(){
		return delete_option( 'wpwhpro_wizard' );
	}

	public function get_wizard_steps(){

		$wizard_steps = array( 
			'home',
			'settings',
		);

		if( ! WPWHPRO()->license->is_active() ){
			$wizard_steps[] = 'license';
		}

		//Maybe add the migration tab for pre 5.0 action urls
		if( $this->requires_action_50_migration !== null ){
			if( $this->requires_action_50_migration ){
				$wizard_steps[] = 'migrateactions';
			}
		} else {
			$actions_data = WPWHPRO()->webhook->get_hooks( 'action' );
			$need_migration = array();
			if( ! empty( $actions_data ) ){
				foreach( $actions_data as $mas => $mad ){
					if( isset( $mad['api_key'] ) && is_string( $mad['api_key'] ) ){
						$need_migration[ $mas ] = $mad;
					}
				}
			}

			if( ! empty( $need_migration ) ){
				$this->requires_action_50_migration = true;
				$wizard_steps[] = 'migrateactions';
			} else {
				$this->requires_action_50_migration = false;
			}
		}

		//Add an informative tab with further help files and information
		$wizard_steps[] = 'info';

		$wizard_steps = apply_filters( 'wpwhpro/wizard/register_steps', $wizard_steps );

		//its only a placeholder that returns to the normal page as it shows the end of the wizard
		$wizard_steps[] = 'complete';

		return $wizard_steps;
	}

	/**
	 * Get the current step of the wizard
	 *
	 * @return string
	 */
	public function get_current_step(){
		return $this->get_wizard();
	}

	/**
	 * Get the number of the current step, starting from 1
	 *
	 * @return integer
	 */
	public function get_current_step_number(){

		$steps = $this->get_wizard_steps();
		$current_step = array_search( $this->get_wizard(), $steps );

		if( empty( $current_step ) ){
			$current_step = 0;
		}

		return $current_step++;
	}

	/**
	 * Set the current step of the wizard
	 *
	 * @return string
	 */
	public function set_current_step( $step ){

		$step = sanitize_title( $step );

		if( in_array( $step, $this->get_wizard_steps() ) ){
			update_option( 'wpwhpro_wizard', $step );
			$this->wizard = $step;
			return true;
		}

		return false;
	}

	/**
	 * Get the next step of the wizard
	 *
	 * @return string
	 */
	public function get_next_step(){

		$steps = $this->get_wizard_steps();
		$next_step = array_search( $this->get_wizard(), $steps );

		if( is_numeric( $next_step ) ){
			$next_step = $next_step + 1;

			if( isset( $steps[ $next_step ] ) ){
				return $steps[ $next_step ];
			} else {
				return 'complete';
			}
		}

		//If no next step is given
		return false;
	}

	/**
	 * Get the previous step of the wizard
	 *
	 * @return string
	 */
	public function get_previous_step(){

		$steps = $this->get_wizard_steps();
		$previous_step = array_search( $this->get_wizard(), $steps );

		if( is_numeric( $previous_step ) ){
			$previous_step = $previous_step - 1;

			if( isset( $steps[ $previous_step ] ) ){
				return $steps[ $previous_step ];
			}
		}

		//If no next step is given
		return false;
	}

}
