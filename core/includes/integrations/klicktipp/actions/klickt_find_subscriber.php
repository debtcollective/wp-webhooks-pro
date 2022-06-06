<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_klicktipp_Actions_klickt_find_subscriber' ) ) :

	/**
	 * Load the klickt_find_subscriber action
	 *
	 * @since 5.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_klicktipp_Actions_klickt_find_subscriber {

	public function get_details(){

		$translation_ident = "action-klickt_find_subscriber-content";

		$parameter = array(
			'username'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Username', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The username of your account to authenticate to KlickTipp.', $translation_ident ),
			),
			'password'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Password', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The password of your account to authenticate to KlickTipp.', $translation_ident ),
			),
			'subscriber'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Subscriber', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The email or the id of the subscriber you want to find within KlickTipp.', $translation_ident ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further data about the request.', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The subscriber was successfully returned.',
			'data' => 
			array (
			  'subscriber' => 
			  array (
				'id' => '101880000',
				'listid' => '0',
				'optin' => '20.05.2022 14:25:27',
				'optin_ip' => '0.0.0.0',
				'email' => 'jon@doe.test',
				'status' => 'Subscribed',
				'bounce' => 'Not Bounced',
				'date' => '20.05.2022 14:25:27',
				'ip' => '0.0.0.0',
				'unsubscription' => '',
				'unsubscription_ip' => '0.0.0.0',
				'referrer' => '',
				'sms_phone' => NULL,
				'sms_status' => NULL,
				'sms_bounce' => NULL,
				'sms_date' => '',
				'sms_unsubscription' => '',
				'sms_referrer' => NULL,
				'fieldFirstName' => '',
				'fieldLastName' => '',
				'fieldCompanyName' => '',
				'fieldStreet1' => '',
				'fieldStreet2' => '',
				'fieldCity' => '',
				'fieldState' => '',
				'fieldZip' => '',
				'fieldCountry' => '',
				'fieldPrivatePhone' => '',
				'fieldMobilePhone' => '',
				'fieldPhone' => '',
				'fieldFax' => '',
				'fieldWebsite' => '',
				'fieldBirthday' => '',
				'fieldLeadValue' => '',
				'tags' => 
				array (
				  0 => '8713617',
				),
				'manual_tags' => 
				array (
				  8713617 => '1653049872',
				),
			  ),
			),
		  );

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Find subscriber',
			'webhook_slug' => 'klickt_find_subscriber',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>username</strong> argument. Please set it to the user name of your account to authenticate at KlickTipp.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>password</strong> argument. Please set it to the password of your account to authenticate at KlickTipp.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>subscriber</strong> argument. Please set it to the subscriber id or email of the subscriber you try to find.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'klickt_find_subscriber', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Find subscriber', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'find a subscriber', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Find a subscriber within "KlickTipp".', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'klicktipp',
			'premium'	   	=> true
		);


		}

		public function execute( $return_data, $response_body ){
			
			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			$username = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'username' );
			$password = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'password' );
			$subscriber = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'subscriber' );

			if( empty( $subscriber ) ){
                $return_args['msg'] = WPWHPRO()->helpers->translate( "Please define a valid email address within the user argument.", 'action-klickt_find_subscriber-error' );
				return $return_args;
            }

			if( ! is_numeric( $subscriber ) && ! is_email( $subscriber ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please define either a valid email or subscriber id within the user argument.", 'action-klickt_find_subscriber-error' );
				return $return_args;
			}

			$klickt_helpers = WPWHPRO()->integrations->get_helper( 'klicktipp', 'klickt_helpers' );

			$connector = $klickt_helpers->get_klicktipp();

			if( ! empty( $connector ) ){

				$connector->login( $username, $password );

				if( is_numeric( $subscriber ) ){
					$subscriber_id = intval( $subscriber );
				} else {
					$subscriber_id = $connector->subscriber_search( $subscriber );
				}

				if( ! empty( $subscriber_id ) ){
					$subscriber = $connector->subscriber_get($subscriber_id);

					if( ! empty( $subscriber ) ){
						$return_args['success'] = true;
						$return_args['msg'] = WPWHPRO()->helpers->translate( "The subscriber was successfully returned.", 'action-klickt_find_subscriber-success' );
						$return_args['data']['subscriber'] = $subscriber;
					} else {
						$return_args['msg'] = WPWHPRO()->helpers->translate( "We could not fetch the subscriber for the found ID.", 'action-klickt_find_subscriber-success' );
						$return_args['data']['error'] = $connector->get_last_error();
					}

				} else {
					$return_args['msg'] = WPWHPRO()->helpers->translate( "We could not find a subscriber for your given data.", 'action-klickt_find_subscriber-success' );
					$return_args['data']['error'] = $connector->get_last_error();
				}
				
				$connector->logout();

			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "An error occured while loading the helper.", 'action-klickt_find_subscriber-success' );
			}
			
			return $return_args;
	
		}

	}

endif; // End if class_exists check.