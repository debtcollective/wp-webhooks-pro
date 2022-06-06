<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_Triggers_wc_product_review_approved' ) ) :

 /**
  * Load the wc_product_review_approved trigger
  *
  * @since 4.3.7
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_woocommerce_Triggers_wc_product_review_approved {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'comment_post',
				'callback' => array( $this, 'wc_product_review_approved_callback' ),
				'priority' => 20,
				'arguments' => 3,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "trigger-wc_product_review_approved-description";

		$parameter = array(
			'review_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the review.', $translation_ident ) ),
			'product_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the reviewed product.', $translation_ident ) ),
			'rating' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The rating of the comment.', $translation_ident ) ),
			'comment' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further details about the comment.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Product viewed',
			'webhook_slug' => 'wc_product_review_approved',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'comment_post',
					'url' => 'https://developer.wordpress.org/reference/hooks/comment_post/',
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
			'trigger'		   => 'wc_product_review_approved',
			'name'			  => WPWHPRO()->helpers->translate( 'Product review approved', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a product review has been approved', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a product has been approved within Woocommerce.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'woocommerce',
			'premium'		   => true,
		);

	}

	public function wc_product_review_approved_callback( $comment_id, $comment_approved, $comment ){

		if( $comment_approved !== 1 ){
			return;
		}

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wc_product_review_approved' );
		$product_id = ( isset( $comment[ 'comment_post_ID' ] ) ) ? $comment[ 'comment_post_ID' ] : 0;
		$rating = get_comment_meta( $comment_id, 'rating', true );
		$payload = array(
			'review_id' => $comment_id,
			'product_id' => $product_id,
			'rating' => $rating,
			'comment' => $comment,
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

		do_action( 'wpwhpro/webhooks/trigger_wc_product_review_approved', $payload, $response_data_array );
	}
	
	public function get_demo( $options = array() ) {

		$data = array (
			'review_id' => 296,
			'product_id' => 8096,
			'rating' => "4",
			'comment' => 
			array (
			  'comment_post_ID' => 8096,
			  'comment_author' => 'jondoe',
			  'comment_author_email' => 'jondoe@domain.test',
			  'comment_author_url' => '',
			  'comment_content' => 'This is a product review description.',
			  'comment_type' => 'review',
			  'comment_parent' => 0,
			  'user_ID' => 1,
			  'user_id' => 1,
			  'comment_author_IP' => '127.0.0.1',
			  'comment_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/98.0.4758.102 Safari/537.36',
			  'comment_date' => '2022-03-11 07:50:07',
			  'comment_date_gmt' => '2022-03-11 07:50:07',
			  'filtered' => true,
			  'comment_approved' => 1,
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.