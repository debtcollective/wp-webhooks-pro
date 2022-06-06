<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_Triggers_wc_product_view' ) ) :

 /**
  * Load the wc_product_view trigger
  *
  * @since 4.3.7
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_woocommerce_Triggers_wc_product_view {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'template_redirect',
				'callback' => array( $this, 'wc_product_view_callback' ),
				'priority' => 20,
				'arguments' => 1,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "trigger-wc_product_view-description";

		$parameter = array(
			'product_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the viewed product.', $translation_ident ) ),
			'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the user who vies it (in case given)', $translation_ident ) ),
			'user' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The user data of the given user.', $translation_ident ) ),
			'user_meta' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The user meta of the given user.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Product viewed',
			'webhook_slug' => 'wc_product_view',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'template_redirect',
					'url' => 'https://developer.wordpress.org/reference/hooks/template_redirect/',
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
			'trigger'		   => 'wc_product_view',
			'name'			  => WPWHPRO()->helpers->translate( 'Product viewed', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a product was viewed', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a product was viewed within Woocommerce.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'woocommerce',
			'premium'		   => true,
		);

	}

	public function wc_product_view_callback(){

		global $post;

		if( is_admin() || ! $post instanceof WP_Post || $post->post_type !== 'product' ){
			return;
		}

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wc_product_view' );
		$user_id = get_current_user_id();
		$product_id = $post->ID;
		$payload = array(
			'product_id' => $product_id,
			'user_id' => $user_id,
			'user' => ( ! empty( $user_id ) ) ? get_userdata( $user_id ) : array(),
			'user_meta' => ( ! empty( $user_id ) ) ? get_user_meta( $user_id ) : array(),
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
				} else {
					$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				}
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_wc_product_view', $payload, $response_data_array );
	}
	
	public function get_demo( $options = array() ) {

		$data = array (
			'product_id' => 8096,
			'user_id' => 1,
			'user' => 
			array (
			  'data' => 
			  array (
				'ID' => '1',
				'user_login' => 'jondoe',
				'user_pass' => '$P$B4B1t8fCXXXXXXXXXXFN8GWC7EbzY1',
				'user_nicename' => 'Jon Doe',
				'user_email' => 'jondoe@demo.test',
				'user_url' => '',
				'user_registered' => '2022-07-27 23:58:11',
				'user_activation_key' => '',
				'user_status' => '0',
				'display_name' => 'Jon Doe',
				'spam' => '0',
				'deleted' => '0',
			  ),
			  'ID' => 1,
			  'caps' => 
			  array (
				'subscriber' => true,
			  ),
			  'cap_key' => 'wp_capabilities',
			  'roles' => 
			  array (
				29 => 'subscriber',
			  ),
			  'allcaps' => 
			  array (
				0 => 'read',
			  ),
			  'filter' => NULL,
			),
			'user_meta' => 
			array (
			  'nickname' => 
			  array (
				0 => 'jondoe',
			  ),
			  'first_name' => 
			  array (
				0 => 'Jon',
			  ),
			  'last_name' => 
			  array (
				0 => 'Doe',
			  ),
			  'description' => 
			  array (
				0 => '',
			  ),
			  'rich_editing' => 
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
				0 => 'false',
			  ),
			  'locale' => 
			  array (
				0 => '',
			  ),
			  'wp_capabilities' => 
			  array ()
			)
		);

		return $data;
	}

  }

endif; // End if class_exists check.