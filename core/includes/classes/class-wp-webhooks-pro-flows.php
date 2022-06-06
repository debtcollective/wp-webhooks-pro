<?php

/**
 * WP_Webhooks_Pro_Flows Class
 *
 * This class contains all of the available flows functions
 *
 * @since 4.3.0
 */

/**
 * The flows class of the plugin.
 *
 * @since 4.3.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Flows {

	/**
	 * The main page name for our admin page
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private $page_name;

	/**
	 * The already loaded data for flow common tags
	 *
	 * @var array
	 * @since 4.3.0
	 */
	private $flow_common_tags_cache = array();

	/**
	 * The already loaded data for flow common tags values
	 *
	 * @var array
	 * @since 4.3.0
	 */
	private $flow_common_tags_cache_value = array();

	/**
	 * A buffer that collects the flow to execute it
	 * accordingly after the trigger was fired.
	 *
	 * @var array
	 * @since 4.3.0
	 */
	private $flow_buffer = array();

	/**
	 * Init everything
	 */
	public function __construct() {
		$this->page_name = WPWHPRO()->settings->get_page_name();

		//Logs
		$this->logs_table_data = WPWHPRO()->settings->get_log_table_data();

		//Flows
		$this->flows_table_data = WPWHPRO()->settings->get_flows_table_data();
		$this->cache_flows = array();
		$this->cache_flows_count = 0;
		$this->table_exists = false;

		//Flows logs
		$this->flow_logs_table_data = WPWHPRO()->settings->get_flow_logs_table_data();
		$this->cache_flow_logs = array();
		$this->cache_flow_logs_count = 0;
		$this->flow_logs_table_exists = false;

		//Async process
		$this->flow_async_class = null;
	}

	/**
	 * Wether the flows feature is active or not
	 *
	 * Authentication will now be active by default
	 *
	 * @deprecated deprecated since version 4.0.0
	 * @return boolean - True if active, false if not
	 */
	public function is_active() {
		return true;
	}

	/**
	 * Execute feature related hooks and logic to get
	 * everything running
	 *
	 * @since 4.3.0
	 * @return void
	 */
	public function execute() {

		add_action( 'wp_ajax_ironikus_flows_handler',  array( $this, 'ironikus_flows_handler' ) );

		//execute single actions
		add_filter( 'wpwhpro/async/process/wpwh_execute_flow',  array( $this, 'wpwh_execute_flow_callback' ), 20 );
		add_action( 'wpwhpro/async/process/completed/wpwh_execute_flow',  array( $this, 'wpwh_execute_flow_completed_callback' ), 20 );

		//Execute flows
		add_action( 'wpwhpro/admin/webhooks/webhook_trigger_sent', array( $this, 'register_buffered_flows' ), 20 );

		//Clean possible temp actions
		$this->clean_abandoned_temp_actions();

		//Load async handler
		add_action( 'plugins_loaded',  array( $this, 'load_flow_async_class' ) );

	}

	public function register_buffered_flows( $response ){

		if( ! empty( $this->flow_buffer ) ){
			foreach( $this->flow_buffer as $flow_id => $flows ){
				if( ! empty( $flows ) ){
					foreach( $flows as $flow_data ){
						WPWHPRO()->flows->run_flow( $flow_id, $flow_data );
					}
				}
			}
		}

	}

	/**
	 * Get the flow async class
	 *
	 * @since 5.0
	 *
	 * @return WP_Webhooks_Pro_Async_Process
	 */
	public function get_flow_async(){

		if( $this->flow_async_class === null ){
			$this->load_flow_async_class();
		}

		return $this->flow_async_class;
	}

	/**
	 * Initiate the Flow async class
	 *
	 * @since 5.0
	 *
	 * @return void
	 */
	public function load_flow_async_class(){
		$this->flow_async_class = WPWHPRO()->async->new_process( array(
			'action' => 'wpwh_execute_flow'
		) );
	}

	public function wpwh_execute_flow_callback( $data ){
		$return = $data;

		if( ! isset( $data['data']['action'] ) ){
			$data['data']['action'] = array();
		}

		//Initialize retry as false to only renew it once we set that data
		if( ! isset( $data['retry'] ) ){
			$data['retry'] = false;
		}

		$flow_id = 0;
		if( isset( $data['flow_id'] ) ){
			$flow_id = intval( $data['flow_id'] );
		}

		$flow_log_id = 0;
		$flow_log = false;
		if( isset( $data['flow_log_id'] ) ){
			$flow_log_id = intval( $data['flow_log_id'] );
			$flow_log = $this->get_flow_log( $flow_log_id, false );
		}

		//If no id is given, abort with the next task
		if( empty( $flow_id ) || empty( $flow_log ) ){
			return $return;
		}

		$trigger_slug = '';
		if(
			isset( $flow_log->flow_config )
			&& isset( $flow_log->flow_config['triggers'] )
		){
			foreach( $flow_log->flow_config['triggers'] as $trigger_key => $trigger_data ){
				if( isset( $trigger_data['trigger'] ) ){
					$trigger_slug = sanitize_title( $trigger_data['trigger'] );
				}
				break;
			}
		}

		$current_action_key = $data['current'];

		$validated_body = array();
		$is_action_valid = true;
		if(
			isset( $flow_log->flow_config )
			&& isset( $flow_log->flow_config['actions'])
			&& isset( $flow_log->flow_config['actions'][ $current_action_key ] )
			&& isset( $flow_log->flow_config['actions'][ $current_action_key ]['action'] )
			&& isset( $flow_log->flow_payload )
		){
			$current_action = $flow_log->flow_config['actions'][ $current_action_key ]['action'];
			$current_action_data = $flow_log->flow_config['actions'][ $current_action_key ];
			if( isset( $current_action_data['fields'] ) ){
				$validated_body = $this->validate_action_fields( $current_action_data['fields'], $flow_log->flow_payload );
			}

			if(
				isset( $current_action_data['conditionals'] )
				&& ! empty( $current_action_data['conditionals'] )
				&& isset( $current_action_data['conditionals']['conditions'] )
				&& ! empty( $current_action_data['conditionals']['conditions'] )
			){
				$is_action_valid = $this->validate_action_conditions( $current_action_data['conditionals'], $flow_log->flow_payload );
			}
		}

		if( $is_action_valid && ! empty( $validated_body ) && ! empty( $current_action ) ){

			$endpoint_url	= '';
			$webhook_action = 'wpwh-flow-' . $flow_id . '-' . sanitize_title( $current_action_key );
			$webhook        = WPWHPRO()->webhook->get_hooks( 'action', $current_action, $webhook_action );

			if( is_array( $webhook ) ){
				if( isset( $webhook['api_key'] ) && isset( $webhook['webhook_name'] ) ){
					$endpoint_url = WPWHPRO()->webhook->built_url( $webhook['webhook_url_name'], $webhook['api_key'], array(
						'action' => sanitize_title( $current_action ),
						'flow_log_id' => $flow_log_id,
						'block_trigger' => $trigger_slug,
					) );

				}
			}

			$http_args = array(
				'headers'	=> array(
					'Content-Type' => 'application/x-www-form-urlencoded'
				),
				'body'		=> $validated_body,
				'blocking'	=> true,
				'timeout'	=> 30,
				'sslverify'	=> false,
				'reject_unsafe_urls'	=> false,
			);

			$action_response = wp_remote_post( $endpoint_url, $http_args );
			if( ! empty( $action_response ) && ! is_wp_error( $action_response ) ){

				//Append new data to payload
				$payload = $flow_log->flow_payload;

				if( ! isset( $payload['actions'] ) ){
					$payload['actions'] = array();
				}

				$action_body = wp_remote_retrieve_body( $action_response );

				if( is_string( $action_body ) && WPWHPRO()->helpers->is_json( $action_body ) ){
					$action_body = json_decode( $action_body, true );
				}

				$payload['actions'][ $current_action_key ] = $action_body;

				$update_data = array(
					'flow_payload' => $payload,
				);

				$check = $this->update_flow_log( $flow_log_id, $update_data );

			} else {

				//maybe return s log if debug mode is activated
				if( is_wp_error( $action_response ) ){
					WPWHPRO()->helpers->log_issue( sprintf( WPWHPRO()->helpers->translate( 'The action "%1$s" of the Flow #%2$d was not executed due to the following error: %3$s', 'admin-debug-feature' ), $current_action, $flow_id, $action_response->get_error_message() ) );
				}

			}
		}

		return $return;
	}

	public function wpwh_execute_flow_completed_callback( $class ){
		if( isset( $class->flow_log_id ) ){
			$flow_log_id = intval( $class->flow_log_id );

			$update_data = array(
				'flow_completed' => 1,
			);

			$this->update_flow_log( $flow_log_id, $update_data );
		}

	}

	/**
	 * Manage WP Webhooks flows
	 *
	 * @return void
	 */
	public function ironikus_flows_handler() {
		check_ajax_referer( md5( $this->page_name ), 'ironikusflows_nonce' );

		$flow_handler = isset( $_REQUEST['handler'] ) ? sanitize_title( $_REQUEST['handler'] ) : '';
		$response = array( 'success' => false );

		if ( empty( $flow_handler ) ) {
			$response['msg'] = WPWHPRO()->helpers->translate( 'There was an issue localizing the remote data', 'ajax-settings' );
			return $response;
		}

		switch( $flow_handler ) {
			case 'get_flows':
				$response['msg'] = WPWHPRO()->helpers->translate( 'Flows have been successfully returned.', 'ajax-settings' );
				break;
			case 'get_flow_condition_labels':
				$response = $this->ajax_get_flow_condition_labels();
				break;
			case 'get_flow_status_labels':
				$response = $this->ajax_get_flow_status_labels();
				break;
			case 'get_flow':
				$response = $this->ajax_get_flow();
				break;
			case 'update_flow':
				$response = $this->ajax_update_flow();
				break;
			case 'delete_flow':
				$response = $this->ajax_delete_flow();
				break;
			case 'get_logs':
				$response = $this->ajax_get_trigger_logs();
				break;
			case 'get_integrations':
				$response = $this->ajax_get_integrations();
				break;
			case 'get_triggers':
				$response = $this->ajax_get_triggers();
				break;
			case 'get_trigger':
				$response = $this->ajax_get_trigger();
				break;
			case 'get_flow_common_tags':
				$response = $this->ajax_get_flow_common_tags();
				break;
			case 'fire_action':
				$response = $this->ajax_fire_step_action();
				break;
			case 'get_receivable_trigger_url':
				$response = $this->ajax_get_receivable_trigger_url();
				break;
			case 'get_field_query':
				$response = $this->ajax_get_field_query();
				break;
		}

		if( $response['success'] ){
			wp_send_json_success( $response );
		} else {
			wp_send_json_error( $response );
		}

		die();
	}

	/**
	 * AJAX: Get flow status labels
	 *
	 * @return void
	 */
	private function ajax_get_flow_condition_labels() {

		$response = array(
			'success' => false,
			'msg' => WPWHPRO()->helpers->translate('No conditionals available.', 'ajax-settings'),
		);

		$condition_labels = WPWHPRO()->settings->get_flow_condition_labels();

		if( ! empty( $condition_labels ) ){
			$response['success'] = true;
			$response['result'] = $condition_labels;
			$response['msg'] = WPWHPRO()->helpers->translate('The condition labels have been successfully returned.', 'ajax-settings');
		} else {
			$response['msg'] = WPWHPRO()->helpers->translate('An error occured while returning the condition labels.', 'ajax-settings');
		}

		return $response;
	}

	/**
	 * AJAX: Get flow status labels
	 *
	 * @return void
	 */
	private function ajax_get_flow_status_labels() {

		$response = array(
			'success' => false,
			'msg' => WPWHPRO()->helpers->translate('No labels available.', 'ajax-settings'),
		);

		$status_labels = WPWHPRO()->settings->get_flow_status_labels();

		if( ! empty( $status_labels ) ){
			$response['success'] = true;
			$response['result'] = $status_labels;
			$response['msg'] = WPWHPRO()->helpers->translate('The flow status labels have been successfully returned.', 'ajax-settings');
		} else {
			$response['msg'] = WPWHPRO()->helpers->translate('An error occured while returning the flow status labels.', 'ajax-settings');
		}

		return $response;
	}

	/**
	 * AJAX: Get flow
	 *
	 * @return void
	 */
	private function ajax_get_flow() {

		$response = array(
			'success' => false,
			'msg' => WPWHPRO()->helpers->translate('Nothing happened.', 'ajax-settings'),
		);
		$flow_id = isset( $_REQUEST['flow_id'] ) ? intval( $_REQUEST['flow_id'] ) : 0;

		if( ! empty( $flow_id ) ){

			$flow = $this->get_flows( array( 'template' => $flow_id ) );

			if( is_object( $flow ) && isset( $flow->flow_date ) ){
				$flow->flow_date = date( 'M j, Y, g:i a', strtotime( $flow->flow_date ) );
			}

			if( ! empty( $flow ) ){
				$response['success'] = true;
				$response['result'] = $flow;
				$response['msg'] = WPWHPRO()->helpers->translate('The flow was successfully returned.', 'ajax-settings');

				return $response;
			}

		}

		$response['msg'] = WPWHPRO()->helpers->translate('An error occured while returning the flow.', 'ajax-settings');
		return $response;
	}

	/**
	 * AJAX: Update flow
	 *
	 * @return void
	 */
	private function ajax_update_flow() {

		$response = array(
			'success' => false,
			'msg' => WPWHPRO()->helpers->translate('Nothing happened.', 'ajax-settings'),
		);

		$flow_id = isset( $_REQUEST['flow_id'] ) ? intval( $_REQUEST['flow_id'] ) : 0;
		$flow_title = isset( $_REQUEST['flow_title'] ) ? wp_strip_all_tags( sanitize_text_field( $_REQUEST['flow_title'] ) ) : false;
		$flow_name = isset( $_REQUEST['flow_name'] ) ? sanitize_title( $_REQUEST['flow_name'] ) : sanitize_title( $flow_title );
		$flow_trigger = isset( $_REQUEST['flow_trigger'] ) ? $_REQUEST['flow_trigger'] : '';
		$flow_config = isset( $_REQUEST['flow_config'] ) ? $_REQUEST['flow_config'] : false;
		$flow_status = isset( $_REQUEST['flow_status'] ) ? sanitize_title( $_REQUEST['flow_status'] ) : 'inactive';
		$flow_author = isset( $_REQUEST['flow_author'] ) ? intval( $_REQUEST['flow_author'] ) : 0;

		//validate flow status
		$status_labels = WPWHPRO()->settings->get_flow_status_labels();
		if( ! isset( $status_labels[ $flow_status ] ) ){
			$flow_status = 'inactive';
		}

		if( ! empty( $flow_config ) && is_array( $flow_config ) ){
			$flow_config = $this->validate_flow_values( 'stripslashes', $flow_config );
		}

		if( ! empty( $flow_id ) ){

			$flow = $this->update_flow( $flow_id, array(
				'flow_title' => $flow_title,
				'flow_name' => $flow_name,
				'flow_config' => $flow_config,
				'flow_trigger' => $flow_trigger,
				'flow_status' => $flow_status,
				'flow_author' => $flow_author,
			  ) );

			if( ! empty( $flow ) ){
				$response['success'] = true;
				$response['result'] = $flow;
				$response['msg'] = WPWHPRO()->helpers->translate('The flow was successfully updated.', 'ajax-settings');
				$response['flow_config'] = $flow_config;
				return $response;
			}

			$response['flow'] = $flow;

		}

		$response['flow_id'] = $flow_id;

		$response['msg'] = WPWHPRO()->helpers->translate('An error occured while updating the flow.', 'ajax-settings');
		return $response;
	}

	/**
	 * AJAX: Delete flow
	 *
	 * @return void
	 */
	private function ajax_delete_flow() {

		$response = array(
			'success' => false,
			'msg' => WPWHPRO()->helpers->translate('Nothing happened.', 'ajax-settings'),
		);

		$flow_id = isset( $_REQUEST['flow_id'] ) ? intval( $_REQUEST['flow_id'] ) : 0;

		if( ! empty( $flow_id ) ){

			$flow = $this->delete_flow( $flow_id );

			if( ! empty( $flow ) ){
				$response['success'] = true;
				$response['result'] = $flow;
				$response['msg'] = WPWHPRO()->helpers->translate('The flow was successfully deleted.', 'ajax-settings');

				return $response;
			}

		}

		$response['msg'] = WPWHPRO()->helpers->translate('An error occured while deleting the flow.', 'ajax-settings');

		return $response;
	}

	/**
	 * AJAX: Get logs
	 *
	 * @return void
	 */
	private function ajax_get_trigger_logs( $type = 'all' ) {

		$response = array(
			'success' => false,
			'msg' => WPWHPRO()->helpers->translate('Nothing happened.', 'ajax-settings'),
		);
		$flow_id = isset( $_REQUEST['flow_id'] ) ? intval( $_REQUEST['flow_id'] ) : 0;
		$step_id = isset( $_REQUEST['step_id'] ) ? intval( $_REQUEST['step_id'] ) : 0;
		$integration = isset( $_REQUEST['integration'] ) ? intval( $_REQUEST['integration'] ) : 0;
		$endpoint_name = ( isset( $_REQUEST['trigger'] ) && ! empty( $_REQUEST['trigger'] ) ) ? sanitize_title( $_REQUEST['trigger'] ) : false;
		$endpoint_name = ( empty( $endpoint_name ) && isset( $_REQUEST['wpwh_action'] ) && ! empty( $_REQUEST['wpwh_action'] ) ) ? sanitize_title( $_REQUEST['wpwh_action'] ) : $endpoint_name;
		$webhook_request_type = isset( $_REQUEST['wpwh_action'] ) ? 'action' : 'trigger';

		if( ! empty( $flow_id ) ){

			$output = array();
			$logs = WPWHPRO()->logs->get_log( 0, 20 );

			if ( ! empty( $logs ) && is_array( $logs ) ) {
				$i = 0;
				foreach ( $logs as $log  ) {
					$i++;
					$log_time = date( 'F j, Y, g:i a', strtotime( $log->log_time ) );
					$log_version = '';
					$message = htmlspecialchars( base64_decode( $log->message ) );
					$content_backend = base64_decode( $log->content );
					$identifier = '';
					$webhook_type = '';
					$webhook_name = '';
					$webhook_url_name = '';
					$endpoint_response = '';
					$content = '';

					if( WPWHPRO()->helpers->is_json( $content_backend ) ){
							$single_data = json_decode( $content_backend, true );
							if( $single_data && is_array( $single_data ) ){

								if(
									! isset( $single_data['webhook_type'] )
									|| (
										$single_data['webhook_type'] !== $webhook_request_type
										&& $single_data['webhook_type'] !== 'flow_' . $webhook_request_type
										)
								){
									continue;
								}

								if( ! isset( $single_data['webhook_name'] ) || $single_data['webhook_name'] !== $endpoint_name ){
									continue;
								}

								if( isset( $single_data['request_data'] ) ){
									if( is_array( $single_data['request_data'] ) && isset( $single_data['request_data']['body'] ) ){
										if( WPWHPRO()->helpers->is_json( $single_data['request_data']['body'] ) ){
											$content = json_encode( json_decode( $single_data['request_data']['body'], true ), JSON_PRETTY_PRINT );
										} else {
											$content = json_encode( WPWHPRO()->logs->sanitize_array_object_values( $single_data['request_data']['body'] ), JSON_PRETTY_PRINT );
										}

									} else {
										$content = json_encode( WPWHPRO()->logs->sanitize_array_object_values( $single_data['request_data'] ), JSON_PRETTY_PRINT );
									}

								}

								if( isset( $single_data['response_data'] ) ){
									$endpoint_response = WPWHPRO()->logs->sanitize_array_object_values( $single_data['response_data'] );
								}

								if( isset( $single_data['identifier'] ) ){
									$identifier = htmlspecialchars( $single_data['identifier'] );
								}

								if( isset( $single_data['webhook_type'] ) ){
									$webhook_type = htmlspecialchars( $single_data['webhook_type'] );
								}

								if( isset( $single_data['webhook_name'] ) ){
									$webhook_name = htmlspecialchars( $single_data['webhook_name'] );
								}

								if( isset( $single_data['webhook_url_name'] ) ){
									$webhook_url_name = htmlspecialchars( $single_data['webhook_url_name'] );
								}

								if( isset( $single_data['log_version'] ) ){
									$log_version = htmlspecialchars( $single_data['log_version'] );
								}
							}
					}

					$output[] = array(
						'id' => $log->id,
						'log_time' => $log_time,
						'log_version' => $log_version,
						'message' => $message,
						'content_backend' => $content_backend,
						'identifier' => $identifier,
						'webhook_type' => $webhook_type,
						'webhook_name' => $webhook_name,
						'webhook_url_name' => $webhook_url_name,
						'endpoint_response' => $endpoint_response,
						'content' => $content,
						'title' => sprintf( WPWHPRO()->helpers->translate('Log #%1$s', 'ajax-settings'), $log->id) . ' - ' . $log_time,
					);
				}
			}

			if( ! empty( $logs ) ){
				$response['success'] = true;
				$response['result'] = $output;
				$response['msg'] = WPWHPRO()->helpers->translate('The logs were successfully returned.', 'ajax-settings');

				return $response;
			}

		}

		$response['msg'] = WPWHPRO()->helpers->translate('An error occured while returning the logs.', 'ajax-settings');

		return $response;
	}

	/**
	 * AJAX: Get Integrations
	 *
	 * @return void
	 */
	private function ajax_get_integrations() {

		$response = array(
			'success' => false,
			'msg' => WPWHPRO()->helpers->translate('Nothing happened.', 'ajax-settings'),
		);

		$integrations = WPWHPRO()->integrations->get_integrations();
		$required_trigger_settings = WPWHPRO()->settings->get_required_trigger_settings();
		$default_trigger_settings = WPWHPRO()->settings->get_default_trigger_settings();
		$receivable_trigger_settings = WPWHPRO()->settings->get_receivable_trigger_settings();
		$required_action_settings = WPWHPRO()->settings->get_required_action_settings();
		$data_mapping_templates = WPWHPRO()->data_mapping->get_data_mapping();
		$authentication_templates = WPWHPRO()->auth->get_auth_templates();
		$whitelisted_required_action_settings = array(
			'wpwhpro_action_data_mapping',
			'wpwhpro_action_data_mapping_response',
		);
		$whitelisted_required_trigger_settings = array(
			'wpwhpro_trigger_data_mapping',
			'wpwhpro_trigger_data_mapping_response',
			'wpwhpro_trigger_data_mapping_cookies',
			'wpwhpro_trigger_allow_unsafe_urls',
			'wpwhpro_trigger_allow_unverified_ssl',
			'wpwhpro_trigger_single_instance_execution',
			// 'wpwhpro_trigger_demo_text', // TODO: Remove after testing
			// 'wpwhpro_trigger_demo_text_variable', // TODO: Remove after testing
			// 'wpwhpro_trigger_demo_select', // TODO: Remove after testing
			// 'wpwhpro_trigger_demo_select_def', // TODO: Remove after testing
			// 'wpwhpro_trigger_demo_select_mult', // TODO: Remove after testing
			// 'wpwhpro_trigger_demo_select_mult_def', // TODO: Remove after testing
			// 'wpwhpro_trigger_demo_checkbox', // TODO: Remove after testing
			// 'wpwhpro_trigger_demo_radio', // TODO: Remove after testing
			// 'wpwhpro_trigger_demo_textarea', // TODO: Remove after testing
			// 'wpwhpro_trigger_demo_wysiwyg', // TODO: Remove after testing
			// 'wpwhpro_trigger_demo_repeater', // TODO: Remove after testing
		);

		if ( ! empty( $integrations ) ) {
			$output = array();

			// Loop through all the ingrations.
			foreach( $integrations as $integration_slug => $integration ) {

				// Don't continue if no details are given
				if ( ! method_exists( $integration, 'get_details' ) ) {
					continue;
				}

				$integration_details = $integration->get_details();
				$integration_image = isset( $integration_details['icon'] ) ? $integration_details['icon'] : WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/wp-icon.svg';
				$integration_name = isset( $integration_details['name'] ) ? $integration_details['name'] : WPWHPRO()->helpers->translate( 'Undefined', 'wpwhpro-page-flows-single' );

				$actions = WPWHPRO()->integrations->get_actions( $integration_slug );

				foreach( $actions as $ak => $ad ){
					if( is_array( $actions[ $ak ] ) && isset( $actions[ $ak ]['description'] ) ){
						unset( $actions[ $ak ]['description'] );
					}

					if( ! isset( $actions[ $ak ]['settings'] ) ){
						$actions[ $ak ]['settings'] = array();
					}

					if( ! isset( $actions[ $ak ]['settings']['data'] ) ){
						$actions[ $ak ]['settings']['data'] = array();
					}

					foreach( $required_action_settings as $settings_ident => $settings_data ){

						if( ! in_array( $settings_ident, $whitelisted_required_action_settings ) ){
							unset( $required_action_settings[ $settings_ident ] );
						}

                        if( $settings_ident == 'wpwhpro_action_data_mapping' ){
							if( isset( $required_action_settings[ $settings_ident ] ) ){
								if( ! empty( $data_mapping_templates ) ){

									if( ! is_array( $required_action_settings[ $settings_ident ]['choices'] ) ){
										$required_action_settings[ $settings_ident ]['choices'] = array();
									}

									$required_action_settings[ $settings_ident ]['choices'] = array_replace( $required_action_settings[ $settings_ident ]['choices'], WPWHPRO()->data_mapping->flatten_data_mapping_data( $data_mapping_templates ) );
								} else {
									unset( $required_action_settings[ $settings_ident ] ); //if empty
								}
							}
                        }

                        if( $settings_ident == 'wpwhpro_action_data_mapping_response' ){
							if( isset( $required_action_settings[ $settings_ident ] ) ){
								if( ! empty( $data_mapping_templates ) ){

									if( ! is_array( $required_action_settings[ $settings_ident ]['choices'] ) ){
										$required_action_settings[ $settings_ident ]['choices'] = array();
									}

									$required_action_settings[ $settings_ident ]['choices'] = array_replace( $required_action_settings[ $settings_ident ]['choices'], WPWHPRO()->data_mapping->flatten_data_mapping_data( $data_mapping_templates ) );
								} else {
									unset( $required_action_settings[ $settings_ident ] ); //if empty
								}
							}
                        }

                        if( $settings_ident == 'wpwhpro_action_authentication' ){
							if( isset( $required_action_settings[ $settings_ident ] ) ){
								if( ! empty( $authentication_templates ) ){

									if( ! is_array( $required_action_settings[ $settings_ident ]['choices'] ) ){
										$required_action_settings[ $settings_ident ]['choices'] = array();
									}

									$required_action_settings[ $settings_ident ]['choices'] = array_replace( $required_action_settings[ $settings_ident ]['choices'], WPWHPRO()->auth->flatten_authentication_data( $authentication_templates ) );
								} else {
									unset( $required_action_settings[ $settings_ident ] ); //if empty
								}
							}
                        }

                    }
					$actions[ $ak ]['settings']['data'] = array_merge( $actions[ $ak ]['settings']['data'], $required_action_settings );

				}

				$triggers = WPWHPRO()->integrations->get_triggers( $integration_slug );
				foreach( $triggers as $ak => $ad ){

					if( is_array( $triggers[ $ak ] ) && isset( $triggers[ $ak ]['description'] ) ){
						unset( $triggers[ $ak ]['description'] );
					}

					if( ! isset( $triggers[ $ak ]['settings'] ) ){
						$triggers[ $ak ]['settings'] = array();
					}

					if( ! isset( $triggers[ $ak ]['settings']['data'] ) ){
						$triggers[ $ak ]['settings']['data'] = array();
					}

					$required_trigger_settings_temp = $required_trigger_settings;
					foreach( $required_trigger_settings_temp as $settings_ident => $settings_data ){

						if( ! isset( $required_trigger_settings_temp[ $settings_ident ] ) ){
							continue;
						}

						if( ! in_array( $settings_ident, $whitelisted_required_trigger_settings ) ){
							unset( $required_trigger_settings_temp[ $settings_ident ] );
						}

                        if( $settings_ident == 'wpwhpro_trigger_data_mapping' ){
							if( isset( $required_trigger_settings_temp[ $settings_ident ] ) ){
								if( ! empty( $data_mapping_templates ) ){

									if( ! is_array( $required_trigger_settings_temp[ $settings_ident ]['choices'] ) ){
										$required_trigger_settings_temp[ $settings_ident ]['choices'] = array();
									}

									$required_trigger_settings_temp[ $settings_ident ]['choices'] = array_replace( $required_trigger_settings_temp[ $settings_ident ]['choices'], WPWHPRO()->data_mapping->flatten_data_mapping_data( $data_mapping_templates ) );
								} else {
									unset( $required_trigger_settings_temp[ $settings_ident ] ); //if empty
								}
							}
                        }

                        if( $settings_ident == 'wpwhpro_trigger_data_mapping_response' ){
							if( isset( $required_trigger_settings_temp[ $settings_ident ] ) ){
								if( ! empty( $data_mapping_templates ) ){

									if( ! is_array( $required_trigger_settings_temp[ $settings_ident ]['choices'] ) ){
										$required_trigger_settings_temp[ $settings_ident ]['choices'] = array();
									}

									$required_trigger_settings_temp[ $settings_ident ]['choices'] = array_replace( $required_trigger_settings_temp[ $settings_ident ]['choices'], WPWHPRO()->data_mapping->flatten_data_mapping_data( $data_mapping_templates ) );
								} else {
									unset( $required_trigger_settings_temp[ $settings_ident ] ); //if empty
								}
							}
                        }

                        if( $settings_ident == 'wpwhpro_trigger_data_mapping_cookies' ){
							if( isset( $required_trigger_settings_temp[ $settings_ident ] ) ){
								if( ! empty( $data_mapping_templates ) ){

									if( ! is_array( $required_trigger_settings_temp[ $settings_ident ]['choices'] ) ){
										$required_trigger_settings_temp[ $settings_ident ]['choices'] = array();
									}

									$required_trigger_settings_temp[ $settings_ident ]['choices'] = array_replace( $required_trigger_settings_temp[ $settings_ident ]['choices'], WPWHPRO()->data_mapping->flatten_data_mapping_data( $data_mapping_templates ) );
								} else {
									unset( $required_trigger_settings_temp[ $settings_ident ] ); //if empty
								}
							}
                        }

                        if( $settings_ident == 'wpwhpro_trigger_authentication' ){
							if( isset( $required_trigger_settings_temp[ $settings_ident ] ) ){
								if( ! empty( $authentication_templates ) ){

									if( ! is_array( $required_trigger_settings_temp[ $settings_ident ]['choices'] ) ){
										$required_trigger_settings_temp[ $settings_ident ]['choices'] = array();
									}

									$required_trigger_settings_temp[ $settings_ident ]['choices'] = array_replace( $required_trigger_settings_temp[ $settings_ident ]['choices'], WPWHPRO()->auth->flatten_authentication_data( $authentication_templates ) );
								} else {
									unset( $required_trigger_settings_temp[ $settings_ident ] ); //if empty
								}
							}
                        }

                    }

					if( isset( $triggers[ $ak ]['settings']['load_default_settings'] ) && $triggers[ $ak ]['settings']['load_default_settings'] === true ){
						$triggers[ $ak ]['settings']['data'] = array_merge( $triggers[ $ak ]['settings']['data'], $default_trigger_settings );
					}

					$triggers[ $ak ]['settings']['data'] = array_merge( $triggers[ $ak ]['settings']['data'], $required_trigger_settings_temp );


					if( isset( $triggers[ $ak ]['receivable_url'] ) && $triggers[ $ak ]['receivable_url'] === true ){
						$triggers[ $ak ]['settings']['data'] = array_merge( $receivable_trigger_settings, $triggers[ $ak ]['settings']['data'] );
					}

				}

				$output[] = array(
					'name' => $integration_name,
					'slug' => $integration_slug,
					'icon' => array(
						'src' => $integration_image,
						'alt' => sprintf( WPWHPRO()->helpers->translate( 'The logo of the %1$s integration.', 'wpwhpro-page-flows-single' ), $integration_name ),
					),
					'actions' => $actions,
					'triggers' => $triggers,
					'helpers' => $integration->helpers,
				);
			}

			$response['success'] = true;
			$response['result'] = $output;
			$response['msg'] = WPWHPRO()->helpers->translate('Integrations have been returned successfully.', 'ajax-settings');

			return $response;
		}

		$response['msg'] = WPWHPRO()->helpers->translate('No integrations have been found.', 'ajax-settings');

		return $response;
	}

	/**
	 * AJAX: Get Triggers
	 *
	 * @return void
	 */
	public function ajax_get_triggers() {

    $integration = isset( $_REQUEST['integration'] ) ? sanitize_text_field( $_REQUEST['integration'] ) : '';

    // Setting name.
    $triggers = WPWHPRO()->integrations->get_triggers( $integration );

    if ( ! empty( $triggers ) ) {
      $output = array();

      foreach ( $triggers as $trigger_name => $trigger_value ) {
        $output[$trigger_name] = array(
          'name' => $trigger_value['name'],
          'short_description' => $trigger_value['short_description'],
          'trigger' => $trigger_value['trigger'],
          'settings' => $trigger_value['settings'],
          'returns_code' => $trigger_value['returns_code'],
          'premium' => $trigger_value['premium']
        );
      }

	  	$response['success'] = true;
		$response['result'] = $output;
		$response['msg'] = WPWHPRO()->helpers->translate('Triggers have been returned successfully.', 'ajax-settings');

		return $response;
    }

	$response['msg'] = WPWHPRO()->helpers->translate('No triggers have been found.', 'ajax-settings');

	return $response;
  }

	/**
	 * AJAX: Get Trigger
	 *
	 * @return void
	 */
  public function ajax_get_trigger() {

    // Setting name.
    $trigger_name = isset( $_REQUEST['trigger_name'] ) ? sanitize_text_field( $_REQUEST['trigger_name'] ) : '';

    $trigger_data = array();

    if ( ! empty( $trigger_data ) ) {
      $output = array();

      $output = array(
        'fields' => array(

        )
      );

	  	$response['success'] = true;
		$response['result'] = $output;
		$response['msg'] = WPWHPRO()->helpers->translate('Trigger has been returned successfully.', 'ajax-settings');

		return $response;

    }

	$response['msg'] = WPWHPRO()->helpers->translate('No trigger has been found.', 'ajax-settings');

	return $response;
  }

	/**
	 * AJAX: Get Common Flow tags
	 *
	 * @return void
	 */
  public function ajax_get_flow_common_tags() {

    // Setting name.
    $common_tags = WPWHPRO()->settings->get_flow_common_tags();

    if ( is_array( $common_tags ) && ! empty( $common_tags ) ) {
	  	$response['success'] = true;
			$response['result'] = $common_tags;
			$response['msg'] = WPWHPRO()->helpers->translate('Common flow tags have been returned successfully.', 'ajax-settings');

			return $response;
    }

		$response['msg'] = WPWHPRO()->helpers->translate('No common flow tags have been found.', 'ajax-settings');

		return $response;
  }

	/**
	 * AJAX: Fire step action
	 *
	 * @return void
	 */
  public function ajax_fire_step_action() {

		$step_data = isset( $_REQUEST['step_data'] ) ? $_REQUEST['step_data'] : array();
		$action = isset( $_REQUEST['wpwh_action'] ) ? sanitize_title( $_REQUEST['wpwh_action'] ) : '';
		$flow_id = isset( $_REQUEST['flow_id'] ) ? intval( $_REQUEST['flow_id'] ) : 0;
		$step_id = isset( $_REQUEST['step_id'] ) ? sanitize_text_field( $_REQUEST['step_id'] ) : 0;
		$response = array(
			'success' => false
		);

		if( ! empty( $flow_id ) && ! empty( $step_id ) ){
			$webhook_action = 'wpwh-flow-temp-' . intval( $flow_id ) . '-' . sanitize_title( $step_id );
			$flow = $this->get_flows( array( 'template' => $flow_id ) );
			$action_fields = array();

			//Create the temporary action URL
			$new_action_args = array(
				'flow_id' => $flow_id,
				'flow_step' => $step_id,
				'webhook_name' => $webhook_action,
			);

			if( ! empty( $step_data ) && is_array( $step_data ) ){
				//$step_data = json_decode( stripslashes( json_encode( $step_data ) ), true); //keep that documented in case fe face issues with any previous validation
				$step_data = $this->validate_flow_values( 'stripslashes', $step_data );
			}

			if( isset( $step_data['fields'] ) && ! empty( $step_data['fields'] ) ){

				$new_action_args['settings'] = $step_data['fields'];

				foreach( $new_action_args['settings'] as $ssk => $ssv ){

					//Ignore empty values
					if( ! isset( $ssv['value'] ) || ! $this->is_filled_setting( $ssv['value'] ) ){
						unset( $new_action_args['settings'][ $ssk ] );
						continue;
					}

					//Add argument to action fields
					if( isset( $ssv['type'] ) && $ssv['type'] === 'argument' ){
						$action_fields[ $ssk ] = $ssv;
					}

					//Only allow settings
					if( ! isset( $ssv['type'] ) || $ssv['type'] !== 'setting' ){
						unset( $new_action_args['settings'][ $ssk ] );
						continue;
					}

					$new_action_args['settings'][ $ssk ] = $ssv['value'];
				}

			}

			//add whitelisted action
			if( ! empty( $action ) ){
				$new_action_args['settings']['wpwhpro_action_action_whitelist'] = array( $action );
			}

			//since 5.0
			$new_action_args['webhook_group'] = $action;

			$this->create_flow_action( $new_action_args );

			//Reload webhooks
			WPWHPRO()->webhook->reload_webhooks();
			$webhook       = WPWHPRO()->webhook->get_hooks( 'action', $action, $webhook_action );

			//build the action payload
			$action_payload = array(
				'trigger' => array(),
				'actions' => array(),
			);

			if( isset( $flow->flow_config->triggers ) ){
				foreach( $flow->flow_config->triggers as $single_trigger ){
					if( isset( $single_trigger->variableData ) ){
						$action_payload['trigger'] = $single_trigger->variableData;
						break;
					}
				}
			}

			if( isset( $flow->flow_config->actions ) ){
				foreach( $flow->flow_config->actions as $single_action => $single_action_data ){
					if( isset( $single_action_data->variableData ) ){
						$action_payload['actions'][ $single_action ] = $single_action_data->variableData;
						break;
					}
				}
			}

			//Make the temporary API endpoint call
			if( is_array( $webhook ) ){
				if( isset( $webhook['api_key'] ) && isset( $webhook['webhook_name'] ) ){
					$endpoint_url = WPWHPRO()->webhook->built_url( $webhook['webhook_url_name'], $webhook['api_key'], array(
						'action' => $action,
						'flow_log_id' => $flow_id,
						'block_trigger' => true,
					) );

					$validated_body = $this->validate_action_fields( $action_fields, $action_payload );

					if( ! empty( $validated_body ) && ! empty( $action ) ){

						$http_args = array(
							'headers'	=> array(
								'Content-Type' => 'application/x-www-form-urlencoded'
							),
							'body'		=> $validated_body,
							'blocking'	=> true,
							'timeout'	=> 30,
							'sslverify'	=> false,
							'reject_unsafe_urls'	=> false,
						);

						$action_response = wp_remote_post( $endpoint_url, $http_args );

						if( ! empty( $action_response ) && ! is_wp_error( $action_response ) ){

							$action_body = wp_remote_retrieve_body( $action_response );

							if( is_string( $action_body ) && WPWHPRO()->helpers->is_json( $action_body ) ){
								$action_body = json_decode( $action_body, true );
							}

							$response['success'] = true;
							$response['result'] = $action_body;
							$response['msg'] = WPWHPRO()->helpers->translate('The action was executed successfully.', 'ajax-settings');

							//Delete temporarily created webhook action
							$this->delete_flow_action( $new_action_args );

							return $response;
						} else {

							if( is_wp_error( $action_response ) ){

								$error_response_data = array(
									'success' => false,
								);

								$error_response_data = array_merge( $error_response_data, WPWHPRO()->http->generate_wp_error_response( $action_response ) );

								$response['success'] = true;
								$response['msg'] = WPWHPRO()->helpers->translate('The actionw as executed, but it returned an error.', 'ajax-settings');
								$response['result'] = $error_response_data;

								return $response;

							}
						}
					}
				}
			}

			//Delete temporarily created webhook action
			$this->delete_flow_action( $new_action_args );

		}

		$response['msg'] = WPWHPRO()->helpers->translate('There was an issue executing the action.', 'ajax-settings');

		return $response;
  }

  	public function ajax_get_receivable_trigger_url(){

		$flow_id = isset( $_REQUEST['flow_id'] ) ? intval( $_REQUEST['flow_id'] ) : 0;
		$flow_trigger = isset( $_REQUEST['trigger'] ) ? sanitize_title( $_REQUEST['trigger'] ) : '';
		$response = array(
			'success' => false,
			'msg' => WPWHPRO()->helpers->translate('The trigger URL was not returned.', 'ajax-settings'),
			'settings_value' => '',
			'settings_key' => 'wpwhpro_trigger_single_receivable_url',
		);

		if( empty( $flow_id ) ){
			return $response;
		}

		$flow_trigger_url_name = $this->get_flow_trigger_url_name( $flow_id );

		if( ! empty( $flow_trigger ) ){
			$response['success'] = true;
			$response['msg'] = WPWHPRO()->helpers->translate('The trigger URL was successfully returned.', 'ajax-settings');
			$response['settings_value'] = WPWHPRO()->webhook->built_trigger_receivable_url( $flow_trigger, $flow_trigger_url_name );
		}

		return $response;
	}

  	public function ajax_get_field_query(){

        $webhook_type = isset( $_REQUEST['webhook_type'] ) ? sanitize_title( $_REQUEST['webhook_type'] ) : '';
		$webhook_group      = isset( $_REQUEST['webhook_group'] ) ? sanitize_text_field( $_REQUEST['webhook_group'] ) : '';
        $webhook_integration   = ( isset( $_REQUEST['webhook_integration'] ) && ! empty( $_REQUEST['webhook_integration'] ) ) ? sanitize_title( $_REQUEST['webhook_integration'] ) : '';
        $webhook_field   = ( isset( $_REQUEST['webhook_field'] ) && ! empty( $_REQUEST['webhook_field'] ) ) ? sanitize_title( $_REQUEST['webhook_field'] ) : '';
        $field_search   = ( isset( $_REQUEST['field_search'] ) && ! empty( $_REQUEST['field_search'] ) ) ? esc_sql( $_REQUEST['field_search'] ) : '';
        $paged = ( isset( $_REQUEST['page'] ) && ! empty( $_REQUEST['page'] ) ) ? intval( $_REQUEST['page'] ) : 1;
        $selected = ( isset( $_REQUEST['selected'] ) && ! empty( $_REQUEST['selected'] ) ) ? $_REQUEST['selected'] : '';
        $response           = array(
			'success' => false,
			'msg' => WPWHPRO()->helpers->translate('No items have been returned.', 'ajax-settings'),
			'data' => array(
				'total' => 0,
				'choices' => array(),
			)
		);
		$endpoint = null;

		if( ! empty( $webhook_type ) && ! empty( $webhook_group ) && ! empty( $webhook_integration ) && ! empty( $webhook_field ) ){
		    switch( $webhook_type ){
				case 'action':
					$endpoint = WPWHPRO()->integrations->get_actions( $webhook_integration, $webhook_group );
					break;
				case 'trigger':
					$endpoint = WPWHPRO()->integrations->get_triggers( $webhook_integration, $webhook_group );
					break;
			}

			if( ! empty( $endpoint ) ){

				if(
					isset( $endpoint['settings']['data'] )
					&& is_array( $endpoint['settings']['data'] )
					&& isset( $endpoint['settings']['data'][ $webhook_field ] )
				){
					$query_items = WPWHPRO()->fields->get_query_items( $endpoint['settings']['data'][ $webhook_field ], $args = array(
						's' => $field_search,
						'paged' => $paged,
						'selected' => $selected,
					) );

					$response['data']['total'] = $query_items['total'];
					$response['data']['per_page'] = $query_items['per_page'];
					$response['data']['item_count'] = $query_items['item_count'];
					$response['data']['page'] = $paged;

					if( ! empty( $query_items ) && is_array( $query_items ) && isset( $query_items['items'] ) ){
						$response['success'] = true;

						//validate items to make them compatible with select2
						foreach( $query_items['items'] as $item_name => $item_value ){

							if( ! is_array( $item_value ) || ! isset( $item_value['label'] ) ){
								continue;
							}

							$response['data']['choices'][] = array(
								'id' => $item_value['value'],
								'text' => $item_value['label'],
							);

						}

					}
				}

			}
        }

		//set a success in case items have been given
		if( ! empty( $response['data']['choices'] ) ){
			$response['success'] = true;
			$response['msg'] = WPWHPRO()->helpers->translate('The items have been returned successfully.', 'ajax-settings');
		}

        return $response;
	}

  	/**
	 * ######################
	 * ###
	 * #### Flows Logs SQL table
	 * ###
	 * ######################
	 */

	/**
	 * Initialize the flows table
	 *
	 * @return void
	 */
	public function maybe_setup_flows_logs_table() {

		//shorten circle if already set up
		if ( $this->flow_logs_table_exists ) {
			return;
		}

		if ( ! WPWHPRO()->sql->table_exists( $this->flow_logs_table_data['table_name'] ) ) {
			WPWHPRO()->sql->run_dbdelta( $this->flow_logs_table_data['sql_create_table'] );
		}

		$this->flow_logs_table_exists = true;

	}

	/**
	 * Get the data flow logs
	 *
	 * @param array $args Further flows to filter for
	 * @return mixed - an array of multiple flows or an object for a single flow
	 */
	public function get_flow_log( $id, $cached = true ) {

		if ( empty( $id ) || ! is_numeric( $id ) ) {
			return false;
		}

		$id = intval( $id );

		if ( ! empty( $this->cache_flow_logs ) && $cached ) {

			if ( isset( $this->cache_flow_logs[ $id ] ) ) {
				return $this->cache_flow_logs[ $id ];
			} else {
				return false;
			}

		}

		$this->maybe_setup_flows_table();

		$sql = 'SELECT * FROM {prefix}' . $this->flow_logs_table_data['table_name'] . ' WHERE id = ' . $id . ';';
		$data = WPWHPRO()->sql->run( $sql );

		$validated_data = array();

		if ( ! empty( $data ) && is_array( $data ) ) {
			foreach( $data as $single ) {
				if ( ! empty( $single->id ) ) {
					$newSingle = $single;
					$newSingle->flow_payload = ! empty( $newSingle->flow_payload ) ? json_decode( base64_decode( $newSingle->flow_payload ), true ) : '';
					$newSingle->flow_config = ! empty( $newSingle->flow_config ) ? json_decode( base64_decode( $newSingle->flow_config ), true ) : '';
					$validated_data = $newSingle;
				}
			}
		}

		$this->cache_flows[ $id ] = apply_filters( 'wpwhpro/flows/logs/get_flow_log', $validated_data, $id, $cached, $data );

		return $this->cache_flows[ $id ];
	}

	/**
	 * Add a log data item to the logs
	 *
	 * @param string $msg
	 * @param mixed $data can be everything that should be saved as log data
	 * @return bool - True if the function runs successfully
	 */
	public function add_flow_log( $flow_id, $args = array() ){

		if( empty( $flow_id ) ){
			return false;
		}

		$flow_id = intval( $flow_id );
		$flow_config = isset( $args['flow_config'] ) ? $args['flow_config'] : '';
		$flow_payload = isset( $args['flow_payload'] ) ? $args['flow_payload'] : '';

		$this->maybe_setup_flows_logs_table();

		$sql_vals = array(
			'flow_id' => $flow_id,
			'flow_config' => ( is_string( $flow_config ) ) ?  base64_encode( $flow_config ) : base64_encode( json_encode( $flow_config ) ),
			'flow_payload' => ( is_string( $flow_payload ) ) ?  base64_encode( $flow_payload ) : base64_encode( json_encode( $flow_payload ) ),
			'flow_completed' => 0,
			'flow_date' => date( 'Y-m-d H:i:s' )
		);

		// START UPDATE PRODUCT
		$sql_keys = '';
		$sql_values = '';
		foreach( $sql_vals as $key => $single ){

			$sql_keys .= esc_sql( $key ) . ', ';
			$sql_values .= '"' . $single . '", ';

		}

		$sql = 'INSERT INTO {prefix}' . $this->flow_logs_table_data['table_name'] . ' (' . trim($sql_keys, ', ') . ') VALUES (' . trim($sql_values, ', ') . ');';
		$id = WPWHPRO()->sql->run( $sql, OBJECT, array( 'return_id' => true ) );

		return $id;

	}

	/**
	 * Update an existing flows template
	 *
	 * @param int $id - the template id
	 * @param array $data - the new template data
	 * @return bool - True if update was successful, false if not
	 */
	public function update_flow_log( $id, $data ) {

		$id = intval( $id );

		$this->maybe_setup_flows_logs_table();

		$flow_log = $this->get_flow_log( $id );
		if ( ! $flow_log ) {
			return false;
		}

		$sql_vals = array();

		if ( isset( $data['flow_config'] ) ) {
			$sql_vals['flow_config'] = base64_encode( json_encode( $data['flow_config'] ) );
		}

		if ( isset( $data['flow_payload'] ) ) {
			$sql_vals['flow_payload'] = base64_encode( json_encode( $data['flow_payload'] ) );
		}

		if ( isset( $data['flow_completed'] ) ) {
			$sql_vals['flow_completed'] = intval( $data['flow_completed'] );
		}

		if ( isset( $data['flow_id'] ) ) {
			$sql_vals['flow_id'] = intval( $data['flow_id'] );
		}

		if ( empty( $sql_vals ) ) {
			return false;
		}

		$sql_string = '';
		foreach( $sql_vals as $key => $single ) {

			$sql_string .= $key . ' = "' . $single . '", ';

		}
		$sql_string = trim( $sql_string, ', ' );

		$sql = 'UPDATE {prefix}' . $this->flow_logs_table_data['table_name'] . ' SET ' . $sql_string . ' WHERE id = ' . $id . ';';
		WPWHPRO()->sql->run($sql);

		return true;

	}

	/**
	 * ######################
	 * ###
	 * #### Common tags logic
	 * ###
	 * ######################
	 */

	public function get_common_tag_value( $tag ){
		$value = '';

		$user_id = 0;
		if( isset( $this->flow_common_tags_cache['user_id'] ) ){
			$user_id = $this->flow_common_tags_cache['user_id'];
		} else {
			$user_id = get_current_user_id();
			$this->flow_common_tags_cache['user_id'] = $user_id;
		}

		$user = null;
		if( ! empty( $user_id ) ){
			if( isset( $this->flow_common_tags_cache['user'] ) ){
				$user = $this->flow_common_tags_cache['user'];
			} else {
				$user = get_user_by( 'id', $user_id );
				$this->flow_common_tags_cache['user'] = $user;
			}
		}

		if( isset( $this->flow_common_tags_cache_value[ $tag ] ) ){
			$value = $this->flow_common_tags_cache_value[ $tag ];
		} else {

			switch( $tag ){
				case 'common:user_first_name':
					if( ! empty( $user ) && ! is_wp_error( $user ) && isset( $user->first_name ) ){
						$value = $user->first_name;
					}
					break;
				case 'common:user_last_name':
					if( ! empty( $user ) && ! is_wp_error( $user ) && isset( $user->last_name ) ){
						$value = $user->last_name;
					}
					break;
				case 'common:user_login':
					if( ! empty( $user ) && ! is_wp_error( $user ) && isset( $user->data ) && isset( $user->data->user_login ) ){
						$value = $user->data->user_login;
					}
					break;
				case 'common:user_email':
					if( ! empty( $user ) && ! is_wp_error( $user ) && isset( $user->data ) && isset( $user->data->user_email ) ){
						$value = $user->data->user_email;
					}
					break;
				case 'common:user_display_name':
					if( ! empty( $user ) && ! is_wp_error( $user ) && isset( $user->data ) && isset( $user->data->display_name ) ){
						$value = $user->data->display_name;
					}
					break;
				case 'common:user_id':
					if( ! empty( $user_id ) ){
						$value = $user_id;
					}
					break;
				case 'common:reset_pw_url':
					$value = wp_lostpassword_url();
					break;
				case 'common:admin_email':
					$value = get_option( 'admin_email' );
					break;
				case 'common:site_name':
					$value = get_option( 'blogname' );
					break;
				case 'common:site_url':
					$value = get_option( 'siteurl' );
					break;
				case 'common:current_date':
					$value = wp_date( get_option( 'date_format' ), get_post_timestamp() );
					break;
			}

			$this->flow_common_tags_cache_value[ $tag ] = $value;
		}

		return apply_filters( 'wpwhpro/flows/get_common_tag_value', $value, $tag );
	}

  	/**
	 * ######################
	 * ###
	 * #### Flows SQL table
	 * ###
	 * ######################
	 */

	/**
	 * Initialize the flows table
	 *
	 * @return void
	 */
	public function maybe_setup_flows_table() {

		//shorten circle if already set up
		if ( $this->table_exists ) {
			return;
		}

		if ( ! WPWHPRO()->sql->table_exists( $this->flows_table_data['table_name'] ) ) {
			WPWHPRO()->sql->run_dbdelta( $this->flows_table_data['sql_create_table'] );
		}

		$this->table_exists = true;

	}

	/**
	 * Get the data flows template/S
	 *
	 * @param array $further flows to filter for
	 * @return mixed - an array of multiple flows or an object for a single flow
	 */
	public function get_flows( $args = array(), $cached = true ) {

		$template = 'all';
		if( is_array( $args ) && isset( $args['template'] ) ){
			$template = $args['template'];
		}

		$flow_trigger = '';
		if( is_array( $args ) && isset( $args['flow_trigger'] ) ){
			$flow_trigger = $args['flow_trigger'];
		}

		if ( ! is_numeric( $template ) && $template !== 'all' ) {
			return false;
		}

		if( is_numeric( $template ) ){
			$template = intval( $template );
		}

		if ( ! empty( $this->cache_flows ) && $cached ) {

			if ( $template !== 'all' ) {
				if ( isset( $this->cache_flows[ $template ] ) ) {
					return $this->cache_flows[ $template ];
				} else {
					return false;
				}
			} else {

				if( ! $flow_trigger ){
					return $this->cache_flows;
				} else {
					$filtered_flows = array();
					if( ! empty( $this->cache_flows ) && is_array( $this->cache_flows ) ){
						foreach( $this->cache_flows as $fkey => $fdata ){
							if( is_object( $fdata ) && isset( $fdata->flow_trigger ) && $fdata->flow_trigger === $flow_trigger ){
								$filtered_flows[ $fkey ] = $fdata;
							}
						}
					}

					return $filtered_flows;
				}

			}

		}

		$this->maybe_setup_flows_table();

		$sql = 'SELECT * FROM {prefix}' . $this->flows_table_data['table_name'] . ' ORDER BY id ASC;';

		$data = WPWHPRO()->sql->run($sql);

		$validated_data = array();

		if ( ! empty( $data ) && is_array( $data ) ) {
			foreach( $data as $single ) {
				if ( ! empty( $single->id ) ) {
					$newSingle = $single;
					$newSingle->flow_config = json_decode( base64_decode( $newSingle->flow_config, true ) );
					$validated_data[ $single->id ] = $newSingle;
				}
			}
		}

		$this->cache_flows = $validated_data;

		if ( $template !== 'all' ) {
			if ( isset( $this->cache_flows[ $template ] ) ) {
				return $this->cache_flows[ $template ];
			} else {
				return false;
			}
		} else {

			if( ! $flow_trigger ){
				return $this->cache_flows;
			} else {
				$filtered_flows = array();
				if( ! empty( $this->cache_flow ) && ! is_array( $this->cache_flow ) ){
					foreach( $this->cache_flows as $fkey => $fdata ){
						if( is_object( $fdata ) && isset( $fdata->flow_trigger ) && $fdata->flow_trigger === $flow_trigger ){
							$filtered_flows[ $fkey ] = $fdata;
						}
					}
				}

				return $filtered_flows;
			}

		}
	}

	/**
	 * Helper function to flatten flows specific data
	 *
	 * @param mixed $data - the data value that needs to be flattened
	 * @return mixed - the flattened value
	 */
	public function flatten_flows_data( $data ) {
		$flattened = array();

		foreach( $data as $id => $sdata ) {
			$flattened[ $id ] = $sdata->flow_title;
		}

		return $flattened;
	}

	/**
	 * Delete a flows template
	 *
	 * @param ind $id - the id of the flows template
	 * @return bool - True if deletion was succesful, false if not
	 */
	public function delete_flow( $id ) {

		$this->maybe_setup_flows_table();

		$id = intval( $id );
		$flow = $this->get_flows( array( 'template' => $id ) );

		if ( ! $flow ) {
			return false;
		}

		//Delete related trigger
		if( isset( $flow->flow_trigger ) && ! empty( $flow->flow_trigger ) ){
			$this->delete_flow_trigger( array( 'flow_id' => $id, 'webhook_group' => $flow->flow_trigger ) );
		}

		//Delete related actions
		if( isset( $flow->flow_config ) && is_object( $flow->flow_config ) && isset( $flow->flow_config->actions ) && ! empty( $flow->flow_config->actions ) ){
			foreach( $flow->flow_config->actions as $sak => $sav ){

				$action = '';
				if( isset( $sav->action ) ){
					$action = sanitize_title( $sav->action );
				}

				$this->delete_flow_action( array( 'flow_id' => $id, 'flow_step' => $sak, 'webhook_group' => $action ) );
			}
		}

		$sql = 'DELETE FROM {prefix}' . $this->flows_table_data['table_name'] . ' WHERE id = ' . $id . ';';
		WPWHPRO()->sql->run($sql);

		return true;

	}

	/**
	 * Get a global count of all flows templates
	 *
	 * @return mixed - int if count is available, false if not
	 */
	public function get_flows_count() {

		if ( ! empty( $this->cache_flows_count ) ) {
			return intval( $this->cache_flows_count );
		}

		$this->maybe_setup_flows_table();

		$sql = 'SELECT COUNT(*) FROM {prefix}' . $this->flows_table_data['table_name'] . ';';
		$data = WPWHPRO()->sql->run($sql);

		if ( is_array( $data ) && ! empty( $data ) ) {
			$this->cache_flows_count = $data;
			return intval( $data[0]->{"COUNT(*)"} );
		} else {
			return false;
		}

	}

	/**
	 * Add a flows template
	 *
	 * @param array $data - an array contianing all relevant data
	 * @return bool - True if the creation was successful, false if not
	 */
	public function add_flow( $data = array() ) {

		$this->maybe_setup_flows_table();

		$flow_author = isset( $data['flow_author'] ) ? $data['flow_author'] : get_current_user_id();
		$flow_status = isset( $data['flow_status'] ) ? $data['flow_status'] : 'inactive';
		$flow_trigger = isset( $data['flow_trigger'] ) ? $data['flow_trigger'] : '';
		$flow_title = isset( $data['flow_title'] ) ? wp_strip_all_tags( sanitize_text_field( $data['flow_title'] ) ) : WPWHPRO()->helpers->translate( 'unnamed', 'wpwhpro-page-flows-single' );
		$flow_name = isset( $data['flow_name'] ) ? sanitize_title( $data['flow_name'] ) : sanitize_title( $flow_title );
		$flow_date = isset( $data['flow_date'] ) ? $data['flow_date'] : date( 'Y-m-d H:i:s' );

		$sql_vals = array(
			'flow_title' => $flow_title,
			'flow_name' => $flow_name,
			'flow_status' => $flow_status,
			'flow_trigger' => $flow_trigger,
			'flow_author' => intval( $flow_author ),
			'flow_date' => $flow_date,
		);

		if( isset( $data['flow_config'] ) ){
			$sql_vals['flow_config'] = ( is_array( $data['flow_config'] ) ) ? base64_encode( json_encode( $data['flow_config'] ) ) : base64_encode( $data['flow_config'] );
		}

		if( isset( $data['id'] ) && ! empty( $data['id'] ) && is_numeric( $data['id'] ) ){
			$sql_vals['id'] = intval( $data['id'] );
		}

		$sql_keys = '';
		$sql_values = '';
		foreach( $sql_vals as $key => $single ) {

			$sql_keys .= esc_sql( $key ) . ', ';
			$sql_values .= '"' . $single . '", ';

		}

		$sql = 'INSERT INTO {prefix}' . $this->flows_table_data['table_name'] . ' ( ' . trim($sql_keys, ', ' ) . ' ) VALUES ( ' . trim($sql_values, ', ' ) . ' );';
		$flow_id = WPWHPRO()->sql->run( $sql, OBJECT, array( 'return_id' => true ) );

		return $flow_id;

	}

	/**
	 * Update an existing flows template
	 *
	 * @param int $id - the template id
	 * @param array $data - the new template data
	 * @return bool - True if update was successful, false if not
	 */
	public function update_flow( $id, $data ) {

		$id = intval( $id );

		$this->maybe_setup_flows_table();

		$flow = $this->get_flows( array( 'template' => $id ) );
		if ( ! $flow ) {
			return false;
		}

		$sql_vals = array();

		if ( isset( $data['flow_title'] ) ) {
			$sql_vals['flow_title'] = wp_strip_all_tags( sanitize_text_field( $data['flow_title'] ) );
		}

		if ( isset( $data['flow_name'] ) ) {
			$sql_vals['flow_name'] = sanitize_title( $data['flow_name'] );
		}

		if ( isset( $data['flow_config'] ) ) {
			$sql_vals['flow_config'] = base64_encode( json_encode( $data['flow_config'] ) );
		}

		if ( isset( $data['flow_status'] ) ) {
			$sql_vals['flow_status'] = sanitize_title( $data['flow_status'] );
		}

		if ( isset( $data['flow_trigger'] ) ) {
			$sql_vals['flow_trigger'] = sanitize_title( $data['flow_trigger'] );
		}

		if ( isset( $data['flow_author'] ) ) {
			$sql_vals['flow_author'] = intval( $data['flow_author'] );
		}

		if ( isset( $data['flow_date'] ) ) {
			$sql_vals['flow_date'] = date( 'Y-m-d H:i:s', strtotime( $data['flow_date'] ) );
		}

		if ( empty( $sql_vals ) ) {
			return false;
		}

		$sql_string = '';
		foreach( $sql_vals as $key => $single ) {

			$sql_string .= $key . ' = "' . $single . '", ';

		}
		$sql_string = trim( $sql_string, ', ' );

		$sql = 'UPDATE {prefix}' . $this->flows_table_data['table_name'] . ' SET ' . $sql_string . ' WHERE id = ' . $id . ';';
		WPWHPRO()->sql->run($sql);

		if( isset( $flow->flow_trigger ) ){

			$date_created = '';
			$trigger_secret = '';
			$new_status = ( isset( $sql_vals['flow_status'] ) ) ? $sql_vals['flow_status'] : $flow->flow_status;

			//Delete old trigger
			if( isset( $flow->flow_trigger ) && ! empty( $flow->flow_trigger ) ){

				$webhook_trigger = $this->get_flow_trigger_url_name( $id );
				$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', $flow->flow_trigger );

				if( isset( $webhooks[ $webhook_trigger ] ) && isset( $webhooks[ $webhook_trigger ]['secret'] ) ){
					$trigger_secret = $webhooks[ $webhook_trigger ]['secret'];
				}

				if( isset( $webhooks[ $webhook_trigger ] ) && isset( $webhooks[ $webhook_trigger ]['date_created'] ) ){
					$date_created = $webhooks[ $webhook_trigger ]['date_created'];
				}

				$this->delete_flow_trigger( array( 'flow_id' => $id, 'webhook_group' => $flow->flow_trigger ) );
			}

			//Add new trigger
			if( isset( $sql_vals['flow_trigger'] ) && ! empty( $sql_vals['flow_trigger'] ) ){
				$new_trigger_args = array(
					'flow_id' => $id,
					'webhook_group' => $sql_vals['flow_trigger'],
					'secret' => $trigger_secret,
					'date_created' => $date_created,
				);

				if( isset( $data['flow_config'] ) && is_array( $data['flow_config'] ) && isset( $data['flow_config']['triggers'] ) && ! empty( $data['flow_config']['triggers'] ) ){
					foreach( $data['flow_config']['triggers'] as $tk => $tv ){
						if( isset( $tv['fields'] ) && ! empty( $tv['fields'] ) ){
							$new_trigger_args['settings'] = $tv['fields'];

							foreach( $new_trigger_args['settings'] as $ssk => $ssv ){
								if( isset( $ssv['value'] ) && $this->is_filled_setting( $ssv['value'] ) ){
									$new_trigger_args['settings'][ $ssk ] = $ssv['value'];
								} else {
									unset( $new_trigger_args['settings'][ $ssk ] );
								}

							}
						}
						break; // it's only one available anyways
					}
				}
				$this->create_flow_trigger( $new_trigger_args );
			}

			if( $new_status === 'active' ){

				//Delete old actions
				if( isset( $flow->flow_config ) && is_object( $flow->flow_config ) && isset( $flow->flow_config->actions ) && ! empty( $flow->flow_config->actions ) ){
					foreach( $flow->flow_config->actions as $sak => $sav ){

						$action = '';
						if( isset( $sav->action ) ){
							$action = sanitize_title( $sav->action );
						}

						$this->delete_flow_action( array( 'flow_id' => $id, 'flow_step' => $sak, 'webhook_group' => $action ) );
					}
				}

				//Add new actions
				if( isset( $data['flow_config'] ) && is_array( $data['flow_config'] ) && isset( $data['flow_config']['actions'] ) && ! empty( $data['flow_config']['actions'] ) ){
					foreach( $data['flow_config']['actions'] as $tk => $tv ){

						$new_action_args = array(
							'flow_id' => $id,
							'flow_step' => $tk,
						);

						$action = '';
						if( isset( $tv['action'] ) ){
							$action = sanitize_title( $tv['action'] );
						}

						if( isset( $tv['fields'] ) && ! empty( $tv['fields'] ) ){

							$new_action_args['settings'] = $tv['fields'];

							//this foreach is only for testing as of now
							foreach( $new_action_args['settings'] as $ssk => $ssv ){

								//Only allow settings
								if( ! isset( $ssv['type'] ) || $ssv['type'] !== 'setting' ){
									unset( $new_action_args['settings'][ $ssk ] );
									continue;
								}

								if( isset( $ssv['value'] ) && $this->is_filled_setting( $ssv['value'] ) ){
									$new_action_args['settings'][ $ssk ] = $ssv['value'];
								} else {
									unset( $new_action_args['settings'][ $ssk ] );
								}
							}

						}

						//add whitelisted action
						if( ! empty( $action ) ){
							$new_action_args['settings']['wpwhpro_action_action_whitelist'] = array( $action );
						}

						//since 5.0
						$new_action_args['webhook_group'] = $action;

						$this->create_flow_action( $new_action_args );
					}
				}
			} else {
				//Delete actions
				if( isset( $flow->flow_config ) && is_object( $flow->flow_config ) && isset( $flow->flow_config->actions ) && ! empty( $flow->flow_config->actions ) ){
					foreach( $flow->flow_config->actions as $sak => $sav ){

						$action = '';
						if( isset( $sav->action ) ){
							$action = sanitize_title( $sav->action );
						}

						$this->delete_flow_action( array( 'flow_id' => $id, 'flow_step' => $sak, 'webhook_group' => $action ) );
					}
				}
			}

		} else {
			//Delete trigger
			$new_flow_trigger = ( isset( $sql_vals['flow_trigger'] ) ) ? $sql_vals['flow_trigger'] : '';
			if( ! empty( $new_flow_trigger ) ){
				$this->delete_flow_trigger( array( 'flow_id' => $id, 'webhook_group' => $new_flow_trigger ) );
			}
		}

		//reload the endpoints
		WPWHPRO()->webhook->reload_webhooks();

		return true;

	}

	/**
	 * Delete the whole flows table
	 *
	 * @return bool - wether the deletion was successful or not
	 */
	public function delete_table() {

		$check = true;

		if ( WPWHPRO()->sql->table_exists( $this->flows_table_data['table_name'] ) ) {
			$check = WPWHPRO()->sql->run( $this->flows_table_data['sql_drop_table'] );
		}

		$this->table_exists = false;

		return $check;
    }

	/**
	 * Delete the whole flow logs table
	 *
	 * @since 5.0
	 *
	 * @return bool - wether the deletion was successful or not
	 */
	public function delete_logs_table() {

		$check = true;

		if ( WPWHPRO()->sql->table_exists( $this->flow_logs_table_data['table_name'] ) ) {
			$check = WPWHPRO()->sql->run( $this->flow_logs_table_data['sql_drop_table'] );
		}

		$this->flow_logs_table_exists = false;

		return $check;
    }

	public function create_flow_trigger( $args = array() ){
		$return = false;

		if( ! isset( $args['flow_id'] ) || empty( $args['flow_id'] ) ){
			return $return;
		}

		if( ! isset( $args['webhook_group'] ) || empty( $args['webhook_group'] ) ){
			return $return;
		}

		$webhook_group = $args['webhook_group'];
		$webhook_trigger = $this->get_flow_trigger_url_name( $args['flow_id'] );

		$trigger_args = array(
			'group' => $webhook_group,
			'webhook_url' => 'wpwhflow',
			'webhook_url_name' => $webhook_trigger,
			'settings' => array(),
		);

		if( isset( $args['secret'] ) && ! empty( $args['secret'] ) ){
			$trigger_args['secret'] = $args['secret'];
		}

		if( isset( $args['date_created'] ) && ! empty( $args['date_created'] ) ){
			$trigger_args['date_created'] = $args['date_created'];
		}

		if( isset( $args['settings'] ) && is_array( $args['settings'] ) && ! empty( $args['settings'] ) ){
			$trigger_args['settings'] = $args['settings'];

			//provide compatibility to the default select field structure
			foreach( $trigger_args['settings'] as $sk => $sv ){

				//Make sure for non-multiple values, we only serve one scalar value
				$allow_multiple = ( isset( $sv['multiple'] ) && $sv['multiple'] ) ? true : false;
				if( isset( $sv['type'] ) && $sv['type'] === 'select' && ! $allow_multiple ){
					if( isset( $sv['value'] ) && $sv['value'] !== '' ){
						$trigger_args['settings'][ $sk ]['value'] = WPWHPRO()->helpers->serve_first( $sv['value'] );
					}
				}

			}
		}

		$check = WPWHPRO()->webhook->create( $webhook_trigger, 'trigger', $trigger_args );

		if( $check ){
			$return = true;
		}

		return $return;
	}

	public function update_flow_trigger( $args = array() ){
		$return = false;

		if( ! isset( $args['flow_id'] ) || empty( $args['flow_id'] ) ){
			return $return;
		}

		if( ! isset( $args['webhook_group'] ) || empty( $args['webhook_group'] ) ){
			return $return;
		}

		$webhook_group = $args['webhook_group'];
		$webhook_trigger = $this->get_flow_trigger_url_name( $args['flow_id'] );

		$trigger_args = array(
			'settings' => array(),
		);

		if( isset( $args['settings'] ) && is_array( $args['settings'] ) && ! empty( $args['settings'] ) ){
			$trigger_args['settings'] = $args['settings'];

			//provide compatibility to the default select field structure
			foreach( $trigger_args['settings'] as $sk => $sv ){

				//Make sure for non-multiple values, we only serve one scalar value
				$allow_multiple = ( isset( $sv['multiple'] ) && $sv['multiple'] ) ? true : false;
				if( isset( $sv['type'] ) && $sv['type'] === 'select' && ! $allow_multiple ){
					if( isset( $sv['value'] ) && $sv['value'] !== '' ){
						$trigger_args['settings'][ $sk ]['value'] = WPWHPRO()->helpers->serve_first( $sv['value'] );
					}
				}

			}
		}

		$check = WPWHPRO()->webhook->update( $webhook_trigger, 'trigger', $webhook_group, $trigger_args );

		if( $check ){
			$return = true;
		}

		return $return;
	}

	public function delete_flow_trigger( $args = array() ){
		$return = false;

		if( ! isset( $args['flow_id'] ) || empty( $args['flow_id'] ) ){
			return $return;
		}

		if( ! isset( $args['webhook_group'] ) || empty( $args['webhook_group'] ) ){
			return $return;
		}

		$webhook_group = $args['webhook_group'];
		$webhook_trigger = $this->get_flow_trigger_url_name( $args['flow_id'] );
		$webhooks       = WPWHPRO()->webhook->get_hooks( 'trigger', $webhook_group );

		if( isset( $webhooks[ $webhook_trigger ] ) ){
			$check = WPWHPRO()->webhook->unset_hooks( $webhook_trigger, 'trigger', $webhook_group );
			if( $check ){
			    $return = true;
            }
		}

		return $return;
	}

	public function create_flow_action( $args = array() ){
		$return = false;

		if( ! isset( $args['flow_id'] ) || empty( $args['flow_id'] ) ){
			return $return;
		}

		if( ! isset( $args['flow_step'] ) || empty( $args['flow_step'] ) ){
			return $return;
		}

		if( ! isset( $args['webhook_group'] ) || empty( $args['webhook_group'] ) ){
			return $return;
		}

		if( isset( $args['webhook_name'] ) ){
			$webhook_action = sanitize_title( $args['webhook_name'] );
		} else {
			$webhook_action = 'wpwh-flow-' . intval( $args['flow_id'] ) . '-' . sanitize_title( $args['flow_step'] );
		}

		$action_args = array(
			'settings' => array(),
			'group' => $args['webhook_group'],
		);

		if( isset( $args['settings'] ) && is_array( $args['settings'] ) && ! empty( $args['settings'] ) ){
			$action_args['settings'] = $args['settings'];

			//provide compatibility to the default select field structure
			foreach( $action_args['settings'] as $sk => $sv ){

				//Make sure for non-multiple values, we only serve one scalar value
				$allow_multiple = ( isset( $sv['multiple'] ) && $sv['multiple'] ) ? true : false;
				if( isset( $sv['type'] ) && $sv['type'] === 'select' && ! $allow_multiple ){
					if( isset( $sv['value'] ) && $sv['value'] !== '' ){
						$action_args['settings'][ $sk ]['value'] = WPWHPRO()->helpers->serve_first( $sv['value'] );
					}
				}

			}
		}

		$check = WPWHPRO()->webhook->create( $webhook_action, 'action', $action_args );

		if( $check ){
			$return = true;
		}

		return $return;
	}

	public function update_flow_action( $args = array() ){
		$return = false;

		if( ! isset( $args['flow_id'] ) || empty( $args['flow_id'] ) ){
			return $return;
		}

		if( ! isset( $args['flow_step'] ) || empty( $args['flow_step'] ) ){
			return $return;
		}

		if( ! isset( $args['webhook_group'] ) || empty( $args['webhook_group'] ) ){
			return $return;
		}

		if( isset( $args['webhook_name'] ) ){
			$webhook_action = sanitize_title( $args['webhook_name'] );
		} else {
			$webhook_action = 'wpwh-flow-' . intval( $args['flow_id'] ) . '-' . sanitize_title( $args['flow_step'] );
		}

		$action_args = array(
			'settings' => array(),
		);

		if( isset( $args['settings'] ) && is_array( $args['settings'] ) && ! empty( $args['settings'] ) ){
			$action_args['settings'] = $args['settings'];

			//provide compatibility to the default select field structure
			foreach( $action_args['settings'] as $sk => $sv ){

				//Make sure for non-multiple values, we only serve one scalar value
				$allow_multiple = ( isset( $sv['multiple'] ) && $sv['multiple'] ) ? true : false;
				if( isset( $sv['type'] ) && $sv['type'] === 'select' && ! $allow_multiple ){
					if( isset( $sv['value'] ) && $sv['value'] !== '' ){
						$action_args['settings'][ $sk ]['value'] = WPWHPRO()->helpers->serve_first( $sv['value'] );
					}
				}

			}
		}

		$check = WPWHPRO()->webhook->update( $webhook_action, 'action', $args['webhook_group'], $action_args );

		if( $check ){
			$return = true;
		}

		return $return;
	}

	public function delete_flow_action( $args = array() ){
		$return = false;

		if( ! isset( $args['flow_id'] ) || empty( $args['flow_id'] ) ){
			return $return;
		}

		if( ! isset( $args['flow_step'] ) || empty( $args['flow_step'] ) ){
			return $return;
		}

		if( ! isset( $args['webhook_group'] ) || empty( $args['webhook_group'] ) ){
			return $return;
		}

		if( isset( $args['webhook_name'] ) ){
			$webhook_action = sanitize_title( $args['webhook_name'] );
		} else {
			$webhook_action = 'wpwh-flow-' . intval( $args['flow_id'] ) . '-' . sanitize_title( $args['flow_step'] );
		}

		$webhook = WPWHPRO()->webhook->get_hooks( 'action', $args['webhook_group'], $webhook_action );

		if( ! empty( $webhook ) ){
			$check = WPWHPRO()->webhook->unset_hooks( $webhook_action, 'action', $args['webhook_group'] );
			if( $check ){
			    $return = true;
            }
		}

		return $return;
	}

	/**
	 * This function checks prior the execution if there are any
	 * abandoned temp actions available and if so, the will be deleted
	 * to avoid issues and save performance
	 *
	 * @return void
	 */
	private function clean_abandoned_temp_actions(){

		if( ! is_admin() ){
			return;
		}

		$webhooks = WPWHPRO()->webhook->get_hooks( 'action' );
		$ident = 'wpwh-flow-temp-';
		$ident_length = strlen( $ident );

		if( ! empty( $webhooks ) && is_array( $webhooks ) ){
			foreach( $webhooks as $action => $action_data ){

				if( ! isset( $action_data['api_key'] ) || ! is_string( $action_data['api_key'] ) ){
					foreach( $action_data as $action_group => $action_group_data ){
						if( substr( $action_group, 0, $ident_length ) === $ident ){

							if( isset( $action_group_data['date_created'] ) ){
								if( strtotime( $action_group_data['date_created'] ) < strtotime( '-2 days' ) ){
									WPWHPRO()->webhook->unset_hooks( $action_group, 'action', $action );
								}
							}

						}
					}
				} else {
					//compatibility with pre 5.0 versions
					if( substr( $action, 0, $ident_length ) === $ident ){

						if( isset( $action_data['date_created'] ) ){
							if( strtotime( $action_data['date_created'] ) < strtotime( '-2 days' ) ){
								WPWHPRO()->webhook->unset_hooks( $action, 'action' );
							}
						}

					}
				}
			}
		}
	}

	public function process_flow( $flow_id, $args = array() ){
		$return = array(
			'success' => false,
			'msg' => WPWHPRO()->helpers->translate( 'We had issues processing the flow.', 'wpwh-flows-run_flow' ),
			'data' => array()
		);

		if( empty( $flow_id ) ){
			$return['msg'] = WPWHPRO()->helpers->translate( 'We could not process the flow. The flow_id was not given.', 'wpwh-flows-run_flow' );
			return $return;
		}

		$flow = $this->get_flows( array( 'template' => $flow_id ) );
		if ( ! $flow ) {
			$return['msg'] = WPWHPRO()->helpers->translate( 'We could not verify the flow. The flow was aborted.', 'wpwh-flows-run_flow' );
			return $return;
		}

		if( ! isset( $flow->flow_status ) || $flow->flow_status !== 'active' ){
			$return['msg'] = sprintf( WPWHPRO()->helpers->translate( 'The flow for id %d was aborted as the flow is not active. Logs have been created nevertheless.', 'wpwh-flows-run_flow' ), $flow_id );
			$return['cancel_processing'] = true;
			return $return;
		}

		if( ! isset( $this->flow_buffer[ $flow_id ] ) ){
			$this->flow_buffer[ $flow_id ] = array();
		}

		$this->flow_buffer[ $flow_id ][] = $args;

		$return['success'] = true;
		$return['msg'] = WPWHPRO()->helpers->translate( 'The flow was successfully processed.', 'wpwh-flows-run_flow' );
		$return['data']['flow_id'] = $flow_id;

		return $return;
	}

	public function run_flow( $flow_id, $args = array() ){
		$return = array(
			'success' => false,
			'msg' => WPWHPRO()->helpers->translate( 'We had issues executing the flow.', 'wpwh-flows-run_flow' ),
		);

		$flow_id = intval( $flow_id );

		if( empty( $flow_id ) ){
			$return['msg'] = WPWHPRO()->helpers->translate( 'We could not find the flow id.', 'wpwh-flows-run_flow' );
			return $return;
		}

		$payload = array();
		if( isset( $args['payload'] ) && is_array( $args['payload'] ) ){
			$payload = array( 'trigger' => $args['payload'] );
		}

		$flow = $this->get_flows( array( 'template' => $flow_id ) );

		if( ! empty( $flow ) && is_object( $flow ) ){

			$trigger = false;
			if( isset( $flow->flow_config ) && isset( $flow->flow_config->triggers ) ){
				foreach( $flow->flow_config->triggers as $trigger_data ){
					$trigger = $trigger_data;
					break;
				}
			}

			if( ! empty( $trigger ) ){

				//Add log entry
				$flow_log_data = array(
					'flow_config' => isset( $flow->flow_config ) ? $flow->flow_config : '',
					'flow_payload' => $payload,
				);
				$flow_log_id = $this->add_flow_log( $flow_id, $flow_log_data );

				if( ! empty( $flow_log_id ) && is_numeric( $flow_log_id ) ){

					$actions = array();
					if( isset( $flow->flow_config ) && isset( $flow->flow_config->actions ) ){
						$actions = (array) $flow->flow_config->actions;
					}

					$validated_actions = array();
					if( ! empty( $actions ) ){
						foreach( $actions as $action_key => $action_data ){

							$validated_actions[] = array(
								'flow_log_id' => $flow_log_id,
								'current' => $action_key,
								'flow_id' => $flow_id,
								'set_class_data' => array(
									'flow_log_id' => $flow_log_id,
								),
							);
						}
					}

					foreach( $validated_actions as $vaction ){
						$this->get_flow_async()->push_to_queue( $vaction );
					}

					//dispatch
					$this->get_flow_async()->save()->dispatch();
					$return['success'] = true;
					$return['msg'] = WPWHPRO()->helpers->translate( 'Flow successfully executed.', 'wpwh-flows-run_flow' );
				}

			}

		}

		return $return;
	}

	public function validate_action_fields( $fields, $payload ){

		$validated_fields = array();

		if( is_array( $fields ) ){
			foreach( $fields as $key => $data ){

				//Only allow arguments
				if( ! isset( $data['type'] ) || $data['type'] !== 'argument' ){
					continue;
				}

				if( isset( $data['value'] ) && $this->is_filled_setting( $data['value'] ) ){

					$field_type = isset( $data['field_type'] ) ? $data['field_type'] : 'string';

					switch( $field_type ){
						case 'repeater':

							$sub_entry = array();

							if( ! empty( $data['value'] ) ){
								foreach( $data['value'] as $entry ){

									if( isset( $entry['key'] ) && isset( $entry['value'] ) ){
										if( isset( $entry['mappings'] ) ){
											$sub_entry[ $entry['key'] ] = $this->validate_mappings( $entry['value'], $entry['mappings'], $payload );
										} else {
											$sub_entry[ $entry['key'] ] = $entry['value'];
										}
									}

								}
							}

							$validated_fields[ $key ] = $sub_entry;

							break;
						case 'select':

							if( ! isset( $data['multiple'] ) || $data['multiple'] === false ){
								if( is_array( $data['value'] ) ){
									$data['value'] = WPWHPRO()->helpers->serve_first( $data['value'] );
								}
							}

						case 'text':
						default:

							if( isset( $data['mappings'] ) ){
								$validated_fields[ $key ] = $this->validate_mappings( $data['value'], $data['mappings'], $payload );
							} else {
								$validated_fields[ $key ] = $data['value'];
							}

							break;
					}

				}
			}
		}

		return $validated_fields;
	}

	public function validate_action_conditions( $conditionals, $payload ){

		$is_valid = false;

		if(
			! is_array( $conditionals )
			|| ! isset( $conditionals['relation'] )
			|| empty( $conditionals['relation'] )
			|| ! isset( $conditionals['conditions'] )
			|| empty( $conditionals['conditions'] )
		){
			return $is_valid;
		}

		$relation = $conditionals['relation'];
		$conditions = $conditionals['conditions'];

		if( is_array( $conditions ) ){
			foreach( $conditions as $condition ){

				if( isset( $condition['condition_input']['mappings'] ) ){
					$condition_input = $this->validate_mappings( $condition['condition_input']['value'], $condition['condition_input']['mappings'], $payload );
				} else {
					$condition_input = $condition['condition_input']['value'];
				}

				$condition_operator = $condition['condition_operator']['value'];

				if( isset( $condition['condition_value']['mappings'] ) ){
					$condition_value = $this->validate_mappings( $condition['condition_value']['value'], $condition['condition_value']['mappings'], $payload );
				} else {
					$condition_value = $condition['condition_value']['value'];
				}

				switch( $condition_operator ){
					case 'contains':
						if( strpos( $condition_input, $condition_value ) !== FALSE ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'doesnotcontain':
						if( strpos( $condition_input, $condition_value ) === FALSE ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'is':
						if( $condition_input == $condition_value ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'isnot':
						if( $condition_input != $condition_value ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'isgreaterthan':
						if( $condition_input > $condition_value ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'isgreaterthanorequalto':
						if( $condition_input >= $condition_value ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'islessthan':
						if( $condition_input < $condition_value ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
					case 'islessthanorequalto':
						if( $condition_input <= $condition_value ){
							$is_valid = true;
						} else {
							if( $relation === 'all' ){
								$is_valid = false; //reset current state
								break 2; //condition is false
							}
						}
						break;
				}

			}
		}

		return apply_filters( 'wpwhpro/flows/validate_action_conditions', $is_valid, $conditionals, $payload );
	}

	public function validate_mappings( $string, $mapping, $payload ){

		if( is_array( $mapping ) ){
			foreach( $mapping as $tag => $data ){

				if( isset( $data['trigger'] ) ){
					$mapping_array = array(
						'trigger',
					);
				} elseif( isset( $data['action'] ) ){

					if( $data['action'] === 'common' ){
						$common_tag = '';
						if( isset( $data['mapping'] ) && is_array( $data['mapping'] ) ){
							foreach( $data['mapping'] as $key ){
								$common_tag = $key;
								break;
							}
						}

						$string = str_replace( '{{' . $tag . '}}', $this->get_common_tag_value( $common_tag ), $string );
						continue;
					} else {
						$mapping_array = array(
							'actions',
							$data['action'],
						);
					}

				}

				if( isset( $data['mapping'] ) && is_array( $data['mapping'] ) ){
					foreach( $data['mapping'] as $key ){
						$mapping_array[] = $key;
					}
				}

				$string = str_replace( '{{' . $tag . '}}', $this->locate_value( $mapping_array, $payload ), $string );

			}
		}

		return $string;
	}

	public function locate_value( $mapping, $payload ){
		$string = '';

		foreach( $mapping as $key ){

			if( is_array( $payload ) && isset( $payload[ $key ] ) ){
				$payload = $payload[ $key ];

				if( next( $mapping ) === false ){
					$string = ( is_array( $payload ) || is_object( $payload ) ) ? json_encode( $payload ) : $payload;
					break;
				}
			} elseif( is_object( $payload ) && isset( $payload->{$key} ) ){
				$payload = $payload->{$key};

				if( next( $mapping ) === false ){
					$string = ( is_array( $payload ) || is_object( $payload ) ) ? json_encode( $payload ) : $payload;
					break;
				}
			}

		}

		return $string;
	}

	public function is_filled_setting( $value ){
		$return = false;

		if( is_string( $value ) && $value !== '' ){
			$return = true;
		} elseif( is_array( $value ) ) {

			if( count( $value ) > 1 ){
				$return = true;
			} else {
				$first_data = reset( $value );
				if( $first_data !== '' && $first_data !== false ){
					$return = true;
				}
			}

		}

		return $return;
	}

	/**
	 * Validate the flow values to show the real value
	 * This counts as well for the test action
	 *
	 * @since 4.3.4
	 *
	 * @param string $validator
	 * @param array $flow_config
	 * @param boolean $validate_all
	 * @return array
	 */
	public function validate_flow_values( $validator, $flow_config, $validate_all = false ){
		$fields_to_validate = array(
			'value',
			'variableData',
		);

		if( is_array( $flow_config ) ){
			foreach( $flow_config as $fk => $fv ){

				if( is_string( $fv ) ){

					if( $validate_all || in_array( $fk, $fields_to_validate ) ){

						switch( $validator ){
							case 'addslashes':
								$flow_config[ $fk ] = addslashes( $fv );
								break;
							case 'stripslashes':
								$flow_config[ $fk ] = stripslashes( $fv );
								break;
						}

					}

				} elseif( is_array( $fv ) ){

					if( $validate_all || in_array( $fk, $fields_to_validate ) ){
						$flow_config[ $fk ] = $this->validate_flow_values( $validator, $fv, true );
					} else {
						$flow_config[ $fk ] = $this->validate_flow_values( $validator, $fv );
					}

				}



			}
		}

		return $flow_config;
	}

	public function get_flow_trigger_url_name( $flow_id ){
		$flow_name = 'wpwh-flow-' . intval( $flow_id );

		return apply_filters( 'wpwhpro/flows/logs/get_flow_trigger_url_name', $flow_name );
	}

}
