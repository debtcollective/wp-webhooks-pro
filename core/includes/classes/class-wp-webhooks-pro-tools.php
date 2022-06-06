<?php

/**
 * WP_Webhooks_Pro_Tools Class
 *
 * This class contains all of the available tools functions
 *
 * @since 5.0
 */

/**
 * The tools class of the plugin.
 *
 * @since 5.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Tools {
	
	/**
	 * Create an export of the given plugin data
	 *
	 * @return array
	 */
	public function generate_plugin_export(){

		$export_data = array(
			'webhook_options' => WPWHPRO()->webhook->get_hooks(),
			'flows' => WPWHPRO()->flows->get_flows(),
			'authentication_templates' => WPWHPRO()->auth->get_auth_templates(),
			'data_mapping_templates' => WPWHPRO()->data_mapping->get_data_mapping(),
			'whitelist' => WPWHPRO()->whitelist->get_list(),
			'settings' => WPWHPRO()->settings->get_settings(),
		);

		return apply_filters( 'wpwhpro/tools/generate_plugin_export', $export_data );
	}

	public function import_plugin_export( $data ){
		$errors = array();

		$data = apply_filters( 'wpwhpro/tools/import_plugin_export', $data );

		if( empty( $data ) ){
			return $errors;
		}

		if( is_string( $data ) && WPWHPRO()->helpers->is_json( $data ) ){
			$data = json_decode( $data, true );
		}

		if( ! is_array( $data ) ){
			$errors[] = WPWHPRO()->helpers->translate( 'The given import data could not be validated.', 'wpwhpro-tools-import' );
			return $errors;
		}

		//reset the existing data
		WPWHPRO()->webhook->reset_wpwhpro();

		// Add all webhook related settings
		if( isset( $data['webhook_options'] ) && ! empty( $data['webhook_options'] ) ){
			$webhook_options_key = WPWHPRO()->settings->get_webhook_option_key();
			update_option( $webhook_options_key, $data['webhook_options'] );
		}

		// Add all flow settings
		if( isset( $data['flows'] ) && ! empty( $data['flows'] ) && is_array( $data['flows'] ) ){
			foreach( $data['flows'] as $flow_id => $flow_data ){
				$check = WPWHPRO()->flows->add_flow( $flow_data );
				if( empty( $check ) ){
					$errors[] = sprintf( WPWHPRO()->helpers->translate( 'There was an issue creating the flow with the id: %d', 'wpwhpro-tools-import' ), intval( $flow_id ) );
				}
			}
		}

		// Add all authentication templates
		if( isset( $data['authentication_templates'] ) && ! empty( $data['authentication_templates'] ) && is_array( $data['authentication_templates'] ) ){
			foreach( $data['authentication_templates'] as $auth_id => $auth_data ){

				if( 
					! isset( $auth_data['auth_type'] ) 
					|| ! isset( $auth_data['id'] ) 
					|| ! isset( $auth_data['name'] )
				){
					continue;
				}

				$authentication_args = array(
					'id' => $auth_data['id'],
				);

				if( isset( $auth_data['template'] ) && ! empty( $auth_data['template'] ) ){
					$authentication_args['template'] = base64_decode( $auth_data['template'] );
				}

				$check = WPWHPRO()->auth->add_template( $auth_data['name'], $auth_data['auth_type'], $authentication_args );
				if( empty( $check ) ){
					$errors[] = sprintf( WPWHPRO()->helpers->translate( 'There was an issue creating the authentication template with the id: %d', 'wpwhpro-tools-import' ), intval( $auth_id ) );
				}
			}
		}

		// Add all data mapping templates
		if( isset( $data['data_mapping_templates'] ) && ! empty( $data['data_mapping_templates'] ) && is_array( $data['data_mapping_templates'] ) ){
			foreach( $data['data_mapping_templates'] as $mapping_id => $mapping_data ){

				if( ! isset( $mapping_data['name'] ) ){
					continue;
				}

				if( isset( $mapping_data['template'] ) && ! empty( $mapping_data['template'] ) ){
					$mapping_data['template'] = base64_decode( $mapping_data['template'] );
				}

				$check = WPWHPRO()->data_mapping->add_template( $mapping_data['name'], $mapping_data );
				if( empty( $check ) ){
					$errors[] = sprintf( WPWHPRO()->helpers->translate( 'There was an issue creating the data mapping template with the id: %d', 'wpwhpro-tools-import' ), intval( $mapping_id ) );
				}
			}
		}

		// Add all whitelist items
		if( isset( $data['whitelist'] ) && ! empty( $data['whitelist'] ) && is_array( $data['whitelist'] ) ){
			foreach( $data['whitelist'] as $whitelist_key => $whitelist_ip ){

				if( empty( $whitelist_ip ) ){
					continue;
				}

				$check = WPWHPRO()->whitelist->add_item( esc_html( $whitelist_ip ), array( 'key' => $whitelist_key ) );
				if( empty( $check ) ){
					$errors[] = sprintf( WPWHPRO()->helpers->translate( 'There was an issue creating the whitelist item with the id: %s', 'wpwhpro-tools-import' ), sanitize_title( $whitelist_key ) );
				}
			}
		}

		// Add all settings data
		if( isset( $data['settings'] ) && ! empty( $data['settings'] ) && is_array( $data['settings'] ) ){
			foreach( $data['settings'] as $settings_key => $settings_data ){

				if( ! is_array( $settings_data ) || ! isset( $settings_data['value'] ) || $settings_data['value'] === '' ){
					continue;
				}

				update_option( $settings_key, $settings_data['value'] );
			}
		}

		return $errors;
	}
}
