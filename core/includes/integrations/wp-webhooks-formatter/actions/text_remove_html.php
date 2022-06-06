<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_remove_html' ) ) :

	/**
	 * Load the text_remove_html action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_remove_html {

	public function get_details(){

		$translation_ident = "action-text_remove_html-content";

		$parameter = array(
			'value'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Value', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The string we are going to remove the HTML from.', $translation_ident ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The HTML has been successfully removed.',
			'data' => 'The cleared HTML text string.',
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Text remove HTML',
			'webhook_slug' => 'text_remove_html',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>value</strong> argument. Please set it to the string you want to remove the HTML from.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'text_remove_html', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Text remove HTML', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'remove HTML from a given text', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Remove HTML from a given text value.', $translation_ident ),
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
			$value = wp_kses( $value, array() );

			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "The HTML has been successfully removed.", 'action-text_remove_html-success' );
			$return_args['data'] = $value;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.