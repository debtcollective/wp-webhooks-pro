<?php

/**
 * WP_Webhooks_Pro_Whitelabel Class
 *
 * This class contains the whole whitelabel functionality
 *
 * @since 3.0.6
 */

/**
 * The whitelist class of the plugin.
 *
 * @since 3.0.6
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Whitelabel {

	/**
	 * WP_Webhooks_Pro_Whitelabel constructor.
	 */
	public function __construct() {
        $this->whitelabel_settings = WPWHPRO()->settings->get_whitelabel_settings( true );
	}

	/**
	 * Check if the whitelist is active
	 *
	 * @return bool - true if active, false if not
	 */
	public function is_active(){
        $is_active = false;

        if( 
			! empty( $this->whitelabel_settings )
			&& isset( $this->whitelabel_settings[ 'wpwhpro_whitelabel_activate' ] )
			&& $this->whitelabel_settings[ 'wpwhpro_whitelabel_activate' ] === 'yes'
		){
            $is_active = true;
        }

		$is_active = apply_filters( 'wpwhpro/whitelabel/is_active', $is_active, $this->whitelabel_settings );

		if( ! WPWHPRO()->license->is_active() ){
			$is_active = false;
		}

        return $is_active;
	}

	/**
	 * Execute feature related hooks and logic to get 
	 * everything running
	 *
	 * @since 4.2.3
	 * @return void
	 */
	public function execute(){

		add_action( 'admin_init',  array( $this, 'save_whitelabel_settings' ) );

	}

	/*
     * Functionality to save the whitelabel settings of the whitelabel settings page
     */
	public function save_whitelabel_settings(){

        if( ! is_admin() || ! WPWHPRO()->helpers->is_page( WPWHPRO()->settings->get_page_name() ) ){
			return;
		}

		if( ! isset( $_POST['wpwh_whitelabel_submit'] ) ){
			return;
		}

		$whitelabel_nonce_data = WPWHPRO()->settings->get_whitelabel_nonce();

		if ( ! check_admin_referer( $whitelabel_nonce_data['action'], $whitelabel_nonce_data['arg'] ) ){
			return;
		}

		if ( ! current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwh-save-whitelabel-settings' ) ) ){
			return;
		}

		if ( ! WPWHPRO()->license->is_active() ){
			return;
		}

		$current_url = WPWHPRO()->helpers->get_current_url();

		WPWHPRO()->settings->save_whitelabel_settings( $_POST );

		wp_redirect( $current_url );
		exit;

    }

	/**
	 * Check if the whitelist is active
	 *
	 * @return bool - true if active, false if not
	 */
	public function get_setting( $setting = '' ){
        $return = false;
        
        if( empty( $this->whitelabel_settings ) ){
            return $return;
        }

        if( ! isset( $this->whitelabel_settings[ $setting ] ) ){
            return $return;
        }

        $return = $this->whitelabel_settings[ $setting ];

        return apply_filters( 'wpwhpro/whitelabel/get_setting', $return, $setting, $this->whitelabel_settings );
    }
    
    /**
	 * Verify whitelabel feature based on a given license
	 *
	 * @param array $args - The arguments for the request (Currently supports only license)
	 * @return array - The response data of the request
	 */
	public function verify_whitelabel( $args ){

		if( empty( $args['license'] ) )
			return false;

		$home_url = home_url();

		$api_params = array(
			'wpwh_check_whitelabel'    	=> $args['license'],
			'wpwh_check_whitelabel_url'	=> $home_url
		);

		$response = wp_remote_post( IRONIKUS_STORE, array( 'timeout' => 30, 'sslverify' => true, 'body' => $api_params ) );

		return $response;
	}
}
