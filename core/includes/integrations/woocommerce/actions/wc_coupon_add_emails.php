<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_Actions_wc_coupon_add_emails' ) ) :

	/**
	 * Load the wc_coupon_add_emails action
	 *
	 * @since 4.3.7
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_woocommerce_Actions_wc_coupon_add_emails {

	public function get_details(){

		$translation_ident = "action-wc_coupon_add_emails-content";

			$parameter = array(
				'coupon_id'		=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Set this argument to the id of the coupon. ', $translation_ident ) ),
				'emails'	=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Add the emails you want to add to the user. This argument accepts a comma-separated string, as well as a JSON construct.', $translation_ident ) ),
				'replace'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this to yes to replace the existing emails. If set to no, the emails are appended to the existing ones. Default: no', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data about the fired actions.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "In case you want to add multiple emails to the coupon, you can either comma-separate them like <code>test@email.com,another@email.com</code>, or you can add them via a JSON construct:", $translation_ident ); ?>
<pre>{
  demo@email.test,
  anotheremail@test.com,
  *@test.test
}</pre>
<?php echo WPWHPRO()->helpers->translate( "You can also use an asterisk (*) to match parts of an email. For example \"*@gmail.com\" would match all gmail addresses.", $translation_ident ); ?>
		<?php
		$parameter['emails']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>wc_coupon_add_emails</strong> action was fired.", $translation_ident ); ?>
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
			'msg' => 'The emails have been successfully added.',
			'data' => 
			array (
			  'coupon_id' => 8090,
			  'emails' => 
			  array (
				0 => 'demo@email.test',
				1 => 'anotheremail@test.com',
				4 => '*@test.test',
			  ),
			  'replace' => false,
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Add emails',
			'webhook_slug' => 'wc_coupon_add_emails',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>coupon_id</strong> argument. Please set it to the coupon id of the coupon you want to add the user emails to.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Please also set the <strong>emails</strong> argument. This argument accepts a comma-separated list of emails, as well as a JSON with each email on a separate line. Please see the argument definition for further information.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'wc_coupon_add_emails', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Add coupon emails', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'add one or multiple emails to a coupon', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Add one or multiple emails to a coupon within Woocommerce.', $translation_ident ),
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
					'emails' => array(),
					'replace' => false,
				)
			);

			$coupon_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'coupon_id' ) );
			$emails = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'emails' );
			$replace = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'replace' ) === 'yes' ) ? true : false;
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $coupon_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the coupon_id argument.", 'action-wc_coupon_add_emails-error' );
				return $return_args;
			}

			if( empty( $emails ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the emails argument.", 'action-wc_coupon_add_emails-error' );
				return $return_args;
			}

			$validated_emails = array();
			if( WPWHPRO()->helpers->is_json( $emails ) ){
                $validated_emails = json_decode( $emails, true );
            } elseif( is_array( $emails ) || is_object( $emails ) ) {
				$validated_emails = json_decode( json_encode( $emails ), true );
			} else {
				$validated_emails = explode( ',', $emails );
			}

            if( ! is_array( $validated_emails ) && ! empty( $validated_emails ) ){
                $validated_emails = array( $validated_emails );
            }

			$asterisk_replacement = "wpwhasteriskreplacement";
			foreach( $validated_emails as $ek => $ev ){

				$temp_email = str_replace( '*', $asterisk_replacement, $ev );

				if( is_email( $temp_email ) ){
					$validated_emails[ $ek ] = str_replace( $asterisk_replacement, '*', sanitize_email( $ev ) );
				} else {
					unset( $validated_emails[ $ek ] );
				}
			}

            if( empty( $validated_emails ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "We could not locate any valid emails", 'action-wc_coupon_add_emails-error' );
				return $return_args;
			}

			if( ! $replace ){
				$existing_emails = get_post_meta( $coupon_id, 'customer_email', true );
				if( ! empty( $existing_emails ) && is_array( $existing_emails ) ){

					foreach( $validated_emails as $email ){

						//Skip existing emails
						if( in_array( $email, $existing_emails ) ){
							continue;
						}

						$existing_emails[] = $email;
					}

					$validated_emails = $existing_emails;
				}
			}

			$check = update_post_meta( $coupon_id, 'customer_email', $validated_emails );
			
			if( $check ){
				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The emails have been successfully added.", 'action-wc_coupon_add_emails-success' );
				$return_args['data']['coupon_id'] = $coupon_id;
				$return_args['data']['emails'] = $validated_emails;
				$return_args['data']['replace'] = $replace;
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "No emails have been added.", 'action-wc_coupon_add_emails-success' );
				$return_args['data']['coupon_id'] = $coupon_id;
				$return_args['data']['emails'] = $validated_emails;
				$return_args['data']['replace'] = $replace;
			}
			

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.