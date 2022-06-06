<?php

/**
 * WP_Webhooks_Pro_Data_Mapping Class
 *
 * This class contains all of the available data mapping functions
 *
 * @since 2.0.0
 */

/**
 * The log class of the plugin.
 *
 * @since 2.0.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Data_Mapping {

	/**
	 * Init everything 
	 */
	public function __construct() {

		$this->page_name    = WPWHPRO()->settings->get_page_name();
		$this->data_mapping_table_data = WPWHPRO()->settings->get_data_mapping_table_data();
		$this->cache_data_mapping = array();
		$this->cache_data_mapping_count = 0;
		$this->table_exists = false;

	}

	/**
	 * Wether the Data Mapping feature is active or not
	 * 
	 * Data Mapping will now be active by default
	 * 
	 * @deprecated deprecated since version 4.0.0
	 * @return boolean - True if active, false if not
	 */
	public function is_active(){
		return true;
	}

	/**
	 * Execute feature related hooks and logic to get 
	 * everything running
	 *
	 * @since 4.2.3
	 * @return void
	 */
	public function execute(){

		//Template related
		add_filter( 'wpwhpro/helpers/validate_response_body', array( $this, 'apply_data_mapping_template' ), 10, 4 );

		//Ajax related
		add_action( 'wp_ajax_ironikus_load_data_mapping_data',  array( $this, 'ironikus_load_data_mapping_data' ) );
		add_action( 'wp_ajax_ironikus_delete_data_mapping_template',  array( $this, 'ironikus_delete_data_mapping_template' ) );
		add_action( 'wp_ajax_ironikus_add_data_mapping_template',  array( $this, 'ironikus_add_data_mapping_template' ) );
		add_action( 'wp_ajax_ironikus_save_data_mapping_template',  array( $this, 'ironikus_save_data_mapping_template' ) );
		add_action( 'wp_ajax_ironikus_data_mapping_create_preview',  array( $this, 'ironikus_data_mapping_create_preview' ) );

	}

	public function apply_data_mapping_template( $return, $current_content_type = '', $response = array(), $custom_data = array() ){

		$current_action = WPWHPRO()->webhook->get_incoming_action( $return );
		if( empty( $current_action ) ){
			$current_action = '';
		}	

		$response_ident_param = WPWHPRO()->settings->get_webhook_ident_param();
		$response_ident_value = ! empty( $_REQUEST[ $response_ident_param ] ) ? sanitize_key( $_REQUEST[ $response_ident_param ] ) : '';

		//Apply mapping for action data
		$webhook = WPWHPRO()->webhook->get_hooks( 'action', $current_action, $response_ident_value );
		
		if( is_array($webhook) && isset( $webhook['settings'] ) && ! empty( $webhook['settings'] ) ) {

			foreach ( $webhook['settings'] as $settings_name => $settings_data ) {

				if( $settings_name === 'wpwhpro_action_data_mapping' && ! empty( $settings_data ) ){

					//An error caused by the Flows feature to save errors as arrays
					if( is_array( $settings_data ) && isset( $settings_data[0] ) ){
						$settings_data = $settings_data[0];
					}

					if( is_numeric( $settings_data ) ){
						$template = $this->get_data_mapping( $settings_data );
						if( ! empty( $template ) && ! empty( $template->template ) ){
							$sub_template_data = base64_decode( $template->template );
							if( ! empty( $sub_template_data ) && WPWHPRO()->helpers->is_json( $sub_template_data ) ){
								$template_data = json_decode( $sub_template_data, true );
								if( ! empty( $template_data ) ){
									$return = $this->map_data_to_template( $return, $template_data, 'action' );
								}
							}
						}
					}

				}

			}

		}

		do_action( 'wpwhpro/admin/webhooks/webhook_action_after_settings', $webhook, $response_ident_value, $response_ident_param, $return );

		return $return;
	 }

	 /*
     * Functionality to load the currently chosen wdata mapping
     */
	public function ironikus_load_data_mapping_data(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $data_mapping_id    = isset( $_REQUEST['data_mapping_id'] ) ? intval( $_REQUEST['data_mapping_id'] ) : '';
        $response           = array( 'success' => false );

		if( ! empty( $data_mapping_id ) && is_numeric( $data_mapping_id ) ){
		    $check = $this->get_data_mapping( intval( $data_mapping_id ) );
		    $template_settings = WPWHPRO()->settings->get_data_mapping_template_settings();

		    if( ! empty( $check ) ){

				$response['success'] = true;
		        $response['text'] 	 = array(
					'add_button_text' => WPWHPRO()->helpers->translate( 'Add Row', 'wpwhpro-page-data-mapping' ),
					'import_button_text' => WPWHPRO()->helpers->translate( 'Import', 'wpwhpro-page-data-mapping' ),
					'export_button_text' => WPWHPRO()->helpers->translate( 'Export', 'wpwhpro-page-data-mapping' ),
					'add_first_row_text' => WPWHPRO()->helpers->translate( 'Add a row to get started!', 'wpwhpro-page-data-mapping' ),
				);
				$response['data'] = $check;
				$response['template_settings'] = $template_settings;

				//Unencode and validate original json
				if( isset( $response['data']->template ) && ! empty( $response['data']->template ) ){
					$response['data']->template = base64_decode( $response['data']->template );
				}

            }
        }

        echo json_encode( $response );
		die();
    }

	/*
     * Functionality to delete the currently chosen data mapping template
     */
	public function ironikus_delete_data_mapping_template(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $data_mapping_id    = isset( $_REQUEST['data_mapping_id'] ) ? intval( $_REQUEST['data_mapping_id'] ) : '';
        $response           = array( 'success' => false );

		if( ! empty( $data_mapping_id ) && is_numeric( $data_mapping_id ) ){
		    $check = $this->delete_dm_template( intval( $data_mapping_id ) );

		    if( ! empty( $check ) ){

				$response['success'] = true;

            }
        }

        echo json_encode( $response );
		die();
	}

	/*
     * Functionality to add the currently chosen data mapping
     */
	public function ironikus_add_data_mapping_template(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $data_mapping_name    = isset( $_REQUEST['data_mapping_name'] ) ? sanitize_title( $_REQUEST['data_mapping_name'] ) : '';
        $response           = array( 'success' => false );

		if( ! empty( $data_mapping_name ) ){
		    $check = $this->add_template( $data_mapping_name );

		    if( ! empty( $check ) ){

				$response['success'] = true;

            }
        }

        echo json_encode( $response );
		die();
    }

	/*
     * Functionality to save the current data mapping template
     */
	public function ironikus_save_data_mapping_template(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $data_mapping_id    = isset( $_REQUEST['data_mapping_id'] ) ? intval( $_REQUEST['data_mapping_id'] ) : '';
        $data_mapping_template    = isset( $_REQUEST['data_mapping_json'] ) ? $_REQUEST['data_mapping_json'] : '';
		$response           = array( 'success' => false );

		//Maybe validate the incoming template data
		if( empty( $data_mapping_template ) ){
			$data_mapping_template = array();
		}

		//Validate arrays
		if( is_array( $data_mapping_template ) ){
			$data_mapping_template = json_encode( $data_mapping_template );
		}

		//make sure we only save the necessary slashes
		$data_mapping_template = stripslashes( $data_mapping_template );

		if( ! empty( $data_mapping_id ) && is_string( $data_mapping_template ) ){

			if( WPWHPRO()->helpers->is_json( $data_mapping_template ) ){
				$check = $this->update_template( $data_mapping_id, array(
					'template' => $data_mapping_template
				) );

				if( ! empty( $check ) ){

					$response['success'] = true;

				}
			}
        }

        echo json_encode( $response );
		die();
    }

	/*
     * Functionality to save the current data mapping template
     */
	public function ironikus_data_mapping_create_preview(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $original_data    = isset( $_REQUEST['original_data'] ) ? stripslashes( $_REQUEST['original_data'] ) : '';
        $current_mapping_template    = isset( $_REQUEST['current_mapping_template'] ) ? $_REQUEST['current_mapping_template'] : '';
        $mapping_type    = isset( $_REQUEST['mapping_type'] ) ? sanitize_title( $_REQUEST['mapping_type'] ) : '';
		$response           = array( 'success' => false );
		$payload_data = array(
			'content' => $original_data,
			'content_type' => '',
		);

		if( ! empty( $current_mapping_template ) ){
			if( is_array( $current_mapping_template ) ){
				$current_mapping_template = json_encode( $current_mapping_template );
			}

			//make sure we only save the necessary slashes
			$current_mapping_template = stripslashes( $current_mapping_template );

			if( ! is_string( $current_mapping_template ) ){
				echo json_encode( $response );
				die();
			}
		} else {
			$current_mapping_template = null; //allows us to return the default data
		}

		if( WPWHPRO()->helpers->is_json( $original_data ) ){
			$payload_data['content_type'] = 'application/json';
		} elseif( WPWHPRO()->helpers->is_xml( $original_data ) ){
			$payload_data['content_type'] = 'application/xml';
		} else {
			parse_str( $original_data, $query_output );
			if( ! empty( $query_output ) ){
				$payload_data['content_type'] = 'application/x-www-form-urlencoded';
			}
		}

		remove_filter( 'wpwhpro/helpers/validate_response_body', array( $this, 'apply_data_mapping_template' ), 10 );

		$preview_response = WPWHPRO()->http->get_response( $payload_data );

		if( $mapping_type === 'trigger' ){
			$preview_response = (array) $preview_response['content'];
		}

		$validated_preview_response = $this->apply_data_mapping_preview_template( $preview_response, $current_mapping_template, $mapping_type );

		add_filter( 'wpwhpro/helpers/validate_response_body', array( $this, 'apply_data_mapping_template' ), 10, 4 );

		if( ! empty( $validated_preview_response ) && is_array( $validated_preview_response ) ){
			$response['success'] = true;

			if( $mapping_type === 'trigger' ){
				$response['payload'] = $validated_preview_response;
			} else {
				$response['payload'] = $validated_preview_response['content'];
			}

		}

        echo json_encode( $response );
		die();
    }

	public function apply_data_mapping_preview_template( $return, $mapping_template, $mapping_type ){

		if( ! empty( $mapping_template ) && WPWHPRO()->helpers->is_json( $mapping_template ) ){
			$template_data = json_decode( $mapping_template, true );
			if( ! empty( $template_data ) ){
				$return = $this->map_data_to_template( $return, $template_data, $mapping_type );
			}
		}

		do_action( 'wpwhpro/admin/webhooks/webhook_action_after_data_mapping_preview', $return, $mapping_template );

		return $return;
	 }

	/**
	 * Initialize the data mappnig tables
	 *
	 * @return void
	 */
	public function maybe_setup_data_mapping_table(){

		//shorten circle if already set up
		if( $this->table_exists ){
			return;
		}

		if( ! WPWHPRO()->sql->table_exists( $this->data_mapping_table_data['table_name'] ) ){
			WPWHPRO()->sql->run_dbdelta( $this->data_mapping_table_data['sql_create_table'] );
		}

		$this->table_exists = true;

	}

	/**
	 * Hanler function of mapping the new values
	 * to the actual data
	 *
	 * @param mixed $data - array of all currently set values
	 * @param array $template - an array of all currently available data mapping templates
	 * @param string $webhook_type - Wether it is a trigger or an action
	 * @return array - the data array
	 */
	public function map_data_to_template( $data, $template, $webhook_type ){

		$this->precached_data_mapping_data = $data;
		$this->precached_data_mapping_type = $webhook_type;

		//backwards compatibility
		if( ! isset( $template['template_data'] ) ){
			$temp_data = array(
				'template_data' => $template,
				'template_settings' => array(),
			);
			$template = $temp_data;
		}

		if( ! isset( $template['template_settings'] ) ){
			$template['template_settings'] = array();
		}

		foreach( $template['template_data'] as $row ){

			//Avoid empty singles
			if( empty( $row['singles'] ) ){
				continue;
			}

			switch( $webhook_type ){
				case 'trigger':
					$current_value = $this->get_current_array_value( $data, $row['singles'] );

					if( is_string( $current_value ) ){
						$data[ $row['new_key'] ] = $this->validate_mapping_tags( $current_value, (object) $data );
					} else {
						$data[ $row['new_key'] ] = $current_value;
					}
					
				break;
				case 'action':
					$current_value = $this->get_current_array_value( $data['content'], $row['singles'] );

					if( is_string( $current_value ) ){
						$validated_value = $this->validate_mapping_tags( $current_value, $data['content'] );
					} else {
						$validated_value = $current_value;
					}
					
					if( is_array( $data['content'] ) ){
						$data['content'][ $row['new_key'] ] = $validated_value;
					} else {
						$data['content']->{$row['new_key']} = $validated_value;
					}	
					
				break;
			}

			$this->precached_data_mapping_data = $data;
			
		}

		foreach( $template['template_settings'] as $setting_name => $setting_val ){

			if( $setting_name === 'wpwhpro_data_mapping_whitelist_payload' && ! empty( $setting_val ) ){

				switch( $webhook_type ){
					case 'trigger':
						
						if( $setting_val === 'whitelist' ){

							$whitelisted_data = array();

							foreach( $template['template_data'] as $row ){
								if( isset( $data[ $row['new_key'] ] ) ){
									$whitelisted_data[ $row['new_key'] ] = $data[ $row['new_key'] ];
								}
							}
			
							$data = $whitelisted_data;

						} elseif( $setting_val === 'blacklist' ) {

							$blacklisted_data = $data;

							foreach( $template['template_data'] as $row ){
								if( isset( $data[ $row['new_key'] ] ) ){
									unset( $blacklisted_data[ $row['new_key'] ] );
								}
							}
			
							$data = $blacklisted_data;

						}
						

					break;
					case 'action':

						if( $setting_val === 'whitelist' ){

							if( is_array( $data['content'] ) ){
								$whitelisted_data = array();
							} else {
								$whitelisted_data = new stdClass();
							}
			
							foreach( $template['template_data'] as $row ){
			
								if( is_array( $data['content'] ) ){
									if( isset( $data['content'][ $row['new_key'] ] ) ){
										$whitelisted_data[ $row['new_key'] ] = $data['content'][ $row['new_key'] ];
									}
								} else {
									if( isset( $data['content']->{$row['new_key']} ) ){
										$whitelisted_data->{$row['new_key']} = $data['content']->{$row['new_key']};
									}
								}
								
							}
			
							$data['content'] = $whitelisted_data;

						} elseif( $setting_val === 'blacklist' ) {

							$blacklisted_data = $data['content'];
			
							foreach( $template['template_data'] as $row ){
			
								if( is_array( $data['content'] ) ){
									if( isset( $data['content'][ $row['new_key'] ] ) ){
										unset( $blacklisted_data[ $row['new_key'] ] );
									}
								} else {
									if( isset( $data['content']->{$row['new_key']} ) ){
										unset( $blacklisted_data->{$row['new_key']} );
									}
								}
								
							}
			
							$data['content'] = $blacklisted_data;

						}
						
					break;
				}
				
			}

		}

		return $data;
	}

	/**
	 * The core function of mapping single values
	 *
	 * @param mixed $data - the whole data construct
	 * @param mixed $singles - the valuedsof the current iteration
	 * @return void
	 */
	public function get_current_array_value( $data, $singles ){
		$return = apply_filters( 'wpwhpro/data_mapping/return_value_default', false, $data, $singles );
		$fallback_value = null;
		$convertion_type = null;
		$unserialize = false;
		$json_encode = false;
		$serialize = false;
		$urlencode = false;
		$urldecode = false;
		$stripslashes = false;
		$addslashes = false;
		$is_value = false;

		foreach( $singles as $key => $single ){	

			//Prefilter data on value conversion to keep track of variables
			if( is_string( $single ) && isset( $this->precached_data_mapping_type ) && isset( $this->precached_data_mapping_data ) ){
				if( $this->precached_data_mapping_type === 'trigger' ){
					$single = $this->validate_mapping_tags( $single, (object) $this->precached_data_mapping_data );
				} else {
					$single = $this->validate_mapping_tags( $single, $this->precached_data_mapping_data['content'] );
				}
			}

			//Follow the new notation since 3.0.6
			if( WPWHPRO()->helpers->is_json( $single ) ){

				$single_data = json_decode( $single, true );

				if( isset( $single_data['value'] ) ){
					$single_value = $single_data['value'];
				}

				if( isset( $single_data['settings'] ) ){
					foreach( $single_data['settings'] as $setting_name => $setting_value ){

						if( $setting_name === 'wpwhpro_data_mapping_fallback_value' && !empty( $setting_value ) ){
							$fallback_value = $setting_value;

						}
						
						if( $setting_name === 'wpwhpro_data_mapping_value_type' ){
							if( $setting_value === 'data_value' ){
								
								//Keep it backwards compatible
								//It is possible that with previously conerted, old mapping templates, the ident is saved within the value field
								$integer = 'wpwhval:';
								if( is_string( $single_value ) && substr( $single_value , 0, strlen( $integer ) ) !== $integer ){
									$single_value = 'wpwhval:' . $single_value;
								}

							}
						}

						if( $setting_name === 'wpwhpro_data_mapping_convert_data' && ! empty(  $setting_value ) && $setting_value !== 'none' ){
							$convertion_type = $setting_value;
						}

						if( $setting_name === 'wpwhpro_data_mapping_decode_data' && !empty(  $setting_value ) && $setting_value !== 'none' ){

							if( $setting_value === 'json_decode' ){
								//Keep it backwards compatible
								//It is possible that with previously conerted, old mapping templates, the ident is saved within the value field
								$integer = 'wpwhjson_decode:';
								if( is_string( $single_value ) && substr( $single_value , 0, strlen( $integer ) ) !== $integer ){
									$single_value = 'wpwhjson_decode:' . $single_value;
								}
							} elseif( $setting_value === 'unserialize' ){
								$unserialize = true;
							} elseif( $setting_value === 'json_encode' ){
								$json_encode = true;
							} elseif( $setting_value === 'serialize' ){
								$serialize = true;
							} elseif( $setting_value === 'urlencode' ){
								$urlencode = true;
							} elseif( $setting_value === 'urldecode' ){
								$urldecode = true;
							} elseif( $setting_value === 'stripslashes' ){
								$stripslashes = true;
							} elseif( $setting_value === 'addslashes' ){
								$addslashes = true;
							}

						}

					}
				}

			} else {
				$single_value = $single;
			}

			//Validate data mapping value
			$integer = 'wpwhint:';
			if( is_string( $single_value ) && strpos( $single_value, $integer ) !== FALSE ){
				$single_value = intval( str_replace( $integer, '', $single_value ) );
			}

			//Validate data mapping value to an actual value
			$rl_value = 'wpwhval:';
			if( is_string( $single_value ) && strpos( $single_value, $rl_value ) !== FALSE ){
				$is_value = true;
				$single_value = str_replace( $rl_value, '', $single_value );

				if( empty( $single_value ) && $fallback_value !== null ){
					$single_value = $fallback_value;
				}

				if( $convertion_type !== null ){
					$data = $this->convert_variable_type( $single_value, $convertion_type );
				} else {
					$data = WPWHPRO()->helpers->get_original_data_format( $single_value );
				}

				//re-apply the formatted data as well to the single value since it is a value in the first place
				$single_value = $data;
			}	

			//Validate data mapping value to an actual value
			$rl_value = 'wpwhjson_decode:';
			$json_decode = false;
			if( is_string( $single_value ) && strpos( $single_value, $rl_value ) !== FALSE ){
				
				$json_decode = true;
				$single_value = str_replace( $rl_value, '', $single_value );

				//Make sure we also validate the data in case the data is a value
				if( $is_value ){
					$data = str_replace( $rl_value, '', $data );
				}
			}

			if( $convertion_type !== null ){
				$single_value = $this->convert_variable_type( $single_value, $convertion_type );
			}

			if( is_object( $data ) && ! $is_value ){

				if( isset( $data->$single_value ) ){
					unset( $singles[ $key ] );
	
					if( $json_encode ){
						//encode the given data using json_encode()
						$return = json_encode( $data->$single_value );
					} elseif( $serialize ){
						//serialize the given data using maybe_serialize()
						$return = maybe_serialize( $data->$single_value );
					} elseif( $urlencode ){
						$return = urlencode( $data->$single_value );
					} elseif( $urldecode ){
						$return = urldecode( $data->$single_value );
					} elseif( $stripslashes ){
						$return = stripslashes( $data->$single_value );
					} elseif( $addslashes ){
						$return = addslashes( $data->$single_value );
					} else {
						if( count( $singles ) > 0 ){
							$return = call_user_func( array( $this, 'get_current_array_value' ), $data->$single_value, $singles );	
						} else {
							$return = $data->$single_value;
						}
					}
					
				} elseif( $fallback_value !== null && isset( $data->$fallback_value ) ){
					unset( $singles[ $key ] );
	
					if( $json_encode ){
						//encode the given data using json_encode()
						$return = json_encode( $data->$fallback_value );
					} elseif( $serialize ){
						//serialize the given data using maybe_serialize()
						$return = maybe_serialize( $data->$fallback_value );
					} elseif( $urlencode ){
						$return = urlencode( $data->$fallback_value );
					} elseif( $urldecode ){
						$return = urldecode( $data->$fallback_value );
					} elseif( $stripslashes ){
						$return = stripslashes( $data->$fallback_value );
					} elseif( $addslashes ){
						$return = addslashes( $data->$fallback_value );
					} else {
						if( count( $singles ) > 0 ){
							$return = call_user_func( array( $this, 'get_current_array_value' ), $data->$fallback_value, $singles );
						} else {
							$return = $data->$fallback_value;
						}
					}
					
				}

			} elseif( is_array( $data ) && ! $is_value ){

				if( isset( $data[ $single_value ] ) ){
					unset( $singles[ $key ] );

					if( $json_encode ){
						//encode the given data using json_encode()
						$return = json_encode( $data[ $single_value ] );
					} elseif( $serialize ){
						//serialize the given data using maybe_serialize()
						$return = maybe_serialize( $data[ $single_value ] );
					} elseif( $urlencode ){
						$return = urlencode( $data[ $single_value ] );
					} elseif( $urldecode ){
						$return = urldecode( $data[ $single_value ] );
					} elseif( $stripslashes ){
						$return = stripslashes( $data[ $single_value ] );
					} elseif( $addslashes ){
						$return = addslashes( $data[ $single_value ] );
					} else {
						if( count( $singles ) > 0 ){
							$return = call_user_func( array( $this, 'get_current_array_value' ), $data[ $single_value ], $singles );
						} else {
							$return = $data[ $single_value ];
						}
					}
					
				} elseif( $fallback_value !== null && isset( $data[ $fallback_value ] ) ){
					unset( $singles[ $key ] );
	
					if( $json_encode ){
						//encode the given data using json_encode()
						$return = json_encode( $data[ $fallback_value ] );
					} elseif( $serialize ){
						//serialize the given data using maybe_serialize()
						$return = maybe_serialize( $data[ $fallback_value ] );
					} elseif( $urlencode ){
						$return = urlencode( $data[ $fallback_value ] );
					} elseif( $urldecode ){
						$return = urldecode( $data[ $fallback_value ] );
					} elseif( $stripslashes ){
						$return = stripslashes( $data[ $fallback_value ] );
					} elseif( $addslashes ){
						$return = addslashes( $data[ $fallback_value ] );
					} else {
						if( count( $singles ) > 0 ){
							$return = call_user_func( array( $this, 'get_current_array_value' ), $data[ $fallback_value ], $singles );
						} else {
							$return = $data[ $fallback_value ];
						}
					}
					
				}

			} else {
				$singles = array(); //reset to make sure we signalize a value

				if( $json_encode ){
					//encode the given data using json_encode()
					$return = json_encode( $data );
				} elseif( $serialize ){
					//serialize the given data using maybe_serialize()
					$return = maybe_serialize( $data );
				} elseif( $urlencode ){
					$return = urlencode( $data );
				} elseif( $urldecode ){
					$return = urldecode( $data );
				} elseif( $stripslashes ){
					$return = stripslashes( $data );
				} elseif( $addslashes ){
					$return = addslashes( $data );
				} else {
					$return = $data;
				}
			}
				
			//reload json array construct as a temporary item to iterate through it as well
			if( $json_decode && is_string( $return ) && WPWHPRO()->helpers->is_json( $return ) ){
				if( count( $singles ) > 0 ){
					$json_array = json_decode( $return, true );
					$return = call_user_func( array( $this, 'get_current_array_value' ), $json_array, $singles );
				} else {
					$return = json_decode( $return, true );
				}
			}

			//reload json array construct as a temporary item to iterate through it as well
			if( $unserialize && is_string( $return ) ){
				if( count( $singles ) > 0 ){
					$unserialized_data = maybe_unserialize( $return, true );
					$return = call_user_func( array( $this, 'get_current_array_value' ), $unserialized_data, $singles );
				} else {
					$return = maybe_unserialize( $return, true );
				}
			}

			break;

		}

		return apply_filters( 'wpwhpro/data_mapping/return_value', $return, $data, $singles );
	}

	/**
	 * Get the data mapping template/S
	 *
	 * @param string $template
	 * @return mixed - an array of the data mapping templates or an object for a single item
	 */
	public function get_data_mapping( $template = 'all', $nocache = false ){

		if( ! is_numeric( $template ) && $template !== 'all' ){
			return false;
		}

		if( ! empty( $this->cache_data_mapping ) && ! $nocache ){

			if( $template !== 'all' ){
				if( isset( $this->cache_data_mapping[ $template ] ) ){
					return $this->cache_data_mapping[ $template ];
				} else {
					return false;
				}
			} else {
				return $this->cache_data_mapping;
			}

		}

		$this->maybe_setup_data_mapping_table();

		$sql = 'SELECT * FROM {prefix}' . $this->data_mapping_table_data['table_name'] . ' ORDER BY name ASC;';

		$data = WPWHPRO()->sql->run($sql);

		$validated_data = array();
		if( ! empty( $data ) && is_array( $data ) ){
			foreach( $data as $single ){
				if( ! empty( $single->id ) ){
					$validated_data[ $single->id ] = $single;
				}
			}
		}

		$this->cache_data_mapping = $validated_data;

		if( $template !== 'all' ){
			if( isset( $this->cache_data_mapping[ $template ] ) ){
				return $this->cache_data_mapping[ $template ];
			} else {
				return false;
			}
		} else {
			return $this->cache_data_mapping;
		}
	}

	/**
	 * Helper function to flatten data mapping specific data
	 *
	 * @param mixed $data - the data value that needs to be flattened
	 * @return mixed - the flattened value
	 */
	public function flatten_data_mapping_data( $data ){
		$flattened = array();

		foreach( $data as $id => $sdata ){
			$flattened[ $id ] = array( 
				'label' => $sdata->name,
				'value' => $id,
			);
		}

		return $flattened;
	}

	/**
	 * Validate the dynamic mapping tags
	 *
	 * @param string $value - the value of the given entry
	 * @param array $data - the globally available data (can be partially validated)
	 * @return mixed - the validated value
	 */
	public function validate_mapping_tags( $value, $data ){
		$validated = $value;

		preg_match_all('/((?<=\{:)(.*?)(?=:\}))/is', $value, $match);

		if( ! empty( $match ) && is_array( $match ) && isset( $match[0] ) && is_array( $match[0] ) ){
			
			foreach( $match[0] as $sd ){
				if( is_object( $data ) && isset( $data->{$sd} ) ){

					$validated = str_replace( '{:' . $sd . ':}', trim( json_encode( $data->{$sd} ), '"' ), $validated );
					
				} elseif ( is_array( $data ) && isset( $data[ $sd ] ) ){

					$validated = str_replace( '{:' . $sd . ':}', trim( json_encode( $data[ $sd ] ), '"' ), $validated );

				}
			}
		}

		return $validated;
	}

	/**
	 * Clear the dynamic mapping tags and remove them
	 *
	 * @param string $value - the value of the given entry
	 * @param array $default - a default return value for the removed tag
	 * @return mixed - the validated value
	 */
	public function clear_mapping_tags( $value, $default = '' ){
		$validated = $value;

		preg_match_all('/((?<=\{:)(.*?)(?=:\}))/is', $value, $match);

		if( ! empty( $match ) && is_array( $match ) && isset( $match[0] ) && is_array( $match[0] ) ){
			
			foreach( $match[0] as $sd ){
				$validated = str_replace( '{:' . $sd . ':}', $default, $validated );
			}
		}

		return $validated;
	}

	/**
	 * Delete a dapa mapping template
	 *
	 * @param ind $id - the id of the data mapping template
	 * @return bool - True if deletion was succesful, false if not
	 */
	public function delete_dm_template( $id ){

		$this->maybe_setup_data_mapping_table();

		$id = intval( $id );

		if( ! $this->get_data_mapping( $id ) ){
			return false;
		}

		$sql = 'DELETE FROM {prefix}' . $this->data_mapping_table_data['table_name'] . ' WHERE id = ' . $id . ';';
		WPWHPRO()->sql->run($sql);

		return true;

	}

	/**
	 * Get a global count of all data mappig templates
	 *
	 * @return mixed - int if count is available, false if not
	 */
	public function get_dm_count(){

		if( ! empty( $this->cache_data_mapping_count ) ){
			return intval( $this->cache_data_mapping_count );
		}

		$this->maybe_setup_data_mapping_table();

		$sql = 'SELECT COUNT(*) FROM {prefix}' . $this->data_mapping_table_data['table_name'] . ';';
		$data = WPWHPRO()->sql->run($sql);

		if( is_array( $data ) && ! empty( $data ) ){
			$this->cache_data_mapping_count = $data;
			return intval( $data[0]->{"COUNT(*)"} );
		} else {
			return false;
		}

	}

	/**
	 * Add a data mapping template
	 *
	 * @param string $name - the name of the data mapping template
	 * @return bool - True if the creation was successful, false if not
	 */
	public function add_template( $name, $args = array() ){

		$this->maybe_setup_data_mapping_table();

		$sql_vals = array(
			'name' => sanitize_title( $name ),
			'log_time' => date( 'Y-m-d H:i:s' )
		);

		if( isset( $args['id'] ) && ! empty( $args['id'] ) && is_numeric( $args['id'] ) ){
			$sql_vals['id'] = intval( $args['id'] );
		}

		if( isset( $args['template'] ) && ! empty( $args['template'] ) ){
			$sql_vals['template'] = base64_encode( $args['template'] );
		}

		if( isset( $args['log_time'] ) && ! empty( $args['log_time'] ) ){
			$sql_vals['log_time'] = date( 'Y-m-d H:i:s', strtotime( $args['log_time'] ) );
		}

		$sql_keys = '';
		$sql_values = '';
		foreach( $sql_vals as $key => $single ){

			$sql_keys .= esc_sql( $key ) . ', ';
			$sql_values .= '"' . $single . '", ';

		}

		$sql = 'INSERT INTO {prefix}' . $this->data_mapping_table_data['table_name'] . ' (' . trim($sql_keys, ', ') . ') VALUES (' . trim($sql_values, ', ') . ');';
		WPWHPRO()->sql->run($sql);

		return true;

	}

	/**
	 * Update an existing data mapping template
	 *
	 * @param int $id - the template id
	 * @param array $data - the new template data
	 * @return bool - True if update was successful, false if not
	 */
	public function update_template( $id, $data ){

		$this->maybe_setup_data_mapping_table();

		$id = intval( $id );

		if( ! $this->get_data_mapping( $id ) ){
			return false;
		}

		$sql_vals = array();

		if( isset( $data['name'] ) ){
			$sql_vals['name'] = sanitize_title( $data['name'] );
		}

		if( isset( $data['template'] ) ){
			$sql_vals['template'] = base64_encode( $data['template'] );
		}

		if( empty( $sql_vals ) ){
			return false;
		}

		$sql_string = '';
		foreach( $sql_vals as $key => $single ){

			$sql_string .= $key . ' = "' . $single . '", ';

		}
		$sql_string = trim( $sql_string, ', ' );

		$sql = 'UPDATE {prefix}' . $this->data_mapping_table_data['table_name'] . ' SET ' . $sql_string . ' WHERE id = ' . $id . ';';
		WPWHPRO()->sql->run($sql);

		return true;

	}

	/**
	 * Delete the whole data mapping table
	 *
	 * @return bool - wether the deletion was successful or not
	 */
	public function delete_table(){

		$check = true;
		
		if( WPWHPRO()->sql->table_exists( $this->data_mapping_table_data['table_name'] ) ){
			$check = WPWHPRO()->sql->run( $this->data_mapping_table_data['sql_drop_table'] );
		}
		
		$this->table_exists = false;

		return $check;
	}

	public function convert_variable_type( $string, $type ){
		
		switch( $type ){
			case 'bool':
				$string = boolval( $string );
			break;
			case 'null':
				$string = null;
			break;
			case 'float':
				$string = floatval( $string );
			break;
			case 'integer':
				$string = intval( $string );
			break;
			case 'string':
				$string = strval( $string );
			break;
		}

		return $string;
	}

}
