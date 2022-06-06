<?php

/**
 * WP_Webhooks_Pro_HTTP Class
 *
 * This class is a wrapper for the standard WP_Http class
 * that optimizes the responses based on certain values
 *
 * @since 5.0
 */

/**
 * The api class of the plugin.
 *
 * @since 5.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_HTTP {

    /**
     * The cached, current request
     *
     * @var mixed
     */
    private $request = null;

    /**
     * The cached request body of 
     * the current request
     *
     * @var mixed
     */
    private $request_body = '';

    /**
     * This is the default request structure used for 
     *
     * @return array
     */
	public function get_default_request_structure(){
        $structure = array(
            'headers' => array(),
            'cookies' => array(),
            'method' => '',
            'content_type' => '',
            'code' => '', //http_response_code()
            'origin' => '',
            'query' => '',
            'content' => '', //same as body but necessary for backward compatibility
            'msg' => '',
        );

        return apply_filters( 'wpwhpro/http/get_default_request_structure', $structure );
    }

    /**
     * This is the default request structure used for 
     *
     * @return array
     */
	public function get_default_response_structure(){
        $structure = array(
            'success' => false,
            'msg' => '',
            'headers' => array(),
            'cookies' => array(),
            'method' => '',
            'content_type' => '',
            'code' => '', //http_response_code()
            'origin' => '',
            'query' => '',
            'content' => '', //same as body but necessary for backward compatibility
        );

        return apply_filters( 'wpwhpro/http/get_default_response_structure', $structure );
    }

    public function get_http_origin(){
        $validated_request_origin = get_http_origin();

        return apply_filters( 'wpwhpro/http/get_http_origin', $validated_request_origin );
    }

    /*
     * ###############################
     * ###
     * ###### CURRENT REQUEST FUNCTIONS
     * ###
     * ###############################
     */

    public function get_current_request( $cached = true ){

        if( $cached && $this->request !== null ){
            return $this->request;
        }

        $args = array(
            'headers' => $this->get_current_request_headers(),
            'cookies' => $this->get_current_request_cookies(),
            'method' => $this->get_current_request_method(),
            'content_type' => $this->get_current_request_content_type(),
            'code' => $this->get_current_request_code(),
            'origin' => $this->get_http_origin(),
            'query' => $this->get_current_request_query(),
            'content' => $this->get_current_request_body(),
        );

        $request = wp_parse_args( $args, $this->get_default_request_structure() );

        //Parameters are kept for backward compatibility
        $request = apply_filters( 'wpwhpro/helpers/validate_response_body', $request, $request['content_type'], file_get_contents('php://input'), array() );

        $request = apply_filters( 'wpwhpro/http/get_current_request', $request );
        $this->request = $request;

        return $request;
    }

    public function get_current_request_headers(){
        $validated_headers = array();

        $headers = WPWHPRO()->helpers->get_all_headers();
        if( ! empty( $headers ) && is_array( $headers ) ){
            foreach( $headers as $header_key => $header_value ){

                $header_key_validated = sanitize_title( $header_key );

                //Skip cookies as they are fetched from get_current_cookies()
                if( $header_key_validated === 'cookie' ){
                    continue;
                }

                $validated_headers[ $header_key_validated ] = $header_value;
            }
        }

        return apply_filters( 'wpwhpro/http/get_current_request_headers', $validated_headers );
    }

    public function get_current_request_cookies(){
        $validated_cookies = array();

        $cookies = $_COOKIE;
        if( ! empty( $cookies ) && is_array( $cookies ) ){
            foreach( $cookies as $cookie_key => $cookie_value ){

                $cookie_key_validated = sanitize_title( $cookie_key );

                $validated_cookies[ $cookie_key_validated ] = $cookie_value;
            }
        }

        return apply_filters( 'wpwhpro/http/get_current_request_cookies', $validated_cookies );
    }

    public function get_current_request_method(){
        $validated_method = '';

        if( isset( $_SERVER['REQUEST_METHOD'] ) ){
			$validated_method = $_SERVER["REQUEST_METHOD"];
		}

        return apply_filters( 'wpwhpro/http/get_current_request_method', $validated_method );
    }

    public function get_current_request_content_type(){
        $validated_content_type = '';

        if( isset( $_SERVER["CONTENT_TYPE"] ) ){
			$validated_content_type = $_SERVER["CONTENT_TYPE"];
		}

        return apply_filters( 'wpwhpro/http/get_current_request_content_type', $validated_content_type );
    }

    public function get_current_request_code(){
        $validated_request_code = http_response_code();

        return apply_filters( 'wpwhpro/http/get_current_request_code', $validated_request_code );
    }

    public function get_current_request_query(){
        $validated_request_query = $_GET;

        return apply_filters( 'wpwhpro/http/get_current_request_query', $validated_request_query );
    }

    public function get_current_request_body( $cached = true ){

		$validated_data = '';
        $request_type = $this->get_current_request_content_type();

        //Cache current content
        if( $cached && ! empty( $this->request_body ) ){
			return $this->request_body;
        }

		$request_body = file_get_contents('php://input');
		$content_evaluated = false;

		if( strpos( $request_type, 'application/json' ) !== false ){
			if( WPWHPRO()->helpers->is_json( $request_body ) ){
				$validated_data = ( json_decode( $request_body ) !== null ) ? json_decode( $request_body ) : (object) json_decode( $request_body, true );
				$content_evaluated = true;
			} else {
				WPWHPRO()->helpers->log_issue( WPWHPRO()->helpers->translate( "The incoming webhook content was sent as application/json, but did not contain a valid JSON: ", 'admin-debug-feature' ) . WPWHPRO()->helpers->display_var( $request_body ) );
			}
        }

		if( strpos( $request_type, 'application/xml' ) !== false && ! $content_evaluated ){
			if( WPWHPRO()->helpers->is_xml( $request_body ) ){
				$validated_data = simplexml_load_string( $request_body );
				$content_evaluated = true;
			} else {
				WPWHPRO()->helpers->log_issue( WPWHPRO()->helpers->translate( "The incoming webhook content was sent as application/xml, but did not contain a valid XML: ", 'admin-debug-feature' ) . WPWHPRO()->helpers->display_var( $request_body ) );
			}
        }

		if( strpos( $request_type, 'multipart/form-data' ) !== false && ! $content_evaluated ){

			$multipart = array();

			if( isset( $_POST ) ){
				$multipart = array_merge( $multipart, $_POST );
			}

			if( isset( $_FILES ) ){
				$multipart = array_merge( $multipart, $_FILES );
			}

			$validated_data = (object) $multipart;
			$content_evaluated = true;

			if( empty( $multipart ) ){
				WPWHPRO()->helpers->log_issue( WPWHPRO()->helpers->translate( "The incoming webhook content was sent as multipart/form-data, but did not contain any values: ", 'admin-debug-feature' ) . WPWHPRO()->helpers->display_var( $request_body ) );
			}

        }

		if( strpos( $request_type, 'application/x-www-form-urlencoded' ) !== false && ! $content_evaluated ){
			parse_str( $request_body, $form_data );
			$form_data = (object)$form_data;
			if( is_object( $form_data ) ){
				$validated_data = $form_data;
				$content_evaluated = true;
            }
        }

        //Added for backward compatibility
        //If nothing is set, we take the content as it comes
        if( ! $content_evaluated && is_string( $request_body ) ){
			if( ! empty( $request_body ) && is_string( $request_body ) ){
				$validated_data = $request_body;
			} else {

                //Provide a more optimized way of validating only GET requests
                if( $this->get_current_request_method() === 'GET' ){
                    $validated_data = ! empty( $_GET ) ? $_GET : array();
                }
			}
		}

        //force array
        if( ! is_array( $validated_data ) ){
            $validated_data = WPWHPRO()->helpers->force_array( $validated_data );
        }

        //Backward compatibility with the Zapier setup
		if( is_object( $validated_data ) && isset( $validated_data->wpwhpro_zapier_arguments ) ){
			foreach( $validated_data->wpwhpro_zapier_arguments as $zap_key => $zap_val ){
				$validated_data->{$zap_key} = $zap_val;
			}
		} elseif( is_array( $validated_data ) && isset( $validated_data['wpwhpro_zapier_arguments'] ) ){
			foreach( $validated_data['wpwhpro_zapier_arguments'] as $zap_key => $zap_val ){
				$validated_data[ $zap_key ] = $zap_val;
			}
		}

        $validated_data_original = $validated_data; //Preserve original data for additional filtering
		
		$this->request_body = $validated_data;

		return apply_filters( 'wpwhpro/http/get_current_request_body', $validated_data, $cached, $validated_data_original );
	}

    /*
     * ###############################
     * ###
     * ###### REQUEST FUNCTIONS
     * ###
     * ###############################
     */

    public function send_http_request( $url, $args = array() ){

        $args['content_type'] = $this->get_request_content_type( $args );
        $args = $this->validate_request_body( $args );  

        $url = apply_filters( 'wpwhpro/http/send_http_request/url', $url, $args );
        $args = apply_filters( 'wpwhpro/http/send_http_request/args', $args, $url );
  
        $response = wp_remote_post( $url, $args );

        $validated_response = $this->get_response( $response );

        return apply_filters( 'wpwhpro/http/send_http_request', $validated_response, $url, $args );
    }

    public function get_request_content_type( $data ){
        $content_type = '';

        if( is_array( $data ) ){
            if( isset( $data['content_type'] ) ){
                $content_type = $data['content_type'];
            } elseif( isset( $data['headers'] ) && !empty( $data['headers'] ) && isset( $data['headers']['content-type'] ) ){
                $content_type = $data['headers']['content-type'];
            }
        }

        return apply_filters( 'wpwhpro/http/get_request_content_type', $content_type, $data );
    }

    public function validate_request_body( $data ){
        $validated_data = '';
        $original_data = $data;

        if( ! isset( $data['body'] ) ){
            $data['body'] = $validated_data;
        }

        $request_type = isset( $data['content_type'] ) ? $data['content_type'] : ''; 

        if( empty( $request_type ) ){
            return $data;
        }

		if( strpos( $request_type, 'application/json' ) !== false ){

            if( WPWHPRO()->helpers->is_json( $data['body'] ) ){
                $validated_data = trim( $data['body'] );
            } else {
                $validated_data = trim( wp_json_encode( $data['body'] ) );
            }
			
        } elseif( strpos( $request_type, 'application/xml' ) !== false ){

            if( WPWHPRO()->helpers->is_xml( $data['body'] ) ){
                $validated_data = $data['body'];
            } else{
                $sxml_data = apply_filters( 'wpwhpro/http/validate_request_body/simplexml_data', '<data/>', $data );
                $xml = WPWHPRO()->helpers->convert_to_xml( new SimpleXMLElement( $sxml_data ), $data['body'] );
                $validated_data = $xml->asXML();
            }
			
        } else {
            $validated_data = $data['body'];
        }

        $data['body'] = $validated_data;

		return apply_filters( 'wpwhpro/http/validate_request_body', $data, $original_data );
    }

    /*
     * ###############################
     * ###
     * ###### RESPONSE FUNCTIONS
     * ###
     * ###############################
     */

     /**
      * Generate a predefined structure for a 
      * specific request
      *
      * @param mixed $args
      * @return array The validated request
      */
    public function get_response( $args = array() ){

        if( is_wp_error( $args ) ){
            $args = $this->generate_wp_error_response( $args );
        } else {
            $args['success'] = true;
        }

        //Keep for backwards compatibility
        if( isset( $args['payload'] ) ){
            $args['content'] = $args['payload'];
            unset( $args['payload'] );
        }

        //Merge WP_Http object keys
        $args = $this->merge_wp_http_object_data( $args );

        if( ! isset( $args['content_type'] ) ){
            if( isset( $args['headers'] ) && isset( $args['headers']['content-type'] ) ){
                $args['content_type'] = $args['headers']['content-type'];
            }
        }
          
        $args['origin'] = $this->get_http_origin();

        $args = $this->validate_response_body( $args );

        $default_structure = $this->get_default_response_structure();
        $response = wp_parse_args( $args, $default_structure );

        return apply_filters( 'wpwhpro/http/get_request', $response, $args );
    }

    /**
     * Merge a given WP_Http response to our 
     * own structure for cross-compatibility    
     *
     * @return array
     */
    public function merge_wp_http_object_data( $args ){

        if( is_array( $args ) ){

            //Merge the body to our content key
            if( isset( $args['body'] ) ){
                $args['content'] = $args['body'];
                unset( $args['body'] );
            }

            //Merge response code values
            if( isset( $args['response'] ) ){

                if( ! is_wp_error( $args['response'] ) ){
                    $code = wp_remote_retrieve_response_code( $args );
                    if( ! empty( $code ) ){
                        $args['code'] = $code;
                    }
                } else {
                    unset( $args['response'] );
                }
                
            }

            //Merge the headers
            if( isset( $args['headers'] ) && is_object( $args['headers'] ) ){
                $headers_validated = array();
                $headers = wp_remote_retrieve_headers( $args ); //Used to validate against object erorrs
                if( ! empty( $headers ) ){
                    foreach( $headers as $header_key => $header_value ){
                        $headers_validated[ $header_key ] = $header_value;
                    }
                }

                $args['headers'] = $headers_validated;
            }

            //Merge the cookies
            if( isset( $args['cookies'] ) && ( is_object( $args['cookies'] ) || is_array( $args['cookies'] ) ) ){
                $cookies_validated = array();

                $cookies = wp_remote_retrieve_cookies( $args ); //Used to validate against object erorrs
                if( ! empty( $cookies ) ){
                    foreach( $cookies as $cookie_key => $cookie ){

                        if ( ! is_a( $cookie, 'WP_Http_Cookie' ) ) {
                            $cookies_validated[ $cookie->name ] = $cookie->value;
                        } else {
                            $cookies_validated[ $cookie_key ] = $cookie;
                        }
    
                    }
                }

                $args['cookies'] = $cookies_validated;
            }
        }

        return $args;
    }

    /**
     * Validate the data of any kind of response body
     * This function is used for validating an existing request
     *
     * @param array $args
     * @return mixed
     */
    public function validate_response_body( $args = true ){

		$validated_data = '';

        if( ! isset( $args['content'] ) ){
            $args['content'] = $validated_data;
            return $args;
        }

        $request_type = isset( $args['content_type'] ) ? $args['content_type'] : '';

        if( empty( $request_type ) ){
            return $args;
        }

		$request_body = $args['content'];
		$content_evaluated = false;

		if( strpos( $request_type, 'application/json' ) !== false ){
			if( WPWHPRO()->helpers->is_json( $request_body ) ){
				$validated_data = ( json_decode( $request_body ) !== null ) ? json_decode( $request_body ) : (object) json_decode( $request_body, true );
				$content_evaluated = true;
			} else {
				WPWHPRO()->helpers->log_issue( WPWHPRO()->helpers->translate( "The incoming webhook content was sent as application/json, but did not contain a valid JSON: ", 'admin-debug-feature' ) . WPWHPRO()->helpers->display_var( $request_body ) );
			}
        }

		if( strpos( $request_type, 'application/xml' ) !== false && ! $content_evaluated ){
			if( WPWHPRO()->helpers->is_xml( $request_body ) ){
				$validated_data = simplexml_load_string( $request_body );
				$content_evaluated = true;
			} else {
				WPWHPRO()->helpers->log_issue( WPWHPRO()->helpers->translate( "The incoming webhook content was sent as application/xml, but did not contain a valid XML: ", 'admin-debug-feature' ) . WPWHPRO()->helpers->display_var( $request_body ) );
			}
        }

		if( strpos( $request_type, 'application/x-www-form-urlencoded' ) !== false && ! $content_evaluated ){
			parse_str( $request_body, $form_data );
			$form_data = (object)$form_data;
			if( is_object( $form_data ) ){
				$validated_data = $form_data;
				$content_evaluated = true;
            }
        }

        //Added for backward compatibility
        //If nothing is set, we take the content as it comes
        if( ! $content_evaluated && is_string( $request_body ) ){
			if( ! empty( $request_body ) && is_string( $request_body ) ){
				$validated_data = $request_body;
			}
		}

        //force array
        if( ! is_array( $validated_data ) ){
            $validated_data = WPWHPRO()->helpers->force_array( $validated_data );
        }

        $original_args = $args; //Preserve original data for additional filtering
        $args['content'] = $validated_data;

        //Parameters are kept for backward compatibility
        $args = apply_filters( 'wpwhpro/helpers/validate_response_body', $args, $request_type, $request_body, $args );

		return apply_filters( 'wpwhpro/http/validate_response_body', $args, $original_args );
	}

    /**
     * Generate a formatted response
     * based on a given WP_Error object
     *
     * @param WP_Error $wp_error
     * @return array The formatted data 
     */
    public function generate_wp_error_response( $wp_error ){

        $response_data = $this->get_default_response_structure();

        if( empty( $wp_error ) || ! is_wp_error( $wp_error ) ){
            return $response_data;
        }

        $response_data['msg'] = $wp_error->get_error_message();
        $response_data['code'] = $wp_error->get_error_code();
        $response_data['content'] = $wp_error->get_all_error_data();

        return apply_filters( 'wpwhpro/http/generate_wp_error_response', $response_data, $wp_error );
    }

}
