<?php

/**
 * WP_Webhooks_Pro_License Class
 *
 * This class contains all of the available license functions
 *
 * @since 1.0.0
 */

/**
 * The license class of the plugin.
 *
 * @since 1.0.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_License {

	/**
	 * WP_Webhooks_Pro_License constructor.
	 */
	public function __construct() {

		$this->license_data         	= WPWHPRO()->settings->get_license();
		$this->license_option_key		= WPWHPRO()->settings->get_license_option_key();
		$this->license_transient_key	= WPWHPRO()->settings->get_license_transient_key();

	}

	/**
	 * Execute feature related hooks and logic to get 
	 * everything running
	 *
	 * @since 5.0
	 * @return void
	 */
	public function execute(){

		add_action( 'admin_notices', array( $this, 'ironikus_throw_admin_notices' ), 100 );
		add_action( 'wpwh_daily_maintenance', array( $this, 'verify_license_status' ), 10 );

	}

	/**
	 * Check and sync the license status
	 *
	 * @since 5.0
	 * @return void
	 */
	public function verify_license_status(){

		//Do nothing if the license is not active
		if( ! $this->is_active() ){
			return;
		}

		$license_is_active = $this->verify_renewal();
		if( $license_is_active === false ){
			$this->update( 'status', 'expired' );
		}

	}

	/**
	 * Throw custom admin notice based on the given license settings
	 *
	 * @return void
	 */
	public function ironikus_throw_admin_notices(){

		if ( empty( $this->license_data['key'] ) ) {
			echo sprintf(WPWHPRO()->helpers->create_admin_notice( 'If you run a WP Webhook Pro integration on a live site, we recommend our annual support license for updates and premium support. <a href="%s" target="_blank" rel="noopener">More Info</a>', 'warning', false ), 'https://wp-webhooks.com/?utm_source=wp-webhooks-pro&utm_medium=notice-license-not-set&utm_campaign=WP%20Webhooks%20Pro');
		} else {
			if ( empty( $this->license_data['status'] ) || $this->license_data['status'] !== 'valid' ) {

				$license_is_expired = false;
				if ( ! empty( $this->license_data['expires'] ) ) {
					$license_is_expired = $this->is_expired( $this->license_data['expires'] );
				}

				if ( $license_is_expired ) {
					echo sprintf(WPWHPRO()->helpers->create_admin_notice( 'Your license key has expired. We recommend in renewing your annual support license to continue to get automatic updates and premium support. <a href="%s" target="_blank" rel="noopener">More Info</a>', 'warning' ), 'https://wp-webhooks.com/?utm_source=wp-webhooks-pro&utm_medium=notice-license-expired&utm_campaign=WP%20Webhooks%20Pro');
				} else {
					echo sprintf(WPWHPRO()->helpers->create_admin_notice( 'If you run a WP Webhook Pro integration on a live site, we recommend our annual support license for updates and premium support. <a href="%s" target="_blank" rel="noopener">More Info</a>', 'warning', false ), 'https://wp-webhooks.com/?utm_source=wp-webhooks-pro&utm_medium=notice-license-not-activated&utm_campaign=WP%20Webhooks%20Pro');
				}
				
			}
		}

	}
	
	/**
	 * The initial license renewal function
	 * This function is deprecated.
	 * 
	 * @deprecated deprecated since version 5.0
	 * @return void
	 */
	public function ironikus_verify_license_renewal(){

			if( ! is_admin() ){
				return;
			}

			$license_expires = WPWHPRO()->settings->get_license('expires');
			if( ! $this->is_expired( $license_expires ) ){
				//Clear transient as well in case te licesne isn't expired
				delete_transient( $this->license_transient_key );
				return;
			}

			$check_license_renewal = get_transient( $this->license_transient_key );
			if( ! empty( $check_license_renewal ) ){
				return;
			}

			$is_renewed = $this->verify_renewal();

			if( $is_renewed ){
				delete_transient( $this->license_transient_key );
			} else {
				set_transient( $this->license_transient_key, $license_expires, 60 * 60 * 2 );
			}
	}

	public function verify_renewal(){
		$is_renewed = null;
		$license_key = WPWHPRO()->settings->get_license('key');
		$response = $this->check( array( 'license' => $license_key	) );

		if ( ! is_wp_error( $response ) && 200 === intval( wp_remote_retrieve_response_code( $response ) ) ) {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if( ! empty( $license_data ) && $license_data->license == 'valid' ){
				$this->update( 'status', $license_data->license );
				$this->update( 'expires', $license_data->expires );
				
				$is_renewed = $this->license_data;
			} else {
				$is_renewed = false;
			}

		}

		return $is_renewed;
	}

	/**
	 * Check if the license is expired
	 *
	 * @param $expiry_date - The date of the expiration of the license
	 *
	 * @return bool - false if the license is expired
	 */
	public function is_expired( $expiry_date ) {

		if( empty( $expiry_date ) ){
			return true;
		}

		if( $expiry_date === 'lifetime' ){
			return false;
		}

		$today = date( 'Y-m-d H:i:s' );

		if ( WPWHPRO()->helpers->get_datetime($expiry_date) < WPWHPRO()->helpers->get_datetime($today) ) {
			$is_expired = true;
		} else {
			$is_expired = false;
		}

		return $is_expired;

	}

	/**
	 * Check if the license is active
	 *
	 * @since 5.0
	 *
	 * @return bool - false if the license is expired
	 */
	public function is_active() {
		$is_active = true;

		$license_key        = $this->license_data['key'];
		$license_status     = $this->license_data['status'];
		$license_expires    = $this->license_data['expires'];

		if( empty( $license_key ) ){
			$is_active = false;
		}

		if( empty( $license_status ) || $license_status !== 'valid' ){
			$is_active = false;
		}

		if( empty( $license_expires ) ){
			$is_active = false;
		}

		if( $license_expires !== 'lifetime' && $this->is_expired( $license_expires ) ){
			$is_active = false;
		}

		return $is_active;

	}

	/**
	 * Update the license status based on the given data
	 *
	 * @param string $key - the license data key
	 * @param string $value - the value that should be updated
	 * @return bool - True if license data was updates, false if not
	 */
	public function update($key, $value = ''){
		$return = false;

		if( empty( $key ) ){
			return $return;
		}

		$this->license_data[ $key ] = $value;
		$return = update_option( $this->license_option_key, $this->license_data );

		return $return;
	}

	/**
	 * Check if the license is still active
	 *
	 * @param array $args - The arguments for the request (Currently supports only license)
	 * @return array - The response data of the request
	 */
	public function check( $args ){

		if( empty( $args['license'] ) )
			return false;

		$home_url = home_url();

		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => $args['license'],
			'item_id'    => WPWHPRO_PLUGIN_ID,
			'url'        => $home_url
		);

		$response = wp_remote_post( IRONIKUS_STORE, array( 'timeout' => 30, 'sslverify' => true, 'body' => $api_params ) );

		return $response;
	}

	/**
	 * Activate the license if possible
	 *
	 * @param array $args - The arguments for the request (Currently supports only license)
	 * @return mixed - The response data of the request
	 */
	public function activate( $args ){

		if( empty( $args['license'] ) )
			return false;

		$home_url = home_url();

		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $args['license'],
			'item_id'    => WPWHPRO_PLUGIN_ID,
			'url'        => $home_url
		);

		$response = wp_remote_post( IRONIKUS_STORE, array( 'timeout' => 30, 'sslverify' => true, 'body' => $api_params ) );

		return $response;
	}

	/**
	 * Deactivate a given license
	 *
	 * @param array $args - The arguments for the request (Currently supports only license)
	 * @return array - The response data of the request
	 */
	public function deactivate( $args ){

		if( empty( $args['license'] ) )
			return false;

		$home_url = home_url();

		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $args['license'],
			'item_id'    => WPWHPRO_PLUGIN_ID,
			'url'        => $home_url
		);

		$response = wp_remote_post( IRONIKUS_STORE, array( 'timeout' => 30, 'sslverify' => true, 'body' => $api_params ) );

		return $response;
	}

}
