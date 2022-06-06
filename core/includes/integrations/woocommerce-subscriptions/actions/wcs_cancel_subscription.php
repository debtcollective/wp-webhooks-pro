<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_subscriptions_Actions_wcs_cancel_subscription' ) ) :

	/**
	 * Load the wcs_cancel_subscription action
	 *
	 * @since 5.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_woocommerce_subscriptions_Actions_wcs_cancel_subscription {

		public function get_details(){

			$translation_ident = "action-wcs_cancel_subscription-content";

			$parameter = array(
				'user'		=> array( 
					'required' => true, 
					'label' => WPWHPRO()->helpers->translate( 'User ID/email', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( 'The user you want to assign the membership to. This argument accepts either the user ID or the user email.', $translation_ident )
				),
				'subscription_ids'		=> array( 
					'label' => WPWHPRO()->helpers->translate( 'Subscription IDs', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( 'A comma-separated list of subscription IDs for which you want to cancel the subscriptions. Please note: In case the subscription contains more products, they will be cancelled as well.', $translation_ident )
				),
				'product_ids'		=> array( 
					'label' => WPWHPRO()->helpers->translate( 'Product IDs', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( 'A comma-separated list of product IDs for which you want to cancel the subscriptions. Please note: In case the subscription contains more products, they will be cancelled as well.', $translation_ident )
				),
				'cancel_all'		=> array( 
					'type' => 'checkbox',
					'label' => WPWHPRO()->helpers->translate( 'Cancel all', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( 'Cancel all subscriptions for the given user.', $translation_ident )
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data about the fired actions.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The user subscriptions have been successfully cancelled.',
				'data' => 
				array (
				'user_id' => 8,
				'cancelled' => array(
					9278
				),
				'errors' => array(),
				),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Cancel user subscription',
				'webhook_slug' => 'wcs_cancel_subscription',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'Please set the <strong>user</strong> argument to either the user ID or the user email.', $translation_ident ),
				),
			) );

			return array(
				'action'			=> 'wcs_cancel_subscription', //required
				'name'			   => WPWHPRO()->helpers->translate( 'Cancel user subscription', $translation_ident ),
				'sentence'			   => WPWHPRO()->helpers->translate( 'cancel one or multiple user subscriptions', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Cancel one or multiple user subscriptions within WooCommerce Subscriptions.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'woocommerce-subscriptions',
				'premium'	   	=> true,
			);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'user_id' => 0,
					'cancelled' => array(),
					'errors' => array(),
				)
			);

			$user = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$subscription_ids = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'subscription_ids' );
			$product_ids = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'product_ids' );
			$cancel_all = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'cancel_all' ) === 'yes' ) ? true : false;

			if( empty( $user ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the user argument.", 'action-wcs_cancel_subscription-error' );
				return $return_args;
			}

			$validated_subscription_ids = array();
			if( ! empty( $subscription_ids ) ){
				$validated_subscription_ids = array_map( 'intval', array_map( 'trim' , explode( ',', $subscription_ids ) ) );
			}

			$validated_product_ids = array();
			if( ! empty( $product_ids ) ){
				$validated_product_ids = array_map( 'intval', array_map( 'trim' , explode( ',', $product_ids ) ) );
			}

			$user_id = 0;

            if( ! empty( $user ) && is_numeric( $user ) ){
                $user_id = intval( $user );
            } elseif( ! empty( $user ) && is_email( $user ) ) {
                $user_data = get_user_by( 'email', $user );
                if( ! empty( $user_data ) && isset( $user_data->ID ) && ! empty( $user_data->ID ) ){
                    $user_id = $user_data->ID;
                }
            }

            if( empty( $user_id ) ){
                $return_args['msg'] = WPWHPRO()->helpers->translate( "We could not find a user for your given user id/email.", 'action-wpfs_add_tags-error' );
				return $return_args;
            }

			$subscriptions = wcs_get_users_subscriptions( $user_id );

            if( empty( $subscriptions ) ){
                $return_args['msg'] = WPWHPRO()->helpers->translate( "There are no subscriptions given for your current user.", 'action-wpfs_add_tags-error' );
				return $return_args;
            }

			$subs_to_cancel = array();
			$cancelled = array();
			$errors = array();

			foreach( $subscriptions as $subscription ){
				$sub_id = $subscription->get_id();	

				if( ! $cancel_all ){
					if( in_array( $sub_id, $validated_subscription_ids ) ){
						$subs_to_cancel[] = $subscription;
						continue;
					}
				} else {
					$subs_to_cancel[] = $subscription;
				}

				$items = $subscription->get_items();
				if( ! empty( $items ) ){
					foreach( $items as $index => $item ){

						$product_id = $item->get_product_id();

						if( in_array( $product_id, $validated_product_ids ) ){
							$subs_to_cancel[] = $subscription;
							break;
						}

						
					}
				}
				
			}

			if( ! empty( $subs_to_cancel ) ){
				foreach( $subs_to_cancel as $sub ){
					if( $sub->has_status( array( 'active' ) ) ){

						if( $sub->can_be_updated_to( 'cancelled' ) ){
							$sub->update_status( 'cancelled' );
							$cancelled[] = $sub->get_id();
							break;
						} else {
							$errors[] = sprintf( WPWHPRO()->helpers->translate( "WooCommerce Subscriptions prevented the cancellation of the subscription #%$1d", 'action-wpfs_add_tags-error' ), $sub->get_id() );
						}
						
					}
				}
			}

			$return_args['data']['user_id'] = $user_id;
			
			if( empty( $errors ) ){
				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The user subscriptions have been successfully cancelled.", 'action-wcs_cancel_subscription-success' );
				$return_args['data']['cancelled'] = $cancelled;
				$return_args['data']['errors'] = $errors;
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Some of the subscription could not be cancelled.", 'action-wcs_cancel_subscription-success' );
				$return_args['data']['cancelled'] = $cancelled;
				$return_args['data']['errors'] = $errors;
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.