<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_ifttt_Actions_ifttt_send_webhook' ) ) :

	/**
	 * Load the ifttt_send_webhook action
	 *
	 * @since 5.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_ifttt_Actions_ifttt_send_webhook {

		public function get_details(){

			$translation_ident = "action-ifttt_send_webhook-description";

			//These are the main arguments the user can use to input. You should always grab them within your action function.
			$parameter = array(
				'url'	   => array( 
					'required' => true, 
					'multiple' => true, 
					'label' => WPWHPRO()->helpers->translate( 'IFTTT "Webhooks" URL', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( '(string) The URL you want to send the data to from the "Webhooks" applet from within IFTTT. You will find your unique URL by clicking on the documentation at https://ifttt.com/maker_webhooks/my_applets', $translation_ident ), 
				),
				'method'	=> array( 
					'label' => WPWHPRO()->helpers->translate( 'Request method', $translation_ident ), 
					'default_value' => 'POST', 
					'type' => 'select', 
					'choices' => array(
						'POST' => array( 'label' => 'POST' ),
						'GET' => array( 'label' => 'GET' ),
						'HEAD' => array( 'label' => 'HEAD' ),
						'PUT' => array( 'label' => 'PUT' ),
						'DELETE' => array( 'label' => 'DELETE' ),
						'TRACE' => array( 'label' => 'TRACE' ),
						'OPTIONS' => array( 'label' => 'OPTIONS' ),
						'PATCH' => array( 'label' => 'PATCH' ),
					), 
					'short_description' => WPWHPRO()->helpers->translate( '(string) The request type used to send the request.', $translation_ident ),
				),
				'headers'	   => array( 
					'type' => 'repeater', 
					'multiple' => true, 
					'label' => WPWHPRO()->helpers->translate( 'Headers', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( '(string) A JSON formatted string containing further header details.', $translation_ident ),
				),
				'raw_body'	   => array(
					'label' => WPWHPRO()->helpers->translate( 'Raw body (Payload data)', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( '(string) The raw body. If this argument is set, the "Body" argument is ignored.', $translation_ident ),
				),
				'body'	   => array( 
					'type' => 'repeater', 
					'variable' => false, 
					'multiple' => true, 
					'label' => WPWHPRO()->helpers->translate( 'Body (Payload data)', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( '(string) A JSON formatted string containing further payoad data.', $translation_ident ),
				),
				'timeout'	=> array( 
					'label' => WPWHPRO()->helpers->translate( 'Timeout', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( '(integer) Filters the timeout value for an HTTP request. Default: 5', $translation_ident ),
				),
				'redirection'	=> array( 
					'label' => WPWHPRO()->helpers->translate( 'Allowed redirects', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( '(integer) Filters the number of redirects allowed during an HTTP request. Default 5', $translation_ident ),
				),
				'httpversion'	=> array( 
					'label' => WPWHPRO()->helpers->translate( 'HTTP version', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( '(string) Filters the version of the HTTP protocol used in a request. Default: 1.0', $translation_ident ),
				),
				'user-agent'	=> array( 
					'label' => WPWHPRO()->helpers->translate( 'User agent', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( '(string) Filters the user agent value sent with an HTTP request.', $translation_ident ),
				),
				'blocking'	=> array( 
					'type' => 'select', 
					'default_value' => 'yes', 
					'label' => WPWHPRO()->helpers->translate( 'Wait for response', $translation_ident ), 
					'choices' => array( 
						'yes' => WPWHPRO()->helpers->translate( 'Yes', $translation_ident ),
						'no' => WPWHPRO()->helpers->translate( 'No', $translation_ident ),
					), 
					'short_description' => WPWHPRO()->helpers->translate( '(bool) Filter whether to wait for a response of the recipient or not. Default: yes', $translation_ident ) 
				),
				'reject_unsafe_urls'	=> array( 
					'type' => 'select', 
					'default_value' => 'no',
					'label' => WPWHPRO()->helpers->translate( 'Reject unsafe URLs', $translation_ident ), 
					'choices' => array( 
						'yes' => WPWHPRO()->helpers->translate( 'Yes', $translation_ident ),
						'no' => WPWHPRO()->helpers->translate( 'No', $translation_ident ),
					), 'short_description' => WPWHPRO()->helpers->translate( '(string) Filters whether to pass URLs through wp_http_validate_url() in an HTTP request. Default: no', $translation_ident ) ),
				'sslverify'	=> array( 
					'type' => 'select', 
					'default_value' => 'yes', 
					'label' => WPWHPRO()->helpers->translate( 'Verify SSL', $translation_ident ), 
					'choices' => array( 
						'yes' => WPWHPRO()->helpers->translate( 'Yes', $translation_ident ),
						'no' => WPWHPRO()->helpers->translate( 'No', $translation_ident ),
					), 'short_description' => WPWHPRO()->helpers->translate( '(string) Validates the senders SSL certificate before sending the data. Default: yes', $translation_ident ) ),
				'limit_response_size'	=> array( 
					'label' => WPWHPRO()->helpers->translate( 'Limit response size', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( '(integer) Limit the response size of the data coming back from the recpient. Default: null', $translation_ident ),
				),
				'cookies'	   => array( 
					'type' => 'repeater', 
					'multiple' => true, 
					'label' => WPWHPRO()->helpers->translate( 'Cookies', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( '(string) A JSON formatted string containing additional cookie data.', $translation_ident ),
				),
				'do_action'	=> array( 
					'label' => WPWHPRO()->helpers->translate( 'Custom WordPress action', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the webhook fires.', $translation_ident ),
				),
			);

			//This is a more detailed view of how the data you sent will be returned.
			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further details about the sent data.', $translation_ident ) ),
			);

			ob_start();
			?>
			<?php echo WPWHPRO()->helpers->translate( "The header argument accepts a JSON formatted string, containing additional header information. Down below you will find an example using two simple header settings:", $translation_ident ); ?>
			<pre>{
  "Content-Type": "application/json",
  "Custom-Header": "Some demo header"
}</pre>
			<?php
			$parameter['headers']['description'] = ob_get_clean();

			ob_start();
			?>
			<?php echo WPWHPRO()->helpers->translate( "The body argument accepts a JSON formatted string, containing your main information. Down below you will find an example for the body:", $translation_ident ); ?>
			<pre>{
  "user-email": "jon@doe.test",
  "user-name": "Jon Doe"
}</pre>
			<?php
			$parameter['body']['description'] = ob_get_clean();

			ob_start();
			?>
			<?php echo WPWHPRO()->helpers->translate( "The cookies argument accepts a JSON formatted string, containing further cookie information. Down below you will find an example for the body:", $translation_ident ); ?>
			<pre>{
  "test-cookie": "The Test Cookie"
}</pre>
			<?php
			$parameter['cookies']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to change the mehtod of this request. Default is POST.", $translation_ident ); ?>
		<?php
		$parameter['method']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to either send the request synchronously (waiting for a response) or asynchronously (response will be empty).", $translation_ident ); ?>
		<?php
		$parameter['blocking']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "Set this argument to false to use unsafe looking URLs like zfvshjhfbssdf.szfdhdf.com.", $translation_ident ); ?>
		<?php
		$parameter['reject_unsafe_urls']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "Set this argument to no to use unverified SSL connections for this URL.", $translation_ident ); ?>
		<?php
		$parameter['sslverify']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>ifttt_send_webhook</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $check, $arguments, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$check</strong> (bool)<br>
		<?php echo WPWHPRO()->helpers->translate( "Returns the HTTP object if the request was successful - WP Error or false if not.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$arguments</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "The arguments used to send the HTTP request.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "Contains the response data of the request.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			$returns_code = array (
				'success' => true,
				'msg' => 'The webhook request was sent successfully.',
				'data' => 
				array (
				  'success' => true,
				  'msg' => '',
				  'headers' => 
				  array (
					'content-type' => 'text/html; charset=utf-8',
					'content-length' => '55',
					'date' => 'Fri, 25 Mar 2022 06:22:09 GMT',
					'x-powered-by' => 'Sad Unicorns',
					'x-robots-tag' => 'none',
					'x-top-secrettt' => 'xxxxxxxxx',
					'etag' => 'x/"xxxxxxxx/xxxxxxxxxxxxxxxxxxx"',
					'x-cache' => 'Miss from cloudfront',
					'via' => '1.1 xxxxxxxxxx.cloudfront.net (CloudFront)',
					'x-amz-cf-pop' => 'xxxxxxxx',
					'x-amz-cf-id' => 'xxxxxxxxxxxx',
				  ),
				  'cookies' => 
				  array (
				  ),
				  'method' => '',
				  'content_type' => 'text/html; charset=utf-8',
				  'code' => 200,
				  'origin' => '',
				  'query' => '',
				  'content' => 
				  array (
					0 => 'Congratulations! You\'ve fired the wpwebhooks json event',
				  ),
				  'response' => 
				  array (
					'code' => 200,
					'message' => 'OK',
				  ),
				  'filename' => NULL,
				  'http_response' => 
				  array (
					'data' => NULL,
					'headers' => NULL,
					'status' => NULL,
				  ),
				),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Send data to IFTTT webhook',
				'webhook_slug' => 'ifttt_send_webhook',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'It is also required to set the <strong>url</strong> argument. Please set it to the recipient URL from within the "Webhooks" app of IFTTT via the "Receive a web request with a JSON payload" trigger.', $translation_ident ),
				),
				'tipps' => array(
					WPWHPRO()->helpers->translate( 'You will be able to find your IFTTT webhok URL via the following link: <a title="Go to IFTTT" target="_blank" href="https://ifttt.com/maker_webhooks/my_applets">https://ifttt.com/maker_webhooks/my_applets</a>', $translation_ident ),
				),
			) );

			return array(
				'action'			=> 'ifttt_send_webhook',
				'name'			  => WPWHPRO()->helpers->translate( 'Send data to IFTTT webhook', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'send data to a IFTTT webhook', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'This webhook action allows you to send data to the "Webhooks" app of IFTTT from your WordPress website, via the "Receive a web request with a JSON payload" trigger.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'ifttt',
				'premium' 			=> false,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array()
			);

			$url	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'url' );
			$headers	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'headers' );
			$body	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'body' );
			$raw_body	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'raw_body' );
			$cookies	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'cookies' );
			$method	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'method' );
			$timeout	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'timeout' );
			$redirection	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'redirection' );
			$httpversion	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'httpversion' );
			$blocking	 = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'blocking' ) === 'no' ) ? false : true;
			$user_agent	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user-agent' );
			$sslverify	 = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'sslverify' ) === 'no' ) ? false : true;
			$reject_unsafe_urls	 = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'reject_unsafe_urls' ) === 'no' ) ? false : true;
			$limit_response_size	 = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'limit_response_size' );
			$do_action		  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $url ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the url argument.", 'action-ifttt_send_webhook-failure' );
				return $return_args;
			}

			$arguments = array(
				'blocking' => $blocking,
				'sslverify' => $sslverify,
				'headers' => array(
					'content-type' => 'application/json'
				),
			);

			if( ! empty( $headers ) ){
				if( is_array( $headers ) || is_object( $headers ) ){

					foreach( $headers as $header_key => $header_data ){
						$arguments['headers'][ $header_key ] = $header_data;
					}

				} elseif( WPWHPRO()->helpers->is_json( $headers ) ){
					$arguments['headers'] = array_merge( $arguments['headers'], json_decode( $headers, true ) );
				}
			}

			if( isset( $raw_body ) ){
				$arguments['body'] = $raw_body;
			} else {
				if( ! empty( $body ) ){
					if( is_array( $body ) || is_object( $body ) ){
						$arguments['body'] = $body;
					} elseif( WPWHPRO()->helpers->is_json( $body ) ){
						$arguments['body'] = json_decode( $body, true );
					}
				}
			}

			if( ! empty( $cookies ) ){
				if( is_array( $cookies ) || is_object( $cookies ) ){
					$arguments['cookies'] = $cookies;
				} elseif( WPWHPRO()->helpers->is_json( $cookies ) ){
					$arguments['cookies'] = json_decode( $cookies, true );
				}
			}

			if( ! empty( $sslverify ) ){
				$arguments['sslverify'] = $sslverify;
			}

			if( ! empty( $method ) ){
				$arguments['method'] = $method;
			}

			if( ! empty( $timeout ) ){
				$arguments['timeout'] = $timeout;
			}

			if( ! empty( $redirection ) ){
				$arguments['redirection'] = $redirection;
			}

			if( ! empty( $httpversion ) ){
				$arguments['httpversion'] = $httpversion;
			}

			if( ! empty( $user_agent ) ){
				$arguments['user-agent'] = $user_agent;
			}

			if( ! empty( $reject_unsafe_urls ) ){
				$arguments['reject_unsafe_urls'] = $reject_unsafe_urls;
			}

			if( ! empty( $limit_response_size ) ){
				$arguments['limit_response_size'] = $limit_response_size;
			}	

			$response = WPWHPRO()->http->send_http_request( $url, $arguments );

			if( $response['success'] ){
				$return_args['data'] = $response;	

				$return_args['msg'] = WPWHPRO()->helpers->translate( "The webhook request was sent successfully.", 'action-ifttt_send_webhook-success' );
				$return_args['success'] = true;
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "An error occured while sending the webhook request.", 'action-ifttt_send_webhook-success' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $response, $arguments, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.