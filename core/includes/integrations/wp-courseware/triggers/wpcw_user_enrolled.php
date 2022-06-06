<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_wp_courseware_Triggers_wpcw_user_enrolled' ) ) :

 /**
  * Load the wpcw_user_enrolled trigger
  *
  * @since 4.3.5
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_wp_courseware_Triggers_wpcw_user_enrolled {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'wpcw_enroll_user',
				'callback' => array( $this, 'wpcw_user_enrolled_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-wpcw_user_enrolled-description";

		$parameter = array(
			'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the user.', $translation_ident ) ),
			'courses_enrolled' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The enrolled course ids.', $translation_ident ) ),
			'student' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further details about the student (user).', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'User enrolled',
			'webhook_slug' => 'wpcw_user_enrolled',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'wpcw_enroll_user',
				),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_wp_courseware_trigger_on_selected_courses' => array(
					'id'		  => 'wpwhpro_wp_courseware_trigger_on_selected_courses',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'helpers',
						'args'		=> array(
							'integration' => 'wp-courseware',
							'helper' => 'wpcw_helpers',
							'function' => 'get_query_courses',
							'course_args' => array(
								'status'  => 'publish',
								'orderby' => 'post_title',
							),
						)
					),
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected courses', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the courses you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'wpcw_user_enrolled',
			'name'			  => WPWHPRO()->helpers->translate( 'User enrolled', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a user enrolled into a course', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a user enrolled into a course within WP Courseware.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'wp-courseware',
			'premium'		   => false,
		);

	}

	public function wpcw_user_enrolled_callback( $user_id, $courses_enrolled ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wpcw_user_enrolled' );
		$response_data_array = array();

		$payload = array(
			'user_id' => $user_id,
			'courses_enrolled' => $courses_enrolled,
			'student' => wpcw_get_student( $user_id ),
		);

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){
	  
				  if( $settings_name === 'wpwhpro_wp_courseware_trigger_on_selected_courses' && ! empty( $settings_data ) ){
					  $is_valid = false;

					  foreach( $courses_enrolled as $single_course ){
						if( in_array( $single_course, $settings_data ) ){
							$is_valid = true;
						  }
					  }
					
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

		do_action( 'wpwhpro/webhooks/trigger_wpcw_user_enrolled', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'user_id' => 72,
			'courses_enrolled' => 
			array (
			  0 => 2,
			),
			'student' => 
			array (
				'ID' => '144',
				'user_login' => 'demo',
				'user_pass' => '$P$BEKoON56hLTtFZXXXXXXXXxMPkF0',
				'user_nicename' => 'demo',
				'user_email' => 'demo@user.test',
				'user_url' => '',
				'user_registered' => '2021-09-08 17:21:54',
				'user_activation_key' => '',
				'user_status' => '0',
				'display_name' => 'Demo User',
				'first_name' => 'Demo',
				'last_name' => 'User',
				'email' => NULL,
				'billing_address_1' => '',
				'billing_address_2' => '',
				'billing_city' => '',
				'billing_postcode' => '',
				'billing_country' => '',
				'billing_state' => '',
				'orders' => NULL,
				'subscriptions' => NULL,
			),
		  );

		return $data;
	}

  }

endif; // End if class_exists check.