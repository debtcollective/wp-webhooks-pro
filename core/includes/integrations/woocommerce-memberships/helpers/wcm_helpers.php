<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_memberships_Helpers_wcm_helpers' ) ) :

	/**
	 * Load the WooCommerce Memberships helpers
	 *
	 * @since 4.3.7
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_woocommerce_memberships_Helpers_wcm_helpers {

		public function get_membership_plans(){

			$validated_plans = array();

			if( ! function_exists('wc_memberships_get_membership_plans') ){
				return $validated_plans; 
			}

			$membership_plans = wc_memberships_get_membership_plans();

			if( ! empty( $membership_plans ) ){
				foreach( $membership_plans as $plan ){
					$validated_plans[ $plan->get_id() ] = $plan->get_name();
				}
			}

			return $validated_plans;
		}

		public function get_query_membership_plans( $entries, $query_args, $args ){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

            $plan_items = $this->get_membership_plans();

			foreach( $plan_items as $name => $title ){

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

		/**
		 * The equivalent function of the WooCommerce membership get_formatted_item_data()
		 *
		 * @since 5.2
		 *
		 * @param null|int|\WP_Post|\WC_Memberships_User_Membership $user_membership user membership
		 * @param null|\WP_REST_Response optional response object
		 * @return array associative array of data
		 */
		public function get_formatted_item_data( $user_membership ) {

			if ( is_numeric( $user_membership ) || $user_membership instanceof \WP_Post ) {
				$user_membership = wc_memberships_get_user_membership( $user_membership );
			}

			if ( $user_membership instanceof \WC_Memberships_User_Membership ) {

				$datetime_format = 'Y-m-d\TH:i:s';
				$order           = $user_membership->get_order();
				$product         = $user_membership->get_product( true );
				$data            = [
					'id'                 => $user_membership->get_id(),
					'customer_id'        => $user_membership->get_user_id(),
					'plan_id'            => $user_membership->get_plan_id(),
					'status'             => $user_membership->get_status(),
					'order_id'           => $order   ? $order->get_id()   : null,
					'product_id'         => $product ? $product->get_id() : null,
					'date_created'       => wc_memberships_format_date( $user_membership->post->post_date, $datetime_format ),
					'date_created_gmt'   => wc_memberships_format_date( $user_membership->post->post_date_gmt, $datetime_format ),
					'start_date'         => $user_membership->get_local_start_date( $datetime_format ),
					'start_date_gmt'     => $user_membership->get_start_date( $datetime_format ),
					'end_date'           => $user_membership->get_local_end_date( $datetime_format ),
					'end_date_gmt'       => $user_membership->get_end_date( $datetime_format ),
					'paused_date'        => $user_membership->get_local_paused_date( $datetime_format ),
					'paused_date_gmt'    => $user_membership->get_paused_date( $datetime_format ),
					'cancelled_date'     => $user_membership->get_local_cancelled_date( $datetime_format ),
					'cancelled_date_gmt' => $user_membership->get_cancelled_date( $datetime_format ),
					'view_url'           => $user_membership->get_view_membership_url(),
					'profile_fields'     => [],
					'meta_data'          => $this->prepare_item_meta_data( $user_membership ),
				];

				$profile_fields = $user_membership->get_profile_fields();

				foreach ( $profile_fields as $profile_field ) {

					$data['profile_fields'][] = [
						'slug'  => $profile_field->get_slug(),
						'value' => $profile_field->get_value(),
					];
				}

			} else {

				$data            = [];
				$user_membership = null;
			}

			/**
			 * Filters the user membership data for the REST API.
			 *
			 * @since 1.11.0
			 *
			 * @param array $data associative array of membership data
			 * @param null|\WC_Memberships_User_Membership $user_membership membership object or null if undetermined
			 * @param null|\WP_REST_Request optional request object
			 */
			return (array) apply_filters( 'wc_memberships_rest_api_user_membership_data', $data, $user_membership, null );
		}

		/**
		 * The copied prepare_item_meta_data() function from Within WooCommerce Memberships
		 *
		 * @since 5.2
		 *
		 * @param \WC_Memberships_User_Membership|\WC_Memberships_Membership_Plan $object membership object
		 * @return array associative array of formatted meta data
		 */
		protected function prepare_item_meta_data( $object ) {
			global $wpdb;

			$formatted = array();
			$raw_meta  = $wpdb->get_results( $wpdb->prepare("
				SELECT * FROM $wpdb->postmeta
				WHERE post_id  = %d
			", $object->get_id() ) );

			if ( ! empty( $raw_meta ) ) {

				$post_type        = 'wc_user_membership';
				$wp_internal_keys = array(
					'_edit_lock',
					'_edit_last',
					'_wp_old_slug',
				);

				if ( 'wc_membership_plan' === $post_type ) {
					$object_name = 'membership_plan';
				} elseif ( 'wc_user_membership' === $post_type ) {
					$object_name = 'user_membership';
				} else {
					$object_name = $post_type;
				}

				/**
				 * Filters the list of meta data keys to exclude from REST API responses.
				 *
				 * @since 1.11.0
				 *
				 * @param array $excluded_keys keys to exclude from memberships item meta data list
				 * @param \WC_Memberships_User_Membership|\WC_Memberships_Membership_Plan $object memberships object
				 */
				$excluded_keys = apply_filters( "wc_memberships_rest_api_{$object_name}_excluded_meta_keys", array_merge( $object->get_meta_keys(), $wp_internal_keys ), $object );

				foreach( $raw_meta as $meta_object ) {

					if ( empty( $excluded_keys ) || ! in_array( $meta_object->meta_key, $excluded_keys, true ) ) {

						$formatted[] = array(
							'id'    => (int) $meta_object->meta_id,
							'key'   => (string) $meta_object->meta_key,
							'value' => $meta_object->meta_value,
						);
					}
				}
			}

			return $formatted;
		}

	}

endif; // End if class_exists check.