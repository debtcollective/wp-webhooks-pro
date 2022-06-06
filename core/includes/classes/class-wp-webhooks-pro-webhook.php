<?php

/**
 * WP_Webhooks_Pro_Webhook Class
 *
 * This class contains all of the available api functions
 *
 * @since 1.0.0
 */

/**
 * The webhook class of the plugin.
 *
 * @since 1.0.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Webhook {

	/**
	 * Add the option key 
	 *
	 * @var - the webhook option key
	 */
	private $webhook_options_key;

	/**
	 * Add the processed triggers 
	 *
	 * @since 4.3.2
	 * @var - the processed triggers
	 */
	private $processed_triggers = array();

	/**
	 * If an action call is present, this var contains the webhook
	 *
	 * @since 4.0.0
	 * @var - The currently present action webhook
	 */
	private $current_webhook_action = null;

	public function __construct() {
		$this->webhook_options_key = WPWHPRO()->settings->get_webhook_option_key();
		$this->webhook_ident_param = WPWHPRO()->settings->get_webhook_ident_param();
		$this->webhook_receivable_trigger_param = WPWHPRO()->settings->get_webhook_receivable_trigger_param();

		$this->webhook_options = $this->setup_webhooks();
		$this->add_hooks();
	}

	/**
	 * Add all necessary hooks for preloading the data
	 */
	private function add_hooks(){
		add_action( 'init', array( $this, 'validate_incoming_data' ), 100 );
		add_action( 'init', array( $this, 'execute_receivable_trigger' ), 50 );

		//Maybe migrate action URLs
		add_action( 'plugins_loaded', array( $this, 'wpwh_check_4_4_action_migration' ), 11 );
	}

	/**
	 * DO NOT USE ANYWHERE
	 * This function is for internal use only
	 *
	 * @return void
	 */
	public function wpwh_check_4_4_action_migration(){
		$endpoints = $this->get_hooks('action');
		$backup_export = null;

		if( is_array( $endpoints ) ){

			if( empty( $endpoints ) ){
				return; //nothing to do if no actions are given
			}	

			//If we made it until here, we can safely synchronize the old action URLs
			$actions = $this->get_actions();	
			foreach( $actions as $action_slug => $action_config ){

				if( empty( $action_slug ) ){
					continue;
				}
				
				foreach( $endpoints as $action_data_slug => $action_data ){

					if( ! is_array( $action_data ) ){
						continue;
					}

					if( ! isset( $action_data['api_key'] ) || ! is_string( $action_data['api_key'] ) ){
						/*
						 * no migration required if the first given value is not an api_key
						 * In the rare case of an action URL called api_key, we check against
						 * the type of the value as with the 5.0 notation it must be an array
						 */
						continue;
					}

					//only migrate if the action whitelist setting is set to avoid performance issues
					if( 
						! isset( $action_data['settings'] ) 
						|| ! isset( $action_data['settings']['wpwhpro_action_action_whitelist'] ) 
						|| ! is_array( $action_data['settings']['wpwhpro_action_action_whitelist'] )
						|| count( $action_data['settings']['wpwhpro_action_action_whitelist'] ) > 10
					){
						continue;
					}

					//only create if the given action was whitelisted
					if( ! in_array( $action_slug, $action_data['settings']['wpwhpro_action_action_whitelist'] ) ){
						continue;
					}


					if( $backup_export === null ){
						$backup_export = WPWHPRO()->tools->generate_plugin_export(); //store all available hooks
					}

					//add the webhook group
					$action_data['group'] = $action_slug;

					$migrated = $this->create( $action_data_slug, 'action', $action_data, $action_data['permission'] );
					if( $migrated ){
						$this->unset_hooks( $action_data_slug, 'action' );
					}
				}

			}

			//Make a backup of the existing and initial hooks if any migration has been done
			if( $backup_export !== null ){
				update_option( 'wpwh_before_migration_5_0_backup', $backup_export, false );
			}
			
			$this->reload_webhooks();
		}

	}

	/**
	 * ######################
	 * ###
	 * #### OPTION LOGIC
	 * ###
	 * ######################
	 */

	/**
	 * Initialize all available webhooks for
	 * a better performance
	 *
	 * @return array
	 */
	private function setup_webhooks(){
		$webhook_data = get_option( $this->webhook_options_key );

		if( empty( $webhook_data ) || ! is_array( $webhook_data ) ){
			$webhook_data = array();
		}

		foreach( $webhook_data as $wd_key => $wd_val ){

			switch( $wd_key ){
				case 'action':

					foreach( $webhook_data[ $wd_key ] as $wds_key => $wds_val ){
						if( is_array( $webhook_data[ $wd_key ][ $wds_key ] ) ){

							//If the api key is set and it contains a string, we know it belongs to the old structure before 5.0
							if( isset( $webhook_data[ $wd_key ][ $wds_key ]['api_key'] ) && is_string( $webhook_data[ $wd_key ][ $wds_key ]['api_key'] ) ){
								$webhook_data[ $wd_key ][ $wds_key ]['webhook_name'] = $wds_key;
							} else {
								//New structure since 5.0
								foreach( $webhook_data[ $wd_key ][ $wds_key ] as $wdss_key => $wdss_val ){
									if( is_array( $webhook_data[ $wd_key ][ $wds_key ][ $wdss_key ] ) ){
										$webhook_data[ $wd_key ][ $wds_key ][ $wdss_key ]['webhook_name'] = $wds_key;
										$webhook_data[ $wd_key ][ $wds_key ][ $wdss_key ]['webhook_url_name'] = $wdss_key;
									}
								}
							}
							
						}
					}
					break;
				case 'trigger':
					foreach( $webhook_data[ $wd_key ] as $wds_key => $wds_val ){
						if( is_array( $webhook_data[ $wd_key ][ $wds_key ] ) ){
							foreach( $webhook_data[ $wd_key ][ $wds_key ] as $wdss_key => $wdss_val ){
								if( is_array( $webhook_data[ $wd_key ][ $wds_key ][ $wdss_key ] ) ){
									$webhook_data[ $wd_key ][ $wds_key ][ $wdss_key ]['webhook_name'] = $wds_key;
									$webhook_data[ $wd_key ][ $wds_key ][ $wdss_key ]['webhook_url_name'] = $wdss_key;
								}
							}
						}
					}
					break;
			}

		}

		return $webhook_data;
	}

	/**
	 * Reload webhook hooks 
	 *
	 * @return array The webhook hooks
	 */
	public function reload_webhooks(){
		$this->webhook_options = $this->setup_webhooks();
		return $this->webhook_options;
	}

	/**
	 * Get all of the available webhooks
	 *
	 * This is the main handler function for all
	 * of our triggers and actions.
	 *
	 * @param string $type - the type of the hooks you want to get (trigger, action, all (default))
	 * @param string $group - Wether you want to display grouped ones or not
	 * @param string $single - In case you want to output a single one
	 *
	 * @return array|mixed - An array of the available webhooks
	 */
	public function get_hooks( $type = 'all', $group = '', $single = '' ){
		if( $type != 'all' ){
			if( isset( $this->webhook_options[ $type ] ) && ! empty( $group ) ){
				if( isset( $this->webhook_options[ $type ][ $group ] ) ){
					if( ! empty( $single ) ){

						$return = array();

						if( isset( $this->webhook_options[ $type ][ $group ][ $single ] ) ){
							$return = $this->webhook_options[ $type ][ $group ][ $single ];
						} else {
							//Provide backward compatibility for pre 5.0 actions
							if( $type === 'action' ){
								if( 
									isset( $this->webhook_options[ $type ][ $single ] ) 
									&& isset( $this->webhook_options[ $type ][ $single ]['api_key'] )
									&& is_string( $this->webhook_options[ $type ][ $single ]['api_key'] )
								){
									$return = $this->webhook_options[ $type ][ $single ];
								}
							}
						}
						
					} else {
						$return = $this->webhook_options[ $type ][ $group ];
					}
				} else {

					$return = array();

					//Provide backward compatibility for pre 5.0 actions
					if( $type === 'action' && ! empty( $single ) ){
						if( 
							isset( $this->webhook_options[ $type ][ $single ] ) 
							&& isset( $this->webhook_options[ $type ][ $single ]['api_key'] )
							&& is_string( $this->webhook_options[ $type ][ $single ]['api_key'] )
						){
							$return = $this->webhook_options[ $type ][ $single ];
						}
					}
					
				}
			} else {

				if( isset( $this->webhook_options[ $type ] ) ){
					if( ! empty( $single ) ){

						if( isset( $this->webhook_options[ $type ][ $single ] ) ){
							$return = $this->webhook_options[ $type ][ $single ];
						} else {
							$return = array();
						}
						
					} else {
						$return = $this->webhook_options[ $type ];
					}
				} else {
					//Return empty array if nothing is set
					$return = array();
				}

			}
		} else {
			$return = $this->webhook_options;
		}

		if( empty( $return ) ){
			$return = array();
		}

		return apply_filters( 'wpwhpro/admin/webhooks/get_hooks', $return, $type, $group, $single ) ;
	}

	/**
	 * Set custom webhooks inside of our array()
	 *
	 * @param $key - The key of the single webhook (not the idetifier)
	 * @param $type - the type of the hooks you want to get (triggers, actions, all (default))
	 * @param $data - (array) The custom data of the specified webhook
	 * @param string $group - (Optional) A webhook group
	 *
	 * @return bool - True if the hook was successfully set
	 */
	public function set_hooks( $key, $type, $data, $group = '' ){
		$return = false;

		if( empty( $key ) || empty( $type ) || empty( $data ) ){
			return $return;
		}

		if( ! isset( $this->webhook_options[ $type ] ) ){
			$this->webhook_options[ $type ] = array();
		}

		if( ! empty( $group ) ){
			if( ! isset( $this->webhook_options[ $type ][ $group ] ) ){
				$this->webhook_options[ $type ][ $group ] = array();
			}

			$this->webhook_options[$type][ $group ][ $key ] = $data;
			$return = update_option( $this->webhook_options_key, $this->webhook_options );
		} else {
			$this->webhook_options[$type][ $key ] = $data;
			$return = update_option( $this->webhook_options_key, $this->webhook_options );
		}

		return $return;
	}

	/**
	 * Remove a hook from the currently set arrays
	 *
	 * @param $webhook - The slug of the webhook
	 * @param $type - the type of the hooks you want to get (triggers, actions, all (default))
	 * @param string $group - (Optional) A webhook group
	 *
	 * @return bool - Wether the webhook was deleted or not
	 */
	public function unset_hooks( $webhook, $type, $group = '' ){	

		if( empty( $webhook ) || empty( $type ) )
			return false;


		if( isset( $this->webhook_options[ $type ] ) ){
			if( ! empty( $group ) ){
				if( isset( $this->webhook_options[ $type ][$group][ $webhook ] ) ){
					unset( $this->webhook_options[ $type ][$group][ $webhook ] );
					return update_option( $this->webhook_options_key, $this->webhook_options );
				}
			} else {
				if( isset( $this->webhook_options[ $type ][ $webhook ] ) ){
					unset( $this->webhook_options[ $type ][ $webhook ] );
					return update_option( $this->webhook_options_key, $this->webhook_options );
				}
			}
		} else {
			//return true if it doesnt exist
			return true;
		}

		return false;
	}

	/**
	 * Register a new webhook URL
	 *
	 * @param $webhook - The webhook name
	 * @param $type - the type of the hooks you want to get (triggers, actions, all (default))
	 * @param array $args - Custom attributes depending on the webhooks
	 * @param string $permission - in case a custom permission is set
	 *
	 * @return bool - Wether the webhook url was created or not
	 */
	public function create( $webhook, $type, $args = array(), $permission = '' ){

		if( empty( $webhook ) || empty( $type ) ){
			return false;
		}

		$permission_set = WPWHPRO()->settings->get_admin_cap('default_webhook');
		if( ! empty( $permission ) ){
			$permission_set = $permission;
		}

		$data = array(
			'permission'    => $permission_set,
			'date_created'  => date( 'Y-m-d H:i:s' )
		);

		$group = '';
		switch( $type ){
			case 'action':

				if( isset( $args['api_key'] ) && ! empty( $args['api_key'] ) ){
					$data['api_key'] = $args['api_key'];
				} else {
					$data['api_key'] = $this->generate_api_key();
				}

				if( isset( $args['settings'] ) && is_array( $args['settings'] ) ){
					$data['settings'] = $args['settings'];
				}

				if( isset( $args['date_created'] ) && ! empty( $args['date_created'] ) ){
					$data['date_created'] = $args['date_created'];
				}

				if( isset( $args['status'] ) && ! empty( $args['status'] ) ){
					$data['status'] = $args['status'];
				}

				//required since 5.0
				if( isset( $args['group'] ) ){
					$group = $args['group'];
				}

				break;
			case 'trigger':
				$data['webhook_url'] = $args['webhook_url'];

				if( isset( $args['secret'] ) && ! empty( $args['secret'] ) ){
					$data['secret'] = $args['secret'];
				} else {
					$data['secret'] = $this->generate_trigger_secret();
				}

				if( isset( $args['date_created'] ) && ! empty( $args['date_created'] ) ){
					$data['date_created'] = $args['date_created'];
				}

				if( isset( $args['settings'] ) && is_array( $args['settings'] ) ){
					$data['settings'] = $args['settings'];
				}

				if( isset( $args['status'] ) && ! empty( $args['status'] ) ){
					$data['status'] = $args['status'];
				}

				$group = $args['group'];
				break;
		}

		$check = $this->set_hooks( $webhook, $type, $data, $group );

		if( $check ){
			$this->reload_webhooks();
		}

		return $check;

	}

	/**
	 * Update an existig webhook URL
	 *
	 * @param $key - The webhook identifier
	 * @param $type - the type of the hooks you want to get (triggers, actions, all (default))
	 * @param array $args - Custom attributes depending on the webhooks
	 *
	 * @return bool - Wether the webhook was updated or not
	 */
	public function update( $key, $type, $group = '', $args = array() ){

		if( empty( $key ) || empty( $type ) ){
			return false;
		}

		$current_hooks = $this->get_hooks();
		$group = ( ! empty( $group ) ) ? $group : '';


		$data = array();

		if( ! empty( $group ) ){
			if( isset( $current_hooks[ $type ] ) ){
				if( isset( $current_hooks[ $type ][ $group ] ) ){
					if( isset( $current_hooks[ $type ][ $group ][ $key ] ) ){
						$data = $current_hooks[ $type ][ $group ][ $key ];
					}
				}
			}
		} else {
			if( isset( $current_hooks[ $type ] ) ){
				if( isset( $current_hooks[ $type ][ $key ] ) ){
					$data = $current_hooks[ $type ][ $key ];
				}
			}
		}

		$check = false;
		if( ! empty( $data ) ){
			$data = array_merge( $data, $args );

			//Revalidate the settings data with the $data array
			if( isset( $args['settings'] ) ){

				$data['settings'] = $args['settings'];

				//Remove empty entries since we don't want to save what's not necessary
				foreach( $data['settings'] as $skey => $sdata ){
					if( $sdata === '' ){
						unset( $data['settings'][ $skey ] );
					}
				}

			}

			$check = $this->set_hooks( $key, $type, $data, $group );
		}

		if( $check ){
			$this->reload_webhooks();
		}

		return $check;

	}

	/**
	 * Initialize the default webhook url
	 * This function is deprecated since version 5.0
	 * as actions have been moved to the sub structure of endpoints
	 * 
	 * @deprecated 5.0
	 */
	public function initialize_default_webhook(){

		WPWHPRO()->helpers->log_issue( WPWHPRO()->helpers->translate( "The function WPWHPRO()->webhook->initialize_default_webhook() is deprecated since 5.0. Webhook actions are now available for each endpoint separately.", 'admin-debug-feature' ) );
		return;

		if( ! empty( $this->webhook_options['action'] ) ){
			return;
		}

		$default_wehook = apply_filters( 'wpwhpro/admin/webhooks/default_webhook_name', 'main_' . rand( 1000, 9999 ) );

		$data = array(
			'api_key'       => $this->generate_api_key(),
			'permission'    => WPWHPRO()->settings->get_admin_cap('default_webhook'),
			'date_created'  => date( 'Y-m-d H:i:s' )
		);
		$this->set_hooks( $default_wehook, 'action', $data );

	}

	public function generate_api_key( $length = 64 ){

		if( ! is_int( $length ) ){
			$length = 64; //Fallack on non-integers
		}

		$password = strtolower( wp_generate_password( $length, false ) );

		return apply_filters( 'wpwhpro/admin/webhooks/generate_api_key', $password, $length );
	}

	public function generate_trigger_secret( $length = 32 ){

		if( ! is_int( $length ) ){
			$length = 32; //Fallack on non-integers
		}

		$password = strtolower( wp_generate_password( $length, false ) );

		return apply_filters( 'wpwhpro/admin/webhooks/generate_trigger_secret', $password, $length );
	}

	/**
	 * Return a list of all available, processed triggers
	 *
	 * @return array
	 */
	public function get_processed_triggers(){
		return apply_filters( 'wpwhpro/admin/webhooks/get_processed_triggers', $this->processed_triggers );
	}

	/**
	 * Set a trigger to the processed trigger list
	 *
	 * @return array
	 */
	public function set_processed_trigger( $trigger, $data ){

		$all_processed_triggers = $this->get_processed_triggers();

		if( is_array( $all_processed_triggers ) ){
			$all_processed_triggers[ $trigger ] = $data;
		}

		$this->processed_triggers = $all_processed_triggers;

		return apply_filters( 'wpwhpro/admin/webhooks/set_processed_trigger', $this->processed_triggers );
	}

	/**
	 * ######################
	 * ###
	 * #### CORE LOGIC
	 * ###
	 * ######################
	 */

	/*
	 * The core logic for reseting our plugin
	 *
	 * @since 1.6.4
	 */
	public function reset_wpwhpro(){

		//Reset settings
		$settings = WPWHPRO()->settings->get_settings();
		foreach( $settings as $key => $value ){
			if( $key ){
				delete_option( $key );
			}
		}

		//Reset Whitelist
		WPWHPRO()->whitelist->delete_item('all');
		delete_option( WPWHPRO()->settings->get_whitelist_requests_option_key() );

		//Reset Whitelabel logic
		delete_option( WPWHPRO()->settings->get_whitelabel_settings_option_key() );

		//Reset active webhook parameter and all its data
		delete_option( WPWHPRO()->settings->get_active_webhooks_ident() );

		//Reset all the webhook settings
		delete_option( WPWHPRO()->settings->get_webhook_option_key() );

		//Reset licensing setup & deactivate license
		$license_key = WPWHPRO()->settings->get_license('key');
		if( ! empty( $license_key ) ){
			WPWHPRO()->license->deactivate( array( 'license' => $license_key ) );
		}
		delete_option( WPWHPRO()->settings->get_license_option_key() );

		//Reset the log key - Keep for backwards compatibility
		delete_option( 'wpwhpro_activate_logs' );

		//Delete a possible backup key from the 5.0 action migration
		delete_option( 'wpwh_before_migration_5_0_backup' );

		//Reset the flows tables
		WPWHPRO()->flows->delete_table();
		WPWHPRO()->flows->delete_logs_table();

		//Reset the log table
		WPWHPRO()->logs->delete_table();

		//Reset data mapping
		WPWHPRO()->data_mapping->delete_table();

		//Reset authentication
		WPWHPRO()->auth->delete_table();

		//Reset transients
		delete_transient( WPWHPRO()->settings->get_news_transient_key() );
		delete_transient( WPWHPRO()->settings->get_extensions_transient_key() );
		delete_transient( WPWHPRO()->settings->get_license_transient_key() );

		//Reset custom post meta entries
		WPWHPRO()->sql->run( "DELETE FROM {postmeta} WHERE meta_key LIKE 'wpwhpro_create_post_temp_status%';" );

	}

	/**
	 * Create the webhook url for the specified webhook
	 *
	 * @param $webhook - the webhook ident
	 * @param $api_key - the api key on the webhook
	 *
	 * @return string - the webhook url
	 */
	public function built_url( $webhook, $api_key, $additional_args = array() ){

		$args = array_merge( array(
			$this->webhook_ident_param => $webhook,
			'wpwhpro_api_key' => $api_key
		), $additional_args );

		$args = apply_filters( 'wpwhpro/admin/webhooks/url_args', $args, $additional_args );

		$url = add_query_arg( $args, WPWHPRO()->helpers->safe_home_url( '/' ) );
		return $url;
	}

	/**
	 * Create the receivable trigger URL for a specific trigger
	 *
	 * @param $group - The trigger group (e.g. post_update)
	 * @param $trigger - the trigger URL name
	 * @param $additional_args - additional arguments for the URL
	 * @since 4.3.7
	 *
	 * @return string - the webhook url
	 */
	public function built_trigger_receivable_url( $group, $trigger, $additional_args = array() ){
		$url = false;

		if( empty( $group ) || empty( $trigger ) ){
			return $url;
		}

		$trigger = $this->get_hooks( 'trigger', $group, $trigger );

		if( ! isset( $trigger['secret'] ) || empty( $trigger['secret'] ) ){
			return $url;
		}

		$data = array( 
			'date_created' => $trigger['date_created'],
			'webhook_name' => $trigger['webhook_name'],
			'webhook_url_name' => $trigger['webhook_url_name'],
		 );
		$signature = strtr( $this->generate_trigger_signature( json_encode( $data ), $trigger['secret'] ), '+/=', '._-' );

		$args = array_merge( $additional_args, array(
			$this->webhook_receivable_trigger_param . '_group' => $trigger['webhook_name'],
			$this->webhook_receivable_trigger_param . '_name' => $trigger['webhook_url_name'],
			$this->webhook_receivable_trigger_param => urlencode( $signature ),
		) );

		$args = apply_filters( 'wpwhpro/admin/webhooks/trigger_receivable_url_args', $args, $additional_args );

		$url = WPWHPRO()->helpers->built_url( WPWHPRO()->helpers->safe_home_url( '/' ), $args );
		return $url;
	}

	/**
	 * Function to output all the available arguments for actions
	 *
	 * @since 3.0.7
	 * @param array $args
	 */
	public function echo_action_data( $args = array() ){

		$current_webhook = $this->get_current_webhook_action();
		$action = $this->get_incoming_action();

		if( is_array( $current_webhook ) && isset( $current_webhook['settings'] ) && ! empty( $current_webhook['settings'] ) ){

			foreach( $current_webhook['settings'] as $settings_name => $settings_data ){

				if( $settings_name === 'wpwhpro_action_data_mapping_response' && ! empty( $settings_data ) ){

					//An error caused by the Flows feature to save errors as arrays
					if( is_array( $settings_data ) && isset( $settings_data[0] ) ){
						$settings_data = $settings_data[0];
					}

					if( is_numeric( $settings_data ) ){
						$template = WPWHPRO()->data_mapping->get_data_mapping( $settings_data );
						if( ! empty( $template ) && ! empty( $template->template ) ){
							$sub_template_data = base64_decode( $template->template );
							if( ! empty( $sub_template_data ) && WPWHPRO()->helpers->is_json( $sub_template_data ) ){
								$template_data = json_decode( $sub_template_data, true );
								if( ! empty( $template_data ) ){
									$args = WPWHPRO()->data_mapping->map_data_to_template( $args, $template_data, 'trigger' ); //Map it as a trigger since it leaves the site
								}
							}
						}
					}

				}

			}

		}
		
		$validated_data = $this->echo_response_data( $args );

		do_action( 'wpwhpro/webhooks/echo_action_data', $action, $validated_data );

		return $validated_data;
	}

	/**
	 * Function to output all the available json arguments.
	 *
	 * @param array $args
	 */
	public function echo_response_data( $args = array(), $compromised = false ){
		$return = array(
			'arguments' => $args,
			'response_type' => '',
		);

		$filter_prefix = '';
		if( $compromised ){
			$filter_prefix = 'compromised_';
		}

		$response_type = WPWHPRO()->http->get_current_request_content_type();

		if( empty( $response_type ) ){
			$response_type = 'application/json';
		}

		$response_type = apply_filters( 'wpwhpro/webhooks/' . $filter_prefix . 'response_response_type', $response_type, $args );
		$args = apply_filters( 'wpwhpro/webhooks/' . $filter_prefix . 'response_json_arguments', $args, $response_type );

		if( strpos( $response_type, 'application/xml' ) !== FALSE ){
			header( 'Content-Type: application/xml' );
			$xml = new SimpleXMLElement('<root/>');
			array_walk_recursive($args, array ($xml, 'addChild'));
			print $xml->asXML();
		} else {
			header( 'Content-Type: application/json' );
			echo json_encode( $args );
		}

		$return['arguments'] = $args;
		$return['response_type'] = $response_type;

		return $return;
	}

	/**
	 * ######################
	 * ###
	 * #### RECIPIENTS LOGIC
	 * ###
	 * ######################
	 */

	/**
	 * Display the actions in our backend actions table
	 *
	 * The structure to include your recpient looks like this:
	 * array( 'action' => 'my-action', 'parameter' => array( 'my_parameter' => array( 'short_description => 'my text', 'required' => true ) ), 'short_description' => 'This is my short description.', 'description' => 'My HTML Content' )
	 */
	public function get_actions( $active_webhooks = true ){
		$actions = WPWHPRO()->integrations->get_actions();
		return apply_filters( 'wpwhpro/webhooks/get_webhooks_actions', $actions, $active_webhooks );
	}
	/**
	 * Display the actions in our frontend actions table
	 *
	 * The structure to include your recpient looks like this:
	 * array( 'action' => 'my-action', 'parameter' => array( 'my_parameter' => array( 'short_description => 'my text', 'required' => true ) ), 'short_description' => 'This is my short description.', 'description' => 'My HTML Content' )
	 */
	public function get_triggers( $single = '', $active_webhooks = true ){

		$triggers = WPWHPRO()->integrations->get_triggers();

		$triggers = apply_filters( 'wpwhpro/webhooks/get_webhooks_triggers', $triggers, $active_webhooks );

		if( ! empty( $single ) ){
			if( isset( $triggers[ $single ] ) ){
				return $triggers[ $single ];
			} else {
				return false;
			}
		} else {
			return $triggers;
		}

	}

	/**
	 * Find a webhook action based on given webhook data
	 * 
	 * @since 5.0
	 *
	 * @param string $webhook_name
	 * @param string $api_key
	 * @return void
	 */
	private function find_action_group_from_webhook( $webhook_name, $api_key ){
		$webhook_action = false;
		$webhooks = $this->get_hooks( 'action' );

		if( ! empty( $webhooks ) ){
			foreach( $webhooks as $webhook_group => $webhook_data ){

				//it can only be found on 5.0 URLs
				if( ! isset( $webhook_data['api_key'] ) || ! is_string( $webhook_data['api_key'] ) ){
					if( isset( $webhook_data[ $webhook_name ] ) ){
						$webhook_action = $webhook_group;
						break;
					}
				}

			}
		}

		return $webhook_action;
	}

	/**
	 * Get the currently present webhook action
	 * 
	 * This feature is different from the get_incoming_action 
	 * as an action could potentially have multiple requests
	 *
	 * @since 4.0.0
	 * @return mixed Array on success, null on no webhook given
	 */
	public function get_current_webhook_action(){
		return apply_filters( 'wpwhpro/webhooks/get_current_webhook_action', $this->current_webhook_action );
	}

	/**
	 * Get the action from the current request
	 * 
	 * Since v5.0, we prioritise request parameters 
	 */
	public function get_incoming_action( $response_body = false ){

		if( ! empty( $_REQUEST['action'] ) ){
			$action = sanitize_title( $_REQUEST['action'] );
		} else {
			$action = '';
		}
		
		if( empty( $action ) ){
			if( $response_body === false ){
				$response_body = WPWHPRO()->http->get_current_request();
			}

			$action = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'action' );

			if( empty( $action ) ){
				WPWHPRO()->helpers->log_issue( WPWHPRO()->helpers->translate( "The incoming webhook call did not contain any action argument.", 'admin-debug-feature' )  );
			}
		}

		return apply_filters( 'wpwhpro/webhooks/get_incoming_action', $action, $response_body );
	}

	/**
	 * Get a properly formatted description of a webhook endpoint
	 *
	 * @since 4.2.2
	 * @return string the HTML formatted webhook description
	 */
	public function get_endpoint_description( $type = 'trigger', $data = array() ){

		$description = '';

		switch( $type ){
			case 'trigger':
				ob_start();
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/descriptions/trigger.php' );
				$description = ob_get_clean();
				break;
			case 'action':
				ob_start();
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/descriptions/action.php' );
				$description = ob_get_clean();
				break;
		}

		return apply_filters( 'wpwhpro/webhooks/get_endpoint_description', $description, $type, $data );
	}

	/**
	 * Validate an incoming webhook action
	 */
	public function validate_incoming_data(){
		
		$response_auth_request = ( isset( $_REQUEST['wpwhpro_auth_response'] ) && intval( $_REQUEST['wpwhpro_auth_response'] ) === 1 ) ? true : false;
		$response_api_key = ! empty( $_REQUEST['wpwhpro_api_key'] ) ? sanitize_key( $_REQUEST['wpwhpro_api_key'] ) : '';
		$response_ident_value = ! empty( $_REQUEST[$this->webhook_ident_param] ) ? sanitize_key( $_REQUEST[$this->webhook_ident_param] ) : '';

		if( empty( $response_api_key ) || empty( $response_ident_value ) ){
			return;
		}

		//Setup default response
		$return = array(
			'success' => false
		);

		//Define flow
		$is_flow = false;
		$flow_id = 0;
		$flow_action_key = '';
		$flow_log_id = 0;
		if( strpos( $response_ident_value, 'wpwh-flow-' ) !== FALSE && substr( $response_ident_value, 0, 10 ) === 'wpwh-flow-' ){
			$flow_data = str_replace( 'wpwh-flow-', '', $response_ident_value );
			$flow_data_array = explode( '-', $response_ident_value );
			if( 
				isset( $flow_data_array[0] ) 
				&& ! empty( $flow_data_array[0] )
				&& isset( $flow_data_array[1] )
				&& ! empty( $flow_data_array[1] )
			){
				$is_flow = true;
				$flow_id = intval( $flow_data_array[0] );
				$flow_action_key = sanitize_title( $flow_data_array[1] );

				if( isset( $flow_log_id ) ){
					$flow_log_id = intval( $flow_log_id );
				}
			}
		}

		if( WPWHPRO()->whitelist->is_active() ){
			if( ! WPWHPRO()->whitelist->is_valid_request() ){
				WPWHPRO()->whitelist->add_request( WPWHPRO()->helpers->get_current_ip() );

				status_header( 403 );
				$return['msg'] = WPWHPRO()->helpers->translate( 'You don\'t have permission to post data to this site since you are not on the whitelist.', 'webhooks-invalid-ip-invalid' );
				WPWHPRO()->webhook->echo_response_data( $return, true );
				exit;
			}
		}

		$response_body = WPWHPRO()->http->get_current_request();	
		$action = $this->get_incoming_action( $response_body );
		$webhook = $this->get_hooks( 'action', $action, $response_ident_value );

		// set the output to be JSON. (Default)
		header( 'Content-Type: application/json' );

		/**
		 * Required for compatibility with authentication URLs
		 * This is necessary to keep third-party apps compatible
		 * For security reasons, this function is only available 
		 * for authentication requests.
		 */
		if( $response_auth_request && empty( $webhook ) ){
			$found_action = $this->find_action_group_from_webhook( $response_ident_value, $response_api_key );
			if( ! empty( $found_action ) ){
				$webhook = $this->get_hooks( 'action', $found_action, $response_ident_value );
			}
		}

		//Validate against inactive action webhooks
		if( is_array( $webhook ) && isset( $webhook['status'] ) ){
			if( $webhook['status'] === 'inactive' ){
				status_header( 403 );
				$return['msg'] = sprintf( WPWHPRO()->helpers->translate( 'Your current %s webhook action URL is deactivated. Please activate it first.', 'webhooks-deactivated-webhook' ), WPWHPRO()->settings->get_page_title() );
				WPWHPRO()->webhook->echo_response_data( $return, true );
				exit;
			}
		}

		if( ! empty( $webhook ) && is_array( $webhook ) ){
			if( isset( $webhook['api_key'] ) ){
				if( $webhook['api_key'] != $response_api_key ){
					status_header( 403 );
					$return['msg'] = sprintf( WPWHPRO()->helpers->translate( 'The given %s API Key is not valid, please enter a valid API key and try again.', 'webhooks-invalid-license-invalid' ), WPWHPRO()->settings->get_page_title() );
					WPWHPRO()->webhook->echo_response_data( $return, true );
					exit;
				}
			} else {
				status_header( 403 );
				$return['msg'] = sprintf( WPWHPRO()->helpers->translate( 'The given %s API Key is missing, please add it first.', 'webhooks-invalid-license-missing' ), WPWHPRO()->settings->get_page_title() );
				WPWHPRO()->webhook->echo_response_data( $return, true );
				exit;
			}
			
		} else {
			status_header( 403 );
			$return['msg'] = sprintf( WPWHPRO()->helpers->translate( 'We could not locate a webhook action URL for your given data. Please make sure you set the URL (query) parameter &action=youraction', 'webhooks-invalid-license-missing' ), WPWHPRO()->settings->get_page_title() );
			WPWHPRO()->webhook->echo_response_data( $return, true );
			exit;
		}

		$this->current_webhook_action = $webhook;

		$log_data = array(
			'webhook_type' => ( $is_flow ) ? 'flow_action' : 'action',
			'identifier' => WPWHPRO()->helpers->get_current_ip(),
			'webhook_url_name' => $response_ident_value,
			'webhook_name' => $action,
			'request_data' => $response_body,
			'log_version' => WPWHPRO_VERSION,
		);

		if( $is_flow ){
			$log_data['flow_data'] = array(
				'flow_id' => $flow_id,
				'flow_action_key' => $flow_action_key,
				'flow_log_id' => $flow_log_id,
			);
			$message = WPWHPRO()->helpers->translate( 'Flow action request received.', 'wpwhpro-admin-webhooks' );
		} else {
			$message = WPWHPRO()->helpers->translate( 'Action request received.', 'wpwhpro-admin-webhooks' );
		}

		//Return auth request
		if( $response_auth_request ){
			$return_auth = array(
				'success' => true,
				'msg' => WPWHPRO()->helpers->translate( 'The authentication was successful', 'webhooks-auth-response-success' ),
				'domain' => home_url(),
				'name' => ( ! empty( $response_ident_value ) ) ? $response_ident_value : 'none'
			);

			$webhook_response = WPWHPRO()->webhook->echo_response_data( $return_auth );

			$log_data['response_data'] = $webhook_response;
			WPWHPRO()->logs->add_log( $message, $log_data );
			die();
		}

		if( is_array($webhook) && isset( $webhook['settings'] ) && ! empty( $webhook['settings'] ) ){

			foreach( $webhook['settings'] as $settings_name => $settings_data ){

				if( $settings_name === 'wpwhpro_action_access_token' && ! empty( $settings_data ) ){
					$access_token = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'access_token' );

					if( empty( $access_token ) || $access_token !== $settings_data ){
						status_header( 403 );
						$return['msg'] = WPWHPRO()->helpers->translate( 'Your configured access token for this webhook URL is not valid.', 'webhooks-invalid-access-token' );
						
						$webhook_response = WPWHPRO()->webhook->echo_response_data( $return );

						$log_data['response_data'] = $webhook_response;
						WPWHPRO()->logs->add_log( $message, $log_data );
						die();
					}
				}

				if( $settings_name === 'wpwhpro_action_accepted_methods' && ! empty( $settings_data ) ){
					$current_request_method = WPWHPRO()->helpers->get_current_request_method();

					if( empty( $current_request_method ) || ! in_array( $current_request_method, $settings_data ) ){
						status_header( 403 );
						$return['msg'] = WPWHPRO()->helpers->translate( 'Your current request method is not allowed. Please use only the request methods you selected within the webhook action URL settings.', 'webhooks-invalid-access-token' );
						
						$webhook_response = WPWHPRO()->webhook->echo_response_data( $return );

						$log_data['response_data'] = $webhook_response;
						WPWHPRO()->logs->add_log( $message, $log_data );
						die();
					}
				}

				if( $settings_name === 'wpwhpro_action_action_whitelist' && ! empty( $settings_data ) ){
					if( ! in_array( $action, $settings_data ) ){
						status_header( 403 );
						$return['msg'] = WPWHPRO()->helpers->translate( 'You don\'t have permission to use this specific webhook action.', 'webhooks-invalid-access-token' );
						
						$webhook_response = WPWHPRO()->webhook->echo_response_data( $return );

						$log_data['response_data'] = $webhook_response;
						WPWHPRO()->logs->add_log( $message, $log_data );
						die();
					}
				}

				if( $settings_name === 'wpwhpro_action_authentication' && ! empty( $settings_data ) ){

					if( is_numeric( $settings_data ) ){
						$is_valid_auth = WPWHPRO()->auth->verify_incoming_request( $settings_data );

						if( empty( $is_valid_auth['success'] ) ){
							status_header( 401 );
							$return['msg'] = $is_valid_auth['msg'];

							$webhook_response = WPWHPRO()->webhook->echo_response_data( $return );

							$log_data['response_data'] = $webhook_response;
							WPWHPRO()->logs->add_log( $message, $log_data );
							die();
						}
					}
				}

			}

		}

		$default_return_data = array(
            'success' => false,
			'action' => $action,
			'msg' => WPWHPRO()->helpers->translate("It looks like your current webhook call has no action argument defined, it is deactivated or it does not have any action function.", 'action-add-webhook-actions' ),
        );

		if( apply_filters( 'wpwhpro/webhooks/validate_webhook_action', true, $action, $response_ident_value, $response_api_key ) ){
			//Keep the old hook to keep other extensions working (Extensions need to adjust as this way we won't be able to catch the response)
			do_action( 'wpwhpro/webhooks/add_webhooks_actions', $action, $response_ident_value, $response_api_key );

			//since 4.2.0
			$return_data = WPWHPRO()->integrations->execute_actions( $default_return_data, $action );
		} else {
			$default_return_data['msg'] = WPWHPRO()->helpers->translate("The webhook action was prevented from execution due to the wpwhpro/webhooks/validate_webhook_action filter returning false.", 'action-add-webhook-actions' );
			$return_data = $default_return_data;
		}

		$return_data = apply_filters( 'wpwhpro/webhooks/add_webhook_actions', $return_data, $action, $response_ident_value, $response_api_key );

		//Maybe fire a custom action
		$custom_action = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'wpwh_call_action' );
		if( ! empty( $custom_action ) ){
			$validated_custom_action = sanitize_text_field( $custom_action );
			do_action( $validated_custom_action, $return_data, $response_body );
		}
		
		if( $return_data === $default_return_data ){
			$webhook_response = WPWHPRO()->webhook->echo_response_data( $return_data );
		} elseif( $return_data === null ){
			$webhook_response = WPWHPRO()->webhook->echo_response_data( $default_return_data );
		} else {
			$webhook_response = WPWHPRO()->webhook->echo_action_data( $return_data );
		}
		
		$log_data['response_data'] = $webhook_response;
		WPWHPRO()->logs->add_log( $message, $log_data );
		die();
	}

	public function generate_trigger_signature( $data, $secret ) {
		$hash_signature = apply_filters( 'wpwhpro/admin/webhooks/webhook_trigger_signature', 'sha256', $data );

		return base64_encode( hash_hmac( $hash_signature, $data, $secret, true ) );
	}

	/**
	 * Locates and executes a receivable trigger call if given
	 * 
	 * @since 4.3.7
	 *
	 * @return void
	 */
	public function execute_receivable_trigger(){

		//Keep separate if/else for better performance
		$receivable_trigger_signature = ! empty( $_REQUEST[ $this->webhook_receivable_trigger_param ] ) ? urldecode( $_REQUEST[ $this->webhook_receivable_trigger_param ] ) : '';
		if( empty( $receivable_trigger_signature ) ){
			return;
		}

		$response_auth_request = ( isset( $_REQUEST['wpwhpro_auth_response'] ) && intval( $_REQUEST['wpwhpro_auth_response'] ) === 1 ) ? true : false;
		$receivable_trigger_signature = strtr( $receivable_trigger_signature, '._-', '+/=');
		$receivable_trigger_group = ! empty( $_REQUEST[ $this->webhook_receivable_trigger_param . '_group' ] ) ? sanitize_key( $_REQUEST[ $this->webhook_receivable_trigger_param . '_group' ] ) : '';
		$receivable_trigger_name = ! empty( $_REQUEST[ $this->webhook_receivable_trigger_param . '_name' ] ) ? sanitize_key( $_REQUEST[ $this->webhook_receivable_trigger_param . '_name' ] ) : '';
		
		if( empty( $receivable_trigger_group ) || empty( $receivable_trigger_name ) ){
			return;
		}

		$return = array(
			'success' => false,
			'msg' => '',
		);

		if( WPWHPRO()->whitelist->is_active() ){
			if( ! WPWHPRO()->whitelist->is_valid_request() ){
				WPWHPRO()->whitelist->add_request( WPWHPRO()->helpers->get_current_ip() );

				status_header( 403 );
				$return['msg'] = WPWHPRO()->helpers->translate( 'You don\'t have permission to post data to this site since you are not on the whitelist.', 'webhooks-invalid-ip-invalid' );
				WPWHPRO()->webhook->echo_response_data( $return, true );
				exit;
			}
		}

		$trigger = $this->get_hooks( 'trigger', $receivable_trigger_group, $receivable_trigger_name );
		
		if( empty( $trigger ) ){
			status_header( 403 );
			$return['msg'] = WPWHPRO()->helpers->translate( 'We could not find a valid trigger for your given URL.', 'webhooks-receivable-no-trigger' );
			WPWHPRO()->webhook->echo_response_data( $return, true );
			exit;
		}

		if( ! isset( $trigger['secret'] ) || empty( $trigger['secret'] ) || ! is_string( $trigger['secret'] ) ){
			status_header( 403 );
			$return['msg'] = WPWHPRO()->helpers->translate( 'We could not verify your receivable trigger URL.', 'webhooks-receivable-no-trigger' );
			WPWHPRO()->webhook->echo_response_data( $return, true );
			exit;
		}

		$data = array( 
			'date_created' => $trigger['date_created'],
			'webhook_name' => $trigger['webhook_name'],
			'webhook_url_name' => $trigger['webhook_url_name'],
		 );
		$real_signature = $this->generate_trigger_signature( json_encode( $data ), $trigger['secret'] );
			
		if( $receivable_trigger_signature !== $real_signature ){
			status_header( 403 );
			$return['msg'] = WPWHPRO()->helpers->translate( 'Your given webhook trigger URL is not valid.', 'webhooks-receivable-not-valid' );
			WPWHPRO()->webhook->echo_response_data( $return, true );
			exit;
		}

		$trigger_group = $this->get_triggers( $trigger['webhook_name'] );
		if( empty( $trigger_group ) || ! isset( $trigger_group['receivable_url'] ) || $trigger_group['receivable_url'] !== true ){
			status_header( 403 );
			$return['msg'] = WPWHPRO()->helpers->translate( 'Your given webhook trigger has no permission to do this.', 'webhooks-receivable-not-valid' );
			WPWHPRO()->webhook->echo_response_data( $return, true );
			exit;
		}

		//Return auth request
		if( $response_auth_request ){
			$return_auth = array(
				'success' => true,
				'msg' => WPWHPRO()->helpers->translate( 'The authentication was successful', 'webhooks-auth-response-success' ),
				'domain' => home_url(),
				'webhook_name' => ( isset( $trigger['webhook_name'] ) ) ? $trigger['webhook_name'] : '',
				'webhook_url_name' => ( isset( $trigger['webhook_url_name'] ) ) ? $trigger['webhook_url_name'] : '',
			);

			WPWHPRO()->webhook->echo_response_data( $return_auth );
			exit;
		}

		$default_return_data = array(
            'success' => false,
			'msg' => WPWHPRO()->helpers->translate("The receivable trigger URL has been successfully executed.", 'action-add-webhook-actions' ),
        );

		$trigger_response = WPWHPRO()->integrations->execute_receivable_triggers( $default_return_data, $trigger['webhook_name'], $trigger['webhook_url_name'] );
	
		WPWHPRO()->webhook->echo_response_data( $trigger_response );
		exit;
	}

	/**
	 * Our external API Call to post a certain trigger
	 *
	 * @param $url
	 * @param $data
	 *
	 * @return array
	 */
	public function post_to_webhook( $webhook, $data, $args = array(), $skip_validation = false ){

		//Preserve original values
		$original_webhook = $webhook;
		$original_data = $data;
		$original_args = $args;
		$original_validation = $skip_validation;

		/*
		 * Allow also to send the whole webhook
		 * @since 1.6.4
		 */
		if( is_array( $webhook ) ){
			$url = $webhook['webhook_url'];
		} else {
			$url = $webhook;
		}

		$url_unvalidated = $url;

		/*
		 * Validate default settings
		 *
		 * @since 1.6.4
		 */
		$response = array(
			'success' => false,
			'is_valid' => true,
		);
		$response_content_type_slug = 'json';
		$response_content_type_method = 'POST';
		$response_content_type = 'application/json';
		$webhook_name = ( is_array($webhook) && isset( $webhook['webhook_name'] ) ) ? $webhook['webhook_name'] : '';
		$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : '';
		$webhook_secret = ( is_array($webhook) && isset( $webhook['secret'] ) ) ? $webhook['secret'] : '';
		$authentication_data = array();
		$allow_unsafe_urls = false;
		$allow_unverified_ssl = false;
		$trigger_mapping_response_template = false;

		$is_flow = false;
		$flow_id = 0;
		$flow_blocked_trigger = '';
		if( $url === 'wpwhflow' ){
			$ex_flow_id = str_replace( 'wpwh-flow-', '', $webhook_url_name );
			if( ! empty( $ex_flow_id ) && is_numeric( $ex_flow_id ) ){
				$flow_id = $ex_flow_id;
				$is_flow = true;
			}

			if( isset( $_REQUEST['block_trigger'] ) && ! empty( $_REQUEST['block_trigger'] ) ){
				$flow_blocked_trigger = sanitize_title( $_REQUEST['block_trigger'] );
			}
		}

		//Required settings
		if( is_array($webhook) && isset( $webhook['settings'] ) && ! empty( $webhook['settings'] ) ) {

			foreach ( $webhook['settings'] as $settings_name => $settings_data ) {

				//Data Mapping
				if( $settings_name === 'wpwhpro_trigger_data_mapping' && ! empty( $settings_data ) ){

					if( is_numeric( $settings_data ) ){
						$template = WPWHPRO()->data_mapping->get_data_mapping( $settings_data );
						if( ! empty( $template ) && ! empty( $template->template ) ){
							$sub_template_data = base64_decode( $template->template );
							if( ! empty( $sub_template_data ) && WPWHPRO()->helpers->is_json( $sub_template_data ) ){
								$template_data = json_decode( $sub_template_data, true );
								if( ! empty( $template_data ) ){
									$data = WPWHPRO()->data_mapping->map_data_to_template( $data, $template_data, 'trigger' );
								}
							}
						}
					}
				
				}

				//Response Data Mapping
				if( $settings_name === 'wpwhpro_trigger_data_mapping_response' && ! empty( $settings_data ) ){

					if( is_numeric( $settings_data ) ){
						$response_template = WPWHPRO()->data_mapping->get_data_mapping( $settings_data );
						if( ! empty( $response_template ) && ! empty( $response_template->template ) ){
							$response_sub_template_data = base64_decode( $response_template->template );
							if( ! empty( $response_sub_template_data ) && WPWHPRO()->helpers->is_json( $response_sub_template_data ) ){
								$response_template_data = json_decode( $response_sub_template_data, true );
								if( ! empty( $response_template_data ) ){
									$trigger_mapping_response_template = $response_template_data;
								}
							}
						}
					}
				
				}

				//Authentication
				if( $settings_name === 'wpwhpro_trigger_authentication' && ! empty( $settings_data ) ){

					if( is_numeric( $settings_data ) ){
						$template = WPWHPRO()->auth->get_auth_templates( $settings_data );
						if( ! empty( $template ) && ! empty( $template->template ) && ! empty( $template->auth_type ) ){
							$sub_template_data = base64_decode( $template->template );
							if( ! empty( $sub_template_data ) && WPWHPRO()->helpers->is_json( $sub_template_data ) ){
								$template_data = json_decode( $sub_template_data, true );
								if( ! empty( $template_data ) ){
									$authentication_data = array(
										'auth_type' => $template->auth_type,
										'data' => $template_data
									);
								}
							}
						}
					}
				
				}

				if( $settings_name === 'wpwhpro_trigger_response_type' && ! empty( $settings_data ) ){

					switch( $settings_data ){
						case 'form':
							$response_content_type_slug = 'form';
							$response_content_type = 'application/x-www-form-urlencoded';
							break;
						case 'form-data':
							$response_content_type_slug = 'form-data';
							$response_content_type = 'multipart/form-data';
							break;
						case 'xml':
							if( extension_loaded('simplexml') ){
								$response_content_type_slug = 'xml';
								$response_content_type = 'application/xml';
							} else {
								$response['msg'] = WPWHPRO()->helpers->translate( 'SimpleXML is not activated on your server. Please activate it first or switch the content type of your webhook.', 'wpwhpro-admin-webhooks' );
								$response['is_valid'] = false;
							}
							break;
						case 'json':
						default:
							//Just for reference
							$response_content_type_slug = 'json';
							$response_content_type = 'application/json';
							break;
					}

				}

				if( $settings_name === 'wpwhpro_trigger_request_method' && ! empty( $settings_data ) ){

					switch( $settings_data ){
						case 'GET':
							$response_content_type_method = 'GET';
							break;
						case 'HEAD':
							$response_content_type_method = 'HEAD';
							break;
						case 'PUT':
							$response_content_type_method = 'PUT';
							break;
						case 'DELETE':
							$response_content_type_method = 'DELETE';
							break;
						case 'TRACE':
							$response_content_type_method = 'TRACE';
							break;
						case 'OPTIONS':
							$response_content_type_method = 'OPTIONS';
							break;
						case 'PATCH':
							$response_content_type_method = 'PATCH';
							break;
						case 'POST':
						default:
							//Just for reference
							$response_content_type_method = 'POST';
							break;
					}

				}

				//Allow unsafe URLs
				if( $settings_name === 'wpwhpro_trigger_allow_unsafe_urls' && (integer) $settings_data === 1 ){
					$allow_unsafe_urls = true;
				}

				//Allow unverified SSL
				if( $settings_name === 'wpwhpro_trigger_allow_unverified_ssl' && (integer) $settings_data === 1 ){
					$allow_unverified_ssl = true;
				}

			}
		}

		if( is_array($webhook) && isset( $webhook['settings'] ) && ! empty( $webhook['settings'] ) && ! $skip_validation ){

			foreach( $webhook['settings'] as $settings_name => $settings_data ){

				if( $settings_name === 'wpwhpro_user_must_be_logged_in' && (integer) $settings_data === 1 ){
					if( ! is_user_logged_in() ){
						$response['msg'] = WPWHPRO()->helpers->translate( 'Trigger not sent because the settings did not match.', 'wpwhpro-admin-webhooks' );
						$response['is_valid'] = false;
					}
				}

				if( $settings_name === 'wpwhpro_user_must_be_logged_out' && (integer) $settings_data === 1 ){
					if( is_user_logged_in() ){
						$response['msg'] = WPWHPRO()->helpers->translate( 'Trigger not sent because the settings did not match.', 'wpwhpro-admin-webhooks' );
						$response['is_valid'] = false;
					}
				}

				if( $settings_name === 'wpwhpro_trigger_backend_only' && (integer) $settings_data === 1 ){
					if( ! is_admin() ){
						$response['msg'] = WPWHPRO()->helpers->translate( 'Trigger not sent because the settings did not match.', 'wpwhpro-admin-webhooks' );
						$response['is_valid'] = false;
					}
				}

				if( $settings_name === 'wpwhpro_trigger_frontend_only' && (integer) $settings_data === 1 ){
					if( is_admin() ){
						$response['msg'] = WPWHPRO()->helpers->translate( 'Trigger not sent because the settings did not match.', 'wpwhpro-admin-webhooks' );
						$response['is_valid'] = false;
					}
				}

				if( $settings_name === 'wpwhpro_trigger_single_instance_execution' && (integer) $settings_data === 1 ){
					
					$all_processed_triggers = $this->get_processed_triggers();
					if( is_array( $all_processed_triggers ) && ! empty( $all_processed_triggers ) && isset( $all_processed_triggers[ $webhook_name . '_' . $webhook_url_name ] ) ){
						$response['msg'] = WPWHPRO()->helpers->translate( 'This was a duplicate request as the Single Instance Execution was set for the webhook.', 'wpwhpro-admin-webhooks' );
						$response['is_valid'] = false;
						$response['duplicate'] = true;
					}

				}

			}

		}

		//Validate against inactive action webhooks
		if( isset( $webhook['status'] ) && ! $skip_validation ){
			if( $webhook['status'] === 'inactive' ){
				$response['msg'] = WPWHPRO()->helpers->translate( 'The following webhook trigger url is deactivated. Please activate it first.', 'webhooks-deactivated-webhook' );
				$response['is_valid'] = false;
			}
		}

		$response = apply_filters( 'wpwhpro/admin/webhooks/is_valid_trigger_response', $response, $webhook, $data, $args );

		if( $response['is_valid'] === false ){
			return $response;
		}

		$http_args = array(
			'method'      => $response_content_type_method,
			'timeout'     => MINUTE_IN_SECONDS,
			'redirection' => 0,
			'httpversion' => '1.0',
			'blocking'    => false,
			'user-agent'  => sprintf(  WPWHPRO()->settings->get_page_title() . '/%s Trigger (WordPress/%s)', WPWHPRO_VERSION, $GLOBALS['wp_version'] ),
			'headers'     => array(
				'Content-Type' => $response_content_type,
			),
			'cookies'     => array(),
		);

		if( $allow_unverified_ssl ){
			$http_args['sslverify'] = false;
		}

		$data = apply_filters( 'wpwhpro/admin/webhooks/webhook_data', $data, $response, $webhook, $args, $authentication_data );

		if( ! $is_flow ){
			$url = WPWHPRO()->data_mapping->validate_mapping_tags( $url, (object) $data );
			$url = WPWHPRO()->data_mapping->clear_mapping_tags( $url );	//Clear to keep the URL valid (Replaces non-mapped tags with nothing)

			switch( $response_content_type_slug ){
				case 'form-data':
				case 'form':
					$http_args['body'] = $data;
					break;
				case 'xml':
					$sxml_data = apply_filters( 'wpwhpro/admin/webhooks/simplexml_data', '<data/>', $http_args );
					$xml_data = $data;
					$xml = WPWHPRO()->helpers->convert_to_xml( new SimpleXMLElement( $sxml_data ), $xml_data );
					$http_args['body'] = $xml->asXML();
					break;
				case 'json':
				default:
					$http_args['body'] = trim( wp_json_encode( $data ) );
					break;
			}
		} else {
			$http_args['body'] = trim( wp_json_encode( $data ) );
		}

		//Add charset if available
		$blog_charset = get_option( 'blog_charset' );
		if ( ! empty( $blog_charset ) ) {
			$http_args['headers']['Content-Type'] .= '; charset=' . $blog_charset;
		}

		$http_args = apply_filters( 'wpwhpro/admin/webhooks/webhook_http_args', array_merge( $http_args, $args ), $args, $url, $webhook, $authentication_data, $url_unvalidated );

		$http_args['headers']['X-WP-Webhook-Source'] = home_url( '/' );
		$http_args['headers']['X-WP-Webhook-Name'] = $webhook_name;
		$http_args['headers']['X-WP-Webhook-URL-Name'] = $webhook_url_name;

		if( ! empty( $webhook_secret ) ){
			$secret_key = $webhook_secret;
		} else {
			$secret_key = get_option( 'wpwhpro_trigger_secret' ); //deprecated since 3.0.1
		}
			
		/*
		 * Set a custom secret key
		 * @since 3.0.1
		 */
		$secret_key = apply_filters( 'wpwhpro/admin/webhooks/secret_key', $secret_key, $webhook, $args, $authentication_data );
		if( ! empty( $secret_key ) ){
			$secret_key_data = array(
				'date_created' => ( is_array($webhook) && isset( $webhook['date_created'] ) ) ? $webhook['date_created'] : '',
				'webhook_name' => ( is_array($webhook) && isset( $webhook['webhook_name'] ) ) ? $webhook['webhook_name'] : '',
				'webhook_url_name' => ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : '',
			);
			$http_args['headers']['X-WP-Webhook-Signature'] = $this->generate_trigger_signature( json_encode( $secret_key_data ), $secret_key );
		}	

		if( is_array($webhook) && isset( $webhook['settings'] ) && ! empty( $webhook['settings'] ) ){

			if( isset( $webhook['settings']['wpwhpro_trigger_data_mapping_header'] ) && ! empty( $webhook['settings']['wpwhpro_trigger_data_mapping_header'] ) ){
				if( is_numeric( $webhook['settings']['wpwhpro_trigger_data_mapping_header'] ) ){
					$template = WPWHPRO()->data_mapping->get_data_mapping( $webhook['settings']['wpwhpro_trigger_data_mapping_header'] );
					if( ! empty( $template ) && ! empty( $template->template ) ){
						$sub_template_data = base64_decode( $template->template );
						if( ! empty( $sub_template_data ) && WPWHPRO()->helpers->is_json( $sub_template_data ) ){
							$template_data = json_decode( $sub_template_data, true );
							if( ! empty( $template_data ) ){
								$http_args['headers'] = WPWHPRO()->data_mapping->map_data_to_template( $http_args['headers'], $template_data, 'trigger' );
							}
						}
					}
				}
			}

			if( isset( $webhook['settings']['wpwhpro_trigger_data_mapping_cookies'] ) && ! empty( $webhook['settings']['wpwhpro_trigger_data_mapping_cookies'] ) ){
				if( is_numeric( $webhook['settings']['wpwhpro_trigger_data_mapping_cookies'] ) ){
					$template = WPWHPRO()->data_mapping->get_data_mapping( $webhook['settings']['wpwhpro_trigger_data_mapping_cookies'] );
					if( ! empty( $template ) && ! empty( $template->template ) ){
						$sub_template_data = base64_decode( $template->template );
						if( ! empty( $sub_template_data ) && WPWHPRO()->helpers->is_json( $sub_template_data ) ){
							$template_data = json_decode( $sub_template_data, true );
							if( ! empty( $template_data ) ){
								$http_args['cookies'] = WPWHPRO()->data_mapping->map_data_to_template( $http_args['cookies'], $template_data, 'trigger' );
							}
						}
					}
				}
			}

		}

		$url = apply_filters( 'wpwhpro/admin/webhooks/webhook_url', $url, $http_args, $webhook, $authentication_data, $url, $url_unvalidated );

		if( $is_flow ){	

			//Avoid loops within triggers
			if( $flow_blocked_trigger !== $webhook_name ){
				$response = WPWHPRO()->flows->process_flow( $flow_id, array(
					'payload' => $data,
				) );
			} else {
				$response = array(
					'success' => false,
					'msg' => WPWHPRO()->helpers->translate( 'The flow got canceled to avoid loops.', 'wpwhpro-admin-webhooks' )
				);
			}

		} else {

			if( ! $allow_unsafe_urls ){
				$http_args['reject_unsafe_urls'] = true;
			}

			$response = WPWHPRO()->http->send_http_request( $url, $http_args );

			if( isset( $response['content'] ) && ! empty( $trigger_mapping_response_template ) ){

				//Must be defined as an action as it is a response
				$data_mapping_content = WPWHPRO()->data_mapping->map_data_to_template( $response, $trigger_mapping_response_template, 'action' );

				if( isset( $data_mapping_content['content'] ) ){
					$response['body_validated'] = $data_mapping_content['content'];
				}
				
			}
	
		}

		if( $is_flow ){
			$message = WPWHPRO()->helpers->translate( 'Flow successfully fired!', 'wpwhpro-admin-webhooks' );
		} else {
			$message = WPWHPRO()->helpers->translate( 'Trigger successfully sent!', 'wpwhpro-admin-webhooks' );
		}
		
		$log_data = array(
			'webhook_type' => ( $is_flow ) ? 'flow_trigger' : 'trigger',
			'webhook_name' => $webhook_name,
			'webhook_url_name' => $webhook_url_name,
			'identifier' => $url,
			'request_data' => $http_args,
			'response_data' => $response,
			'log_version' => WPWHPRO_VERSION,
			'init_vars' => array(
				'webhook' => $original_webhook,
				'data' => $original_data,
				'args' => $original_args,
				'skip_validation' => $original_validation,
				'url_unvalidated' => $url_unvalidated,
			),
		);
		$log_id = WPWHPRO()->logs->add_log( $message, $log_data );

		//Mark the trigger as processed
		$this->set_processed_trigger( $webhook_name . '_' . $webhook_url_name, $log_data );

		do_action( 'wpwhpro/admin/webhooks/webhook_trigger_sent', $response, $url, $http_args, $webhook );

		return $response;
	}

}
