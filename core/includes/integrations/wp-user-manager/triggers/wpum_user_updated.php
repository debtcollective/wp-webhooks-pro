<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_wp_user_manager_Triggers_wpum_user_updated' ) ) :

 /**
  * Load the wpum_user_updated trigger
  *
  * @since 4.3.5
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_wp_user_manager_Triggers_wpum_user_updated {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'wpum_after_user_update',
				'callback' => array( $this, 'wpum_user_updated_callback' ),
				'priority' => 20,
				'arguments' => 3,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-wpum_user_updated-description";

		$parameter = array(
			'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the user that was updated.', $translation_ident ) ),
			'values' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) All of the updated values.', $translation_ident ) ),
			'user' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further data about the user.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'User updated',
			'webhook_slug' => 'wpum_user_updated',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'wpum_after_user_update',
				),
			),
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array()
		);

		return array(
			'trigger'		   => 'wpum_user_updated',
			'name'			  => WPWHPRO()->helpers->translate( 'User updated', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a user was updated', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a user was updated within WP User Manager.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'wp-user-manager',
			'premium'		   => true,
		);

	}

	public function wpum_user_updated_callback( $form, $values, $updated_user_id ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wpum_user_updated' );
			
		$response_data_array = array();

		$payload = array(
			'user_id' => $updated_user_id,
			'values' => $values,
			'user' => get_userdata( $updated_user_id ),
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

		do_action( 'wpwhpro/webhooks/trigger_wpum_user_updated', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'user_id' => 170,
			'values' => 
			array (
			  'account' => 
			  array (
				'user_cover' => 'https://yourdomain.test/wp-content/uploads/wp-user-manager-uploads/2022/02/your-user-cover-image.png',
				'user_email' => 'jondoe@demo.test',
				'user_firstname' => '',
				'user_lastname' => '',
				'user_nickname' => 'jondoe@demo.test',
				'user_displayname' => 'display_nickname',
				'user_website' => '',
				'user_description' => '',
				'user_avatar' => 'https://yourdomain.test/wp-content/uploads/wp-user-manager-uploads/2022/02/your-profile-image.png',
			  ),
			),
			'user' => 
			array (
			  'data' => 
			  array (
				'ID' => '170',
				'user_login' => 'jondoe@demo.test',
				'user_pass' => '$P$BcQhd1FC3piXXXXXXXXXWyAC.',
				'user_nicename' => 'jondoedemo-test',
				'user_email' => 'jondoe@demo.test',
				'user_url' => '',
				'user_registered' => '2022-02-15 06:03:41',
				'user_activation_key' => '1644905021:$P$Bul.8.ZRrlf2/ICbXXXXXXXX38iG/',
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
		);

		return $data;
	}

  }

endif; // End if class_exists check.