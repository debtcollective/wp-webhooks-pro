<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_extract_url' ) ) :

	/**
	 * Load the text_extract_url action
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Actions_text_extract_url {

	public function get_details(){

		$translation_ident = "action-text_extract_url-content";

			$parameter = array(
				'value'		=> array( 
					'required' => true, 
					'label' => WPWHPRO()->helpers->translate( 'Value', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( 'The string we are going to extract the URLs from.', $translation_ident ),
				),
				'return_all' => array( 
					'type' => 'select',
					'multiple' => false,
					'choices' => array(
						'yes' => array( 'label' => 'Yes' ),
						'no' => array( 'label' => 'No' ),
					),
					'default_value' => 'no',
					'label' => WPWHPRO()->helpers->translate( 'Return all URLs', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( 'Define whether to extract only the first, or all URLs.', $translation_ident ),
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

		$returns_code = array (
			'success' => true,
			'msg' => 'The URLs have been successfully extracted.',
			'data' => 
			array (
			  0 => 
			  array (
				'raw_url' => 'https://demo.domain.com/some/path?demo=123&more=argument#navigate',
				'parts' => 
				array (
				  'scheme' => 'https',
				  'host' => 'demo.domain.com',
				  'path' => '/some/path',
				  'query' => 'demo=123&more=argument',
				  'fragment' => 'navigate',
				),
			  ),
			  1 => 
			  array (
				'raw_url' => 'https://www.domain.com/',
				'parts' => 
				array (
				  'scheme' => 'https',
				  'host' => 'www.domain.com',
				  'path' => '/',
				),
			  ),
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Text extract URL',
			'webhook_slug' => 'text_extract_url',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is required to set the <strong>value</strong> argument. Please set it to the string you want to extract the URLs from.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'text_extract_url', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Text extract URL', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'extract one or multiple URLs from text', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Extract one or multiple URLs from a text value.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'wp-webhooks-formatter',
			'premium'	   => true
		);


		}

		public function execute( $return_data, $response_body ){

			$regex = "((https?|ftp)\:\/\/)?"; //validate the protocol
			$regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; //validate subdomains
			$regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; //validate the domain
			$regex .= "(\:[0-9]{2,5})?"; //validate the custom port
			$regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; //validate the custom path
			$regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; //validate query parameters
			$regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; //validate onpage tags
			
			$return_args = array(
				'success' => false,
				'msg' => '',
			);

			$value = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'value' );
			$return_all = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'return_all' ) === 'yes' ) ? true : false;
			
			if( empty( $value ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the value argument as it is required.", 'action-text_extract_url-error' );
				return $return_args;
			}

			preg_match_all( '/' . $regex . '/i', $value, $matches );

			$urls = array();
			if( is_array( $matches ) && isset( $matches[0] ) && is_array( $matches[0] ) ){
				if( $return_all ){
					$urls = $matches[0];
				} else {
					if( isset( $matches[0][0] ) ){
						$urls = $matches[0][0];
					}
				}
			}

			//append the parts
			if( is_array( $urls ) ){
				foreach( $urls as $urlkey => $url ){
					$urls[ $urlkey ] = array(
						'raw_url' => $url,
						'parts' => parse_url( $url ),
					);
				}
			} else {
				$urls = array(
					'raw_url' => $urls,
					'parts' => parse_url( $urls ),
				);
			}

			$return_args['success'] = true;

			if( $return_all ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The URLs have been successfully extracted.", 'action-text_extract_url-success' );
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The URL has been successfully extracted.", 'action-text_extract_url-success' );
			}
			
			$return_args['data'] = $urls;

			return $return_args;
	
		}

	}

endif; // End if class_exists check.