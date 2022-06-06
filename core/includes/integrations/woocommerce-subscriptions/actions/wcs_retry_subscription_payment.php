<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_subscriptions_Actions_wcs_retry_subscription_payment' ) ) :

	/**
	 * Load the wcs_retry_subscription_payment action
	 *
	 * @since 5.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_woocommerce_subscriptions_Actions_wcs_retry_subscription_payment {

		public function get_details(){

			$translation_ident = "action-wcs_retry_subscription_payment-content";

			$parameter = array(
				'order_id'		=> array( 
					'label' => WPWHPRO()->helpers->translate( 'Order ID', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( 'The order ID to retry the payment for.', $translation_ident )
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data about the fired actions.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The retry of the subscription payment has been initiated.',
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Retry subscription payment',
				'webhook_slug' => 'wcs_retry_subscription_payment',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'Please set the <strong>order_id</strong> argument to the order you want to retry the payment for.', $translation_ident ),
				),
			) );

			return array(
				'action'			=> 'wcs_retry_subscription_payment', //required
				'name'			   => WPWHPRO()->helpers->translate( 'Retry subscription payment', $translation_ident ),
				'sentence'			   => WPWHPRO()->helpers->translate( 'retry a subscription payment', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Retry a subscription payment within WooCommerce Subscriptions.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'woocommerce-subscriptions',
				'premium'	   	=> true,
			);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			$order_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'order_id' ) );
			
			if( empty( $order_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the order_id argument.", 'action-wcs_retry_subscription_payment-error' );
				return $return_args;
			}

			$last_order = ! is_object( $order_id ) ? wc_get_order( $order_id ) : $order_id;

			if( empty( $last_order ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "We could not find an order for your given order ID.", 'action-wcs_retry_subscription_payment-error' );
				return $return_args;
			}

			$last_retry = WCS_Retry_Manager::store()->get_last_retry_for_order( wcs_get_objects_property( $last_order, 'id' ) );

			if( null !== $last_retry && 'pending' !== $last_retry->get_status() ){
				$last_retry->update_status( 'pending' );
			}

			WCS_Retry_Manager::maybe_retry_payment( $order_id );
			
			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "The retry of the subscription payment has been initiated.", 'action-wcs_retry_subscription_payment-success' );

			return $return_args;
	
		}

	}

endif; // End if class_exists check.