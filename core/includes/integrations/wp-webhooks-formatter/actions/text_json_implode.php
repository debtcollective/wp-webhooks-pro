<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_json_implode' ) ) :

	/**
	 * Load the text_json_implode action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_json_implode {

	public function get_details(){

		$translation_ident = "action-text_json_implode-content";

		$parameter = array(
			'value'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Value', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The JSON string that should be turned into a character-separated string.', $translation_ident ),
			),
			'separator'		=> array(
				'default_value' => ',', 
				'label' => WPWHPRO()->helpers->translate( 'Separator', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The separator that is used to separate the values of the first level of the JSON. By default, we separate the values using a comma.', $translation_ident ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The JSON was successfully imploded.',
			'data' => 'value1,value2,value3',
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Text JSON implode',
			'webhook_slug' => 'text_json_implode',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>value</strong> argument. Please set it to the JSON string you want to turn into a character-separated string.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'text_json_implode', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Text JSON implode', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'implode a JSON construct to a string', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Implode the first level of a JSON string construct to a character-separated string using your preferred separator.', $translation_ident ),
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

			$validated_value = '';
			if( WPWHPRO()->helpers->is_json( $value ) ){

				$value_array = json_decode( $value, true );
				if( is_array( $value_array ) ){

					//Unset non-scalar values
					foreach( $value_array as $vk => $vv ){
						if( ! is_scalar( $vv ) ){
							unset( $value_array[ $vk ] );
						}
					}

					if( is_array( $value_array ) && ! empty( $value_array ) ){
						$validated_value = implode( $separator, $value_array );
					}
					
				}
				
			}
			

			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "The JSON was successfully imploded.", 'action-text_json_implode-success' );
			$return_args['data'] = $validated_value;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.