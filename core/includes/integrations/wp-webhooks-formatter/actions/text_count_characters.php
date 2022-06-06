<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_count_characters' ) ) :

	/**
	 * Load the text_count_characters action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_count_characters {

	public function get_details(){

		$translation_ident = "action-text_count_characters-content";

		$parameter = array(
			'value'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Value', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The string we are going to count the characters of.', $translation_ident ),
			),
			'ignore_chars' => array( 
				'type' => 'select',
				'multiple' => false,
				'choices' => array(
					'yes' => array( 'label' => 'Yes' ),
					'no' => array( 'label' => 'No' ),
				),
				'default_value' => 'no',
				'label' => WPWHPRO()->helpers->translate( 'Ignore low priority characters', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'Set this value to yes to ignore white spaces, line breaks, and tabs.', $translation_ident ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The characters have been successfully counted.',
			'data' => 223,
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Text count characters',
			'webhook_slug' => 'text_count_characters',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>value</strong> argument. Please set it to the string you want to count the characters of.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'text_count_characters', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Text count characters', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'count the characters of a given text', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Count the total characters of a given text value.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'wp-webhooks-formatter',
			'premium'	   => false
		);


		}

		public function execute( $return_data, $response_body ){
			
			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			$value = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'value' );
			$ignore_chars = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'ignore_chars' ) === 'yes' ) ? true : false;
			
			if( ! is_string( $value ) ){
				$value = '';
			}

			if( $ignore_chars ){
				$chars_to_ignore = array(
					'\n', //linebreak
					'\r', //pilcrow
					'\t', //tab
					PHP_EOL, //eol
					' ', //space
				);

				$value = str_replace( $chars_to_ignore, '', $value );
			}

			$character_count = strlen( $value );

			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "The characters have been successfully counted.", 'action-text_count_characters-success' );
			$return_args['data'] = $character_count;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.