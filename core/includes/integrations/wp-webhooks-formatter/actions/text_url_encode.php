<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_url_encode' ) ) :

	/**
	 * Load the text_url_encode action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_url_encode {

	public function get_details(){

		$translation_ident = "action-text_url_encode-content";

		$parameter = array(
			'value'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Value', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The string we are going to URL-encode.', $translation_ident ),
			),
			'convert_spaces' => array( 
				'type' => 'select',
				'multiple' => false,
				'choices' => array(
					'yes' => array( 'label' => 'Yes' ),
					'no' => array( 'label' => 'No' ),
				),
				'default_value' => 'no',
				'label' => WPWHPRO()->helpers->translate( 'Convert space to +', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'By default, we convert spaces to %20 instead of +. Set this to "yes" to change that.', $translation_ident ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The string has been successfully URL-encoded',
			'data' => 'The%20URL-encoded%20string.',
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Text URL-encode',
			'webhook_slug' => 'text_url_encode',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>value</strong> argument. Please set it to the string you want to URL-encode.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'text_url_encode', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Text URL-encode', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'URL-encode a given text', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'URL-encode a given text to make it compatible with URL query parameters.', $translation_ident ),
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
			$convert_spaces = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'convert_spaces' ) === 'yes' ) ? true : false;
			
			$value = urlencode( $value );

			if( ! $convert_spaces ){
				$value = str_replace( '+', '%20', $value );
			}

			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "The string has been successfully URL-encoded.", 'action-text_url_encode-success' );
			$return_args['data'] = $value;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.