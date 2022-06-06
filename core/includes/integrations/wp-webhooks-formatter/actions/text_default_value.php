<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_default_value' ) ) :

	/**
	 * Load the text_default_value action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_default_value {

	public function get_details(){

		$translation_ident = "action-text_default_value-content";

		$parameter = array(
			'value'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Current value', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The value to check against if it is empty.', $translation_ident ),
			),
			'default_value'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Default value', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The default value in case the Current value was empty. A value is considered empy if nothing was sent through, or one of the following values was given: false, 0, no.', $translation_ident ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The default value has been applied successfully.',
			'data' => 'Some default value',
		  );

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Text default value',
			'webhook_slug' => 'text_default_value',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>value</strong> argument. Please set it to the value of the string we should check against if it is empty.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>default_value</strong> argument. Please set it to the value that should be set in case the given value was empty.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'text_default_value', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Text default value', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'set a default value if value is empty', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Set a default value in case the given value is empty or has one of the following data: false, no, 0', $translation_ident ),
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

			$value	= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'value' );
			$default_value	= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'default_value' );

			if( 
				empty( $value ) 
				|| $value === 'false'
				|| $value === 'no'
				|| $value === '0'
			){
				$value = $default_value;
			}

			$return_args['success'] = true;

			if( $default_value === $value ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The default value has been applied successfully.", 'action-text_default_value-success' );
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The value field was set correctly. No default value has been applied.", 'action-text_default_value-success' );
			}
			
			$return_args['data'] = $value;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.