<?php

/**
 * WP_Webhooks_Pro_Whitelist Class
 *
 * This class contains the whole whitelist functionality
 *
 * @since 1.5.7
 */

/**
 * The whitelist class of the plugin.
 *
 * @since 1.5.7
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Whitelist {

	private $is_active = null;
	private $whitelist = null;
	private $whitelist_requests = null;

	/**
	 * WP_Webhooks_Pro_Whitelist constructor.
	 */
	public function __construct() {
		$this->is_active();
	}

	/**
	 * Check if the whitelist is active
	 *
	 * @return bool - true if active, false if not
	 */
	public function is_active(){
		if( $this->is_active !== null ){
			return $this->is_active;
		}

		if( get_option( 'wpwhpro_activate_whitelist' ) == 'yes' ){
			$this->is_active = true;
			return true;
		} else {
			$this->is_active = false;
			return false;
		}
	}

	/**
	 * Get the whitelist data
	 *
	 * @return array - an array of whitelisted files
	 */
	public function get_list(){
		if( $this->whitelist !== null ){
			return $this->whitelist;
		}

		$whitelist_data = get_option( WPWHPRO()->settings->get_whitelist_option_key() );
		if( ! is_array( $whitelist_data ) ){
			$whitelist_data = array();
		}

		$this->whitelist = $whitelist_data;
		return $whitelist_data;
	}

	/**
	 * Add an ip to the whitelist
	 *
	 * @param $ip - the ip address
	 *
	 * @return bool - true if ip was added, false if not
	 */
	public function add_item( $ip, $args = array() ){
		$list = $this->get_list();
		$key = md5( $ip );
		$return = false;

		if( isset( $args['key'] ) && ! empty( $args['key'] ) ){
			$key = sanitize_title( $args['key'] );
		}

		if( ! isset( $list[ $key ] ) ){
			$list[ $key ] = $ip;

			update_option( WPWHPRO()->settings->get_whitelist_option_key(), $list );
			$this->whitelist = $list;
			$return = true;
		}

		return $return;
	}

	/**
	 * Delete an IP from the whitelist
	 *
	 * @param $key - The key of the deleted item (md5 value of ip)
	 *
	 * @return bool - true if deleted, false if not
	 */
	public function delete_item( $key ){
		
		$return = false;


		if( $key === 'all' ){
			delete_option( WPWHPRO()->settings->get_whitelist_option_key() );
			$this->whitelist = null;
			$return = true;
		} else {
			$list = $this->get_list();
			if( isset( $list[ $key ] ) ){
				unset( $list[ $key ] );
	
				update_option( WPWHPRO()->settings->get_whitelist_option_key(), $list );
				$this->whitelist = $list;
				$return = true;
			}
		}		

		return $return;
	}

	/**
	 * Wether the incoming request is whitelisted or not
	 *
	 * @return bool - true if whitelisted, false if not
	 */
	public function is_valid_request(){
		$list = $this->get_list();
		$current_ip = (string) WPWHPRO()->helpers->get_current_ip();
		$return = false;

		foreach( $list as $ip_rule ){
			if( ! empty( $current_ip ) &&  fnmatch( $ip_rule, $current_ip ) ){
				$return = true;
			}
		}

		return $return;
	}

	/**
	 * ######################
	 * ###
	 * #### WHITELIST REQUESTS
	 * ###
	 * ######################
	 */

	/**
	 * Returns an list of the last 20 ip requests to your website,
	 * that are not whitelisted.
	 *
	 * @return array - an array of the requested ip addresses
	 */
	public function get_request_list(){
		if( $this->whitelist_requests !== null ){
			return $this->whitelist_requests;
		}

		$whitelist_request_data = get_option( WPWHPRO()->settings->get_whitelist_requests_option_key() );
		if( ! is_array( $whitelist_request_data ) ){
			$whitelist_request_data = array();
		}

		krsort( $whitelist_request_data );

		$this->whitelist_requests = $whitelist_request_data;
		return $whitelist_request_data;
	}

	/**
	 * Adds a request from an ip to the request array/option
	 *
	 * @param $ip - the ip address that makes the request
	 * @param array $request_data - DEPRECATED
	 *
	 * @return bool - true if request was added, false if not
	 */
	public function add_request( $ip, $deprecated = array() ){
		$list = $this->get_request_list();
		$key = time();
		$return = false;

		// force array for data
		$request_data = base64_encode( json_encode( $_REQUEST ) );

		if( ! isset( $list[ $key ] ) ){
			$list[ $key ] = array(
				'ip' => $ip,
				'data' => $request_data
			);

			krsort( $list );

			if( count( $list ) > 20 ){
				$diff = count( $list ) - 20;
				while( $diff > 0 ){
					array_pop( $list );
					$diff--;
				}
			}

			update_option( WPWHPRO()->settings->get_whitelist_requests_option_key(), $list );
			$this->whitelist_requests = $list;
			$return = true;
		}

		return $return;
	}
}
