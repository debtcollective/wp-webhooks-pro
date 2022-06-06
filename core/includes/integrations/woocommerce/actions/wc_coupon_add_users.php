<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_Actions_wc_coupon_add_users' ) ) :

	/**
	 * Load the wc_coupon_add_users action
	 *
	 * @since 4.3.7
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_woocommerce_Actions_wc_coupon_add_users {

	public function get_details(){

		$translation_ident = "action-wc_coupon_add_users-content";

			$parameter = array(
				'coupon_id'		=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Set this argument to the id of the coupon. ', $translation_ident ) ),
				'user_ids'	=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Add the user_ids you want to add to the user. This argument accepts a comma-separated string, as well as a JSON construct.', $translation_ident ) ),
				'replace'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this to yes to replace the existing user_ids. If set to no, the user_ids are appended to the existing ones. Default: no', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data about the fired actions.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "In case you want to add multiple emails via user IDs to the coupon, you can either comma-separate them like <code>33,421</code>, or you can add them via a JSON construct:", $translation_ident ); ?>
<pre>{
  423,
  532,
  44
}</pre>
		<?php
		$parameter['user_ids']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>wc_coupon_add_users</strong> action was fired.", $translation_ident ); ?>
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

		$returns_code = array(
			'success' => true,
			'msg' => 'The emails of the user_ids have been successfully added.',
			'data' => 
			array (
			  'coupon_id' => 8090,
			  'user_ids' => 
			  array (
				0 => 'demo@email.test',
				1 => 'anotheremail@test.com',
				4 => '*@test.test',
			  ),
			  'replace' => false,
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Add user_ids',
			'webhook_slug' => 'wc_coupon_add_users',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>coupon_id</strong> argument. Please set it to the coupon id of the coupon you want to add the user emails to.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Please also set the <strong>user_ids</strong> argument. This argument accepts a comma-separated list of user IDs, as well as a JSON with each ID on a separate line. Please see the argument definition for further information.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'wc_coupon_add_users', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Add coupon user IDs', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'add one or multiple  emails via user IDs to a coupon', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Add one or multiple emails via user IDs to a coupon within Woocommerce.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'woocommerce',
			'premium'	   	=> true,
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'coupon_id' => 0,
					'user_ids' => array(),
					'replace' => false,
				)
			);

			$coupon_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'coupon_id' ) );
			$user_ids = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_ids' );
			$replace = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'replace' ) === 'yes' ) ? true : false;
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $coupon_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the coupon_id argument.", 'action-wc_coupon_add_users-error' );
				return $return_args;
			}

			if( empty( $user_ids ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the user_ids argument.", 'action-wc_coupon_add_users-error' );
				return $return_args;
			}

			$validated_user_ids = array();
			if( WPWHPRO()->helpers->is_json( $user_ids ) ){
                $validated_user_ids = json_decode( $user_ids, true );
            } elseif( is_array( $user_ids ) || is_object( $user_ids ) ) {
				$validated_user_ids = json_decode( json_encode( $user_ids ), true );
			} else {
				$validated_user_ids = explode( ',', $user_ids );
			}

            if( ! is_array( $validated_user_ids ) && ! empty( $validated_user_ids ) ){
                $validated_user_ids = array( $validated_user_ids );
            }

			$validated_user_emails = array();
			foreach( $validated_user_ids as $ek => $ev ){

				if( ! is_numeric( $ev ) ){
					continue;
				}

				$user = get_userdata( $ev );
				if( ! empty( $user ) && is_object( $user ) && isset( $user->user_email ) ){
					if( is_email( $user->user_email ) ){
						$validated_user_emails[] = sanitize_email( $user->user_email );
					}
				}

				
			}

            if( empty( $validated_user_emails ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "We could not locate any valid user_ids", 'action-wc_coupon_add_users-error' );
				return $return_args;
			}

			if( ! $replace ){
				$existing_user_emails = get_post_meta( $coupon_id, 'customer_email', true );
				if( ! empty( $existing_user_emails ) && is_array( $existing_user_emails ) ){

					foreach( $validated_user_emails as $email ){

						//Skip existing user_ids
						if( in_array( $email, $existing_user_emails ) ){
							continue;
						}

						$existing_user_emails[] = $email;
					}

					$validated_user_emails = $existing_user_emails;
				}
			}

			$check = update_post_meta( $coupon_id, 'customer_email', $validated_user_emails );
			
			if( $check ){
				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The emails of the user_ids have been successfully added.", 'action-wc_coupon_add_users-success' );
				$return_args['data']['coupon_id'] = $coupon_id;
				$return_args['data']['user_emails'] = $validated_user_emails;
				$return_args['data']['replace'] = $replace;
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "No emails for user_ids have been added.", 'action-wc_coupon_add_users-success' );
				$return_args['data']['coupon_id'] = $coupon_id;
				$return_args['data']['user_emails'] = $validated_user_emails;
				$return_args['data']['replace'] = $replace;
			}
			

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.