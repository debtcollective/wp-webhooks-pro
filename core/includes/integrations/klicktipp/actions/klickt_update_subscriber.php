<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_klicktipp_Actions_klickt_update_subscriber' ) ) :

	/**
	 * Load the klickt_update_subscriber action
	 *
	 * @since 5.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_klicktipp_Actions_klickt_update_subscriber {

	public function get_details(){

		$translation_ident = "action-klickt_update_subscriber-content";

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
			'subscriber_id'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'Subscriber ID', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The ID of the subscriber within KlickTipp.', $translation_ident ),
			),
			'new_email_address'		=> array(
				'label' => WPWHPRO()->helpers->translate( 'New email address', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'A new email address of the subscriber you want to update within KlickTipp.', $translation_ident ),
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
			'msg' => 'The subscriber was successfully updated.',
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Update subscriber',
			'webhook_slug' => 'klickt_update_subscriber',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>username</strong> argument. Please set it to the user name of your account to authenticate at KlickTipp.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>password</strong> argument. Please set it to the password of your account to authenticate at KlickTipp.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>subscriber_id</strong> argument. Please set it to the ID of the subscriber you want to update.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'klickt_update_subscriber', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Update subscriber', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'update a subscriber', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Update a subscriber within "KlickTipp".', $translation_ident ),
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
			$subscriber_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'subscriber_id' ) );
			$new_email_address = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'new_email_address' );
			$fields = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'fields' );
			$smsnumber = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'smsnumber' );

			if( empty( $subscriber_id ) ){
                $return_args['msg'] = WPWHPRO()->helpers->translate( "Please define the subscriber_id argument.", 'action-klickt_update_subscriber-error' );
				return $return_args;
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

				$subscriber = $connector->subscriber_update( $subscriber_id, $fields_validated, $new_email_address, $smsnumber_validated );

				if( ! empty( $subscriber ) ){
					$return_args['success'] = true;
					$return_args['msg'] = WPWHPRO()->helpers->translate( "The subscriber was successfully updated.", 'action-klickt_update_subscriber-success' );
				} else {
					$return_args['msg'] = WPWHPRO()->helpers->translate( "An error occured while updating the subscriber.", 'action-klickt_update_subscriber-success' );
					$return_args['data']['error'] = $connector->get_last_error();
				}
				
				$connector->logout();

			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "An error occured while loading the KlickTipp helper.", 'action-klickt_update_subscriber-success' );
			}
			
			return $return_args;
	
		}

	}

endif; // End if class_exists check.