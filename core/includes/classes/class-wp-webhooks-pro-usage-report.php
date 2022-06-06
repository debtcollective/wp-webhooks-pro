<?php

/**
 * WP_Webhooks_Pro_Usage_Report Class
 *
 * This class contains all of the available api functions
 *
 * @since 5.0
 */

/**
 * The reports class of the plugin.
 *
 * @since 5.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Usage_Report {

	private $reporting_interval = DAY_IN_SECONDS - 3600;
	private $reporting_transient = 'wpwh_reporting_lock';
	private $report_url = 'https://status.wp-webhooks.com';
	private $system_report = null;

	/**
	 * Execute reports related hooks and logic to get 
	 * everything running
	 *
	 * @since 5.0
	 * @return void
	 */
	public function execute(){

		if( ! defined( 'DISABLE_WP_CRON' ) || ! DISABLE_WP_CRON ){
			add_action( 'wpwh_daily_maintenance', array( $this, 'launch_report' ), 10 );
		} else {

			if( ! isset( $_GET['wpwhlaunchreport'] ) ){
				add_action( 'shutdown', array( $this, 'maybe_launch_report' ), 10 );
			} else {
				add_action( 'init', array( $this, 'validate_report_launch' ), 10 );
			}

		}

	}

	public function maybe_launch_report(){

		$transient = get_transient( $this->reporting_transient );
		if( ! empty( $transient ) ){
			return;
		}

		$launcher_url = home_url() . '?wpwhlaunchreport=yes';

		$response = wp_remote_post( $launcher_url, array(
			'timeout'  => 0.01,
			'blocking' => false,
			'body'     => array(),
		) );

		set_transient( $this->reporting_transient, 'yes', $this->reporting_interval );
	}

	public function validate_report_launch(){

		if( ! isset( $_GET['wpwhlaunchreport'] ) ){
			return;
		}

		$transient = get_transient( $this->reporting_transient );
		if( ! empty( $transient ) ){
			return;
		}

		$this->launch_report();
	}

	public function launch_report(){
		$usage_report = $this->generate_report();

		$response = wp_remote_post( $this->report_url . '?wpwhreportentry=yes', array(
			'timeout'  => 15,
			'blocking' => false,
			'body'     => array(
				'action' => 'report',
				'report' => $usage_report
			),
		) );

		return $response;
	}

	public function generate_report(){

		$report = $this->get_system_report();

		$report_data = array(
			'site' => WPWHPRO()->settings->get_site_ident(),
		);

		//Build the system data
		$report_data['system'] = $this->get_validated_system_data( $report );

		//Build the wp data
		$report_data['wp'] = $this->get_validated_wp_data( $report );

		//Build the theme data
		$report_data['theme'] = $this->get_validated_theme_data( $report );

		//Build the plugins data
		$report_data['plugins'] = $this->get_validated_plugins_data( $report );

		//Build the license data
		$report_data['license'] = $this->get_validated_license_data( $report );

		//Build the WP Webhooks data
		$report_data['wpwh'] = $this->get_validated_wpwh_data( $report );

		return $report_data;
	}

	public function get_system_report(){

		if( $this->system_report !== null ){
			return $this->system_report;
		}

		$this->system_report = WPWHPRO()->system->generate_report();

		return $this->system_report;
	}

	public function get_validated_system_data( $report ){

		$validated_keys = array(
			'wp_version',
			'wp_multisite',
			'wp_memory_limit',
			'wp_debug_mode',
			'wp_cron',
			'external_object_cache',
			'server_info',
			'php_version',
			'php_post_max_size',
			'php_max_execution_time',
			'php_max_input_vars',
			'curl_version',
			'max_upload_size',
			'remote_post_response',
			'remote_get_response',
		);
		$validated_system_data = array();
		$environment = ( isset( $report['environment'] ) && is_array( $report['environment'] ) ) ? $report['environment'] : array();

		foreach( $validated_keys as $single_key ){
			$validated_system_data[ $single_key ] = isset( $environment[ $single_key ] ) ? $environment[ $single_key ] : '';
		}

		return $validated_system_data;
	}

	public function get_validated_wp_data( $report ){

		$usercount = count_users();

		$validated_data = array(
			'is_multisite' => is_multisite(),
			'sites_count' => ( function_exists('get_blog_count') ) ? get_blog_count() : 0,
			'total_users' => ( ! empty( $usercount ) && isset( $usercount['total_users'] ) ) ? $usercount['total_users'] : 0,
			'timezone_offset' => date( 'P' ),
			'language' => get_locale(),
		);

		return $validated_data;
	}

	public function get_validated_theme_data( $report ){

		$validated_keys = array(
			'name',
			'version',
			'is_child_theme',
		);
		$validated_theme_data = array();
		$theme = ( isset( $report['theme'] ) && is_array( $report['theme'] ) ) ? $report['theme'] : array();

		foreach( $validated_keys as $single_key ){
			$validated_theme_data[ $single_key ] = isset( $theme[ $single_key ] ) ? $theme[ $single_key ] : '';
		}

		return $validated_theme_data;
	}

	public function get_validated_plugins_data( $report ){

		$validated_plugin_data = array();
		$plugins = ( isset( $report['active_plugins'] ) && is_array( $report['active_plugins'] ) ) ? $report['active_plugins'] : array();

		foreach( $plugins as $plugin_key => $plugin ){
			$validated_plugin_data[ $plugin_key ] = array(
				'name' => ( isset( $plugin['name'] ) ) ? $plugin['name'] : '',
				'version' => ( isset( $plugin['version'] ) ) ? $plugin['version'] : '',
				'network_activated' => ( isset( $plugin['network_activated'] ) ) ? $plugin['network_activated'] : '',
			);
		}

		return $validated_plugin_data;
	}

	public function get_validated_license_data( $report ){
		$license = WPWHPRO()->settings->get_license();

		if( empty( $license ) ){
			$license = array();
		}

		$validated_license_data = array(
			'key' => isset( $license['key'] ) ? $license['key'] : '',
			'status' => isset( $license['status'] ) ? $license['status'] : '',
			'expires' => isset( $license['expires'] ) ? $license['expires'] : '',
			'expires' => isset( $license['expires'] ) ? $license['expires'] : '',
			'home_url' => $report['environment']['home_url'],
			'site_url' => $report['environment']['site_url'],
		);

		return $validated_license_data;
	}

	public function get_validated_wpwh_data( $report ){

		$is_whitelabel = get_option( WPWHPRO()->settings->get_whitelabel_settings_option_key() );

		if( ! empty( $is_whitelabel ) && $is_whitelabel !== 'no' ){
			$is_whitelabel = 'yes';
		} else {
			$is_whitelabel = 'no';
		}

		$validated_wpwh_data = array(
			'version' => WPWHPRO_VERSION,
			'whitelabel' => $is_whitelabel,
			'settings' => $report['wpwhsettings'],
			'flows' => $this->wpwh_get_validated_flows_data(),
			'flows_count' => WPWHPRO()->flows->get_flows_count(),
			'trigger_count' => $this->wpwh_count_trigger_data(),
			'action_count' => $this->wpwh_count_action_data(),
			'authentication_count' => WPWHPRO()->auth->get_authentication_count(),
			'data_mapping_count' => WPWHPRO()->data_mapping->get_dm_count(),
			'whitelist_count' => $this->wpwh_count_whitelist_data(),
		);

		return $validated_wpwh_data;
	}

	private function wpwh_count_trigger_data(){
		$trigger_count = 0;

		$trigger_data = WPWHPRO()->webhook->get_hooks( 'trigger' );
		if( ! empty( $trigger_data ) ){
			foreach( $trigger_data as $mas => $mad ){

				if( is_array( $mad ) ){
					foreach( $mad as $trigger_url_name => $single_trigger_data ){
						if( substr( $trigger_url_name, 0, 10 ) !== 'wpwh-flow-' ){
							$trigger_count++;
						}
					}
				}

			}
		}

		return $trigger_count;
	}

	private function wpwh_count_action_data(){
		$action_count = 0;

		$actions_data = WPWHPRO()->webhook->get_hooks( 'action' );
		if( ! empty( $actions_data ) ){
			foreach( $actions_data as $mas => $mad ){

				if( isset( $mad['api_key'] ) && is_string( $mad['api_key'] ) ){
					$action_count++;
				} else {
					if( is_array( $mad ) ){
						foreach( $mad as $action_url_name => $single_action_data ){
							if( substr( $action_url_name, 0, 10 ) !== 'wpwh-flow-' ){
								$action_count++;
							}
						}
					}
				}

			}
		}

		return $action_count;
	}

	private function wpwh_count_whitelist_data(){
		$whitelist_count = 0;
		$whitelist = WPWHPRO()->whitelist->get_list();

		if( is_array( $whitelist ) && is_array( $whitelist ) ){
			$whitelist_count = count( $whitelist );
		}

		return $whitelist_count;
	}

	private function wpwh_get_validated_flows_data(){
		$validated_flows_data = array(
			'active_flows_count' => 0,
			'inactive_flows_count' => 0,
			'triggers' => array(),
			'actions' => array(),
			'integrations' => array(),
			'integrations_count' => 0,
			'max_flow_actions' => 0,
			'flows' => array(),
		);
		$flows = WPWHPRO()->flows->get_flows();

		if( ! empty( $flows ) ){
			foreach( $flows as $template ){
				
				$single_flow = array(
					'trigger' => array(),
					'actions' => array(),
				);
				
				if( isset( $template->flow_status ) && ! empty( $template->flow_status ) ){
					if( $template->flow_status === 'inactive' ){
						$validated_flows_data['inactive_flows_count']++;
					} else {
						$validated_flows_data['active_flows_count']++;
					}
				}
				
				if( isset( $template->flow_trigger ) && ! empty( $template->flow_trigger ) ){

					$single_flow['trigger']['trigger'] = $template->flow_trigger;

					//locate the trigger integration
					$flow_trigger = WPWHPRO()->webhook->get_triggers( $template->flow_trigger );
					if( ! empty( $flow_trigger ) && is_array( $flow_trigger ) && isset( $flow_trigger['integration'] ) ){
						$single_flow['trigger']['integration'] = $flow_trigger['integration'];
					}

					if( ! isset( $validated_flows_data['triggers'][ $template->flow_trigger ] ) ){
						$validated_flows_data['triggers'][ $template->flow_trigger ] = 1;
					} else {
						$validated_flows_data['triggers'][ $template->flow_trigger ] = $validated_flows_data['triggers'][ $template->flow_trigger ] + 1;
					}
				}

				if( isset( $template->flow_config ) && is_object( $template->flow_config ) && isset( $template->flow_config->actions ) && ! empty( $template->flow_config->actions ) ){
					$max_flow_actions = 0;

					foreach( $template->flow_config->actions as $sak => $sav ){

						$max_flow_actions++;
		
						$action = '';
						if( isset( $sav->action ) ){
							$action = sanitize_title( $sav->action );
						}

						$single_flow['actions'][ $action ] = array( 
							'action' => $action,
							'integration' => '',
						 );
		
						if( ! empty( $action ) ){
							if( ! isset( $validated_flows_data['actions'][ $action ] ) ){
								$validated_flows_data['actions'][ $action ] = 1;
							} else {
								$validated_flows_data['actions'][ $action ] = $validated_flows_data['actions'][ $action ] + 1;
							}
						}
					}

					if( $max_flow_actions > $validated_flows_data['max_flow_actions'] ){
						$validated_flows_data['max_flow_actions'] = $max_flow_actions;
					}
				}

				$validated_flows_data['flows'][] = $single_flow;
			}
		}

		if( ! empty( $validated_flows_data['actions'] ) ){
			$actions = WPWHPRO()->webhook->get_actions();

			foreach( $validated_flows_data['actions'] as $single_action => $single_action_count ){
				if( is_array( $actions ) && isset( $actions[ $single_action ] ) && isset( $actions[ $single_action ]['integration'] ) ){

					$validated_integration = sanitize_title( $actions[ $single_action ]['integration'] );

					if( ! isset( $validated_flows_data['integrations'][ $validated_integration ] ) ){
						$validated_flows_data['integrations'][ $validated_integration ] = 1;
					} else {
						$validated_flows_data['integrations'][ $validated_integration ] = $validated_flows_data['integrations'][ $validated_integration ] + 1;
					}
				}
			}

			//append the flows action integrations
			if( isset( $validated_flows_data['flows'] ) && ! empty( $validated_flows_data['flows'] ) ){
				foreach( $validated_flows_data['flows'] as $validated_flow_data_key => $validated_flow_data ){
					if( isset( $validated_flows_data['flows'][ $validated_flow_data_key ]['actions'] ) ){
						foreach( $validated_flows_data['flows'][ $validated_flow_data_key ]['actions'] as $s_flow_key => $s_flow_data ){
							if( isset( $s_flow_data['action'] ) && isset( $actions[ $s_flow_data['action'] ] ) && isset( $actions[ $s_flow_data['action'] ]['integration'] ) ){
								$validated_flows_data['flows'][ $validated_flow_data_key ]['actions'][ $s_flow_key ]['integration'] = $actions[ $s_flow_data['action'] ]['integration'];
							}
						}
					}
				}
			}
		}

		if( ! empty( $validated_flows_data['integrations'] ) ){
			$validated_flows_data['integrations_count'] = count( $validated_flows_data['integrations'] );
		}

		return $validated_flows_data;
	}

}
