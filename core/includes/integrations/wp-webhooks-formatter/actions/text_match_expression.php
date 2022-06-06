<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_match_expression' ) ) :

	/**
	 * Load the text_match_expression action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_match_expression {

	public function get_details(){

		$translation_ident = "action-text_match_expression-content";

		$parameter = array(
			'value'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Value', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The string we are going to extract the numbers from.', $translation_ident ),
			),
			'expression'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Regular Expression', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The regular expression (PHP) you want to use to extract the data for.', $translation_ident ),
				'description' => WPWHPRO()->helpers->translate( 'This argument accepts regular PHP expressions. To extract an integer number, you can use !\d+!', $translation_ident ),
			),
			'return_all' => array( 
				'type' => 'select',
				'multiple' => false,
				'choices' => array(
					'yes' => array( 'label' => 'Yes' ),
					'no' => array( 'label' => 'No' ),
				),
				'default_value' => 'no',
				'label' => WPWHPRO()->helpers->translate( 'Return all matches', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'Define whether to extract only the first, or all matches.', $translation_ident ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The matches have been successfully extracted.',
			'data' => 
			array (
			  0 => '89',
			  1 => '9',
			  2 => '22',
			  3 => '56',
			  4 => '88',
			  5 => '89',
			),
		  );

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Text match expression',
			'webhook_slug' => 'text_match_expression',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>value</strong> argument. Please set it to the string you want to extract the matches from.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>expression</strong> argument. Please set it to the expression you want to use to find your matches.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'text_match_expression', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Text match expression', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'match a regular expression on a text value', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Match a regular expression to a text value.', $translation_ident ),
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
			$expression = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'expression' );
			$return_all = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'return_all' ) === 'yes' ) ? true : false;

			if( empty( $value ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the value argument as it is required.", 'action-text_match_expression-error' );
				return $return_args;
			}

			preg_match_all( $expression, $value, $matches );

			$matches_validated = array();
			if( is_array( $matches ) && isset( $matches[0] ) && is_array( $matches[0] ) ){
				if( $return_all ){
					$matches_validated = $matches[0];
				} else {
					if( isset( $matches[0][0] ) ){
						$matches_validated = $matches[0][0];
					}
				}
			}

			$return_args['success'] = true;

			if( $return_all ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The matches have been successfully extracted.", 'action-text_match_expression-success' );
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The match has been successfully extracted.", 'action-text_match_expression-success' );
			}
			
			$return_args['data'] = $matches_validated;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.