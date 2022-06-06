<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_restrict_content_pro_Triggers_rcp_membership_cancelled_free' ) ) :

 /**
  * Load the rcp_membership_cancelled_free trigger
  *
  * @since 4.3.6
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_restrict_content_pro_Triggers_rcp_membership_cancelled_free {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'rcp_transition_membership_status_expired',
				'callback' => array( $this, 'rcp_membership_cancelled_free_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-rcp_membership_cancelled_free-description";

		$parameter = array(
			'membership_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the membership.', $translation_ident ) ),
			'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The user id.', $translation_ident ) ),
			'user' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further data about the user.', $translation_ident ) ),
			'membership' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further data about the membership.', $translation_ident ) ),
			'membership_level' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further data about the membership level.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Paid membership activated',
			'webhook_slug' => 'rcp_membership_cancelled_free',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'rcp_transition_membership_status_expired',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'You can fire this trigger as well on specific membership levels only. To do that, simply specify the membership levels within the webhook URL settings.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_restrict_content_pro_trigger_on_selected_levels' => array(
					'id'		  => 'wpwhpro_restrict_content_pro_trigger_on_selected_levels',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'helpers',
						'args'		=> array(
							'integration' => 'restrict-content-pro',
							'helper' => 'rcp_helpers',
							'function' => 'get_query_levels',
						)
					),
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected membership levels', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the membership levels you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'rcp_membership_cancelled_free',
			'name'			  => WPWHPRO()->helpers->translate( 'Free membership cancelled', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a free membership was cancelled', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a free membership was cancelled within Restrict Content Pro.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'restrict-content-pro',
			'premium'		   => false,
		);

	}

	public function rcp_membership_cancelled_free_callback( $old_status, $membership_id ){

		$membership = rcp_get_membership( $membership_id );

		if( empty( $membership ) || $membership->is_paid() ){
            return;
        }

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'rcp_membership_cancelled_free' );
		$response_data_array = array();
		$rcp_helpers = WPWHPRO()->integrations->get_helper( 'restrict-content-pro', 'rcp_helpers' );

		$payload = $rcp_helpers->build_payload( $membership );

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){
	  
				  if( $settings_name === 'wpwhpro_restrict_content_pro_trigger_on_selected_levels' && ! empty( $settings_data ) ){
					if( ! in_array( $payload['membership_level']['id'], $settings_data ) ){
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

		do_action( 'wpwhpro/webhooks/trigger_rcp_membership_cancelled_free', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'membership_id' => '8',
			'user_id' => 78,
			'user' => 
			array (
				'data' => 
				array (
				  'ID' => '113',
				  'user_login' => 'demouser',
				  'user_pass' => '$P$BNHFPR8znhTIMV1dpceF7aUTUcqSrU/',
				  'user_nicename' => 'Demo User',
				  'user_email' => 'demouser@demo.test',
				  'user_url' => '',
				  'user_registered' => '2019-11-14 18:06:50',
				  'user_activation_key' => '',
				  'user_status' => '0',
				  'display_name' => 'demouser',
				  'spam' => '0',
				  'deleted' => '0',
				),
				'ID' => 113,
				'caps' => 
				array (
				  'subscriber' => true,
				),
				'cap_key' => 'zipf_capabilities',
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
			'membership' => 
			array (
			  'customer_id' => '6',
			  'customer' => 
			  array (
				'id' => '6',
				'user_id' => '78',
				'date_registered' => 'February 21, 2022',
				'email_verification_status' => 'none',
				'last_login' => '',
				'ips' => 
				array (
				),
				'has_trialed' => false,
				'notes' => '',
				'is_pending_verification' => false,
				'has_active_membership' => false,
				'has_paid_membership' => false,
				'lifetime_value' => 0,
			  ),
			  'membership_level_name' => 'Demo Level Paid',
			  'currency' => 'USD',
			  'initial_amount' => '10',
			  'recurring_amount' => '0.00',
			  'biling_cycle_formatted' => '&#36;10.00',
			  'status' => 'expired',
			  'expiration_date' => 'February 20, 2022',
			  'expiration_time' => 1645401599,
			  'created_date' => 'February 21, 2022',
			  'activated_date' => '2022-02-21 00:00:00',
			  'trial_end_date' => NULL,
			  'renewed_date' => NULL,
			  'cancellation_date' => 'February 21, 2022',
			  'times_billed' => 0,
			  'maximum_renewals' => '0',
			  'gateway' => 'manual',
			  'gateway_customer_id' => 'demo-gateway-id',
			  'gateway_subscription_id' => 'demo-gateway-subscription-id',
			  'subscription_key' => '',
			  'get_upgraded_from' => '0',
			  'was_upgrade' => false,
			  'payment_plan_completed_date' => NULL,
			  'notes' => 'February 21, 2022 14:59:34 - Status changed from expired to cancelled.
		  
		  February 21, 2022 14:59:39 - Status changed from cancelled to expired.
		  
		  February 21, 2022 14:59:39 - Expiration Date changed from 2022-02-20 11:40:23 to 2022-02-20 23:59:59.
		  
		  February 21, 2022 14:59:39 - Membership edited by admin.',
			  'signup_method' => 'manual',
			  'prorate_credit_amount' => 0,
			  'payments' => 
			  array (
			  ),
			  'card_details' => 
			  array (
			  ),
			),
			'membership_level' => 
			array (
			  'id' => 2,
			  'name' => 'Demo Level Paid',
			  'description' => '',
			  'is_lifetime' => true,
			  'duration' => 0,
			  'duration_unit' => 'day',
			  'has_trial' => false,
			  'trial_duration' => 0,
			  'trial_duration_unit' => 'day',
			  'get_price' => 10,
			  'is_free' => false,
			  'fee' => 0,
			  'renewals' => 0,
			  'access_level' => 0,
			  'status' => 'active',
			  'role' => 'subscriber',
			  'get_date_created' => '2022-02-21 10:38:07',
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.