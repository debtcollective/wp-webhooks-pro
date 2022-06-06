<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_url_decode' ) ) :

	/**
	 * Load the text_url_decode action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_url_decode {

	public function get_details(){

		$translation_ident = "action-text_url_decode-content";

		$parameter = array(
			'value'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Value', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The string we are going to URL-decode.', $translation_ident ),
			),
			'convert_plus' => array( 
				'type' => 'select',
				'multiple' => false,
				'choices' => array(
					'yes' => array( 'label' => 'Yes' ),
					'no' => array( 'label' => 'No' ),
				),
				'default_value' => 'no',
				'label' => WPWHPRO()->helpers->translate( 'Convert + to space', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'By default, we keep the + character instead of turning it into a space. Set this to "yes" to change it to a space too.', $translation_ident ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The string has been successfully URL-decoded',
			'data' => 'The URL-decoded string.',
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Text URL-decode',
			'webhook_slug' => 'text_url_decode',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>value</strong> argument. Please set it to the string you want to URL-decode.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'text_url_decode', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Text URL-decode', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'URL-decode a given text', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'URL-decode a given text to make it compatible with URL query parameters.', $translation_ident ),
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
			$convert_plus = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'convert_plus' ) === 'yes' ) ? true : false;
			
			//temporarily convert pluses
			if( ! $convert_plus ){
				$value = str_replace( '+', '_____plus_____', $value );
			}

			$value = urldecode( $value );

			if( ! $convert_plus ){
				$value = str_replace( '_____plus_____', '+', $value );
			}

			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "The string has been successfully URL-decoded.", 'action-text_url_decode-success' );
			$return_args['data'] = $value;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.