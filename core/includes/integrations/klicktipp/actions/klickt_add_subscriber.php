<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_klicktipp_Actions_klickt_add_subscriber' ) ) :

	/**
	 * Load the klickt_add_subscriber action
	 *
	 * @since 5.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_klicktipp_Actions_klickt_add_subscriber {

	public function get_details(){

		$translation_ident = "action-klickt_add_subscriber-content";

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
			'email_address'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Email Address', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The email address of the subscriber you want to add within KlickTipp.', $translation_ident ),
			),
			'double_optin_process_id' => array(
				'label' => WPWHPRO()->helpers->translate( 'Double Opt-In Process ID', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'In the Automation menu, select the Double-Opt-in Processes option. You can find the ID at the beginning of the table.', $translation_ident ),
			),
			'tag_id' => array( 
				'label' => WPWHPRO()->helpers->translate( 'Tag ID', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'In the "Automation" menu, select the "Tags" option. You can find the ID at the beginning of the table.', $translation_ident ),
			),
			'fields' => array(
				'type' => 'repeater',
				'label' => WPWHPRO()->helpers->translate( 'Additional fields', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'Additional data of the recipient, e.g., affiliate ID, address, or customer number.', $translation_ident ),
			),
			'smsnumber' => array(
				'label' => WPWHPRO()->helpers->translate( 'SMS Number', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The mobile phone number of the subscriber.', $translation_ident ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further data about the request.', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The subscriber was successfully added.',
			'data' => 
			array (
			  'subscriber' => 
			  array (
				'id' => '101880000',
				'listid' => '259000',
				'optin' => '20.05.2022 15:01:32',
				'optin_ip' => '0.0.0.0 - By API Request',
				'email' => 'jon@doe.test',
				'status' => 'Opt-In Pending',
				'bounce' => 'Not Bounced',
				'date' => '',
				'ip' => '0.0.0.0 - By API Request',
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
				  8713617 => '1653051692',
				),
			  ),
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Add subscriber',
			'webhook_slug' => 'klickt_add_subscriber',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>username</strong> argument. Please set it to the user name of your account to authenticate at KlickTipp.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>password</strong> argument. Please set it to the password of your account to authenticate at KlickTipp.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>email_address</strong> argument. Please set it to the email address of the subscriber you want to create.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'klickt_add_subscriber', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Add subscriber', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'add a subscriber', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Add a subscriber within "KlickTipp".', $translation_ident ),
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
			$email_address = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'email_address' );
			$double_optin_process_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'double_optin_process_id' ) );
			$tag_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tag_id' ) );
			$fields = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'fields' );
			$smsnumber = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'smsnumber' );

			if( empty( $email_address ) || ! is_email( $email_address ) ){
                $return_args['msg'] = WPWHPRO()->helpers->translate( "Please define a valid email address within the email_address argument.", 'action-klickt_add_subscriber-error' );
				return $return_args;
            }

			$double_optin_process_id_validated = '';
			if( ! empty( $double_optin_process_id ) ){
				$double_optin_process_id_validated = $double_optin_process_id;
			}

			$tag_id_validated = '';
			if( ! empty( $tag_id ) ){
				$tag_id_validated = $tag_id;
			}

			$fields_validated = '';
			if( ! empty( $fields ) && WPWHPRO()->helpers->is_json( $fields ) ){
				$fields_validated = json_decode( $fields, true );
			}

			$smsnumber_validated = '';
			if( ! empty( $smsnumber ) ){
				$smsnumber_validated = $smsnumber;
			}

			$klickt_helpers = WPWHPRO()->integrations->get_helper( 'klicktipp', 'klickt_helpers' );

			$connector = $klickt_helpers->get_klicktipp();

			if( ! empty( $connector ) ){

				$connector->login( $username, $password );

				$subscriber = $connector->subscribe( $email_address, $double_optin_process_id_validated, $tag_id_validated, $fields_validated, $smsnumber_validated );

				if( ! empty( $subscriber ) ){
					$return_args['success'] = true;
					$return_args['msg'] = WPWHPRO()->helpers->translate( "The subscriber was successfully added.", 'action-klickt_add_subscriber-success' );
					$return_args['data']['subscriber'] = $subscriber;
				} else {
					$return_args['msg'] = WPWHPRO()->helpers->translate( "An error occured while adding the subscriber.", 'action-klickt_add_subscriber-success' );
					$return_args['data']['error'] = $connector->get_last_error();
				}
				
				$connector->logout();

			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "An error occured while loading the KlickTipp helper.", 'action-klickt_add_subscriber-success' );
			}
			
			return $return_args;
	
		}

	}

endif; // End if class_exists check.