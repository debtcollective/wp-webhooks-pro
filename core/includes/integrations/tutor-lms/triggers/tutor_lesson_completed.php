<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_tutor_lms_Triggers_tutor_lesson_completed' ) ) :

 /**
  * Load the tutor_lesson_completed trigger
  *
  * @since 5.1
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_tutor_lms_Triggers_tutor_lesson_completed {

  public function get_callbacks(){

   return array(
	array(
		'type' => 'action',
		'hook' => 'tutor_lesson_completed_after',
		'callback' => array( $this, 'ironikus_trigger_tutor_lesson_completed' ),
		'priority' => 20,
		'arguments' => 2,
		'delayed' => true,
	  ),
	);

  }

	public function get_details(){

		$translation_ident = "trigger-tutor_lesson_completed-description";

		$parameter = array(
			'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the user that has completed the lesson.', $translation_ident ) ),
			'lesson_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the lesson that has been completed.', $translation_ident ) ),
			'lesson' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further details about the lesson.', $translation_ident ) ),
			'lesson_meta' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The lesson meta data.', $translation_ident ) ),
			'lesson_user_reading' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The full lesson reading info.', $translation_ident ) ),
		);

	  	$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Lesson completed',
			'webhook_slug' => 'tutor_lesson_completed',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'tutor_lesson_completed_after',
				),
			)
		) );

	  	$settings = array(
			'load_default_settings' => true,
			'data' => array(
			'wpwhpro_tutor_lesson_completed_trigger_on_lessons' => array(
				'id'	 => 'wpwhpro_tutor_lesson_completed_trigger_on_lessons',
				'type'	=> 'select',
				'multiple'  => true,
				'choices'   => array(),
				'query'			=> array(
					'filter'	=> 'posts',
					'args'		=> array(
						'post_type' => apply_filters( 'tutor_lesson_post_type', 'lesson' ),
					)
				),
				'label'	=> WPWHPRO()->helpers->translate( 'Trigger on selected lessons', $translation_ident ),
				'placeholder' => '',
				'required'  => false,
				'description' => WPWHPRO()->helpers->translate( 'Select only the lessons you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
			),
			)
		);

		return array(
			'trigger'	  => 'tutor_lesson_completed',
			'name'	   => WPWHPRO()->helpers->translate( 'Lesson completed', $translation_ident ),
			'sentence'	   => WPWHPRO()->helpers->translate( 'a lesson was completed', $translation_ident ),
			'parameter'	 => $parameter,
			'settings'	 => $settings,
			'returns_code'   => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after a Tutor LMS lesson was completed.', $translation_ident ),
			'description'	=> $description,
			'callback'	 => 'test_tutor_lesson_completed',
			'integration'	=> 'tutor-lms',
			'premium'	=> true,
		);

	}

	public function ironikus_trigger_tutor_lesson_completed( $lesson_id, $user_id ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'tutor_lesson_completed' );
		$lesson = get_post( $lesson_id );
		$lesson_meta = get_post_meta( $lesson_id );
		$data_array = array(
			'user_id' => $user_id,
			'lesson_id' => $lesson_id,
			'lesson' => $lesson,
			'lesson_meta' => $lesson_meta,
			'lesson_user_reading' => tutor_utils()->get_lesson_reading_info_full( $lesson_id, $user_id ),
		);
		$response_data = array();

		foreach( $webhooks as $webhook ){

			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){

					if( $settings_name === 'wpwhpro_tutor_lesson_completed_trigger_on_lessons' && ! empty( $settings_data ) ){
					if( ! in_array( $lesson_id, $settings_data ) ){
						$is_valid = false;
					}
					}

				}
			}

			if( $is_valid ) {
			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

			if( $webhook_url_name !== null ){
				$response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
			} else {
				$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
			}
			}
		}

		do_action( 'wpwhpro/webhooks/trigger_tutor_lesson_completed', $data_array, $response_data );
	}

	/*
	* Register the demo post delete trigger callback
	*
	* @since 1.2
	*/
	public function get_demo( $options = array() ) {

		$data = array (
			'user_id' => 123,
			'lesson_id' => 9219,
			'lesson' => 
			array (
			'ID' => 9219,
			'post_author' => '1',
			'post_date' => '2022-04-11 15:55:42',
			'post_date_gmt' => '2022-04-11 15:55:42',
			'post_content' => '<p>This is a demo lesson</p>',
			'post_title' => 'Demo Lesson',
			'post_excerpt' => '',
			'post_status' => 'publish',
			'comment_status' => 'open',
			'ping_status' => 'closed',
			'post_password' => '',
			'post_name' => 'demo-lesson-1',
			'to_ping' => '',
			'pinged' => '',
			'post_modified' => '2022-04-11 15:55:42',
			'post_modified_gmt' => '2022-04-11 15:55:42',
			'post_content_filtered' => '',
			'post_parent' => 9218,
			'guid' => 'https://domain.test/courses//lesson/demo-lesson-1/',
			'menu_order' => 1,
			'post_type' => 'lesson',
			'post_mime_type' => '',
			'comment_count' => '0',
			'filter' => 'raw',
			),
			'lesson_meta' => 
			array (
			'_et_dynamic_cached_shortcodes' => 
			array (
				0 => 'a:0:{}',
			),
			'_et_dynamic_cached_attributes' => 
			array (
				0 => 'a:0:{}',
			),
			),
			'lesson_user_reading' => false,
		);

	  return $data;
	}

  }

endif; // End if class_exists check.