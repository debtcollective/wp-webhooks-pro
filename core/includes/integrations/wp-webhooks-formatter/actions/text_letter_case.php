<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_letter_case' ) ) :

	/**
	 * Load the text_letter_case action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_letter_case {

	public function get_details(){

		$translation_ident = "action-text_letter_case-content";

			$parameter = array(
				'formatting_type' => array( 
					'type' => 'select',
					'multiple' => false,
					'choices' => array(
						'uppercase' => array( 'label' => 'Upper case' ),
						'lowercase' => array( 'label' => 'Lower case' ),
						'capitalfirst' => array( 'label' => 'First letter capital' ),
					),
					'required' => true, 
					'label' => WPWHPRO()->helpers->translate( 'Formatting type', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( 'The type of adjustment.', $translation_ident ),
				),
				'value'		=> array( 
					'required' => true, 
					'label' => WPWHPRO()->helpers->translate( 'Value', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( 'The string that will be adjusted.', $translation_ident ),
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

		$returns_code = array (
			'success' => true,
			'msg' => 'The value has been successfully adjusted.',
			'data' => 'some demo value',
		  );

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Letter case adjustments',
			'webhook_slug' => 'text_letter_case',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>formatting_type</strong> argument. Please set it to the type of adjustment you want to apply to the string.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'text_letter_case', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Text letter case adjustments', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'do adjustments to the letter case', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Adjust the letter case of a string to capitalize it, set it to lower case, or set the first character to uppercase.', $translation_ident ),
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

			$wpwhf_helpers = WPWHPRO()->integrations->get_helper( 'wp-webhooks-formatter', 'wpwhf_helpers' );
			$formatting_type = sanitize_title( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'formatting_type' ) );
			$value	= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'value' );

			if( empty( $formatting_type ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the formatting_type argument as it is required.", 'action-text_letter_case-error' );
				return $return_args;
			}

			if( ! is_string( $value ) ){
				$value = '';
			}

			switch( $formatting_type ){
				case 'uppercase':
					$value = strtoupper( $value );
					break;
				case 'lowercase':
					$value = strtolower( $value );
					break;
				case 'capitalfirst':
					$value = $wpwhf_helpers->capitalize_first_character( $value );
					break;
			}

			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "The value has been successfully adjusted.", 'action-text_letter_case-success' );
			$return_args['data'] = $value;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.