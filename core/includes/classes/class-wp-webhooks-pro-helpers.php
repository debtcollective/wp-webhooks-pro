<?php

/**
 * WP_Webhooks_Pro_Helpers Class
 *
 * This class contains all of the available helper functions
 *
 * @since 1.0.0
 */

/**
 * The helpers of the plugin.
 *
 * @since 1.0.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Helpers {

	/**
     * Variable to check if translations
     * are active or not
     *
	 * @var bool - False as default
	 */
    private $activate_translations = false;

	/**
     * The current requests response body
     *
	 * @var mixed - the current response body
	 */
    private $response_body = false;

	/**
	 * WP_Webhooks_Pro_Helpers constructor.
	 */
    public function __construct() {
        $this->activate_translations = ( get_option( 'wpwhpro_activate_translations' ) == 'yes' ) ? true : false;
        $this->activate_debugging = ( get_option( 'wpwhpro_activate_debug_mode' ) == 'yes' ) ? true : false;
    }

	/**
	 * Translate custom Strings
	 *
	 * @param $string - The language string
	 * @param null $cname - If no custom name is set, return the default one
	 * @return string - The translated language string
	 */
	public function translate( $string, $cname = null, $prefix = null ){

		/**
		 * Filter to control the translation and optimize
		 * them to a specific output
		 */
		$trigger = apply_filters( 'wpwhpro/helpers/control_translations', $this->activate_translations, $string, $cname );
		if( empty( $trigger ) ){
			return $string;
		}

		if( empty( $string ) ){
			return $string;
		}

		if( ! empty( $cname ) ){
			$context = $cname;
		} else {
			$context = 'default';
		}

		if( $prefix == 'default' ){
			$front = 'WPWHPRO: ';
		} elseif ( ! empty( $prefix ) ){
			$front = $prefix;
		} else {
			$front = '';
		}

		// WPML String Translation Logic (WPML itself has problems with _x in some versions)
		if( function_exists( 'icl_t' ) ){
			return icl_t( (string) 'wp-webhooks-pro', $context, $string );
		} else {
			return $front . _x( $string, $context, 'wp-webhooks-pro' );
		}
	}

	/**
	 * Checks if the parsed param is available on the current site
	 *
	 * @param $param
	 * @return bool
	 */
	public function is_page( $param ){
		if( empty( $param ) ){
			return false;
		}

		if( isset( $_GET['page'] ) ){
			if( $_GET['page'] == $param ){
				return true;
			}
		}

		return false;
	}

	/**
	 * Creates a formatted admin notice
	 *
	 * @param $content - notice content
	 * @param string $type - Status of the specified notice
	 * @param bool $is_dismissible - If the message should be dismissible
	 * @return string - The formatted admin notice
	 */
	public function create_admin_notice($content, $type = 'info', $is_dismissible = true){
		if(empty($content))
			return '';

		/**
		 * Block an admin notice based onn the specified values
		 */
		$throwit = apply_filters('wpwhpro/helpers/throw_admin_notice', true, $content, $type, $is_dismissible);
		if(!$throwit)
			return '';

		if($is_dismissible !== true){
			$isit = '';
			$bs_isit = '';
		} else {
			$isit = 'is-dismissible';
			$bs_isit = 'alert-dismissible fade show';
		}


		switch($type){
			case 'info':
				$notice = 'notice-info';
				$bs_notice = 'alert-info';
				break;
			case 'success':
				$notice = 'notice-success';
				$bs_notice = 'alert-success';
				break;
			case 'warning':
				$notice = 'notice-warning';
				$bs_notice = 'alert-warning';
				break;
			case 'error':
				$notice = 'notice-error';
				$bs_notice = 'alert-danger';
				break;
			default:
				$notice = 'notice-info';
				$bs_notice = 'alert-info';
				break;
		}

		if( is_array( $content ) ){
			$validated_content = sprintf( $this->translate($content[0], 'create-admin-notice'), $content[1] );
        } else {
			$validated_content = $this->translate($content, 'create-admin-notice');
		}

		$bootstrap_layout = apply_filters('wpwhpro/helpers/throw_admin_notice_bootstrap', false, $content, $type, $is_dismissible);
		if( $bootstrap_layout ){
			ob_start();
			?>
			<div class="alert <?php echo $bs_notice; ?> <?php echo $bs_isit; ?>" role="alert">
				<p class="m-0"><?php echo $validated_content; ?></p>
				<?php if( ! empty( $bs_isit ) ) : ?>
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				<?php endif; ?>
			</div>
			<?php
			$res = ob_get_clean();
		} else {
			ob_start();
			?>
			<div class="notice <?php echo $notice; ?> <?php echo $isit; ?>">
				<p class="m-0"><?php echo $validated_content; ?></p>
			</div>
			<?php
			$res = ob_get_clean();
		}

		return $res;
	}

	/**
	 * Formats a specific date to datetime
	 *
	 * @param $date
	 * @return DateTime
	 */
	public function get_datetime($date){
		$date_new = date('Y-m-d H:i:s', strtotime($date));
		$date_new_formatted = new DateTime($date_new);

		return $date_new_formatted;
	}

	/**
	 * Retrieves a response from an url
	 *
	 * @param $url
	 * @param $data - specifies a special part of the response
	 * @return array|bool|int|string|WP_Error
	 */
	public function get_from_api( $url, $data = '' ){

		if(empty($url))
			return false;

		if(!empty($this->disable_ssl)){
			$setting = array(
				'sslverify'     => false,
				'timeout' => 30
			);
		} else {
			$setting = array(
				'timeout' => 30
			);
		}

		$val = wp_remote_get( $url, $setting );

		if($data == 'body'){
			$val = wp_remote_retrieve_body( $val );
		} elseif ($data == 'response'){
			$val = wp_remote_retrieve_response_code( $val );
		}

		return $val;
	}


	public function get_original_data_format( $value ){

		if( is_string( $value ) ){

			if( $value === 'true' || $value === 'false' ){
				return (bool) $value;
			}

			if( is_numeric( $value ) ){
				$validated_val = $value + 0;

				if( is_int( $validated_val ) ){
					return intval( $validated_val );
				}

				if( is_float( $validated_val ) ){
					return (float) $validated_val;
				}

			}

		}

		return $value;
	}

	/**
	 * Builds an url out of the given values
	 *
	 * @param $url - the default url to set the params to
	 * @param $args - the available args
	 * @return string - the url
	 */
	public function built_url( $url, $args ){
		if( ! empty( $args ) ){
			$url .= '?' . http_build_query( $args );
		}

		return $url;
	}

	/**
	 * Creates the home url in a more optimized way
	 *
	 * @since 2.0.4
	 *
	 * @param $path - the default url to set the params to
	 * @param $scheme - the available args
	 * @return string - the validated url
	 */
	public function safe_home_url( $path = '', $scheme = 'irndefault' ){

		if( $scheme === 'irndefault' ){
			if( is_ssl() ){
				$scheme = 'https';
			} else {
				$scheme = null;
			}
		}

		return home_url( $path, $scheme );
	}

	/**
     * Get Parameters from URL string
     *
	 * @param $url - the url
	 *
	 * @return array - the parameters of the url
	 */
	public function get_parameters_from_url( $url ){

		$parts = parse_url($url);

		parse_str($parts['query'], $url_parameter);

		return empty( $url_parameter ) ? array() : $url_parameter;

    }

	/**
	 * Builds an url out of the mai values
	 *
	 * @param $url - the default url to set the params to
	 * @param $args - the available args
	 * @return string - the url
	 */
	public function get_current_url( $with_args = true, $relative = false ){	
		if( ! $relative ){
			$current_url = ( isset( $_SERVER['HTTPS'] ) && ( $_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == '1' || $_SERVER['HTTPS'] === true ) ) ? 'https://' : 'http://';

			$host_part = $_SERVER['HTTP_HOST'];
	
			//Support custom ports (since 4.2.0)
			$host_part = str_replace( ':80', '', $host_part );
			$host_part = str_replace( ':443', '', $host_part );
	
			$current_url .= sanitize_text_field( $host_part ) . sanitize_text_field( $_SERVER['REQUEST_URI'] );
		} else {
			$current_url = sanitize_text_field( $_SERVER['REQUEST_URI'] );
		}

	    if( ! $with_args ){
	        $current_url = strtok( $current_url, '?' );
        }

		return apply_filters( 'wpwhpro/helpers/get_current_url', $current_url, $with_args, $relative );
	}

	public function get_nonce_field( $nonce_data ){

		if( ! is_array( $nonce_data ) || ! isset( $nonce_data['action'] ) || ! isset( $nonce_data['arg'] ) ){
			return '';
		}

		ob_start();
		wp_nonce_field( $nonce_data['action'], $nonce_data['arg'] );
		$nonce = ob_get_clean();

		$nonce = str_replace( 'id="', 'id="' . mt_rand( 1, 999999 ) . '-', $nonce );

		return apply_filters( 'wpwhpro/helpers/get_nonce_field', $nonce, $nonce_data );
	}

	/**
     * Get value in between two of our custom tags
     *
     * Usage example:
     * if you want to get the key post_id, you need
     * to have the following tags inside of your content
     * @post_id-start@12345@post_id-end@
     *
	 * @param $ident - the key for a tag you want to check against
	 * @param $content - the content that should be checked against the tag
	 *
	 * @return mixed
	 */
	function get_value_between($ident, $content){
		$matches = array();
		$t = preg_match('/@' . $ident . '-start@(.*?)\\@' . $ident . '-end@/s', $content, $matches);
		return isset( $matches[1] ) ? $matches[1] : '';
	}

	/**
	 * Decode a json response
	 *
	 * @param $response - the response
	 * @return array|mixed|object - the encoded content
	 */
	public function decode_response($response){
		if(!empty($response))
			return json_decode($response, true);

		return $response;
	}

	/**
	 * Evaluate the content type and validate its data
	 * This function is deprecated since version 5.0
	 * Please use 
	 * 		WPWHPRO()->http->get_current_request() In case it is the current request
	 * 		WPWHPRO()->http->get_response( $custom_data ) in case it is a response
	 * 
	 * @deprecated 5.0
	 * @return array - the response content and content_type
	 */
	public function get_response_body( $custom_data = array(), $cached = true ){

		$is_custom = ( ! empty( $custom_data ) ) ? true : false;

		if( ! $is_custom ){
			$response_body = WPWHPRO()->http->get_current_request();
		} else {
			$response_body = WPWHPRO()->http->get_response( $custom_data );
		}

		return apply_filters( 'wpwhpro/helpers/get_deprecated_response_body', $response_body, $custom_data );
	}

	/**
     * Check if a given string is a json
     *
	 * @param $string - the string that should be checked
	 *
	 * @return bool - True if it is json, otherwise false
	 */
	public function is_json( $string ) {

		if( ! is_string( $string ) ){
			return false;
		}

		json_decode( $string );
		if( json_last_error() == JSON_ERROR_NONE ){
			return true;
		}

		json_decode( $string, true );
		if( json_last_error() == JSON_ERROR_NONE ){
			return true;
		}

		return false;
	}

	/**
     * Check if a specified content is xml
     *
	 * @param $content - the string that should be checked
	 *
	 * @return bool - True if it is xml, otherwise false
	 */
	public function is_xml($content) {
	    //Make sure libxml is available
	    if( ! function_exists( 'libxml_use_internal_errors' ) ){
	        return false;
        }

		$content = trim( $content );
		if( empty( $content ) ) {
			return false;
		}

		if( stripos( $content, '<!DOCTYPE html>' ) !== false ) {
			return false;
		}

		libxml_use_internal_errors( true );
		simplexml_load_string( $content );
		$errors = libxml_get_errors();
		libxml_clear_errors();

		return empty( $errors );
	}

	/**
	 * Check if a specified content is xml
	 *
	 * @param $object - the simplexml object
	 * @param $data - the data that should be converted
	 *
	 * @return $obbject - The current simple xml element
	 */
	function convert_to_xml( SimpleXMLElement $object, array $data ) {

		foreach( $data as $key => $value ) {
			if( is_array( $value ) ) {
				$new_object = $object->addChild( $key );
				$this->convert_to_xml( $new_object, $value );
			} elseif( is_object( $value ) ) {
				$new_object = $object->addChild( $key );
				$this->convert_to_xml( $new_object, (array) $value );
			} else {
				// if the key is an integer, it needs text with it to actually work.
				if( is_numeric( $key ) ) {
					$prefix = apply_filters( 'wpwhpro/helpers/convert_to_xml_int_prefix', 'wpwh_', $object, $data );
					$key = $prefix . $key;
				}

				$object->addChild( $key, $value );
			}
		}

		return $object;
	}

	/**
     * This function validates all necessary tags for displayable content.
     *
	 * @param $content - The validated content
	 * @since 1.4
	 * @return mixed
	 */
	public function validate_local_tags( $content ){

	    $user = get_user_by( 'id', get_current_user_id() );

	    $user_name = 'there';
	    if( ! empty( $user ) && ! empty( $user->data ) && ! empty( $user->data->display_name ) ){
	        $user_name = $user->data->display_name;
        }

		$content = str_replace(
			array( '%home_url%', '%admin_url%', '%product_version%', '%product_name%', '%user_name%' ),
			array( home_url(), get_admin_url(), WPWHPRO_VERSION, WPWHPRO_NAME, $user_name ),
			$content
		);

		return $content;
    }

	/**
     * Creates a unique user name for existing users
     *
	 * @param $email - the email address of the user
	 * @param string $prefix - a custom prefix
	 *
	 * @return string
	 */
	public function create_random_unique_username( $email, $prefix = '' ){
		$user_exists = 1;
		$email = sanitize_title( $email );
		do {
			$rnd_str = sprintf("%0d", mt_rand(1, 999999));
			$user_exists = username_exists( $prefix . $email . $rnd_str );
		} while( $user_exists > 0 );
		return $prefix . $email . $rnd_str;
	}

	/**
     * Display a particular variable
     *
	 * @param string $code - the variable
	 *
	 * @return false|string
	 */
	public function display_var( $code = '' ){
	    ob_start();
	    print_r( $code );
	    return ob_get_clean();
	}

	/**
     * Log certain data within the debug.log file
	 */
	public function log_issue( $text ){

		if( $this->activate_debugging ){
			error_log( $text );
		}

	}

	/**
     * Main value validator
     *
     * You can use it to validate various tags as listed
     * down below.
     *
     * For our string values, we focus on grabbing
     * the content that is set within our custo mtag logic
     *
	 * @param $content
	 * @param $key
	 */
	public function validate_request_value( $content, $key ){
		$return = false;

        if( is_object( $content ) ){

            if( isset( $content->$key ) ){
                $return = $content->$key;
            }

        } elseif( is_array( $content ) ){

            if( isset( $content[ $key ] ) ){
                $return = $content[ $key ];
            }

        } elseif( is_string( $content ) ) {

	        $return = $this->get_value_between( $key, $content );

        }

        //Validate a left over object to an array
        if( is_object( $return ) ){
            $return = json_decode( json_encode( $return ), true );
        }

        if( is_array( $return ) ){
			//Make sure we don't pass single arrays as well
			if( isset( $return[0] ) && is_string( $return[0] ) && count( $return ) <= 1 ){
				$return = $return[0];
			} else {
				//other arrays will be again encoded to a json
				$return = json_encode( $return );
			}

		}

		//Validate form url encode strings again
		if( is_string( $return ) ){
            $stripslashes = apply_filters( 'wpwhpro/helpers/request_values_stripslashes', false, $return );
			if( $stripslashes ){
				$return = stripslashes( $return );
			}
		}

        return apply_filters( 'wpwhpro/helpers/request_return_value', $return, $content, $key );
	}

	/**
	 * Validate a given server header and return its value
	 *
	 * @param string $key
	 * @return string The server header
	 */
	public function validate_server_header( $key ){
		$header = null;
		$uppercase_header = 'HTTP_' . strtoupper( str_replace( '-', '_', $key ) );

        if( isset( $_SERVER[ $key ] ) ) {
            $header = trim( $_SERVER[ $key ] );
        } elseif( isset( $_SERVER[ $uppercase_header ] ) ) {
            $header = trim( $_SERVER[ $uppercase_header ] );
        } elseif( function_exists( 'apache_request_headers' ) ) {
            $request_headers = apache_request_headers();
            $request_headers = array_combine( array_map( 'ucwords', array_keys( $request_headers ) ), array_values( $request_headers ) );

			if( isset( $request_headers[ $key ] ) ) {
                $header = trim( $request_headers[ $key ] );
            }
        }
        return $header;
    }

	/**
	 * Grab the current user IP from the
	 * server variabbles
	 *
	 * @return string - The IP address
	 */
	public function get_current_ip() {
		$ipaddress = false;
		if ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ){
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ){
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif( isset( $_SERVER['HTTP_X_FORWARDED'] ) ){
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif( isset( $_SERVER['HTTP_FORWARDED_FOR'] ) ){
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif( isset( $_SERVER['HTTP_FORWARDED'] ) ){
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
        } elseif( isset( $_SERVER['REMOTE_ADDR'] ) ){
			$ipaddress = $_SERVER['REMOTE_ADDR'];
        }

		return $ipaddress;
	}

	/**
	 * Get all folders within a given path
	 *
	 * @since 4.2.0
	 * @return string The folders
	 */
	public function get_folders( $path ){

		$folders = array();

		if( ! empty( $path ) && is_dir( $path ) ){
			$all_folders = scandir( $path );
			foreach( $all_folders as $single ){
				$full_path = $path . DIRECTORY_SEPARATOR . $single;

				if( $single == '..' || $single == '.' || ! is_dir( $full_path ) ){
					continue;
				}

				$folders[] = $single;

			}
		}


		return apply_filters( 'wpwhpro/helpers/get_folders', $folders );
	}

	/**
	 * Get all files within a given path
	 *
	 * @since 4.2.0
	 * @return string The files
	 */
	public function get_files( $path, $ignore = array() ){

		$files = array();
		$default_ignore = array(
			'..',
			'.'
		);

		$ignore = array_merge( $default_ignore, $ignore );

		if( ! empty( $path ) && is_dir( $path ) ){
			$all_files = scandir( $path );
			foreach( $all_files as $single ){
				$full_path = $path . DIRECTORY_SEPARATOR . $single;

				if( in_array( $single, $ignore ) || ! file_exists( $full_path ) ){
					continue;
				}

				$files[] = $single;

			}
		}


		return apply_filters( 'wpwhpro/helpers/get_files', $files );
	}

	/**
	 * Get the current request method
	 *
	 * @since 4.0.0
	 * @return string The request method
	 */
	public function get_current_request_method(){
		return apply_filters( 'wpwhpro/helpers/get_current_request_method', $_SERVER['REQUEST_METHOD'] );
	}

	/**
	* Check if a given plugin is installed
	*
	* @param $slug - Plugin slug
	* @return boolean
	*/
	public function is_plugin_installed( $slug ){
		if( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$all_plugins = get_plugins();

		if( ! empty( $all_plugins[ $slug ] ) ){
			return true;
		} else {
			return false;
		}
	}

	/**
	* Check if a given plugin is active
	*
	* @param $plugin - Plugin identifier
	* @return boolean
	*/
	public function is_plugin_active( $plugin = null ){
		$is_active = false;

		if( ! empty( $plugin ) ){
			switch( $plugin ){
				case 'advanced-custom-fields':
					if( class_exists('ACF') ){
						$is_active = true;
					}
				break;
			}
		}

		return apply_filters( 'wpwhpro/helpers/is_plugin_active', $is_active, $plugin );
	}

	/**
	 * Create signature from a given string
	 *
	 * @since 4.3.1
	 * @param mixed $data
	 * @return string
	 */
	public function generate_signature( $data, $secret ) {

		if( is_array( $data ) || is_string( $data ) ){
			$data = json_encode( $data );
		}

		$data = base64_encode( $data );
		$hash_signature = apply_filters( 'wpwhpro/helpers/generate_signature', 'sha256', $data );

		return base64_encode( hash_hmac( $hash_signature, $data, $secret, true ) );
	}

	/**
	 * Return an array in any circumstance
	 *
	 * @since 4.3.6
	 * @param mixed $data
	 * @return array
	 */
	public function force_array( $data ) {

		$return = array();

		if( empty( $data ) ){
			return $return;
		}

		if( is_string( $data ) && $this->is_json( $data ) ){
			$return = json_decode( $data, true );
		} elseif( is_array( $data ) || is_object( $data ) ){
			$return = json_decode( json_encode( $data ), true ); //streamline data
		} else {
			$return = array( $data );
		}

		return apply_filters( 'wpwhpro/helpers/force_array', $return, $data );
	}

	/**
	 * Serves the first value of a given variable. 
	 * 
	 * - If it is a string, the string is served
	 * - With an array, we serve the first value
	 * - With an object, we serve the first value
	 *
	 * @since 5.1.1
	 * @param mixed $data
	 * @return array
	 */
	public function serve_first( $data ) {

		$return = '';

		if( is_array( $data ) ){
			$return = reset( $data );
		} elseif( is_object( $data ) ){
			$return = reset( $data );
		} else {
			$return = (string) $data;
		}

		return apply_filters( 'wpwhpro/helpers/serve_first', $return, $data );
	}

	/**
	 * Return an array in any circumstance
	 *
	 * @since 4.3.6
	 * @param mixed $data
	 * @return array
	 */
	public function get_formatted_date( $date, $date_format = 'Y-m-d H:i:s' ) {

		$return = false;

		if( empty( $date ) ){
			return $return;
		}

		if( is_numeric( $date ) ){
			$return = date( $date_format, $date );
		} else {
			$return = date( $date_format, strtotime( $date ) );
		}

		return apply_filters( 'wpwhpro/helpers/get_formatted_date', $return, $date, $date_format );
	}

	/**
	 * Verify the current user and make allow the 
	 * permission to be customized
	 *
	 * @param string $capability
	 * @param string $permission_type
	 * @return bool
	 */
	public function current_user_can( $capability = '', $permission_type = 'default' ){
		return apply_filters( 'wpwhpro/helpers/current_user_can', current_user_can( $capability ), $capability, $permission_type );
	}

	/**
	 * Create a dynamic URL that points to the equivalend endpoint 
	 * at the store page
	 * 
	 * @since 5.0
	 *
	 * @param string $integration
	 * @param string $endpoint
	 * @param string $type
	 * @return string
	 */
	public function get_wp_webhooks_endpoint_url( $integration, $endpoint = '', $type = 'trigger' ){

		$url = '';

		$integration = sanitize_title( $integration );
		$endpoint = sanitize_title( $endpoint );

		if( ! empty( $integration ) ){
			$url = IRONIKUS_STORE . '/integrations/' . $integration;

			if( ! empty( $endpoint ) ){
				$url .= '/' . sanitize_title( $type ) . 's/' . $endpoint;
			}
		}

		return $url;
	}

	/**
	 * Get all Server request headers
	 * 
	 * @since 5.2
	 *
	 * @return array The server headers
	 */
	public function get_all_headers(){

		$headers = array();

		if( function_exists('getallheaders') ){
			$headers = getallheaders();
		} else {

			foreach( $_SERVER as $name => $value ){
				if( substr ($name, 0, 5 ) == 'HTTP_' ){
					$header_key = str_replace( ' ', '-', ucwords( strtolower( str_replace( '_', ' ', substr( $name, 5 ) ) ) ) );
					$headers[ $header_key ] = $value;
				}
			}
			
		}

		return apply_filters( 'wpwhpro/helpers/get_all_headers', $headers );
	}

}
