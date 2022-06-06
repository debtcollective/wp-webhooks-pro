<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_learndash_Triggers_ld_assignment_uploaded' ) ) :

 /**
  * Load the ld_assignment_uploaded trigger
  *
  * @since 4.3.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_learndash_Triggers_ld_assignment_uploaded {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'learndash_assignment_uploaded',
				'callback' => array( $this, 'learndash_assignment_uploaded_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-ld_assignment_uploaded-description";

		$parameter = array(
			'assignment_post_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the uploaded assignment.', $translation_ident ) ),
			'file_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The name of the uploaded file including the file extension.', $translation_ident ) ),
			'file_link' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The URL of the uploaded assignment file.', $translation_ident ) ),
			'user_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The name of the user that uploaded the assignment.', $translation_ident ) ),
			'disp_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The display name of the user that uploaded the assignment.', $translation_ident ) ),
			'file_path' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The full file path of the uploaded assignment.', $translation_ident ) ),
			'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the user that uploaded the assignment.', $translation_ident ) ),
			'course_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the course the assignment was uploaded to.', $translation_ident ) ),
			'lesson_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the lesson the assignment was uploaded to.', $translation_ident ) ),
			'lesson_title' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The title of the lesson the assignment was uploaded to.', $translation_ident ) ),
			'lesson_type' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The post type of the lesson the assignment was uploaded to.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Assignment uploaded',
			'webhook_slug' => 'ld_assignment_uploaded',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'learndash_assignment_uploaded',
					'url' => 'https://developers.learndash.com/hook/learndash_assignment_uploaded/',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'You can fire this trigger as well on specific courses only. To do that, select one or multiple courses within the webhook URL settings.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'It is also possible to fire this trigger only on specific lessons. To do that, select one or multiple lessons within the webhook URL settings.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_learndash_trigger_on_courses' => array(
					'id'		  => 'wpwhpro_learndash_trigger_on_courses',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'posts',
						'args'		=> array(
							'post_type' => 'sfwd-courses',
							'post_status' => 'publish',
						)
					),
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected courses', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the courses you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
				'wpwhpro_learndash_trigger_on_lessons' => array(
					'id'		  => 'wpwhpro_learndash_trigger_on_lessons',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'posts',
						'args'		=> array(
							'post_type' => 'sfwd-lessons',
							'post_status' => 'publish',
						)
					),
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected lessons', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the lessons you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'ld_assignment_uploaded',
			'name'			  => WPWHPRO()->helpers->translate( 'Assignment uploaded', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'an assignment was uploaded', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as an assignment was uploaded within LearnDash.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'learndash',
			'premium'		   => true,
		);

	}

	/**
	 * Triggers once an assigment was uploaded within LearnDash
	 *
	 * @param array $assignment_post_id - The ID of the uploaded assignment
	 * @param array $assignment_meta - Further data about the course, lesson, etc.
	 */
	public function learndash_assignment_uploaded_callback( $assignment_post_id, $assignment_meta ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'ld_assignment_uploaded' );

		$payload = array(
			'assignment_post_id' => $assignment_post_id,
			'course_id' => 0,
			'lesson_id' => 0,
		);
		$payload = array_merge( $payload, $assignment_meta );

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){

				if( $is_valid && isset( $webhook['settings']['wpwhpro_learndash_trigger_on_courses'] ) && ! empty( $webhook['settings']['wpwhpro_learndash_trigger_on_courses'] ) ){
					if( ! in_array( $payload['course_id'], $webhook['settings']['wpwhpro_learndash_trigger_on_courses'] ) ){
						$is_valid = false;
					}
				}

				if( $is_valid && isset( $webhook['settings']['wpwhpro_learndash_trigger_on_lessons'] ) && ! empty( $webhook['settings']['wpwhpro_learndash_trigger_on_lessons'] ) ){
					if( ! in_array( $payload['lesson_id'], $webhook['settings']['wpwhpro_learndash_trigger_on_lessons'] ) ){
						$is_valid = false;
					}
				}

			}

			if( $is_valid ){
				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				} else {
					$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				}
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_ld_assignment_uploaded', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'assignment_post_id' => 8077,
			'file_name' => 'assignment_8055_163986028318_demo_pdf_file.pdf',
			'file_link' => 'https://doe.test/wp-content/uploads/assignments/assignment_8055_163986028318_demo_pdf_file.pdf',
			'user_name' => 'admin',
			'disp_name' => 'admin',
			'file_path' => '%2Fthe%2Ftull%2Ffile%2Fpath%2Fwp-content%2Fuploads%2Fassignments%assignment_8055_163986028318_demo_pdf_file.pdf',
			'user_id' => 1,
			'lesson_id' => 8055,
			'course_id' => 8053,
			'lesson_title' => 'Demo Lesson 1',
			'lesson_type' => 'sfwd-lessons',
		  );

		return $data;
	}

  }

endif; // End if class_exists check.