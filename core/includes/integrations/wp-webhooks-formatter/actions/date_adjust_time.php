<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_date_adjust_time' ) ) :

	/**
	 * Load the date_adjust_time action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_date_adjust_time {

	public function get_details(){

		$translation_ident = "action-date_adjust_time-content";

		$parameter = array(
			'date'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Date', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The date you would like to add/subtract time from.', $translation_ident ),
			),
			'expression' => array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Expression', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'Set the amount of time you would like to add or substract. Some examples: +4 hours, 1 month, +1 minute, -4 months, -2 hours (You can also combine them: +20 year +2 hours)', $translation_ident ),
			),
			'to_format'		=> array(
				'label' => WPWHPRO()->helpers->translate( 'Format to change to', $translation_ident ), 
				'default_value' => 'Y-m-d H:i:s',
				'short_description' => WPWHPRO()->helpers->translate( 'The date format you would like to change the date to. By default, we set it to: Y-m-d H:i:s', $translation_ident ),
			),
			'from_format'		=> array(
				'label' => WPWHPRO()->helpers->translate( 'Current format', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'By default, we try to map the date format automatically. However, if you see the date format is wrongly interpreted, you can tell us the format here.', $translation_ident ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The date has been successfully adjusted.',
			'data' => '2022-03-10 17:16:18',
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Date add/subtract time',
			'webhook_slug' => 'date_adjust_time',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>date</strong> argument. Please set it to the date you would like to change the format for.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>expression</strong> argument. Please set it to the tie you want to adjust the date for. Some examples: +4 hours, 1 month, +1 minute, -4 months, -2 hours.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'date_adjust_time', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Date add/subtract time', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'add or subtract time for a date', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Add or subtract time from a date.', $translation_ident ),
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

			$date = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'date' );
			$expression = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'expression' );
			$to_format = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'to_format' );
			$from_format = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'from_format' );
			
			if( empty( $expression ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the expression argument as it is required.", 'action-date_adjust_time-error' );
				return $return_args;
			}
			
			if( empty( $to_format ) ){
				$to_format = 'Y-m-d H:i:s';
			}

			if( ! empty( $from_format ) ){
				$date_instance = date_create_from_format( $from_format, $date );
				$date = date_format( $date_instance, 'Y-m-d H:i:s' );
			} else {
				$date = date( 'Y-m-d H:i:s', strtotime( $date ) );
			}

			$date_validated = date( $to_format, strtotime( $date . ' ' . $expression ) );

			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "The date has been successfully adjusted.", 'action-date_adjust_time-success' );
			$return_args['data'] = $date_validated;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.