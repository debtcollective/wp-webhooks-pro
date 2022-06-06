<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_Actions_wpwh_verify_trigger_signature' ) ) :

	/**
	 * Load the wpwh_verify_trigger_signature action
	 *
	 * @since 5.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_Actions_wpwh_verify_trigger_signature {

	public function get_details(){

		$translation_ident = "action-wpwh_verify_trigger_signature-content";

			$parameter = array(
				'trigger_name'			=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The name of the chosen trigger.', $translation_ident ) ),
				'trigger_url_name'		=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The name ot the chosen trigger URL.', $translation_ident ) ),
				'trigger_signature'	=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The signature of the trigger you would like to verify.', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "You will find the trigger signature within the headers of the sent request. The header key is called: x-wp-webhook-signature", $translation_ident ); ?>
		<?php
		$parameter['trigger_signature']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>wpwh_verify_trigger_signature</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 2 );
function my_custom_callback_function( $return_args, $trigger ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "All the values that are sent back as a response to the the initial webhook action caller.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$trigger</strong> (string)<br>
		<?php echo WPWHPRO()->helpers->translate( "The trigger that gets validated.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'The trigger signature is valid.',
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Verify trigger signature',
			'webhook_slug' => 'wpwh_verify_trigger_signature',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>trigger_name</strong> argument. Please set it to the trigger slug of your choice (e.g. post_update)', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Please also set the <strong>trigger_url_name</strong> argument to the trigger name of the trigger you want to validate.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Lastly, please set the <strong>trigger_signature</strong> argument to the signature of the webhook request you want to verify.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'wpwh_verify_trigger_signature', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Verify trigger signature', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'verify the signature of a trigger', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Verify the signature of a trigger URL from the "Send Data" tab.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'wp-webhooks'
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			$trigger_name		= sanitize_title( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'trigger_name' ) );
			$trigger_url_name	= sanitize_title( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'trigger_url_name' ) );
			$trigger_signature	= strtr( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'trigger_signature' ), '._-', '+/=' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $trigger_name ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the trigger_name argument as it is required.", 'action-wpwh_verify_trigger_signature-error' );
				return $return_args;
			}
			
			if( empty( $trigger_url_name ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the trigger_url_name argument as it is required.", 'action-wpwh_verify_trigger_signature-error' );
				return $return_args;
			}

			if( empty( $trigger_signature ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the trigger_signature argument as it is required.", 'action-wpwh_verify_trigger_signature-error' );
				return $return_args;
			}

			$trigger = WPWHPRO()->webhook->get_hooks( 'trigger', $trigger_name, $trigger_url_name );

			if( empty( $trigger ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "We could not find a trigger URL for your given data.", 'action-wpwh_verify_trigger_signature-error' );
				return $return_args;
			}

			if( ! isset( $trigger['secret'] ) || empty( $trigger['secret'] ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Your given trigger URL has no secret key. Please regenerate it first.", 'action-wpwh_verify_trigger_signature-error' );
				return $return_args;
			}

			$data = array( 
				'date_created' => $trigger['date_created'],
				'webhook_name' => $trigger['webhook_name'],
				'webhook_url_name' => $trigger['webhook_url_name'],
			);
			$signature = WPWHPRO()->webhook->generate_trigger_signature( json_encode( $data ), $trigger['secret'] );
			
			if( $signature === $trigger_signature ){
				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The trigger signature is valid.", 'action-wpwh_verify_trigger_signature-success' );
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The trigger signature is not valid.", 'action-wpwh_verify_trigger_signature-error' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $trigger );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.