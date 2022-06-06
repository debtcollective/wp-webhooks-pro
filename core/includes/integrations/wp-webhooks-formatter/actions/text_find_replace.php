<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_find_replace' ) ) :

	/**
	 * Load the text_find_replace action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_find_replace {

	public function get_details(){

		$translation_ident = "action-text_find_replace-content";

		$parameter = array(
			'value'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Value', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The string we are going to remove the HTML from.', $translation_ident ),
			),
			'find'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Find', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The text we are going to look for within the value argument.', $translation_ident ),
			),
			'replace'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Replace', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The text we are using to replace the value of the find argument.', $translation_ident ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The value has been successfully validated.',
			'data' => 'The string with the replaced data.',
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Text find and replace',
			'webhook_slug' => 'text_find_replace',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>value</strong> argument. Please set it to the string you want to replace the text in.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>find</strong> argument. Please set it to the string you want to find within the value argument.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>replace</strong> argument. Please set it to the string you want to replace the text with.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'text_find_replace', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Text find and replace', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'find and replace text within a text value', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Find and replace text wihtin a given text value.', $translation_ident ),
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
			$find = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'find' );
			$replace = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'replace' );

			$value = str_replace( $find, $replace, $value );

			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "The value has been successfully validated.", 'action-text_find_replace-success' );
			$return_args['data'] = $value;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.