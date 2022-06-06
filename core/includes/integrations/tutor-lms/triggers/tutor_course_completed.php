<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_tutor_lms_Triggers_tutor_course_completed' ) ) :

 /**
  * Load the tutor_course_completed trigger
  *
  * @since 5.1
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_tutor_lms_Triggers_tutor_course_completed {

  public function get_callbacks(){

   return array(
	array(
		'type' => 'action',
		'hook' => 'tutor_course_complete_after',
		'callback' => array( $this, 'ironikus_trigger_tutor_course_completed' ),
		'priority' => 20,
		'arguments' => 2,
		'delayed' => true,
	  ),
	);

  }

	public function get_details(){

	  $translation_ident = "trigger-tutor_course_completed-description";

	  $parameter = array(
		'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the user that has completed the course.', $translation_ident ) ),
		'course_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the course that has been completed.', $translation_ident ) ),
		'course' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further details about the course.', $translation_ident ) ),
		'course_meta' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The course meta data.', $translation_ident ) ),
		'course_price' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The course price', $translation_ident ) ),
		'course_product_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The course product id.', $translation_ident ) ),
		'course_rating' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The course rating.', $translation_ident ) ),
		'course_rating_user' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) the course rating based on the course user.', $translation_ident ) ),
		'course_completed' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further data about the completion.', $translation_ident ) ),
		'course_settings' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Course settings.', $translation_ident ) ),
		'course_duration' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further data about the course duration.', $translation_ident ) ),
	  );

	  	$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Course completed',
			'webhook_slug' => 'tutor_course_completed',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'tutor_course_complete_after',
				),
			)
		) );

	  	$settings = array(
		'load_default_settings' => true,
		'data' => array(
		  'wpwhpro_tutor_course_completed_trigger_on_courses' => array(
			'id'	 => 'wpwhpro_tutor_course_completed_trigger_on_courses',
			'type'	=> 'select',
			'multiple'  => true,
			'choices'   => array(),
			'query'			=> array(
				'filter'	=> 'posts',
				'args'		=> array(
					'post_type' => ( function_exists('tutor') && isset( tutor()->course_post_type ) ) ? tutor()->course_post_type : 'courses',
					'post_status' => 'publish',
				)
			),
			'label'	=> WPWHPRO()->helpers->translate( 'Trigger on selected courses', $translation_ident ),
			'placeholder' => '',
			'required'  => false,
			'description' => WPWHPRO()->helpers->translate( 'Select only the courses you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
		  ),
		)
	  );

	  return array(
		'trigger'	  => 'tutor_course_completed',
		'name'	   => WPWHPRO()->helpers->translate( 'Course completed', $translation_ident ),
		'sentence'	   => WPWHPRO()->helpers->translate( 'a course was completed', $translation_ident ),
		'parameter'	 => $parameter,
		'settings'	 => $settings,
		'returns_code'   => $this->get_demo( array() ),
		'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after a Tutor LMS course was completed.', $translation_ident ),
		'description'	=> $description,
		'callback'	 => 'test_tutor_course_completed',
		'integration'	=> 'tutor-lms',
		'premium'	=> false,
	  );

	}

	public function ironikus_trigger_tutor_course_completed( $course_id, $user_id ){

	  $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'tutor_course_completed' );
	  $course = get_post( $course_id );
	  $course_meta = get_post_meta( $course_id );
	  $data_array = array(
		'user_id' => $user_id,
		'course_id' => $course_id,
		'course' => $course,
		'course_meta' => $course_meta,
		'course_price' => tutor_utils()->get_course_price( $course_id ),
		'course_product_id' => tutor_utils()->get_course_product_id( $course_id ),
		'course_rating' => tutor_utils()->get_course_rating( $course_id ),
		'course_rating_user' => tutor_utils()->get_course_rating_by_user( $course_id, $user_id ),
		'course_completed' => tutor_utils()->get_course_completed_percent( $course_id, $user_id, true ),
		'course_settings' => tutor_utils()->get_course_settings( $course_id ),
		'course_duration' => tutor_utils()->get_course_duration( $course_id, '1' ),
	  );
	  $response_data = array();

	  foreach( $webhooks as $webhook ){

		$is_valid = true;

		if( isset( $webhook['settings'] ) ){
		  foreach( $webhook['settings'] as $settings_name => $settings_data ){

			if( $settings_name === 'wpwhpro_tutor_course_completed_trigger_on_courses' && ! empty( $settings_data ) ){
			  if( ! in_array( $course_id, $settings_data ) ){
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

	  do_action( 'wpwhpro/webhooks/trigger_tutor_course_completed', $data_array, $response_data );
	}

	/*
	* Register the demo post delete trigger callback
	*
	* @since 1.2
	*/
	public function get_demo( $options = array() ) {

	  $data = array (
		'user_id' => 123,
		'course_id' => 9213,
		'course' => 
		array (
		  'ID' => 9213,
		  'post_author' => '1',
		  'post_date' => '2022-04-11 13:20:49',
		  'post_date_gmt' => '2022-04-11 13:20:49',
		  'post_content' => 'demo description',
		  'post_title' => 'Demo course',
		  'post_excerpt' => '',
		  'post_status' => 'publish',
		  'comment_status' => 'closed',
		  'ping_status' => 'closed',
		  'post_password' => '',
		  'post_name' => 'demo-course',
		  'to_ping' => '',
		  'pinged' => '',
		  'post_modified' => '2022-04-11 13:20:49',
		  'post_modified_gmt' => '2022-04-11 13:20:49',
		  'post_content_filtered' => '',
		  'post_parent' => 0,
		  'guid' => 'https://domain.test/?post_type=courses&#038;p=9213',
		  'menu_order' => 0,
		  'post_type' => 'courses',
		  'post_mime_type' => '',
		  'comment_count' => '0',
		  'filter' => 'raw',
		),
		'course_meta' => 
		array (
		  '_edit_lock' => 
		  array (
			0 => '1649683249:1',
		  ),
		  '_edit_last' => 
		  array (
			0 => '1',
		  ),
		  '_tutor_course_settings' => 
		  array (
			0 => 'a:1:{s:16:"maximum_students";s:1:"0";}',
		  ),
		  '_course_duration' => 
		  array (
			0 => 'a:3:{s:5:"hours";s:2:"00";s:7:"minutes";s:2:"00";s:7:"seconds";s:2:"00";}',
		  ),
		  '_tutor_course_level' => 
		  array (
			0 => 'intermediate',
		  ),
		  '_tutor_enable_qa' => 
		  array (
			0 => 'no',
		  ),
		  '_tutor_is_public_course' => 
		  array (
			0 => 'no',
		  ),
		),
		'course_price' => NULL,
		'course_product_id' => 0,
		'course_rating' => 
		array (
		  'rating_count' => 0,
		  'rating_sum' => 0,
		  'rating_avg' => 0,
		  'count_by_value' => 
		  array (
			1 => 0,
			2 => 0,
			3 => 0,
			4 => 0,
			5 => 0,
		  ),
		),
		'course_rating_user' => 
		array (
		  'rating' => 0,
		  'review' => '',
		),
		'course_completed' => 
		array (
		  'completed_percent' => 0,
		  'completed_count' => 0,
		  'total_count' => 1,
		),
		'course_settings' => false,
		'course_duration' => 
		array (
		  'duration' => 
		  array (
			'hours' => '00',
			'minutes' => '00',
			'seconds' => '00',
		  ),
		  'durationHours' => '00',
		  'durationMinutes' => '00',
		  'durationSeconds' => '00',
		),
	);

	  return $data;
	}

  }

endif; // End if class_exists check.