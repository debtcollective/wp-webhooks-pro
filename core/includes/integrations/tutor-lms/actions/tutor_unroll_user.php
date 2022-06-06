<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_tutor_lms_Actions_tutor_unroll_user' ) ) :

	/**
	 * Load the tutor_unroll_user action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_tutor_lms_Actions_tutor_unroll_user {

		public function get_details(){

			$translation_ident = "action-tutor_unroll_user-description";

			$parameter = array(
				'user'	   => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(Mixed) The user id or email of the user you want to unroll.', $translation_ident ) ),
				'course_id'	=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(Mixed) The course if of the user you want to unroll the user to. You can also use "any" to unroll the user to all courses.', $translation_ident ) ),
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further details about the sent data.', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The user has been successfully unrolled.',
				'data' => 
				array (
				  'courses' => 
				  array (
					0 => 9213,
				  ),
				),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Unroll user',
				'webhook_slug' => 'tutor_unroll_user',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'It is also required to set the argument <strong>user</strong>. Please set it to either the user id or user email.', $translation_ident ),
					WPWHPRO()->helpers->translate( 'It is also required to set the argument <strong>course_id</strong>. Please set it to the course id you want to unroll.', $translation_ident ),
				),
			) );

			return array(
				'action'			=> 'tutor_unroll_user',
				'name'			  => WPWHPRO()->helpers->translate( 'Unroll user', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'unroll a user to a course', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'This webhook action allows you to unroll a user to a course within "Tutor LMS".', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'tutor-lms',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(),
			);

			$user	 	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$course_id	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'course_id' );

			if( empty( $course_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the course_id argument.", 'action-tutor_unroll_user-failure' );
				return $return_args;
			}

			$course_id = ( $course_id === 'all' ) ? 'all' : intval( $course_id );
			$user_id = 0;
			$courses = array();

            if( ! empty( $user ) && is_numeric( $user ) ){
                $user_id = intval( $user );
            } elseif( ! empty( $user ) && is_email( $user ) ) {
                $user_data = get_user_by( 'email', $user );
                if( ! empty( $user_data ) && isset( $user_data->ID ) && ! empty( $user_data->ID ) ){
                    $user_id = $user_data->ID;
                }
            }

            if( empty( $user_id ) ){
                $return_args['msg'] = WPWHPRO()->helpers->translate( "We could not find a user for your given user data.", 'action-tutor_unroll_user-error' );
				return $return_args;
            }

			if( $course_id === 'any' ) {
				$courses = tutor_utils()->get_enrolled_courses_ids_by_user( $user_id );
			} else {
				$courses[] = $course_id;
			}

			foreach( $courses as $course ){
				//no return given so we have to consider it cancelled
				tutor_utils()->cancel_course_enrol( $course, $user_id );
			}

			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "The user has been successfully unrolled.", 'action-tutor_unroll_user-succcess' );
			$return_args['data']['courses'] = $courses;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.