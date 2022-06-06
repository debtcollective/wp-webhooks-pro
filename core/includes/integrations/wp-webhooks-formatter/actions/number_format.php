<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_number_format' ) ) :

	/**
	 * Load the number_format action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_number_format {

	public function get_details(){

		$translation_ident = "action-number_format-content";

		$parameter = array(
			'number'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Number', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The number you want to format. Accepts float and integer.', $translation_ident ),
			),
			'decimals'		=> array(
				'default_value' => 2,
				'label' => WPWHPRO()->helpers->translate( 'Decimals', $translation_ident ),
				'short_description' => WPWHPRO()->helpers->translate( 'The number of decimals you want to have available. Default is 2.', $translation_ident ),
			),
			'decimal_separator' => array(
				'default_value' => '.',
				'label' => WPWHPRO()->helpers->translate( 'Decimal separator', $translation_ident ),
				'short_description' => WPWHPRO()->helpers->translate( 'The type of separator used for decimals. Default: .', $translation_ident ),
			),
			'thousands_separator' => array(
				'default_value' => ',',
				'label' => WPWHPRO()->helpers->translate( 'Thousands separator', $translation_ident ),
				'short_description' => WPWHPRO()->helpers->translate( 'The type of separator used for thousands. Default: ,', $translation_ident ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The number has been succefully formatted.',
			'data' => '75,238.95',
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Number format',
			'webhook_slug' => 'number_format',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>number</strong> argument. Please set it to the number you would like to format.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'number_format', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Number format', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'format a number', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Format a number to a specific format.', $translation_ident ),
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

			$number = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'number' );
			$decimals = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'decimals' );
			$decimal_separator = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'decimal_separator' );
			$thousands_separator = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'thousands_separator' );
			
			if( empty( $number ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the number argument as it is required.", 'action-number_format-error' );
				return $return_args;
			}

			if( empty( $decimals ) ){
				$decimals = 2;
			}
			
			if( empty( $decimal_separator ) ){
				$decimal_separator = '.';
			}
			
			if( empty( $thousands_separator ) ){
				$thousands_separator = ',';
			}

			$validated_number = number_format( $number, $decimals, $decimal_separator, $thousands_separator );

			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "The number has been succefully formatted.", 'action-number_format-success' );
			$return_args['data'] = $validated_number;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.