<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_klicktipp_Actions_klickt_delete_subscriber' ) ) :

	/**
	 * Load the klickt_delete_subscriber action
	 *
	 * @since 5.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_klicktipp_Actions_klickt_delete_subscriber {

	public function get_details(){

		$translation_ident = "action-klickt_delete_subscriber-content";

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
		);

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further data about the request.', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The subscriber was successfully deleted.',
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Delete subscriber',
			'webhook_slug' => 'klickt_delete_subscriber',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>username</strong> argument. Please set it to the user name of your account to authenticate at KlickTipp.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>password</strong> argument. Please set it to the password of your account to authenticate at KlickTipp.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>subscriber_id</strong> argument. Please set it to the ID of the subscriber you want to delete.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'klickt_delete_subscriber', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Delete subscriber', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'delete a subscriber', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Delete a subscriber within "KlickTipp".', $translation_ident ),
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

			if( empty( $subscriber_id ) ){
                $return_args['msg'] = WPWHPRO()->helpers->translate( "Please define the subscriber_id argument.", 'action-klickt_delete_subscriber-error' );
				return $return_args;
            }

			$klickt_helpers = WPWHPRO()->integrations->get_helper( 'klicktipp', 'klickt_helpers' );

			$connector = $klickt_helpers->get_klicktipp();

			if( ! empty( $connector ) ){

				$connector->login( $username, $password );

				$subscriber = $connector->subscriber_delete( $subscriber_id );

				if( ! empty( $subscriber ) ){
					$return_args['success'] = true;
					$return_args['msg'] = WPWHPRO()->helpers->translate( "The subscriber was successfully deleted.", 'action-klickt_delete_subscriber-success' );
				} else {
					$return_args['msg'] = WPWHPRO()->helpers->translate( "An error occured while deleting the subscriber.", 'action-klickt_delete_subscriber-success' );
					$return_args['data']['error'] = $connector->get_last_error();
				}
				
				$connector->logout();

			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "An error occured while loading the KlickTipp helper.", 'action-klickt_delete_subscriber-success' );
			}
			
			return $return_args;
	
		}

	}

endif; // End if class_exists check.