<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_extract_email' ) ) :

	/**
	 * Load the text_extract_email action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_extract_email {

	public function get_details(){

		$translation_ident = "action-text_extract_email-content";

			$parameter = array(
				'value'		=> array( 
					'required' => true, 
					'label' => WPWHPRO()->helpers->translate( 'Value', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( 'The string we are going to extract the emails from.', $translation_ident ),
				),
				'return_all' => array( 
					'type' => 'select',
					'multiple' => false,
					'choices' => array(
						'yes' => array( 'label' => 'Yes' ),
						'no' => array( 'label' => 'No' ),
					),
					'default_value' => 'no',
					'label' => WPWHPRO()->helpers->translate( 'Return all emails', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( 'Define whether to extract only the first, or all email addresses.', $translation_ident ),
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

		$returns_code = array (
			'success' => true,
			'msg' => 'The emails have been successfully extracted.',
			'data' => 
			array (
			  0 => 'demoemail@test.test',
			  1 => 'jondoe@demo.test',
			),
		  );

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Text extract email',
			'webhook_slug' => 'text_extract_email',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>value</strong> argument. Please set it to the string you want to extract the emails from.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'text_extract_email', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Text extract email', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'extract one or multiple emails from text', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Extract one or multiple emails from a text value.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'wp-webhooks-formatter',
			'premium'	   => true
		);


		}

		public function execute( $return_data, $response_body ){

			$email_regex = '/([_A-Za-z0-9-]+(\\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\\.[A-Za-z0-9-]+)*(\\.[A-Za-z]{2,}))/i';
			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			$value = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'value' );
			$return_all = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'return_all' ) === 'yes' ) ? true : false;

			if( empty( $value ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the value argument as it is required.", 'action-text_extract_email-error' );
				return $return_args;
			}

			preg_match_all( $email_regex, $value, $matches );

			$emails = array();
			if( is_array( $matches ) && isset( $matches[0] ) && is_array( $matches[0] ) ){
				if( $return_all ){
					$emails = $matches[0];
				} else {
					if( isset( $matches[0][0] ) ){
						$emails = $matches[0][0];
					}
				}
			}

			$return_args['success'] = true;

			if( $return_all ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The emails have been successfully extracted.", 'action-text_extract_email-success' );
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The email has been successfully extracted.", 'action-text_extract_email-success' );
			}
			
			$return_args['data'] = $emails;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.