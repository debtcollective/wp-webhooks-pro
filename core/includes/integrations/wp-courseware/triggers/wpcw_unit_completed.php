<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_wp_courseware_Triggers_wpcw_unit_completed' ) ) :

 /**
  * Load the wpcw_unit_completed trigger
  *
  * @since 4.3.5
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_wp_courseware_Triggers_wpcw_unit_completed {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'wpcw_user_completed_module',
				'callback' => array( $this, 'wpcw_unit_completed_callback' ),
				'priority' => 20,
				'arguments' => 3,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-wpcw_unit_completed-description";

		$parameter = array(
			'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the user.', $translation_ident ) ),
			'course_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The course id.', $translation_ident ) ),
			'module_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The module id.', $translation_ident ) ),
			'unit_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The unit id.', $translation_ident ) ),
			'student' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further details about the student (user).', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Unit completed',
			'webhook_slug' => 'wpcw_unit_completed',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'wpcw_user_completed_module',
				),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_wp_courseware_trigger_on_selected_units' => array(
					'id'		  => 'wpwhpro_wp_courseware_trigger_on_selected_units',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'helpers',
						'args'		=> array(
							'integration' => 'wp-courseware',
							'helper' => 'wpcw_helpers',
							'function' => 'get_query_units',
							'unit_args' => array(
								'orderby'   => 'post_title',
							),
						)
					),
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected units', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the units you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
				'wpwhpro_wp_courseware_trigger_on_selected_modules' => array(
					'id'		  => 'wpwhpro_wp_courseware_trigger_on_selected_modules',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'helpers',
						'args'		=> array(
							'integration' => 'wp-courseware',
							'helper' => 'wpcw_helpers',
							'function' => 'get_query_modules',
							'module_args' => array(
								'orderby'   => 'module_order',
								'order'     => 'ASC',
							),
						)
					),
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected modules', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the modules you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
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
			'trigger'		   => 'wpcw_unit_completed',
			'name'			  => WPWHPRO()->helpers->translate( 'Unit completed', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a user completed a unit', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a user completed a unit within WP Courseware.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'wp-courseware',
			'premium'		   => false,
		);

	}

	public function wpcw_unit_completed_callback( $user_id, $unit_id, $unit_parent_data ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wpcw_unit_completed' );
		$response_data_array = array();

		$module_id = ( ! empty( $unit_parent_data ) && isset( $unit_parent_data->parent_module_id ) ) ? intval( $unit_parent_data->parent_module_id ) : 0;
		$course_id = 0;

		if( ! empty( $unit_parent_data ) && isset( $unit_parent_data->course_id ) ){
			$course_id = intval( $unit_parent_data->course_id );
		}

		if( empty( $course_id ) && ! empty( $unit_parent_data ) && isset( $unit_parent_data->course_post_id ) ){
			$course_id = intval( $unit_parent_data->course_post_id );
		}

		$payload = array(
			'user_id' => $user_id,
			'course_id' => $course_id,
			'module_id' => $module_id,
			'unit_id' => $unit_id,
			'student' => wpcw_get_student( $user_id ),
		);

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){
	  
					if( $settings_name === 'wpwhpro_wp_courseware_trigger_on_selected_units' && ! empty( $settings_data ) ){
						if( ! in_array( $unit_id, $settings_data ) ){
							$is_valid = false;
						}
					
					}

					if( $is_valid && $settings_name === 'wpwhpro_wp_courseware_trigger_on_selected_courses' && ! empty( $settings_data ) ){
						if( ! in_array( $course_id, $settings_data ) ){
							$is_valid = false;
						}
					
					}
	  
					if( $is_valid && $settings_name === 'wpwhpro_wp_courseware_trigger_on_selected_modules' && ! empty( $settings_data ) ){
						if( ! in_array( $module_id, $settings_data ) ){
							$is_valid = false;
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

		do_action( 'wpwhpro/webhooks/trigger_wpcw_unit_completed', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'user_id' => 1,
			'course_id' => 3,
			'module_id' => 3,
			'unit_id' => 9103,
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