<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_memberships_Triggers_wcm_mship_pending_cancellation' ) ) :

 /**
  * Load the wcm_mship_pending_cancellation trigger
  *
  * @since 4.3.7
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_woocommerce_memberships_Triggers_wcm_mship_pending_cancellation {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'wc_memberships_user_membership_status_changed',
				'callback' => array( $this, 'wcm_mship_pending_cancellation_callback' ),
				'priority' => 20,
				'arguments' => 3,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "trigger-wcm_mship_pending_cancellation-description";

		$parameter = array(
			'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the user the membership was set to pending cancellation.', $translation_ident ) ),
			'user_membership_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the user membership.', $translation_ident ) ),
			'membership_plan_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the membership plan.', $translation_ident ) ),
			'user' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further details about the user.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Membership cancelled',
			'webhook_slug' => 'wcm_mship_pending_cancellation',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'wc_memberships_user_membership_status_changed',
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
			'trigger'		   => 'wcm_mship_pending_cancellation',
			'name'			  => WPWHPRO()->helpers->translate( 'Membership pending cancellation', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a membership was set to pending cancellation', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a membership was set to pending cancellation within WooCommerce Memberships.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'woocommerce-memberships',
			'premium'		   => true,
		);

	}

	public function wcm_mship_pending_cancellation_callback( $user_membership, $old_status, $new_status ){	

		if( $old_status === $new_status || $new_status !== 'pending' ){
			return;
		}

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wcm_mship_pending_cancellation' );
		$payload = array(
			'user_id' => ( ! empty( $user_membership ) ) ? $user_membership->get_user_id() : 0,
			'user_membership_id' => ( ! empty( $user_membership ) ) ? $user_membership->get_id() : 0,
			'membership_plan_id' => ( ! empty( $user_membership ) ) ? $user_membership->get_plan_id() : 0,
			'user' => ( ! empty( $user_membership ) ) ? get_userdata( $user_membership->get_user_id() ) : array(),
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

		do_action( 'wpwhpro/webhooks/trigger_wcm_mship_pending_cancellation', $payload, $response_data_array );
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