<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_restrict_content_pro_Actions_rcp_create_membership' ) ) :

	/**
	 * Load the rcp_create_membership action
	 *
	 * @since 4.3.6
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_restrict_content_pro_Actions_rcp_create_membership {

	public function get_details(){

		$translation_ident = "action-rcp_create_membership-content";

			$parameter = array(
				'user'		=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Set this argument to the id of the user. In case you do not have the user id, you can also assign the user via a given email.', $translation_ident ) ),
				'membership_level'	=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The ID of the membership level that is used for the membership.', $translation_ident ) ),
				'status'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The status slug of the membership. Default: pending', $translation_ident ) ),
				'auto_renew'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this argument to yes if you want to auto-renew the membership.', $translation_ident ) ),
				'gateway'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Define your gateway. Default: manual', $translation_ident ) ),
				'gateway_customer_id'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The gateway customer id.', $translation_ident ) ),
				'gateway_subscription_id'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The gateway subscription id.', $translation_ident ) ),
				'subscription_key'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'A subscription key (usually the transaction id). If you leave it empty, we generate it autoamtically for you.', $translation_ident ) ),
				'expiration_date'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'A custom expiration date. If you leave it empty, we calculate it based on the membership level.', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data about the fired actions.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "Please note: In order for this action to work, the user must exist beforehand.", $translation_ident ); ?>
		<?php
		$parameter['user']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "You can adjust the status based on a given status slug. Here are some commonly used values:", $translation_ident ); ?>
<ul>
	<li><strong>active</strong></li>
	<li><strong>pending</strong></li>
	<li><strong>expired</strong></li>
	<li><strong>cancelled</strong></li>
</ul>
		<?php
		$parameter['status']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>rcp_create_membership</strong> action was fired.", $translation_ident ); ?>
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
		<?php echo WPWHPRO()->helpers->translate( "The data used to create the membership.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'The membership has been successfully created.',
			'data' => 
			array (
			  'membership_id' => '14',
			  'user_id' => 140,
			  'user' => 
			  array (
				'data' => 
				array (
				  'ID' => '140',
				  'user_login' => ' jondoe',
				  'user_pass' => '$P$BGCgJiuvCNuHNSCmkWOSCP6Fw1Jq8e.',
				  'user_nicename' => ' Jon Doe',
				  'user_email' => 'jondoe@demo.test',
				  'user_url' => '',
				  'user_registered' => '2021-05-28 10:48:40',
				  'user_activation_key' => '',
				  'user_status' => '0',
				  'display_name' => ' jondoe',
				  'spam' => '0',
				  'deleted' => '0',
				),
				'ID' => 140,
				'caps' => 
				array (
				  'homey_host' => true,
				  'subscriber' => true,
				),
				'cap_key' => 'wp_capabilities',
				'roles' => 
				array (
				  0 => 'homey_host',
				  1 => 'subscriber',
				),
				'allcaps' => 
				array (
				  'read' => true,
				  'edit_posts' => false,
				  'delete_posts' => false,
				  'read_listing' => true,
				  'publish_posts' => false,
				  'edit_listing' => true,
				  'create_listings' => true,
				  'edit_listings' => true,
				  'delete_listings' => true,
				  'edit_published_listings' => true,
				  'publish_listings' => true,
				  'delete_published_listings' => true,
				  'delete_private_listings' => true,
				  'level_0' => true,
				  'read_private_locations' => true,
				  'read_private_events' => true,
				  'manage_resumes' => true,
				  'homey_host' => true,
				  'subscriber' => true,
				),
				'filter' => NULL,
			  ),
			  'membership' => 
			  array (
				'customer_id' => '12',
				'customer' => 
				array (
				  'id' => '12',
				  'user_id' => '140',
				  'date_registered' => 'February 22, 2022',
				  'email_verification_status' => 'none',
				  'last_login' => '',
				  'ips' => 
				  array (
				  ),
				  'has_trialed' => false,
				  'notes' => '',
				  'is_pending_verification' => false,
				  'has_active_membership' => true,
				  'has_paid_membership' => true,
				  'lifetime_value' => 0,
				),
				'membership_level_name' => 'Demo Level Paid',
				'currency' => 'USD',
				'initial_amount' => '10',
				'recurring_amount' => '10',
				'biling_cycle_formatted' => '&#36;10.00',
				'status' => 'active',
				'expiration_date' => 'none',
				'expiration_time' => false,
				'created_date' => 'February 22, 2022',
				'activated_date' => '2022-02-22 09:28:45',
				'trial_end_date' => NULL,
				'renewed_date' => NULL,
				'cancellation_date' => NULL,
				'times_billed' => 0,
				'maximum_renewals' => '0',
				'gateway' => 'wpwh',
				'gateway_customer_id' => '',
				'gateway_subscription_id' => '',
				'subscription_key' => '8bbc1b18ba278dc1ed922bbfa51716e4',
				'get_upgraded_from' => '0',
				'was_upgrade' => false,
				'payment_plan_completed_date' => NULL,
				'notes' => 'February 22, 2022 09:28:45 - Membership activated.',
				'signup_method' => 'live',
				'prorate_credit_amount' => 0,
				'payments' => 
				array (
				),
				'card_details' => 
				array (
				),
			  ),
			  'membership_level' => 
			  array (
				'id' => 2,
				'name' => 'Demo Level Paid',
				'description' => '',
				'is_lifetime' => true,
				'duration' => 0,
				'duration_unit' => 'day',
				'has_trial' => false,
				'trial_duration' => 0,
				'trial_duration_unit' => 'day',
				'get_price' => 10,
				'is_free' => false,
				'fee' => 0,
				'renewals' => 0,
				'access_level' => 0,
				'status' => 'active',
				'role' => 'subscriber',
				'get_date_created' => '2022-02-21 10:38:07',
			  ),
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Create membership',
			'webhook_slug' => 'rcp_create_membership',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>user</strong> argument. Please set it to the user id or user email of the user you want to create the membership for.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Please also set the <strong>membership_level</strong> argument. You should set it to the id of the membership level you want to use for this membership.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'rcp_create_membership', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Create membership', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'create a membership', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Create a membership within Restrict Content Pro.', $translation_ident ),
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
			$status		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'status' );
			$auto_renew		= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'auto_renew' ) === 'yes' ) ? true : false;
			$gateway		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'gateway' );
			$gateway_customer_id		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'gateway_customer_id' );
			$gateway_subscription_id		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'gateway_subscription_id' );
			$subscription_key		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'subscription_key' );
			$expiration_date		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'expiration_date' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $user ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the user argument to either the user id or user email of an existing user.", 'action-rcp_create_membership-error' );
				return $return_args;
			}

			if( empty( $membership_level ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the membership_level argument.", 'action-rcp_create_membership-error' );
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
                $return_args['msg'] = WPWHPRO()->helpers->translate( "We could not find a user for your given user argument value.", 'action-rcp_create_membership-error' );
				return $return_args;
            }

			$customer_id = 0;
            $customer = rcp_get_customer_by_user_id( $user_id );
			if( ! empty( $customer ) ){
				$customer_id = $customer->get_id();
			} else {
				$customer_id = rcp_add_customer( array(
					'user_id' => absint( $user_id ),
				) );
			}

			if( empty( $customer_id ) ){
                $return_args['msg'] = WPWHPRO()->helpers->translate( "There was an issue retrieving the customer id.", 'action-rcp_create_membership-error' );
				return $return_args;
            }

			$data = array(
				'customer_id'             => $customer_id,
				'user_id'                 => $user_id,
				'object_id'               => $membership_level,
				'object_type'             => 'membership',
				'created_date'            => current_time( 'mysql' ),
				'auto_renew'              => ( $auto_renew ) ? 1 : 0,
				'status'                  => ( $status ) ? $status : 'pending',
				'gateway'                 => ( $gateway ) ? $gateway : 'manual',
				'gateway_customer_id'     => $gateway_customer_id,
				'gateway_subscription_id' => $gateway_subscription_id,
				'subscription_key'        => ( $subscription_key ) ? $subscription_key : rcp_generate_subscription_key(),
			);

			switch( $status ){
				case 'active':
					$data['expiration_date'] = rcp_calculate_subscription_expiration( $membership_level );
					break;
				case 'expired':
					$data['expiration_date'] = current_time( 'mysql' );
					break;
				case 'cancelled':
					$data['cancellation_date'] = current_time( 'mysql' );
					break;
			}

			if( $expiration_date ){
				$data['expiration_date'] = WPWHPRO()->helpers->get_formatted_date( $expiration_date, 'Y-m-d H:i:s' );
			}

			$membership_id = rcp_add_membership( $data );

			if( ! empty( $membership_id ) ){
				$membership = rcp_get_membership( $membership_id );
				$rcp_helpers = WPWHPRO()->integrations->get_helper( 'restrict-content-pro', 'rcp_helpers' );

				$return_args['data'] = $rcp_helpers->build_payload( $membership );
				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The membership has been successfully created.", 'action-rcp_create_membership-success' );
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "There was an issue creating the membership.", 'action-rcp_create_membership-error' );
				return $return_args;
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $data );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.