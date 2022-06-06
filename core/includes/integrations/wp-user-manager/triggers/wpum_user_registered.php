<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_wp_user_manager_Triggers_wpum_user_registered' ) ) :

 /**
  * Load the wpum_user_registered trigger
  *
  * @since 4.3.5
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_wp_user_manager_Triggers_wpum_user_registered {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'wpum_after_registration',
				'callback' => array( $this, 'wpum_user_registered_callback' ),
				'priority' => 20,
				'arguments' => 3,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-wpum_user_registered-description";

		$parameter = array(
			'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the user that was updated.', $translation_ident ) ),
			'values' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) All of the values from the registration form.', $translation_ident ) ),
			'form_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The registration form id.', $translation_ident ) ),
			'form_name' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The registration form name.', $translation_ident ) ),
			'form_role' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The default role of the registration form.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'User registered',
			'webhook_slug' => 'wpum_user_registered',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'wpum_after_registration',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'You can fire this trigger as well on specific forms only. To do that, simply select the forms within the webhook URL settings.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'By default, this form also sends over the default user password. You can prevent set by enabling the "Remove user password" setting within the webhook URL settings.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_wp_user_manager_trigger_on_forms' => array(
					'id'		  => 'wpwhpro_wp_user_manager_trigger_on_forms',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	=> array(),
					'query'			=> array(
						'filter'	=> 'helpers',
						'args'		=> array(
							'integration' => 'wp-user-manager',
							'helper' => 'wpum_helpers',
							'function' => 'get_query_forms',
						)
					),
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected forms', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Trigger this webhook only on specific forms. You can also choose multiple ones. If none are set, all are triggered.', $translation_ident )
				),
				'wpwhpro_wp_user_manager_remove_password' => array(
					'id'		  => 'wpwhpro_wp_user_manager_remove_password',
					'type'		=> 'checkbox',
					'label'	   => WPWHPRO()->helpers->translate( 'Remove user password', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Switch this on if you want to remove the clean user password from the request.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'wpum_user_registered',
			'name'			  => WPWHPRO()->helpers->translate( 'User registered', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a user was registered', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a user was registered within WP User Manager.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'wp-user-manager',
			'premium'		   => false,
		);

	}

	public function wpum_user_registered_callback( $new_user_id, $values, $form ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wpum_user_registered' );
			
		$response_data_array = array();
		$form_id = ( ! empty( $form ) ) ? intval( $form->get_ID() ) : 0;
		$form_name = ( ! empty( $form ) ) ? $form->get_name() : '';
		$form_role = ( ! empty( $form ) ) ? $form->get_role() : array();

		$payload = array(
			'user_id' => $new_user_id,
			'values' => $values,
			'form_id' => $form_id,
			'form_name' => $form_name,
			'form_role' => $form_role,
		);

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){

				if( isset( $webhook['settings']['wpwhpro_wp_user_manager_trigger_on_forms'] ) && ! empty( $webhook['settings']['wpwhpro_wp_user_manager_trigger_on_forms'] ) ){
					if( ! in_array( $form_id, $webhook['settings']['wpwhpro_wp_user_manager_trigger_on_forms'] ) ){
						$is_valid = false;
					}
				}

				if( isset( $webhook['settings']['wpwhpro_wp_user_manager_remove_password'] ) && ! empty( $webhook['settings']['wpwhpro_wp_user_manager_remove_password'] ) ){
					$wpum_helpers = WPWHPRO()->integrations->get_helper( 'wp-user-manager', 'wpum_helpers' );
					$payload['values'] = $wpum_helpers->remove_clean_password( $payload['values'] );
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

		do_action( 'wpwhpro/webhooks/trigger_wpum_user_registered', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'user_id' => 168,
			'values' => 
			array (
			  'register' => 
			  array (
				'user_email' => 'jondoe@demo.test',
				'user_password' => 'thecleanpassword',
				'robo' => '',
			  ),
			),
			'form_id' => 1,
			'form_name' => 'Default registration form',
			'form_role' => 'Subscriber',
		  );

		return $data;
	}

  }

endif; // End if class_exists check.