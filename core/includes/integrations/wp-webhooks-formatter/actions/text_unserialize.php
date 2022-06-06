<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_unserialize' ) ) :

	/**
	 * Load the text_unserialize action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_unserialize {

	public function get_details(){

		$translation_ident = "action-text_unserialize-content";

		$parameter = array(
			'value'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Value', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The serialized string.', $translation_ident ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The data has been successfuly unserialized.',
			'data' => array(
				'key_1' => 'Value 1',
				'key_2' => 'Value 2',
				'key_3' => 'Value 3',
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Text unserialize',
			'webhook_slug' => 'text_unserialize',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>value</strong> argument. Please set it to the serialized string.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'text_unserialize', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Text unserialize', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'unserialize a serialized data string', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Unserialize a serialized data string into an accessible format.', $translation_ident ),
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
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the value argument as it is required.", 'action-text_unserialize-error' );
				return $return_args;
			}

			$json_data = maybe_unserialize( $value );

			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "The data has been successfuly unserialized.", 'action-text_unserialize-success' );
			$return_args['data'] = $json_data;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.