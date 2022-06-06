<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_subscriptions_Helpers_wcs_helpers' ) ) :

	/**
	 * Load the WooCommerce Subscriptions helpers
	 *
	 * @since 5.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_woocommerce_subscriptions_Helpers_wcs_helpers {

		public function get_query_statuses( $entries, $query_args, $args ){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

            $statuses = wcs_get_subscription_statuses();

			foreach( $statuses as $name => $title ){

				//skip search values that don't occur if set
				if( isset( $args['s'] ) && $args['s'] !== '' ){
					if( 
						strpos( $name, $args['s'] ) === false
						&& strpos( $title, $args['s'] ) === false
					){
						continue;
					}
				}

				//skip unselected values in a selected statement
				if( isset( $args['selected'] ) && ! empty( $args['selected'] ) ){
					if( ! in_array( $name, $args['selected'] ) ){
						continue;
					}
				}

				$entries['items'][ $name ] = array(
					'value' => $name,
					'label' => $title,
				);
			}

			//calculate total
			$entries['total'] = count( $entries['items'] );

			//set all items to be visible on one page
			$entries['per_page'] = count( $entries['items'] );

			return $entries;
		}

		public function get_subscription_array( $subscription ){

			if( is_numeric( $subscription ) ){
				$subscription = wcs_get_subscription( $subscription );
			}

			$subscription_data = array(
				'subscription_id' => $subscription->get_id(),
				'user_id' => $subscription->get_user_id(),
				'products' => array(),
				'billing_period' => $subscription->get_billing_period(),
				'billing_interval' => $subscription->get_billing_interval(),
				'trial_period' => $subscription->get_trial_period(),
				'date_created' => $subscription->get_date('date_created'),
				'date_modified' => $subscription->get_date('date_modified'),
				'view_order_url' => $subscription->get_view_order_url(),
				'is_download_permitted' => $subscription->is_download_permitted(),
				'sign_up_fee' => $subscription->get_sign_up_fee(),
				'start_date' => wc_rest_prepare_date_response( $subscription->get_date( 'start_date' ) ),
				'trial_end' => wc_rest_prepare_date_response( $subscription->get_date( 'trial_end' ) ),
				'next_payment' => wc_rest_prepare_date_response( $subscription->get_date( 'next_payment' ) ),
				'end_date' => wc_rest_prepare_date_response( $subscription->get_date( 'end_date' ) ),
				'date_completed_gmt' => wc_rest_prepare_date_response( $subscription->get_date_completed() ),
				'date_paid_gmt' => wc_rest_prepare_date_response( $subscription->get_date_paid() ),
				'last_order_id' => $subscription->get_last_order( 'ids', 'parent' ),
				'renewal_order_ids' => $subscription->get_last_order( 'ids', 'renewal' ),
			);
	
			$items = $subscription->get_items();
			if( ! empty( $items ) ){
				foreach ( $items as $item_id => $item ) {
					$product      = $item->get_product();
					$product_id   = 0;
					$variation_id = 0;
					$product_sku  = null;
	
					// Check if the product exists.
					if ( is_object( $product ) ) {
						$product_id   = $item->get_product_id();
						$variation_id = $item->get_variation_id();
						$product_sku  = $product->get_sku();
					}
	
					$item_meta = array();
	
					$hideprefix = 'true' === $request['all_item_meta'] ? null : '_';
	
					foreach ( $item->get_formatted_meta_data( $hideprefix, true ) as $meta_key => $formatted_meta ) {
						$item_meta[] = array(
							'key'   => $formatted_meta->key,
							'label' => $formatted_meta->display_key,
							'value' => wc_clean( $formatted_meta->display_value ),
						);
					}
	
					$line_item = array(
						'id'           => $item_id,
						'name'         => $item['name'],
						'sku'          => $product_sku,
						'product_id'   => (int) $product_id,
						'variation_id' => (int) $variation_id,
						'quantity'     => wc_stock_amount( $item['qty'] ),
						'tax_class'    => ! empty( $item['tax_class'] ) ? $item['tax_class'] : '',
						'price'        => wc_format_decimal( $subscription->get_item_total( $item, false, false ), $decimal_places ),
						'subtotal'     => wc_format_decimal( $subscription->get_line_subtotal( $item, false, false ), $decimal_places ),
						'subtotal_tax' => wc_format_decimal( $item['line_subtotal_tax'], $decimal_places ),
						'total'        => wc_format_decimal( $subscription->get_line_total( $item, false, false ), $decimal_places ),
						'total_tax'    => wc_format_decimal( $item['line_tax'], $decimal_places ),
						'taxes'        => array(),
						'meta'         => $item_meta,
					);
	
					$item_line_taxes = maybe_unserialize( $item['line_tax_data'] );
					if ( isset( $item_line_taxes['total'] ) ) {
						$line_tax = array();
	
						foreach ( $item_line_taxes['total'] as $tax_rate_id => $tax ) {
							$line_tax[ $tax_rate_id ] = array(
								'id'       => $tax_rate_id,
								'total'    => $tax,
								'subtotal' => '',
							);
						}
	
						foreach ( $item_line_taxes['subtotal'] as $tax_rate_id => $tax ) {
							$line_tax[ $tax_rate_id ]['subtotal'] = $tax;
						}
	
						$line_item['taxes'] = array_values( $line_tax );
					}
	
					$subscription_data['products'][] = $line_item;
				}
			}

			return apply_filters( 'wpwhpro/webhooks/wcs_helpers/get_subscription_array', $subscription_data, $subscription );
		}

	}

endif; // End if class_exists check.