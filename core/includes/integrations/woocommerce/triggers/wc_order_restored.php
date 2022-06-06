<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_Triggers_wc_order_restored' ) ) :

 /**
  * Load the wc_order_restored trigger
  *
  * @since 4.3.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_woocommerce_Triggers_wc_order_restored {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'untrashed_post',
				'callback' => array( $this, 'wc_order_restored_callback' ),
				'priority' => 20,
				'arguments' => 1,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "trigger-wc_order_restored-description";

		$parameter = array(
			'custom' => array( 'short_description' => WPWHPRO()->helpers->translate( 'A custom data construct from your chosen Woocommerce API.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Order restored',
			'webhook_slug' => 'wc_order_restored',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'untrashed_post',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'Please make sure to set the user id setting within the webhook URL. This setting allows our webhook to request the original payload from the REST API, just as Woocommerce does.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'You can fire this trigger as well on a specific Woocommerce API version. To do that, select a version within the webhook URL settings.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'You can also set a custom secret key just as for the default Woocommerce webhooks. If you do not set one, there will be one automatically generated.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_woocommerce_set_user' => array(
					'id'		  => 'wpwhpro_woocommerce_set_user',
					'type'		=> 'text',
					'label'	   => WPWHPRO()->helpers->translate( 'Set user id', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Set the id of a user that has permission to view the Woocommerce REST API. If you do not set a valid user id, the response will not be verified.', $translation_ident )
				),
				'wpwhpro_woocommerce_set_api_version' => array(
					'id'		  => 'wpwhpro_woocommerce_set_api_version',
					'type'		=> 'select',
					'multiple'	=> false,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'helpers',
						'args'		=> array(
							'integration' => 'woocommerce',
							'helper' => 'wc_helpers',
							'function' => 'get_query_wc_api_versions',
						)
					),
					'label'	   => WPWHPRO()->helpers->translate( 'Set API version', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'default_value'	=> 'wp_api_v2',
					'description' => WPWHPRO()->helpers->translate( 'Select the Woocommerce API version you want to use for this request. By default, we use wp_api_v2', $translation_ident )
				),
				'wpwhpro_woocommerce_set_secret' => array(
					'id'		  => 'wpwhpro_woocommerce_set_secret',
					'type'		=> 'text',
					'label'	   => WPWHPRO()->helpers->translate( 'Set secret', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Set a custom secret that gets validated by Woocommerce, just as you know it from the default Woocommerce webhooks.', $translation_ident )
				),
				'wpwhpro_woocommerce_trigger_on_statuses' => array(
					'id'		  => 'wpwhpro_woocommerce_trigger_on_statuses',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'helpers',
						'args'		=> array(
							'integration' => 'woocommerce',
							'helper' => 'wc_helpers',
							'function' => 'get_query_statuses',
						)
					),
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected statuses', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the statuses you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'wc_order_restored',
			'name'			  => WPWHPRO()->helpers->translate( 'Order restored', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'an order was restored', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as an order was restored within Woocommerce.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'woocommerce',
			'premium'		   => true,
		);

	}

	/**
	 * Triggers once a order was restored
	 *
	 * @param mixed $arg
	 */
	public function wc_order_restored_callback( $arg ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wc_order_restored' );
		$payload = array();
		$payload_track = array();

		$topic = 'order.restored';
		$api_version = 'wp_api_v2';

		if( ! class_exists( 'WC_Webhook' ) ){
			return;
		}

		$wc_helpers = WPWHPRO()->integrations->get_helper( 'woocommerce', 'wc_helpers' );
		$post_id = ( is_numeric( $arg ) ) ? intval( $arg ) : 0;

		$wc_webhook = new WC_Webhook();
		$wc_webhook->set_name( 'wpwh-' . $topic );
		$wc_webhook->set_status( 'active' );
		$wc_webhook->set_topic( $topic );
		$wc_webhook->set_user_id( 0 );
		$wc_webhook->set_pending_delivery( false );
		#$wc_webhook->set_delivery_url(  );

		//Make sure we follow Woocommerce standards of verifying webhooks
		$continue_webhook = false;
		
		//revalidate ourselves to avoid woocommerce issues
		if( is_numeric( $arg ) ){
			$post_id = $arg;
			$post_type = get_post_type( $post_id );
			if( $post_type === 'shop_order' ){
				$continue_webhook = true;
			}
		}

		if ( ! $continue_webhook ) {
			return;
		}

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){

				if( $is_valid && isset( $webhook['settings']['wpwhpro_woocommerce_set_api_version'] ) && ! empty( $webhook['settings']['wpwhpro_woocommerce_set_api_version'] ) ){
					$api_version = $webhook['settings']['wpwhpro_woocommerce_set_api_version'];
				}

				if( $is_valid && isset( $webhook['settings']['wpwhpro_woocommerce_set_secret'] ) && ! empty( $webhook['settings']['wpwhpro_woocommerce_set_secret'] ) ){
					$wc_webhook->set_secret( $webhook['settings']['wpwhpro_woocommerce_set_secret'] );
				}

				if( $is_valid 
					&& isset( $webhook['settings']['wpwhpro_woocommerce_set_user'] ) 
					&& ! empty( $webhook['settings']['wpwhpro_woocommerce_set_user'] ) 
					&& is_numeric( $webhook['settings']['wpwhpro_woocommerce_set_user'] )
				){
					$wc_webhook->set_user_id( intval( $webhook['settings']['wpwhpro_woocommerce_set_user'] ) );
				}

				//Make sure we automatically prevent the webhook from firing twice due to the Woocommerce hook notation
				$webhook['settings']['wpwhpro_trigger_single_instance_execution'] = 1;
			} else {
				$webhook['settings'] = array(
					'wpwhpro_trigger_single_instance_execution' => 1,
				);
			}

			if( $is_valid ){

				$wc_webhook->set_api_version( $api_version );
				$payload = $wc_webhook->build_payload( $arg );

				//Revalidate the given Woocommerce status
				if( is_array( $payload ) && isset( $payload['status'] ) && isset( $webhook['settings'] ) ){

					$status_ident = 'wc-';
					if( substr( $payload['status'], 0, strlen( $status_ident ) ) !== $status_ident ){
						$status = $status_ident . $payload['status'];
					} else {
						$status = $payload['status'];
					}

					if( isset( $webhook['settings']['wpwhpro_woocommerce_trigger_on_statuses'] ) && ! empty( $webhook['settings']['wpwhpro_woocommerce_trigger_on_statuses'] ) ){
						if( ! in_array( $status, $webhook['settings']['wpwhpro_woocommerce_trigger_on_statuses'] ) ){
							continue;
						}
					}
				}

				//Append additional data
				if( ! empty( $post_id ) && is_array( $payload ) ){
					$payload['wpwh_meta_data'] = get_post_meta( $post_id );
					$payload['wpwh_tax_data'] = $wc_helpers->get_validated_taxonomies( $post_id );
				}

				//setup headers
				$headers	                                      = array();
				$headers['Content-Type']      		 = 'application/json';
				$headers['X-WC-Webhook-Source']      = home_url( '/' ); // Since 2.6.0.
				$headers['X-WC-Webhook-Topic']       = $wc_webhook->get_topic();
				$headers['X-WC-Webhook-Resource']    = $wc_webhook->get_resource();
				$headers['X-WC-Webhook-Event']       = $wc_webhook->get_event();
				$headers['X-WC-Webhook-Signature']   = $wc_webhook ->generate_signature( trim( wp_json_encode( $payload ) ) );
				$headers['X-WC-Webhook-ID']          = 0;
				$headers['X-WC-Webhook-Delivery-ID'] = 0;

				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload, array( 'headers' => $headers ) );
					$payload_track[] = $payload;
				} else {
					$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload, array( 'headers' => $headers ) );
				}
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_wc_order_restored', $payload, $response_data_array, $payload_track );
	}
	
	public function get_demo( $options = array() ) {

		$data = array (
			'id' => 8093,
			'parent_id' => 0,
			'status' => 'processing',
			'currency' => 'EUR',
			'version' => '6.0.0',
			'prices_include_tax' => false,
			'date_created' => '2021-12-27T20:18:38',
			'date_modified' => '2021-12-28T06:12:14',
			'discount_total' => '0.00',
			'discount_tax' => '0.00',
			'shipping_total' => '0.00',
			'shipping_tax' => '0.00',
			'cart_tax' => '0.00',
			'total' => '0.00',
			'total_tax' => '0.00',
			'customer_id' => 0,
			'order_key' => 'wc_order_ZRmet2dp37liy',
			'billing' => 
			array (
			  'first_name' => 'Jon',
			  'last_name' => 'Doe',
			  'company' => 'Test Corp',
			  'address_1' => 'Demo St. 5',
			  'address_2' => '',
			  'city' => 'Demo city',
			  'state' => '',
			  'postcode' => '12345',
			  'country' => 'DE',
			  'email' => 'demo@testdomain.test',
			  'phone' => '123456789',
			),
			'shipping' => 
			array (
			  'first_name' => '',
			  'last_name' => '',
			  'company' => '',
			  'address_1' => '',
			  'address_2' => '',
			  'city' => '',
			  'state' => '',
			  'postcode' => '',
			  'country' => '',
			  'phone' => '',
			),
			'payment_method' => '',
			'payment_method_title' => '',
			'transaction_id' => '',
			'customer_ip_address' => '127.0.0.1',
			'customer_user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.93 Safari/537.36',
			'created_via' => 'checkout',
			'customer_note' => '',
			'date_completed' => NULL,
			'date_paid' => '2021-12-27T20:18:38',
			'cart_hash' => 'c804772fedddc0bb7b7c687e8a0e447e',
			'number' => '8093',
			'meta_data' => 
			array (
			  0 => 
			  array (
				'id' => 70873,
				'key' => 'is_vat_exempt',
				'value' => 'no',
			  ),
			  1 => 
			  array (
				'id' => 70880,
				'key' => '_new_order_email_sent',
				'value' => 'true',
			  ),
			  2 => 
			  array (
				'id' => 70881,
				'key' => 'wpwhpro_create_post_temp_status_jobs',
				'value' => 'wc-processing',
			  ),
			),
			'line_items' => 
			array (
			  0 => 
			  array (
				'id' => 41,
				'name' => 'Bookable Product',
				'product_id' => 604,
				'variation_id' => 0,
				'quantity' => 2,
				'tax_class' => '',
				'subtotal' => '0.00',
				'subtotal_tax' => '0.00',
				'total' => '0.00',
				'total_tax' => '0.00',
				'taxes' => 
				array (
				),
				'meta_data' => 
				array (
				),
				'sku' => '',
				'price' => 0,
				'parent_name' => NULL,
			  ),
			),
			'tax_lines' => 
			array (
			),
			'shipping_lines' => 
			array (
			),
			'fee_lines' => 
			array (
			),
			'coupon_lines' => 
			array (
			),
			'refunds' => 
			array (
			),
			'date_created_gmt' => '2021-12-27T20:18:38',
			'date_modified_gmt' => '2021-12-28T06:12:14',
			'date_completed_gmt' => NULL,
			'date_paid_gmt' => '2021-12-27T20:18:38',
			'currency_symbol' => '€',
			'_links' => 
			array (
			  'self' => 
			  array (
				0 => 
				array (
				  'href' => 'https://yourdomain.test/wp-json/wc/v2/orders/8093',
				),
			  ),
			  'collection' => 
			  array (
				0 => 
				array (
				  'href' => 'https://yourdomain.test/wp-json/wc/v2/orders',
				),
			  ),
			),
			'wpwh_meta_data' => 
			array (
				'_order_key' => 
				array (
				0 => 'wc_order_G6tAiKndLB8up',
				),
				'_customer_user' => 
				array (
				0 => '153',
				),
				'_customer_ip_address' => 
				array (
				0 => '127.0.0.1',
				),
				'_customer_user_agent' => 
				array (
				0 => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.93 Safari/537.36',
				),
				'_created_via' => 
				array (
				0 => 'checkout',
				),
				'_cart_hash' => 
				array (
				0 => '06595c37c969ebe1f8a6304602c2f2e4',
				),
				'_billing_first_name' => 
				array (
				0 => 'Demo',
				),
				'_billing_last_name' => 
				array (
				0 => 'User',
				),
				'_billing_company' => 
				array (
				0 => 'Demo Corp',
				),
				'_billing_address_1' => 
				array (
				0 => 'Demo St. 55',
				),
				'_billing_city' => 
				array (
				0 => 'Demo City',
				),
				'_billing_postcode' => 
				array (
				0 => '12345',
				),
				'_billing_country' => 
				array (
				0 => 'DE',
				),
				'_billing_email' => 
				array (
				0 => 'demouser@yourdomain.test',
				),
				'_billing_phone' => 
				array (
				0 => '123456789',
				),
				'_order_currency' => 
				array (
				0 => 'EUR',
				),
				'_cart_discount' => 
				array (
				0 => '0',
				),
				'_cart_discount_tax' => 
				array (
				0 => '0',
				),
				'_order_shipping' => 
				array (
				0 => '0',
				),
				'_order_shipping_tax' => 
				array (
				0 => '0',
				),
				'_order_tax' => 
				array (
				0 => '0',
				),
				'_order_total' => 
				array (
				0 => '0.00',
				),
				'_order_version' => 
				array (
				0 => '6.0.0',
				),
				'_prices_include_tax' => 
				array (
				0 => 'no',
				),
				'_billing_address_index' => 
				array (
				0 => 'Demo User Demo Corp Demo St. 55  Demo City  12345 DE demouser@yourdomain.test 123456789',
				),
				'_shipping_address_index' => 
				array (
				0 => '         ',
				),
				'is_vat_exempt' => 
				array (
				0 => 'no',
				),
				'_date_paid' => 
				array (
				0 => '1640669164',
				),
				'_paid_date' => 
				array (
				0 => '2021-12-28 05:26:04',
				),
				'_download_permissions_granted' => 
				array (
				0 => 'yes',
				),
				'_recorded_sales' => 
				array (
				0 => 'yes',
				),
				'_recorded_coupon_usage_counts' => 
				array (
				0 => 'yes',
				),
				'_order_stock_reduced' => 
				array (
				0 => 'yes',
				),
				'_new_order_email_sent' => 
				array (
				0 => 'true',
				),
				'wpwhpro_create_post_temp_status_jobs' => 
				array (
				0 => 'wc-processing',
				),
				'_edit_lock' => 
				array (
				0 => '1641462604:1',
				),
				'_edit_last' => 
				array (
				0 => '1',
				),
			),
			'wpwh_tax_data' => 
			array (
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.