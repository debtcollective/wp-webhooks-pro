<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_restrict_content_pro_Actions_rcp_cancel_membership' ) ) :

	/**
	 * Load the rcp_cancel_membership action
	 *
	 * @since 4.3.6
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_restrict_content_pro_Actions_rcp_cancel_membership {

	public function get_details(){

		$translation_ident = "action-rcp_cancel_membership-content";

			$parameter = array(
				'user'		=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Set this argument to the id of the user. In case you do not have the user id, you can also assign the user via a given email.', $translation_ident ) ),
				'membership_level'	=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The ID of the membership level that you want to cancel. Set this argument to all to cancel all memberships.', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data about the fired actions.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "You can set this argument to <code>all</code> to cancel all memberships for the user instead.", $translation_ident ); ?>
		<?php
		$parameter['membership_level']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>rcp_cancel_membership</strong> action was fired.", $translation_ident ); ?>
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
		<?php echo WPWHPRO()->helpers->translate( "The data used to cancel the membership.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'The memberships have been successfully cancelled.',
			'data' => 
			array (
			  'customer_id' => '12',
			  'user_id' => 140,
			  'cancelled' => array(
				  14
			  ),
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Cancel membership',
			'webhook_slug' => 'rcp_cancel_membership',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>user</strong> argument. Please set it to the user id or user email of the user you want to create the membership for.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Please also set the <strong>membership_level</strong> argument. You should set it to the id of the membership level you want to use for this membership.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'rcp_cancel_membership', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Cancel user membership', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'cancel one or all user memberships', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Cancel one or all memberships for a user within Restrict Content Pro.', $translation_ident ),
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
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $user ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the user argument to either the user id or user email of an existing user.", 'action-rcp_cancel_membership-error' );
				return $return_args;
			}

			if( empty( $membership_level ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the membership_level argument.", 'action-rcp_cancel_membership-error' );
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
                $return_args['msg'] = WPWHPRO()->helpers->translate( "We could not find a user for your given user argument value.", 'action-rcp_cancel_membership-error' );
				return $return_args;
            }

            $customer = rcp_get_customer_by_user_id( $user_id );

			if( empty( $customer ) ){
                $return_args['msg'] = WPWHPRO()->helpers->translate( "There was an issue retrieving the customer.", 'action-rcp_cancel_membership-error' );
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

			$cancelled = array();
			if( ! empty( $memberships ) ){
				foreach( $memberships as $membership ){

					if( $membership->can_cancel() ){
						$membership->cancel_payment_profile();
					}

					//Make sure the membership is properly cancelled, even if the payment profile wasn't adjusted.
					$membership->cancel();
					$cancelled[] = intval( $membership->get_id() );
				}
			}

			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "The memberships have been successfully cancelled.", 'action-rcp_cancel_membership-success' );
			$return_args['data']['customer_id'] = $customer->get_id();
			$return_args['data']['user_id'] = $user_id;
			$return_args['data']['cancelled'] = $cancelled;

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.