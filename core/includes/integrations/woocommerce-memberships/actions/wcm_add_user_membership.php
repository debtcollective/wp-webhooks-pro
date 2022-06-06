<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_memberships_Actions_wcm_add_user_membership' ) ) :

	/**
	 * Load the wcm_add_user_membership action
	 *
	 * @since 4.3.7
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_woocommerce_memberships_Actions_wcm_add_user_membership {

	public function get_details(){

		$translation_ident = "action-wcm_add_user_membership-content";

			$parameter = array(
				'user'		=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The user you want to assign the membership to. This argument accepts either the user ID or the user email.', $translation_ident ) ),
				'membership_plan_id'	=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The membership plan id.', $translation_ident ) ),
				'product_id'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'A product ID if you want to connect the memberhip with a product.', $translation_ident ) ),
				'order_id'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The order ID if you want to connect the membership with an order.', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data about the fired actions.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>wcm_add_user_membership</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 1 );
function my_custom_callback_function( $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "All the values that are sent back as a response to the initial webhook action caller.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'The user membership has been successfully created.',
			'data' => 
			array (
			  'membership_id' => 9146,
			  'membership_plan_id' => 9143,
			  'user_id' => 8,
			  'product_id' => 0,
			  'order_id' => 0,
			),
		  );

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Add user membership',
			'webhook_slug' => 'wcm_add_user_membership',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>user</strong> argument. Please set it either to the user ID or the user email.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Please also set the <strong>membership_plan_id</strong> argument to the membership plan you want to connect the membership to.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'wcm_add_user_membership', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Add user membership', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'add a user membership', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Add a user membership within WooCommerce Memberships.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'woocommerce-memberships',
			'premium'	   	=> true,
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'membership_id' => 0,
					'membership_plan_id' => 0,
					'user_id' => 0,
					'product_id' => 0,
					'order_id' => 0,
				)
			);

			$user = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$membership_plan_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'membership_plan_id' ) );
			$product_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'product_id' ) );
			$order_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'order_id' ) );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $user ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the user argument.", 'action-wcm_add_user_membership-error' );
				return $return_args;
			}

			if( empty( $membership_plan_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the membership_plan_id argument.", 'action-wcm_add_user_membership-error' );
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
                $return_args['msg'] = WPWHPRO()->helpers->translate( "We could not find a user for your given user id.", 'action-wpfs_add_tags-error' );
				return $return_args;
            }

            if( wc_memberships_is_user_member( $user_id, $membership_plan_id ) ){
                $return_args['msg'] = WPWHPRO()->helpers->translate( "Your user is already a member of that membership plan.", 'action-wpfs_add_tags-error' );
				return $return_args;
            }

			$args = array(
				'plan_id' => $membership_plan_id,
				'user_id' => $user_id
			);

			if( ! empty( $product_id ) ){
				$args['product_id'] = $product_id;
			}

			if( ! empty( $order_id ) ){
				$args['order_id'] = $order_id;
			}

			$user_membership = wc_memberships_create_user_membership( $args );
			
			if( $user_membership ){
				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The user membership has been successfully created.", 'action-wcm_add_user_membership-success' );
				$return_args['data']['membership_id'] = $user_membership->get_id();
				$return_args['data']['membership_plan_id'] = $membership_plan_id;
				$return_args['data']['user_id'] = $user_id;
				$return_args['data']['product_id'] = $product_id;
				$return_args['data']['order_id'] = $order_id;
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "An error occured while creating the membership.", 'action-wcm_add_user_membership-success' );
				$return_args['data']['membership_plan_id'] = $membership_plan_id;
				$return_args['data']['user_id'] = $user_id;
				$return_args['data']['product_id'] = $product_id;
				$return_args['data']['order_id'] = $order_id;
			}
			

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.