<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_Triggers_wc_product_added_to_cart' ) ) :

 /**
  * Load the wc_product_added_to_cart trigger
  *
  * @since 4.3.7
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_woocommerce_Triggers_wc_product_added_to_cart {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'woocommerce_add_to_cart',
				'callback' => array( $this, 'wc_product_added_to_cart_callback' ),
				'priority' => 20,
				'arguments' => 6,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "trigger-wc_product_added_to_cart-description";

		$parameter = array(
			'cart_item_key' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The key of the current cart item.', $translation_ident ) ),
			'product_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the added product.', $translation_ident ) ),
			'quantity' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The quantity of the product.', $translation_ident ) ),
			'variation_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The variation id of the product (in case given).', $translation_ident ) ),
			'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the user who vies it (in case given)', $translation_ident ) ),
			'variation' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further details about the variation.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Product added to cart',
			'webhook_slug' => 'wc_product_added_to_cart',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'woocommerce_add_to_cart',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'You can fire this trigger on specific products only. To do that, select the products within the webhook URL settings.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_woocommerce_trigger_on_product' => array(
					'id'		  => 'wpwhpro_woocommerce_trigger_on_product',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'posts',
						'args'		=> array(
							'post_type' => 'product',
							'post_status' => array( 'private', 'publish' ),
						)
					),
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected products', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the products you want to fire the trigger on. If none is selected, all are triggered.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'wc_product_added_to_cart',
			'name'			  => WPWHPRO()->helpers->translate( 'Product added to cart', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a product was added to the cart', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a product was added to the cart within Woocommerce.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'woocommerce',
			'premium'		   => true,
		);

	}

	/**
	 * Triggers once a coupon was created
	 *
	 * @param mixed $arg
	 */
	public function wc_product_added_to_cart_callback( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wc_product_added_to_cart' );
		$user_id = get_current_user_id();
		$payload = array(
			'cart_item_key' => $cart_item_key,
			'product_id' => $product_id,
			'quantity' => $quantity,
			'variation_id' => $variation_id,
			'user_id' => $user_id,
			'variation' => $variation,
		);

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){

				if( $is_valid && isset( $webhook['settings']['wpwhpro_woocommerce_trigger_on_product'] ) && is_array( $webhook['settings']['wpwhpro_woocommerce_trigger_on_product'] ) ){
					if( ! in_array( $product_id, $webhook['settings']['wpwhpro_woocommerce_trigger_on_product'] ) ){
						$is_valid = false;
					}
				}

			}

			if( $is_valid ){
				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
					$payload_track[] = $payload;
				} else {
					$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				}
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_wc_product_added_to_cart', $payload, $response_data_array, $payload_track );
	}
	
	public function get_demo( $options = array() ) {

		$data = array (
			'cart_item_key' => 'a95aa4e62b22c9bc5bca4e83cadfaa82',
			'product_id' => 8096,
			'quantity' => 1,
			'variation_id' => 0,
			'user_id' => 1,
			'variation' => 
			array (
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.