<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_restrict_content_pro_Actions_rcp_renew_membership' ) ) :

	/**
	 * Load the rcp_renew_membership action
	 *
	 * @since 4.3.6
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_restrict_content_pro_Actions_rcp_renew_membership {

	public function get_details(){

		$translation_ident = "action-rcp_renew_membership-content";

			$parameter = array(
				'user'		=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Set this argument to the id of the user. In case you do not have the user id, you can also assign the user via a given email.', $translation_ident ) ),
				'membership_level'	=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The ID of the membership level that you want to renew. Set this argument to all to renew all memberships.', $translation_ident ) ),
				'recurring'	=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Set this argument to yes if your membership is recurring. Default: no', $translation_ident ) ),
				'status'	=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Customize the status of the membership. Default: active', $translation_ident ) ),
				'expiration_date'	=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Set a custom expiration date for the membership. If not set, we automatically calculate the expiration date based on the membership.', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data about the fired actions.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "You can set this argument to <code>all</code> to renew all memberships for the user instead.", $translation_ident ); ?>
		<?php
		$parameter['membership_level']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>rcp_renew_membership</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 2 );
function my_custom_callback_function( $return_args, $data ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "All the values that are sent back as a response to the initial webhook action caller.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$data</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "The data used to renew the membership.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array(
			"success" => true,
			"msg" => "The memberships have been successfully renewed.",
			"data" => array(
				"customer_id" => "12",
				"user_id" => 140,
				"renewed" => [
					14
				],
				"renewed_objects" => [
					array(
						"membership_id" => "14",
						"user_id" => 140,
						"user" => array(
							"data" => array(
								"ID" => "140",
								"user_login" => "jondoe",
								"user_pass" => "THE_HASHED_USER_PASSWORD",
								"user_nicename" => "Jon Doe",
								"user_email" => "jondoe@demo.test",
								"user_url" => "",
								"user_registered" => "2021-05-28 10:48:40",
								"user_activation_key" => "",
								"user_status" => "0",
								"display_name" => "jondoe",
								"spam" => "0",
								"deleted" => "0"
							),
							"ID" => 140,
							"caps" => array(
								"homey_host" => true,
								"subscriber" => true
							),
							"cap_key" => "wp_capabilities",
							"roles" => [
								"homey_host",
								"subscriber"
							],
							"allcaps" => array(
								"read" => true,
								"edit_posts" => false,
								"delete_posts" => false,
								"read_listing" => true,
								"publish_posts" => false,
								"edit_listing" => true,
								"create_listings" => true,
								"edit_listings" => true,
								"delete_listings" => true,
								"edit_published_listings" => true,
								"publish_listings" => true,
								"delete_published_listings" => true,
								"delete_private_listings" => true,
								"level_0" => true,
								"read_private_locations" => true,
								"read_private_events" => true,
								"manage_resumes" => true,
								"homey_host" => true,
								"subscriber" => true
							),
							"filter" => null
						),
						"membership" => array(
							"customer_id" => "12",
							"customer" => array(
								"id" => "12",
								"user_id" => "140",
								"date_registered" => "February 22, 2022",
								"email_verification_status" => "none",
								"last_login" => "",
								"ips" => [],
								"has_trialed" => false,
								"notes" => "",
								"is_pending_verification" => false,
								"has_active_membership" => true,
								"has_paid_membership" => true,
								"lifetime_value" => 0
							),
							"membership_level_name" => "Demo Level Paid",
							"currency" => "USD",
							"initial_amount" => "10",
							"recurring_amount" => "10",
							"biling_cycle_formatted" => "&#36;10.00",
							"status" => "active",
							"expiration_date" => "none",
							"expiration_time" => false,
							"created_date" => "February 22, 2022",
							"activated_date" => "2022-02-22 09:28:45",
							"trial_end_date" => null,
							"renewed_date" => "February 26, 2022",
							"cancellation_date" => "February 22, 2022",
							"times_billed" => 0,
							"maximum_renewals" => "0",
							"gateway" => "wpwh",
							"gateway_customer_id" => "",
							"gateway_subscription_id" => "",
							"subscription_key" => "8bbc1b18ba278dc1ed922bbfa51716e4",
							"get_upgraded_from" => "0",
							"was_upgrade" => false,
							"payment_plan_completed_date" => null,
							"notes" => "February 22, 2022 09:28:45 - Membership activated.\n\nFebruary 22, 2022 10:16:32 - Status changed from active to cancelled.\n\nFebruary 26, 2022 07:54:17 - Status changed from cancelled to expired.\n\nFebruary 26, 2022 07:54:17 - Expiration Date changed from  to 2022-02-25 07:54:17.\n\nFebruary 26, 2022 07:54:52 - Membership disabled.\n\nFebruary 26, 2022 08:16:37 - Expiration Date changed from 2022-02-25 07:54:17 to .\n\nFebruary 26, 2022 08:16:37 - Status changed from expired to active.\n\nFebruary 26, 2022 08:16:37 - Membership renewed.",
							"signup_method" => "live",
							"prorate_credit_amount" => 0,
							"payments" => [],
							"card_details" => []
						),
						"membership_level" => array(
							"id" => 2,
							"name" => "Demo Level Paid",
							"description" => "",
							"is_lifetime" => true,
							"duration" => 0,
							"duration_unit" => "day",
							"has_trial" => false,
							"trial_duration" => 0,
							"trial_duration_unit" => "day",
							"get_price" => 10,
							"is_free" => false,
							"fee" => 0,
							"renewals" => 0,
							"access_level" => 0,
							"status" => "active",
							"role" => "subscriber",
							"get_date_created" => "2022-02-21 10:38:07"
						)
					)
				]
			)
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Renew membership',
			'webhook_slug' => 'rcp_renew_membership',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>user</strong> argument. Please set it to the user id or user email of the user you want to create the membership for.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Please also set the <strong>membership_level</strong> argument. You should set it to the id of the membership level you want to use for this membership.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'rcp_renew_membership', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Renew user membership', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'renew one or all user memberships', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Renew one or all memberships for a user within Restrict Content Pro.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'restrict-content-pro',
			'premium'	   	=> true,
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array()
			);

			$user		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$membership_level		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'membership_level' );
			$recurring		= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'recurring' ) === 'yes' ) ? true : false;
			$status		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'status' );
			$expiration_date		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'expiration_date' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $user ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the user argument to either the user id or user email of an existing user.", 'action-rcp_renew_membership-error' );
				return $return_args;
			}

			if( empty( $membership_level ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the membership_level argument.", 'action-rcp_renew_membership-error' );
				return $return_args;
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
                $return_args['msg'] = WPWHPRO()->helpers->translate( "We could not find a user for your given user argument value.", 'action-rcp_renew_membership-error' );
				return $return_args;
            }

            $customer = rcp_get_customer_by_user_id( $user_id );

			if( empty( $customer ) ){
                $return_args['msg'] = WPWHPRO()->helpers->translate( "There was an issue retrieving the customer.", 'action-rcp_renew_membership-error' );
				return $return_args;
            }

			if( $membership_level === 'all' ){
				$memberships = rcp_get_memberships( array(
					'customer_id' => absint( $customer->get_id() ),
					'number'      => 999,
				) );
			} else {
				$memberships = rcp_get_memberships( array(
					'customer_id' => absint( $customer->get_id() ),
					'object_id'   => $membership_level,
					'number'      => 999,
				) );
			}

			if( empty( $status ) ){
				$status = 'active';
			}

			if( empty( $expiration_date ) ){
				$expiration_date = '';
			} else {
				$expiration_date = WPWHPRO()->helpers->get_formatted_date( $expiration_date, 'Y-m-d H:i:s' );
			}

			$rcp_helpers = WPWHPRO()->integrations->get_helper( 'restrict-content-pro', 'rcp_helpers' );

			$renewed = array();
			$renewed_objects = array();
			if( ! empty( $memberships ) ){
				foreach( $memberships as $membership ){
					$membership->renew( $recurring, $status, $expiration_date );
					$renewed[] = intval( $membership->get_id() );
					$renewed_objects[] = $rcp_helpers->build_payload( $membership );
				}
			}

			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "The memberships have been successfully renewed.", 'action-rcp_renew_membership-success' );
			$return_args['data']['customer_id'] = $customer->get_id();
			$return_args['data']['user_id'] = $user_id;
			$return_args['data']['renewed'] = $renewed;
			$return_args['data']['renewed_objects'] = $renewed_objects;

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.