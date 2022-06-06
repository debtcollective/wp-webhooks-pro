<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_wp_user_manager_Triggers_wpum_update_cover' ) ) :

 /**
  * Load the wpum_update_cover trigger
  *
  * @since 4.3.5
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_wp_user_manager_Triggers_wpum_update_cover {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'wpum_user_update_change_cover',
				'callback' => array( $this, 'wpum_update_cover_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-wpum_update_cover-description";

		$parameter = array(
			'cover_url' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The URL of the uploaded cover image.', $translation_ident ) ),
			'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the user.', $translation_ident ) ),
			'user' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further user details.', $translation_ident ) ),
			'user_meta' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The user meta.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Cover photo updated',
			'webhook_slug' => 'wpum_update_cover',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'wpum_user_update_change_cover',
				),
			),
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array()
		);

		return array(
			'trigger'		   => 'wpum_update_cover',
			'name'			  => WPWHPRO()->helpers->translate( 'Cover photo updated', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a cover photo was updated', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a cover photo was updated within WP User Manager.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'wp-user-manager',
			'premium'		   => false,
		);

	}

	public function wpum_update_cover_callback( $user_id, $cover_url ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wpum_update_cover' );
		$response_data_array = array();

		$payload = array(
			'cover_url' => $cover_url,
			'user_id' => $user_id,
			'user' => get_userdata( $user_id ),
			'user_meta' => get_user_meta( $user_id ),
		);

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( $is_valid ){
				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				} else {
					$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				}
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_wpum_update_cover', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'cover_url' => 'https://yourdomain.test/wp-content/uploads/wp-user-manager-uploads/2022/02/your-file-name.png',
			'user_id' => 170,
			'user' => 
			array (
			  'data' => 
			  array (
				'ID' => '170',
				'user_login' => 'jondoe@demo.test',
				'user_pass' => '$P$BcQhXXXXXXXXXXqReFcWyAC.',
				'user_nicename' => 'jondoedemo-test',
				'user_email' => 'jondoe@demo.test',
				'user_url' => '',
				'user_registered' => '2022-02-15 06:03:41',
				'user_activation_key' => '1644905021:$P$Bul.8.ZRrlf2/ICbZgRXXXXXXX8iG/',
				'user_status' => '0',
				'display_name' => 'jondoe@demo.test',
				'spam' => '0',
				'deleted' => '0',
				'membership_level' => false,
				'membership_levels' => 
				array (
				),
			  ),
			  'ID' => 170,
			  'caps' => 
			  array (
				'subscriber' => true,
			  ),
			  'cap_key' => 'wp_capabilities',
			  'roles' => 
			  array (
				0 => 'subscriber',
			  ),
			  'allcaps' => 
			  array (
				'read' => true,
				'level_0' => true,
				'read_private_locations' => true,
				'read_private_events' => true,
				'manage_resumes' => true,
				'subscriber' => true,
			  ),
			  'filter' => NULL,
			),
			'user_meta' => 
			array (
			  'nickname' => 
			  array (
				0 => 'jondoe@demo.test',
			  ),
			  'first_name' => 
			  array (
				0 => '',
			  ),
			  'last_name' => 
			  array (
				0 => '',
			  ),
			  'description' => 
			  array (
				0 => '',
			  ),
			  'rich_editing' => 
			  array (
				0 => 'true',
			  ),
			  'syntax_highlighting' => 
			  array (
				0 => 'true',
			  ),
			  'comment_shortcuts' => 
			  array (
				0 => 'false',
			  ),
			  'admin_color' => 
			  array (
				0 => 'fresh',
			  ),
			  'use_ssl' => 
			  array (
				0 => '0',
			  ),
			  'show_admin_bar_front' => 
			  array (
				0 => 'true',
			  ),
			  'locale' => 
			  array (
				0 => '',
			  ),
			  'wp_capabilities' => 
			  array (
				0 => 'a:1:{s:10:"subscriber";b:1;}',
			  ),
			  'wp_user_level' => 
			  array (
				0 => '0',
			  ),
			  'last_update' => 
			  array (
				0 => '1644906467',
			  ),
			  'session_tokens' => 
			  array (
				0 => '',
			  ),
			  'user_cover' => 
			  array (
				0 => '',
			  ),
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.