<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_affiliatewp_Triggers_affwp_new_referral' ) ) :

 /**
  * Load the affwp_new_referral trigger
  *
  * @since 4.2.3
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_affiliatewp_Triggers_affwp_new_referral {

  public function get_callbacks(){

   return array(
	array(
		'type' => 'action',
		'hook' => 'affwp_insert_referral',
		'callback' => array( $this, 'affwp_insert_referral_callback' ),
		'priority' => 20,
		'arguments' => 1,
		'delayed' => true,
	  ),
	);

  }

	public function get_details(){

	  $translation_ident = "trigger-affwp_new_referral-description";

	  $parameter = array(
		'referral_id' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The id of the newly created referral.', $translation_ident ) ),
		'referral' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Additional data about the referral.', $translation_ident ) ),
		'affiliate_id' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The id of the related affiliate.', $translation_ident ) ),
		'affiliate' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Additional data about the affiliate.', $translation_ident ) ),
		'third-party' => array( 'short_description' => WPWHPRO()->helpers->translate( 'In case you use AffiliateWP with various third-party integrations, you will find additional data here.', $translation_ident ) ),
		'user' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The user details about the assigned user.', $translation_ident ) ),
		'user_meta' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The meta data of the assigned user.', $translation_ident ) ),
	  );

	  	$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'New Referral',
			'webhook_slug' => 'affwp_new_referral',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'affwp_insert_referral',
				),
			)
		) );

	  	$settings = array(
		'load_default_settings' => true,
		'data' => array(
		  'wpwhpro_affwp_new_referral_trigger_on_type' => array(
			'id'	 => 'wpwhpro_affwp_new_referral_trigger_on_type',
			'type'	=> 'select',
			'multiple'  => true,
			'choices'   => array(),
			'query'			=> array(
				'filter'	=> 'helpers',
				'args'		=> array(
					'integration' => 'affiliatewp',
					'helper' => 'affwp_helpers',
					'function' => 'get_referral_types',
				)
			),
			'label'	=> WPWHPRO()->helpers->translate( 'Trigger on selected referral type', $translation_ident ),
			'placeholder' => '',
			'required'  => false,
			'description' => WPWHPRO()->helpers->translate( 'Select only the referral type you want to fire the trigger on.', $translation_ident )
		  ),
		  'wpwhpro_affwp_new_referral_trigger_on_status' => array(
			'id'	 => 'wpwhpro_affwp_new_referral_trigger_on_status',
			'type'	=> 'select',
			'multiple'  => true,
			'choices'   => array(),
			'query'			=> array(
				'filter'	=> 'helpers',
				'args'		=> array(
					'integration' => 'affiliatewp',
					'helper' => 'affwp_helpers',
					'function' => 'get_referral_statuses',
				)
			),
			'label'	=> WPWHPRO()->helpers->translate( 'Trigger on selected referral statuses', $translation_ident ),
			'placeholder' => '',
			'required'  => false,
			'description' => WPWHPRO()->helpers->translate( 'Select only the referral statuses you want to fire the trigger on.', $translation_ident )
		  ),
		  'wpwhpro_affwp_new_referral_trigger_on_third_party' => array(
			'id'	 => 'wpwhpro_affwp_new_referral_trigger_on_third_party',
			'type'	=> 'select',
			'multiple'  => true,
			'choices'   => array(),
			'query'			=> array(
				'filter'	=> 'helpers',
				'args'		=> array(
					'integration' => 'affiliatewp',
					'helper' => 'affwp_helpers',
					'function' => 'get_affiliate_integrations',
				)
			),
			'label'	=> WPWHPRO()->helpers->translate( 'Trigger on selected third-party integrations', $translation_ident ),
			'placeholder' => '',
			'required'  => false,
			'description' => WPWHPRO()->helpers->translate( 'Select only the third-party integrations for this referral you want to fire the trigger on. If none is selected, all are triggered. Please note: In case you do not see a specific extension here, make sure you activated it within the settings of AffiliateWP first.', $translation_ident )
		  ),
		)
	  );

	  return array(
		'trigger'	  => 'affwp_new_referral',
		'name'	   => WPWHPRO()->helpers->translate( 'New referral', $translation_ident ),
		'sentence'	   => WPWHPRO()->helpers->translate( 'a new referral was received', $translation_ident ),
		'parameter'	 => $parameter,
		'settings'	 => $settings,
		'returns_code'   => $this->get_demo( array() ),
		'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after a new referral was received within AffiliateWP.', $translation_ident ),
		'description'	=> $description,
		'integration'	=> 'affiliatewp',
		'premium'	=> true,
	  );

	}

	public function affwp_insert_referral_callback( $referral_id ){

	  $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'affwp_new_referral' );
	  $referral = affwp_get_referral( $referral_id );
	  $referral_context = (string) $referral->context;
	  $affiliate_id = $referral->affiliate_id;
	  $affiliate = affwp_get_affiliate( $affiliate_id );
	  $user_id = isset( $affiliate->user_id ) ? $affiliate->user_id : 0;
	  $user = array();
	  $user_meta = array();
	  $data_array = array(
		'referral_id' => $referral_id,
		'referral' => $referral,
		'affiliate_id' => $affiliate_id,
		'affiliate' => $affiliate,
		'third-party' => array(),
	  );
	  $response_data = array();

	  if( ! empty( $user_id ) ){
		$user = get_user_by( 'id', $user_id );
		$user_meta = get_user_meta( $user_id );
	  }
	  $data_array['user'] = $user;
	  $data_array['user_meta'] = $user_meta;

	  switch( $referral_context ){
		  case 'edd':
			$edd_payment_id	= $referral->reference;
			$data_array['third-party']['source'] = $referral_context;
			$data_array['third-party']['payment_id'] = $edd_payment_id;
			if( function_exists( 'edd_get_payment' ) ){
				$payment		= edd_get_payment( $edd_payment_id );
				$data_array['third-party']['payment'] = $payment;
				if( class_exists( 'EDD_Customer' ) && ! empty( $payment ) && $payment instanceof EDD_Payment ){
					$customer = new EDD_Customer( $payment->customer_id );
					$data_array['third-party']['customer'] = $customer;
				}
			}
			break;
		  case 'memberpress':
			$reference_id = $referral->reference;
			$data_array['third-party']['source'] = $referral_context;
			$data_array['third-party']['reference_id'] = $reference_id;
			if( class_exists( 'MeprTransaction' ) ){
				$transaction = new MeprTransaction( $reference_id );
				$data_array['third-party']['transaction'] = $transaction;
				if( ! empty( $transaction ) && $transaction instanceof MeprTransaction ){
					$customer_id = $transaction->user_id;
					$customer = get_user_by( 'id', $customer_id );
					$data_array['third-party']['user_id'] = $customer;
					$data_array['third-party']['user'] = $customer;
				}
			}
			break;
		  case 'woocommerce':
			$order_id = $referral->reference;
			$data_array['third-party']['source'] = $referral_context;
			$data_array['third-party']['order_id'] = $order_id;
			if( function_exists( 'wc_get_order' ) ){
				$order = wc_get_order( $order_id );
				$data_array['third-party']['order'] = $order;
				if( ! empty( $order ) && $order instanceof WC_Order ){
					$customer_id = $order->get_customer_id();
					$customer = get_user_by( 'id', $customer_id );
					$data_array['third-party']['user_id'] = $customer;
					$data_array['third-party']['user'] = $customer;
				}
			}
			break;
	  }

	  foreach( $webhooks as $webhook ){

		$is_valid = true;

		if( isset( $webhook['settings'] ) ){
		  foreach( $webhook['settings'] as $settings_name => $settings_data ){

			if( $settings_name === 'wpwhpro_affwp_new_referral_trigger_on_type' && ! empty( $settings_data ) ){
				if( ! in_array( $referral->type, $settings_data ) ){
					$is_valid = false;
				}
			}

			if( $is_valid && $settings_name === 'wpwhpro_affwp_new_referral_trigger_on_status' && ! empty( $settings_data ) ){
				if( ! in_array( $referral->status, $settings_data ) ){
					$is_valid = false;
				}
			}

			if( $is_valid && $settings_name === 'wpwhpro_affwp_new_referral_trigger_on_third_party' && ! empty( $settings_data ) ){
				if( ! in_array( $referral_context, $settings_data ) ){
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

	  do_action( 'wpwhpro/webhooks/trigger_affwp_new_referral', $data_array, $response_data );
	}

	public function get_demo( $options = array() ) {

	  $data = array (
		'referral_id' => 3,
		'referral' => 
		array (
		  'referral_id' => 3,
		  'affiliate_id' => 5,
		  'visit_id' => 0,
		  'rest_id' => '',
		  'customer_id' => '0',
		  'parent_id' => 0,
		  'description' => 'Some demo description',
		  'status' => 'unpaid',
		  'amount' => '15.00',
		  'currency' => '',
		  'custom' => 'Some custom demo info',
		  'context' => 'PayPal',
		  'campaign' => '',
		  'reference' => 'transactionid1234',
		  'products' => '',
		  'date' => '2021-08-11 00:00:00',
		  'type' => 'sale',
		  'payout_id' => '0',
		),
		'affiliate_id' => 5,
		'affiliate' => 
		array (
		  'affiliate_id' => 5,
		  'rest_id' => '',
		  'user_id' => 97,
		  'rate' => '20',
		  'rate_type' => 'percentage',
		  'flat_rate_basis' => '',
		  'payment_email' => 'payment@email.com',
		  'status' => 'active',
		  'earnings' => 0,
		  'unpaid_earnings' => 15,
		  'referrals' => 0,
		  'visits' => 0,
		  'date_registered' => '2021-08-25 16:01:01',
		),
		'user' => 
		array (
		  'data' => 
		  array (
			'ID' => '97',
			'user_login' => 'profile1',
			'user_pass' => '$P$Bgt27hhP2HHHHHHHIDtLWPPq3AH81E1',
			'user_nicename' => 'profile1',
			'user_email' => 'demo@account.test',
			'user_url' => '',
			'user_registered' => '2019-09-26 23:03:37',
			'user_activation_key' => '',
			'user_status' => '0',
			'display_name' => 'profile1',
			'spam' => '0',
			'deleted' => '0',
		  ),
		  'ID' => 97,
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
			'subscriber' => true,
		  ),
		  'filter' => NULL,
		),
		'user_meta' => 
		array (
		  'nickname' => 
		  array (
			0 => 'profile1',
		  ),
		  'first_name' => 
		  array (
			0 => '',
		  ),
		  'last_name' => 
		  array (
			0 => '',
		  ),
		  'description' => 
		  array (
			0 => '',
		  ),
		  'rich_editing' => 
		  array (
			0 => 'true',
		  ),
		  'syntax_highlighting' => 
		  array (
			0 => 'true',
		  ),
		  'comment_shortcuts' => 
		  array (
			0 => 'false',
		  ),
		  'admin_color' => 
		  array (
			0 => 'fresh',
		  ),
		  'use_ssl' => 
		  array (
			0 => '0',
		  ),
		  'show_admin_bar_front' => 
		  array (
			0 => 'true',
		  ),
		  'locale' => 
		  array (
			0 => '',
		  ),
		  'dismissed_wp_pointers' => 
		  array (
			0 => '',
		  ),
		  'primary_blog' => 
		  array (
			0 => '1',
		  ),
		  'source_domain' => 
		  array (
			0 => 'wpme.dev',
		  ),
		  'wp_capabilities' => 
		  array (
			0 => 'a:1:{s:10:"subscriber";b:1;}',
		  ),
		  'wp_user_level' => 
		  array (
			0 => '0',
		  ),
		  'affwp_referral_notifications' => 
		  array (
			0 => '1',
		  ),
		),
	);

	  return $data;
	}

  }

endif; // End if class_exists check.