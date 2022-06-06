<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_webhooks_Actions_resolve_target_url' ) ) :

	/**
	 * Load the resolve_target_url action
	 *
	 * @since 4.3.6
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_webhooks_Actions_resolve_target_url {

		public function get_details(){

			$translation_ident = "action-resolve_target_url-description";

			//These are the main arguments the user can use to input. You should always grab them within your action function.
			$parameter = array(
				'url'	   => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(string) The URL you want to resolve.', 'action-resolve_target_url-content' ) ),
				'do_action'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the webhook fires.', 'action-resolve_target_url-content' ) ),
			);

			//This is a more detailled view of how the data you sent will be returned.
			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', 'action-resolve_target_url-content' ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', 'action-resolve_target_url-content' ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further details about the sent data.', 'action-resolve_target_url-content' ) ),
			);

			ob_start();
			?>
			<?php echo WPWHPRO()->helpers->translate( "The URL refers to the URL you would like to resolve. This means that we check the destination of the URL until the URL does not have any redirects anymore.", $translation_ident ); ?>
			<?php
			$parameter['url']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>resolve_target_url</strong> action was fired.", $translation_ident ); ?>
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
		<?php echo WPWHPRO()->helpers->translate( "Contains the response data of the request.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			$returns_code = array (
				'success' => true,
				'msg' => 'The URL was successfully resolved.',
				'data' => 
				array (
				  'original_url' => 'https://originaldomain.test',
				  'resolved_url' => 'https://resolveddomain.test/',
				),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Resolve target URL',
				'webhook_slug' => 'resolve_target_url',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'It is also required to set the argument <strong>url</strong>. Please set it to the recipient URL that should receive the request.', $translation_ident ),
				),
			) );

			return array(
				'action'			=> 'resolve_target_url',
				'name'			  => WPWHPRO()->helpers->translate( 'Resolve target URL', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'resolve a target URL', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'This webhook action allows you to resolve a target URL of your choice from your WordPress site.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'webhooks',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array()
			);
			
			$url	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'url' );
			$do_action   = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $url ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the url argument.", 'action-resolve_target_url-failure' );
				return $return_args;
			}

			$ch = curl_init( $url );
			curl_setopt( $ch, CURLOPT_NOBODY, 1 );
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
			curl_setopt( $ch, CURLOPT_AUTOREFERER, 1 );
			curl_exec( $ch );
			$target = curl_getinfo( $ch, CURLINFO_EFFECTIVE_URL );
			curl_close( $ch );

			if( ! empty( $target ) ){
				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The URL was successfully resolved.", 'action-resolve_target_url-succcess' );
				$return_args['data']['original_url'] = $url;
				$return_args['data']['resolved_url'] = $target;
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "An error occured while resolving the URL.", 'action-resolve_target_url-succcess' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.