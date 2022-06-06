<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_json_to_json' ) ) :

	/**
	 * Load the text_json_to_json action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_json_to_json {

	public function get_details(){

		$translation_ident = "action-text_json_to_json-content";

		$parameter = array(
			'json'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'JSON', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The JSON formatted string.', $translation_ident ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The JSON has been successfully constructed.',
			'data' => array(
				'key_1' => 'Value 1',
				'key_2' => 'Value 2',
				'key_3' => 'Value 3',
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Text JSON construct',
			'webhook_slug' => 'text_json_to_json',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>json</strong> argument. Please set it to the JSON formatted string that you want to turn into an accessible JSON construct.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'text_json_to_json', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Text JSON construct', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'convert a JSON string to a JSON construct', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Convert a JSON formatted string to an acessible JSON construct.', $translation_ident ),
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

			$json = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'json' );

			if( empty( $json ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the json argument as it is required.", 'action-text_json_to_json-error' );
				return $return_args;
			}

			$json_validated = '';
			if( WPWHPRO()->helpers->is_json( $json ) ){

				$json_array = json_decode( $json, true );
				if( ! empty( $json_array ) ){
					$json_validated = $json_array;
				}
				
			}

			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "The JSON has been successfully constructed.", 'action-text_json_to_json-success' );
			$return_args['data'] = $json_validated;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.