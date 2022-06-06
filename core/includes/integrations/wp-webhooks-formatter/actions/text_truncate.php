<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_truncate' ) ) :

	/**
	 * Load the text_truncate action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_truncate {

	public function get_details(){

		$translation_ident = "action-text_truncate-content";

		$parameter = array(
			'value'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Value', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The string we are going to remove the HTML from.', $translation_ident ),
			),
			'length'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Length', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The max length of the string allowed. You can also use negative numbers to truncate the string from the end.', $translation_ident ),
			),
			'offset'		=> array(
				'label' => WPWHPRO()->helpers->translate( 'Offset', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The offset of the string. You can also use negative numbers to truncate the string from the end.', $translation_ident ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The string has been successfully truncated.',
			'data' => 'The truncated str',
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Text truncate',
			'webhook_slug' => 'text_truncate',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>value</strong> argument. Please set it to the string you want to truncate.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>length</strong> argument. Please set it to the length of the string that should be returned.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'text_truncate', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Text truncate', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'truncate a given text', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Truncate a given text to a specific length only.', $translation_ident ),
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
			$offset = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'offset' );
			$length = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'length' );

			if( empty( $length ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the length argument as it is required.", 'action-text_extract_number-error' );
				return $return_args;
			}

			if( empty( $offset ) ){
				$offset = 0;
			}

			$value = substr( $value, $offset, $length );

			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "The string has been successfully truncated.", 'action-text_truncate-success' );
			$return_args['data'] = $value;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.