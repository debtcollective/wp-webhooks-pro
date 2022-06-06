<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_number_math_operation' ) ) :

	/**
	 * Load the number_math_operation action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_number_math_operation {

	public function get_details(){

		$translation_ident = "action-number_math_operation-content";

		$parameter = array(
			'numbers'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Numbers', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'A comma-separated string of numbers for your math operation.', $translation_ident ),
			),
			'operator' => array( 
				'type' => 'select',
				'multiple' => false,
				'choices' => array(
					'add' => array( 'label' => WPWHPRO()->helpers->translate( 'Add', $translation_ident ) ),
					'subtract' => array( 'label' => WPWHPRO()->helpers->translate( 'Subtract', $translation_ident ) ),
					'multiply' => array( 'label' => WPWHPRO()->helpers->translate( 'Multiply', $translation_ident ) ),
					'divide' => array( 'label' => WPWHPRO()->helpers->translate( 'Divide', $translation_ident ) ),
				),
				'default_value' => 'add',
				'label' => WPWHPRO()->helpers->translate( 'Operator', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The math operator you would like to use.', $translation_ident ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The math operation has been successfully executed.',
			'data' => '18',
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Number format',
			'webhook_slug' => 'number_math_operation',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>number</strong> argument. Please set it to the number you would like to format.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'number_math_operation', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Number math operation', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'perform a math operation', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Perform a math operation on various numbers.', $translation_ident ),
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

			$numbers = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'numbers' );
			$operator = sanitize_title( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'operator' ) );

			if( empty( $numbers ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the numbers argument as it is required.", 'action-number_math_operation-error' );
				return $return_args;
			}

			if( empty( $operator ) ){
				$operator = 'add';
			}

			$final_number = false;
			$validated_numbers = array();
			$numbers_array = explode( ',', $numbers );
			if( is_array( $numbers_array ) ){
				foreach( $numbers_array as $single_number ){
					$validated_numbers[] = trim( $single_number );
				}
			}

			if( ! empty( $validated_numbers ) ){
				foreach( $validated_numbers as $sn ){

					//set first number
					if( $final_number === false ){
						$final_number = $sn;
						continue; 
					}

					switch( $operator ){
						case 'add':
							$final_number += $sn;
							break;
						case 'subtract':
							$final_number -= $sn;
							break;
						case 'multiply':
							$final_number *= $sn;
							break;
						case 'divide':
							$final_number /= $sn;
							break;
					}

				}
			}
			

			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "The math operation has been successfully executed.", 'action-number_math_operation-success' );
			$return_args['data'] = $final_number;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.