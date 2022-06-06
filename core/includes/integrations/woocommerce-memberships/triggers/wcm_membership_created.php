<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_memberships_Triggers_wcm_membership_created' ) ) :

 /**
  * Load the wcm_membership_created trigger
  *
  * @since 4.3.7
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_woocommerce_memberships_Triggers_wcm_membership_created {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'wc_memberships_user_membership_saved',
				'callback' => array( $this, 'wcm_membership_created_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "trigger-wcm_membership_created-description";

		$parameter = array(
			'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the user the membership was created for.', $translation_ident ) ),
			'user_membership_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the user membership.', $translation_ident ) ),
			'membership_plan_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the membership plan. Please note that if you create the order via the backend, this field is empty.', $translation_ident ) ),
			'user' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further data about the user.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Membership created',
			'webhook_slug' => 'wcm_membership_created',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'wc_memberships_user_membership_saved',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'You can fire this trigger on specific membership plans only. To do that, select the membership plans within the webhook URL settings.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_woocommerce_trigger_on_member_plan' => array(
					'id'		  => 'wpwhpro_woocommerce_trigger_on_member_plan',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'helpers',
						'args'		=> array(
							'integration' => 'woocommerce-memberships',
							'helper' => 'wcm_helpers',
							'function' => 'get_query_membership_plans',
						)
					),
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected membership plans', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the membership plans you want to fire the trigger on. If none is selected, all are triggered.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'wcm_membership_created',
			'name'			  => WPWHPRO()->helpers->translate( 'Membership created', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a membership was created', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a membership was created within WooCommerce Memberships.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'woocommerce-memberships',
			'premium'		   => false,
		);

	}

	public function wcm_membership_created_callback( $membership_plan, $args ){	

		if( empty( $args ) || ! isset( $args['is_update'] ) || $args['is_update'] ){
			return;
		}

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wcm_membership_created' );
		$payload = array(
			'user_id' => ( ! empty( $membership_plan ) ) ? $membership_plan->get_user_id() : $args['user_id'],
			'user_membership_id' => ( ! empty( $membership_plan ) ) ? $membership_plan->get_id() : $args['user_membership_id'],
			'membership_plan_id' => ( ! empty( $membership_plan ) ) ? $membership_plan->get_plan_id() : 0,
			'user' => ( ! empty( $membership_plan ) ) ? get_userdata( $membership_plan->get_user_id() ) : get_userdata( $args['user_id'] ),
		);

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){

				if( $is_valid && isset( $webhook['settings']['wpwhpro_woocommerce_trigger_on_member_plan'] ) && is_array( $webhook['settings']['wpwhpro_woocommerce_trigger_on_member_plan'] ) ){
					if( ! in_array( $payload['membership_plan_id'], $webhook['settings']['wpwhpro_woocommerce_trigger_on_member_plan'] ) ){
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

		do_action( 'wpwhpro/webhooks/trigger_wcm_membership_created', $payload, $response_data_array );
	}
	
	public function get_demo( $options = array() ) {

		$data = array (
			'user_id' => 87,
			'user_membership_id' => 9152,
			'membership_plan_id' => 0,
			'user' => 
			array (
			  'data' => 
			  array (
				'ID' => '87',
				'user_login' => 'jondoe',
				'user_pass' => '$P$BEkpnevKHXvnXXXXXXXXXYTJ85P/',
				'user_nicename' => 'Jon Doe',
				'user_email' => 'jondoe@domain.test',
				'user_url' => '',
				'user_registered' => '2021-07-03 15:44:54',
				'user_activation_key' => '',
				'user_status' => '0',
				'display_name' => 'Jon Doe',
				'spam' => '0',
				'deleted' => '0',
			  ),
			  'ID' => 87,
			  'caps' => 
			  array (
				'subscriber' => true,
			  ),
			  'cap_key' => 'wp_capabilities',
			  'roles' => 
			  array (
				0 => 'subscriber',
			  ),
			  'allcaps' => 
			  array (
				'read' => true,
				'level_0' => true,
				'read_private_locations' => true,
				'read_private_events' => true,
				'manage_resumes' => true,
				'subscriber' => true,
			  ),
			  'filter' => NULL,
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.