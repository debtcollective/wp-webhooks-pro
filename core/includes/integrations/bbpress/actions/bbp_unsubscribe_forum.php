<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_bbpress_Actions_bbp_unsubscribe_forum' ) ) :

	/**
	 * Load the bbp_unsubscribe_forum action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_bbpress_Actions_bbp_unsubscribe_forum {

	public function get_details(){

		$translation_ident = "action-bbp_unsubscribe_forum-content";

		$parameter = array(
			'user'		=> array( 
				'required' => true, 
				'label' => WPWHPRO()->helpers->translate( 'User', $translation_ident ), 
				'short_description' => WPWHPRO()->helpers->translate( 'The user ID or email of the user you want to unsubscribe from the forums.', $translation_ident ),
			),
			'forum_ids'		=> array(
				'label' => WPWHPRO()->helpers->translate( 'Forum ids', $translation_ident ), 
				'default_value' => 'Y-m-d H:i:s',
				'short_description' => WPWHPRO()->helpers->translate( 'A comma-separated list of forum IDs you want to unsubscribe the user from.', $translation_ident ),
			),
		);

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The user has been unsubscribed successfully from the forums.',
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Subscribe user to forum',
			'webhook_slug' => 'bbp_unsubscribe_forum',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>user</strong> argument. Please set it to either the user ID or the user email.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>forum_ids</strong> argument. Set this to your forum id you want to unsubscribe the user from. Comma-separate the forum IDs if you want to remove the user from multiple ones.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'bbp_unsubscribe_forum', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Unsubscribe user from forum', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'unsubscribe a user from a forum', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Unsubscribe a user from one or multiple forums within "bbPress".', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'bbpress',
			'premium'	   => true
		);


		}

		public function execute( $return_data, $response_body ){
			
			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			$user = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$forum_ids = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'forum_ids' );

			if( empty( $forum_ids ) ){
                $return_args['msg'] = WPWHPRO()->helpers->translate( "Please define the forum_ids argument as it is required.", 'action-bbp_unsubscribe_forum-error' );
				return $return_args;
            }

			$validated_forum_ids = array();
			$forum_ids_array = explode( ',', $forum_ids );
			if( is_array( $forum_ids_array ) ){
				foreach( $forum_ids_array as $single_forum_id ){
					$validated_forum_ids[] = intval( trim( $single_forum_id ) );
				}
			}

			if( empty( $validated_forum_ids ) ){
                $return_args['msg'] = WPWHPRO()->helpers->translate( "We could not validate the given forum IDs.", 'action-bbp_unsubscribe_forum-error' );
				return $return_args;
            }
			
			$user_id = 0;

            if( ! empty( $user ) && is_numeric( $user ) ){
                $user_id = intval( $user );
            } elseif( ! empty( $user ) && is_email( $user ) ) {
                $user_data = get_user_by( 'email', $user );
                if( ! empty( $user_data ) && isset( $user_data->ID ) && ! empty( $user_data->ID ) ){
                    $user_id = $user_data->ID;
                }
            }

            if( empty( $user_id ) ){
                $return_args['msg'] = WPWHPRO()->helpers->translate( "We could not find a user for your given user id.", 'action-bbp_unsubscribe_forum-error' );
				return $return_args;
            }

			$errors = array();

			foreach( $validated_forum_ids as $forum_id ){

				//skip if the user is already unsubscribed
				if ( ! bbp_is_user_subscribed( $user_id, $forum_id, 'post' ) ) {
					continue;
				}

				$check = bbp_remove_user_subscription( $user_id, $forum_id );
				if( empty( $check ) ){
					$errors[] = sprintf( WPWHPRO()->helpers->translate( "Unsubscribing the user from the blog %d failed.", 'action-bbp_unsubscribe_forum-error' ), $forum_id );
				}
			}

			if( empty( $errors ) ){
				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The user has been unsubscribed successfully from the forums.", 'action-bbp_unsubscribe_forum-success' );
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "One or more issues occured while unsubscribing the user from the forums.", 'action-bbp_unsubscribe_forum-success' );
				$return_args['errors'] = $errors;
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.