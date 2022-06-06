<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_restrict_content_pro_Helpers_rcp_helpers' ) ) :

	/**
	 * Load the Restrict Content Pro helpers
	 *
	 * @since 4.3.6
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_restrict_content_pro_Helpers_rcp_helpers {

		public function get_membership_levels(){
			$validated_levels = array();

			$levels = rcp_get_membership_levels( array( 'number' => 999 ) );

			if( ! $levels ) {
				return $validated_levels;
			}

			foreach( $levels as $level ) {
				$validated_levels[ $level->get_id() ] = $level->get_name();
			}

			return $validated_levels;
		}

		public function get_query_levels( $entries, $query_args, $args ){
			$default_args = array(
				'number' => $entries['per_page'],
				'offset' => 0,
			);
	
			if( isset( $args['s'] ) ){
				$query_args['search'] = esc_sql( $args['s'] );
			}
	
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				$query_args['offset'] = ( intval( $args['paged'] ) - 1 ) * $default_args['number'];
			}
	
			if( isset( $args['selected'] ) && ! empty( $args['selected'] ) && is_array( $args['selected'] ) ){
	
				//since we work with selected values, make sure we display all
				$query_args['number'] = 0;
				$query_args['offset'] = 0;
	
				$selected_items =  array_map( 'intval', $args['selected'] );
	
				//remove empty items
				foreach( $selected_items as $item_key => $item_val ){
					if( empty( $item_val ) ){
						unset( $selected_items[ $item_key ] );
					}
				}
	
				$query_args['id__in'] = $selected_items;
			}
	
			$query_args = array_merge( $default_args, $query_args );	
			$levels_query = rcp_get_membership_levels( $query_args );
	
			if( ! empty( $levels_query ) && is_array( $levels_query ) ){
				foreach( $levels_query as $level ){

					$level_id = $level->get_id();

					$entries['items'][ $level_id ] = array(
						'value' => $level_id,
						'label' => $level->get_name(),
					);
				}
			}
	
			//since there is no official total, we work with a custom counter + 1
			if( isset( $args['paged'] ) && $args['paged'] > 1 ){
				$entries['total'] = ( $args['paged'] - 1 ) * $query_args['number'];
			} else {
				$entries['total'] = count( $entries['items'] );
			}
	
			if( count( $entries['items'] ) >= $query_args['number'] ){
				//here we can assume that further entries appear, so we add one to the total
				$entries['total'] += 1;
			}
	
			return $entries;
		}

        public function build_payload( $membership ){
            $membership_level = rcp_get_membership_level( $membership->get_object_id() );
			$customer = $membership->get_customer();

			$payload = array(
				'membership_id' => $membership->get_id(),
				'user_id' => $membership->get_user_id(),
				'user' => get_userdata( $membership->get_user_id() ),
				'membership' => array(
					'customer_id' => $membership->get_customer_id(),
					'customer' => array(
						'id' => $customer->get_id(),
						'user_id' => $customer->get_user_id(),
						'date_registered' => $customer->get_date_registered(),
						'email_verification_status' => $customer->get_email_verification_status(),
						'last_login' => $customer->get_last_login(),
						'ips' => $customer->get_ips(),
						'has_trialed' => $customer->has_trialed(),
						'notes' => $customer->get_notes(),
						'is_pending_verification' => $customer->is_pending_verification(),
						'has_active_membership' => $customer->has_active_membership(),
						'has_paid_membership' => $customer->has_paid_membership(),
						'lifetime_value' => $customer->get_lifetime_value(),
					),
					'membership_level_name' => $membership->get_membership_level_name(),
					'currency' => $membership->get_currency(),
					'initial_amount' => $membership->get_initial_amount(),
					'recurring_amount' => $membership->get_recurring_amount(),
					'biling_cycle_formatted' => $membership->get_formatted_billing_cycle(),
					'status' => $membership->get_status(),
					'expiration_date' => $membership->get_expiration_date(),
					'expiration_time' => $membership->get_expiration_time(),
					'created_date' => $membership->get_created_date(),
					'activated_date' => $membership->get_activated_date(),
					'trial_end_date' => $membership->get_trial_end_date(),
					'renewed_date' => $membership->get_renewed_date(),
					'cancellation_date' => $membership->get_cancellation_date(),
					'times_billed' => $membership->get_times_billed(),
					'maximum_renewals' => $membership->get_maximum_renewals(),
					'gateway' => $membership->get_gateway(),
					'gateway_customer_id' => $membership->get_gateway_customer_id(),
					'gateway_subscription_id' => $membership->get_gateway_subscription_id(),
					'subscription_key' => $membership->get_subscription_key(),
					'get_upgraded_from' => $membership->get_upgraded_from(),
					'was_upgrade' => $membership->was_upgrade(),
					'payment_plan_completed_date' => $membership->get_payment_plan_completed_date(),
					'notes' => $membership->get_notes(),
					'signup_method' => $membership->get_signup_method(),
					'prorate_credit_amount' => $membership->get_prorate_credit_amount(),
					'payments' => $membership->get_payments(),
					'card_details' => $membership->get_card_details(),
				),
				'membership_level' => array(
					'id' => $membership_level->get_id(),
					'name' => $membership_level->get_name(),
					'description' => $membership_level->get_description(),
					'is_lifetime' => $membership_level->is_lifetime(),
					'duration' => $membership_level->get_duration(),
					'duration_unit' => $membership_level->get_duration_unit(),
					'has_trial' => $membership_level->has_trial(),
					'trial_duration' => $membership_level->get_trial_duration(),
					'trial_duration_unit' => $membership_level->get_trial_duration_unit(),
					'get_price' => $membership_level->get_price(),
					'is_free' => $membership_level->is_free(),
					'fee' => $membership_level->get_fee(),
					'renewals' => $membership_level->get_maximum_renewals(),
					'access_level' => $membership_level->get_access_level(),
					'status' => $membership_level->get_status(),
					'role' => $membership_level->get_role(),
					'get_date_created' => $membership_level->get_date_created(),
				),
			);

			return $payload;
        }

	}

endif; // End if class_exists check.