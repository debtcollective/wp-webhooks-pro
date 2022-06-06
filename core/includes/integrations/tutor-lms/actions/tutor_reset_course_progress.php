<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_tutor_lms_Actions_tutor_reset_course_progress' ) ) :

	/**
	 * Load the tutor_reset_course_progress action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_tutor_lms_Actions_tutor_reset_course_progress {

		public function get_details(){

			$translation_ident = "action-tutor_reset_course_progress-description";

			//These are the main arguments the user can use to input. You should always grab them within your action function.
			$parameter = array(
				'user'	   => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(Mixed) The user id or email of the user you want to reset the course progress for.', 'action-tutor_reset_course_progress-content' ) ),
				'course_id'	=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(Mixed) The ID of the course you want to reset for the given user.', 'action-tutor_reset_course_progress-content' ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'action-tutor_reset_course_progress-content' ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'action-tutor_reset_course_progress-content' ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further details about the sent data.', 'action-tutor_reset_course_progress-content' ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The course progress was successfully reset.',
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Course progress reset',
				'webhook_slug' => 'tutor_reset_course_progress',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'It is also required to set the argument <strong>user</strong>. Please set it to either the user id or user email.', $translation_ident ),
					WPWHPRO()->helpers->translate( 'It is also required to set the argument <strong>course_id</strong>. Please set it to the course you want to resett the progress for.', $translation_ident ),
				),
			) );

			return array(
				'action'			=> 'tutor_reset_course_progress',
				'name'			  => WPWHPRO()->helpers->translate( 'Course progress reset', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'reset a course progress', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'This webhook action allows you to reset a course progress for a user within "Tutor LMS".', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'tutor-lms',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			global $wpdb;

			$return_args = array(
				'success' => false,
				'msg' => '',
			);
			
			$user	 	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$course_id	 = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'course_id' ) );

			if( empty( $course_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the course_id argument.", 'action-tutor_reset_course_progress-failure' );
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
                $return_args['msg'] = WPWHPRO()->helpers->translate( "We could not find a user for your given user data.", 'action-tutor_reset_course_progress-error' );
				return $return_args;
            }

			tutor_utils()->delete_course_progress( $course_id, $user_id );

			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "The course progress was successfully reset.", 'action-tutor_reset_course_progress-succcess' );

			return $return_args;
	
		}

	}

endif; // End if class_exists check.