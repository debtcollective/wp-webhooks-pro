<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_json_serialize' ) ) :

	/**
	 * Load the text_json_serialize action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_json_serialize {

	public function get_details(){

		$translation_ident = "action-text_json_serialize-content";

		$parameter = array(
			'value'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Value', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The JSON formatted string.', $translation_ident ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The JSON has been successfuly serialized.',
			'data' => 'a:3:{s:5:"key_1";s:7:"Value 1";s:5:"key_2";s:7:"Value 2";s:5:"key_3";s:7:"Value 3";}',
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Text JSON serialize',
			'webhook_slug' => 'text_json_serialize',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>value</strong> argument. Please set it to the JSON formatted string.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'text_json_serialize', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Text JSON serialize', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'serialize a JSON string', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Serialize a JSON formatted string based on its structure.', $translation_ident ),
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

			if( empty( $value ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the value argument as it is required.", 'action-text_json_serialize-error' );
				return $return_args;
			}

			$serialized_data = '';

			if( WPWHPRO()->helpers->is_json( $value ) ){
				$json_data = json_decode( $value, true );
				if( ! empty( $json_data ) ){
					$serialized_data = maybe_serialize( $json_data );
				}
			}
			

			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "The JSON has been successfuly serialized.", 'action-text_json_serialize-success' );
			$return_args['data'] = $serialized_data;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.