<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_trim_character' ) ) :

	/**
	 * Load the text_trim_character action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_trim_character {

	public function get_details(){

		$translation_ident = "action-text_trim_character-content";

		$parameter = array(
			'value'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Value', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The string that is going to be trimmed.', $translation_ident ),
			),
			'characters'		=> array(
				'default_value' => ' ', 
				'label' => WPWHPRO()->helpers->translate( 'Characters', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'A sequence of characters that should be trimmed from the string. By default, we trim whitespaces. If you se this argument to xx, we would trim xx from the beginning and the end.', $translation_ident ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The text was successfully trimmed.',
			'data' => 'The trimmed text.',
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Text trim character',
			'webhook_slug' => 'text_trim_character',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>value</strong> argument. Please set it to the string you want to trim the characters from.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'text_trim_character', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Text trim character', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'trim one or multiple characters from a string', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Trim one or multiple characters from the beginning and the end of a string.', $translation_ident ),
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
			$characters = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'characters' );

			if( empty( $characters ) ){
				$characters = ' ';
			}

			$value = trim( $value, $characters );

			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "The text was successfully trimmed.", 'action-text_trim_character-success' );
			$return_args['data'] = $value;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.