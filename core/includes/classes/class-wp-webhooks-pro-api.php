<?php

/**
 * WP_Webhooks_Pro_API Class
 *
 * This class contains all of the available api functions
 *
 * @since 1.0.0
 */

/**
 * The api class of the plugin.
 *
 * @since 1.0.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_API {

	/**
	 * This is the main page for handling api requests
	 * @var string
	 */
	protected $api_url = 'https://wp-webhooks.com';

	/**
	 * ################################
	 * ###
	 * ##### --- News feed ---
	 * ###
	 * ################################
	 */

	/**
	 * Get the news feed based on a specified post
	 *
	 * @param $news_id
	 * @return mixed bool if response is empty
	 */
	public function get_news_feed($news_id){

		if(!is_numeric($news_id))
			return false;

		$news_transient = get_transient( WPWHPRO()->settings->get_news_transient_key() );

		if( empty ( $news_transient ) || isset( $_GET['wpwhpro_renew_transient'] ) ){
			$news = WPWHPRO()->helpers->get_from_api( $this->api_url . '/wp-json/ironikus/v1/news/display/' . intval($news_id), 'body' );

			if(!empty($news)){
				$news             = ! empty( $news ) ? json_decode( $news, true ) : '' ;
				$news             = ( is_array( $news ) && $news['success'] == true ) ? $news['data'] : '' ;

				set_transient( WPWHPRO()->settings->get_news_transient_key(), $news, strtotime('1 day', 0) );

				return WPWHPRO()->helpers->validate_local_tags( $news );
			} else {
				return false;
			}

		} else {
			return WPWHPRO()->helpers->validate_local_tags( $news_transient );
		}

	}

	/**
	 * Get a list of all available extensions
	 *
	 * @param $news_id
	 * @return mixed bool if response is empty
	 */
	public function get_extension_list(){

		$extensions_transient = get_transient( WPWHPRO()->settings->get_extensions_transient_key() );

		if( empty ( $extensions_transient ) || isset( $_GET['wpwhpro_renew_transient'] ) ){
			$extensions = WPWHPRO()->helpers->get_from_api( $this->api_url . '/wp-json/ironikus/v1/extensions/list/', 'body' );

			if(!empty($extensions)){
				$extensions             = ! empty( $extensions ) ? json_decode( $extensions, true ) : '' ;
				$extensions             = ( is_array( $extensions ) && $extensions['success'] == true ) ? $extensions['data'] : '' ;

				set_transient( WPWHPRO()->settings->get_extensions_transient_key(), $extensions, strtotime('1 day', 0) );

				return $extensions;
			} else {
				return false;
			}

		} else {
			return $extensions_transient;
		}

	}

}
