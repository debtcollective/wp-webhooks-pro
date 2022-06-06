<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_wp_polls_Triggers_wppolls_poll_voted' ) ) :

	/**
	 * Load the wppolls_poll_voted trigger
	*
	* @since 5.1.1
	* @author Ironikus <info@ironikus.com>
	*/
	class WP_Webhooks_Integrations_wp_polls_Triggers_wppolls_poll_voted {

		public function get_callbacks(){

			return array(
				array(
					'type' => 'action',
					'hook' => 'wp_polls_vote_poll_success',
					'callback' => array( $this, 'ironikus_trigger_wppolls_poll_voted' ),
					'priority' => 20,
					'arguments' => 1,
					'delayed' => true,
				),
			);

		}

		public function get_details(){

			$translation_ident = "trigger-wppolls_poll_voted-description";

			$parameter = array(
				'poll_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the poll.', $translation_ident ) ),
				'pollip_user' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The name of the user who voted.', $translation_ident ) ),
				'pollip_user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the user who voted.', $translation_ident ) ),
				'pollip_ip' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The poll host IP.', $translation_ident ) ),
				'pollip_host' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The poll host.', $translation_ident ) ),
				'poll_logging_method' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The logging method of the poll.', $translation_ident ) ),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
				'webhook_name' => 'Poll voted',
				'webhook_slug' => 'wppolls_poll_voted',
				'post_delay' => true,
				'trigger_hooks' => array(
					array( 
						'hook' => 'wp_polls_vote_poll_success',
					),
				)
			) );

			$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_wppolls_poll_voted_trigger_on_poll' => array(
					'id'	 => 'wpwhpro_wppolls_poll_voted_trigger_on_poll',
					'type'	=> 'select',
					'multiple'  => true,
					'choices'   => array(),
					'query'			=> array(
						'filter'	=> 'helpers',
						'args'		=> array(
							'integration' => 'wp-polls',
							'helper' => 'wppolls_helpers',
							'function' => 'get_query_polls',
						)
					),
					'label'	=> WPWHPRO()->helpers->translate( 'Trigger on selected polls', $translation_ident ),
					'placeholder' => '',
					'required'  => false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the polls you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'	  => 'wppolls_poll_voted',
			'name'	   => WPWHPRO()->helpers->translate( 'Poll voted', $translation_ident ),
			'sentence'	   => WPWHPRO()->helpers->translate( 'a user voted on a poll', $translation_ident ),
			'parameter'	 => $parameter,
			'settings'	 => $settings,
			'returns_code'   => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after a user voted on a poll within WP-Polls.', $translation_ident ),
			'description'	=> $description,
			'callback'	 => 'test_wppolls_poll_voted',
			'integration'	=> 'wp-polls',
			'premium'	=> false,
		);

		}

		public function ironikus_trigger_wppolls_poll_voted(){

			global $user_identity, $user_ID;

			$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wppolls_poll_voted' );
			$poll_id = ( isset($_REQUEST['poll_id'] ) ) ? intval( sanitize_key( $_REQUEST['poll_id'] ) ) : 0;

			if( ! empty( $user_identity ) ){
				$pollip_user = $user_identity;
			} elseif( ! empty( $_COOKIE[ 'comment_author_' . COOKIEHASH ] ) ) {
				$pollip_user = $_COOKIE[ 'comment_author_' . COOKIEHASH ];
			} else {
				$pollip_user = '';
			}

			$data_array = array(
				'poll_id' => $poll_id,
				'pollip_user' => sanitize_text_field( $pollip_user ),
				'pollip_user_id' => $user_ID,
				'pollip_ip' => poll_get_ipaddress(),
				'pollip_host' => poll_get_hostname(),
				'poll_logging_method' => (int) get_option('poll_logging_method'),
			);
			$response_data = array();

			foreach( $webhooks as $webhook ){

				$is_valid = true;

				if( isset( $webhook['settings'] ) ){
					foreach( $webhook['settings'] as $settings_name => $settings_data ){

						if( $settings_name === 'wpwhpro_wppolls_poll_voted_trigger_on_poll' && ! empty( $settings_data ) ){
							if( ! in_array( $poll_id, $settings_data ) ){
								$is_valid = false;
							}
						}

					}
				}

				if( $is_valid ) {
					$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

					if( $webhook_url_name !== null ){
						$response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
					} else {
						$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
					}
				}
			}

			do_action( 'wpwhpro/webhooks/trigger_wppolls_poll_voted', $data_array, $response_data );
		}

		public function get_demo( $options = array() ) {

			$data = array (
				'poll_id' => 2,
				'pollip_user' => 'admin',
				'pollip_user_id' => 1,
				'pollip_ip' => '73c2b87c8971d558856fe6f9a7598f62',
				'pollip_host' => 'localhost',
				'poll_logging_method' => 3,
			);

			return $data;
		}

	}

endif; // End if class_exists check.