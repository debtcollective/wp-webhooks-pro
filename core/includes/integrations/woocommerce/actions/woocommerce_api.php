<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_Actions_woocommerce_api' ) ) :

	/**
	 * Load the woocommerce_api action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_woocommerce_Actions_woocommerce_api {

		public function get_details(){

			$translation_ident = "action-woocommerce_api-content";

			$parameter = array(
				'consumer_key'	  => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Your API consumer key. Please see the description for more information.', $translation_ident ) ),
				'consumer_secret'   => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Your API consumer secret. Please see the description for more information.', $translation_ident ) ),
				'api_base'		  => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The action you want to use. E.g. products/1234 - Please see the description for more information.', $translation_ident ) ),
				'api_method'		=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The method of the api call. E.g. get - Please see the description for more information.', $translation_ident ) ),
				'api_data'	  	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Additional data you want to send to the api call. Please see the description for more information.', $translation_ident ) ),
				'api_options'	  	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Extra arguments. Please see the description for more information.', $translation_ident ) ),
				'do_action'	  	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(mixed) The webhook data and response data.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array(
				'success' => false,
				'msg' => '',
				'data' => array()
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Woocommerce API call',
				'webhook_slug' => 'woocommerce_api',
				'before_description' => '<p>' . WPWHPRO()->helpers->translate( 'This webhook enables you to use the full finctionality of the woocommerce REST API. It also works with all integrated extensions like <strong>Woocommerce Memberships</strong>, <strong>Woocommerce Subscriptions</strong> or <strong>Woocommerce Bookings</strong>.', 'action-create_woocommerce_order-content' ) . '<br>' . WPWHPRO()->helpers->translate( 'You can also add a custom action, that fires after the webhook was called. Simply specify your webhook identifier (e.g. my_csutom_webhook) and call it within your theme or plugin (e.g. add_action( "my_csutom_webhook", "my_csutom_webhook_callback" ) ).', $translation_ident ) . '</p>',
				'steps' => array(
					WPWHPRO()->helpers->translate( 'Dou to the complexity of this webhook, we added the exact documentaton within a separate article. Please read it here: ', $translation_ident ) . '<a href="https://wp-webhooks.com/docs/article-categories/wp-webhooks-pro-woocommerce/" target="_blank" title="Go to our docs">https://wp-webhooks.com/docs/article-categories/wp-webhooks-pro-woocommerce/</a>',
				)
			) );

			return array(
				'action'			=> 'woocommerce_api', //required
				'name'			   => WPWHPRO()->helpers->translate( 'Woocommerce API call', $translation_ident ),
				'sentence'			   => WPWHPRO()->helpers->translate( 'perform a Woocommerce API call', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'The full power of the woocommerce API, packed within a webhook.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'woocommerce',
				'premium'		  => true,
			);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array()
			);

			$consumer_key = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'consumer_key' );
			$consumer_secret = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'consumer_secret' );
			$api_base = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'api_base' );
			$api_method = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'api_method' );
			$api_data = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'api_data' );
			$api_options = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'api_options' );

			$do_action = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $api_base ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate("The parameter api_base is required. Please set it first.", 'action-woocommerce_api-error' );
				return $return_args;
			} elseif( empty( $consumer_key ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate("The parameter consumer_key is required. Please set it first.", 'action-woocommerce_api-error' );
				return $return_args;
			} elseif( empty( $consumer_secret ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate("The parameter consumer_secret is required. Please set it first.", 'action-woocommerce_api-error' );
				return $return_args;
			} elseif( empty( $api_method ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate("The parameter api_method is required. Please set it first.", 'action-woocommerce_api-error' );
				return $return_args;
			}

			$validated_data = null;
			if( WPWHPRO()->helpers->is_json( $api_data ) ){
				$validated_data = json_decode( $api_data );
			}

			
			switch( strtolower( $api_method ) ){
				case 'post':
					$method = 'post';
					break;
				case 'put':
					$method = 'put';
					break;
				case 'get':
					$method = 'get';
					break;
				case 'delete':
					$method = 'delete';
					break;
				case 'options':
					$method = 'options';
					break;
				default:
					$method = null;
					break;
			}

			$options = array();
			if( ! empty( $api_options ) && WPWHPRO()->helpers->is_json( $api_options ) ){
				$options = json_decode( $api_options, true );
				if( ! is_array( $options ) ){
					$options = array();
				}
			}

			if( $method !== null ){
				//Require Woocommerce Rest API Class
				$integration_folder = WPWHPRO()->integrations->get_integrations_folder();
				require( $integration_folder . '/woocommerce/misc/class-wpwhpro-woocommerce-api.php' );
				$api_handler = new WPWHPRO_Woocommerce_Load_API();

				$api_handler->load_woocommerce_api( array(
					'store_url' => home_url(),
					'consumer_key' => $consumer_key,
					'consumer_secret' => $consumer_secret,
					'options' => $options
				) );

				$response = $api_handler->wc_call( $method, $api_base, $validated_data );
				if( is_array( $response ) && $response['success'] ){
					$return_args['success'] = true;
					$return_args['data'] = $response;
					$return_args['msg'] = WPWHPRO()->helpers->translate( 'The api call was successful', 'action-woocommerce_api-success' );
				} else {
					$return_args['data'] = $response;
					$return_args['msg'] = WPWHPRO()->helpers->translate( 'Error making the API call', 'action-woocommerce_api-failure' );
				}

			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate("Your defined method is not registered. Please set a correct method. Possible values: post, put, get, delete, options", 'action-woocommerce_api-error' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.