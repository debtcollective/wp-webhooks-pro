<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_explode_json' ) ) :

	/**
	 * Load the text_explode_json action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_explode_json {

		public function get_details(){

			$translation_ident = "action-text_explode_json-content";

			$parameter = array(
				'value'		=> array( 
					'required' => true, 
					'label' => WPWHPRO()->helpers->translate( 'Value', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( 'The character-separated string you want to turn into a JSON construct.', $translation_ident ),
				),
				'separator'		=> array(
					'default_value' => ',', 
					'label' => WPWHPRO()->helpers->translate( 'Separator', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( 'The separator that is used to separate the values used for the JSON construct. By default, we separate the values using a comma.', $translation_ident ),
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The string was successfully exploded to a JSON construct.',
				'data' => 
				array (
				  0 => 'value1',
				  1 => 'value2',
				  2 => 'value3',
				),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Text explode JSON',
				'webhook_slug' => 'text_explode_json',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'It is required to set the <strong>value</strong> argument. Please set it to the character-separated string you want to explode into a JSON construct.', $translation_ident ),
				),
			) );

			return array(
				'action'			=> 'text_explode_json', //required
				'name'			   => WPWHPRO()->helpers->translate( 'Text explode JSON', $translation_ident ),
				'sentence'			   => WPWHPRO()->helpers->translate( 'explode a string to a JSON construct', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Explode a character-separated string to a JSON construct.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'wp-webhooks-formatter',
				'premium'	   => true
			);


		}

		public function execute( $return_data, $response_body ){
			
			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			$value = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'value' );
			$separator = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'separator' );

			if( empty( $separator ) ){
				$separator = ',';
			}

			$value = explode( $separator, $value );

			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "The string was successfully exploded to a JSON construct.", 'action-text_explode_json-success' );
			$return_args['data'] = $value;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.