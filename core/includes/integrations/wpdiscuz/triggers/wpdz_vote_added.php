<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_wpdiscuz_Triggers_wpdz_vote_added' ) ) :

 /**
  * Load the wpdz_vote_added trigger
  *
  * @since 5.1.1
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_wpdiscuz_Triggers_wpdz_vote_added {

  public function get_callbacks(){

   return array(
	array(
		'type' => 'action',
		'hook' => 'wpdiscuz_add_vote',
		'callback' => array( $this, 'ironikus_trigger_wpdz_vote_added' ),
		'priority' => 20,
		'arguments' => 2,
		'delayed' => true,
	  ),
	);

  }

	public function get_details(){

		$translation_ident = "trigger-wpdz_vote_added-description";

		$parameter = array(
			'voteType' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The type of the vote. 1 equals an upvote, -1 a downvote.', $translation_ident ) ),
			'comment' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further details about the comment.', $translation_ident ) ),
		);

	  	$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Vote added',
			'webhook_slug' => 'wpdz_vote_added',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'wpdiscuz_add_vote',
				),
			)
		) );

	  	$settings = array(
			'load_default_settings' => true,
			'data' => array(
			'wpwhpro_wpdz_vote_added_trigger_on_type' => array(
				'id'	 => 'wpwhpro_wpdz_vote_added_trigger_on_type',
				'type'	=> 'select',
				'multiple'  => true,
				'choices'   => array(
					'upvote' => WPWHPRO()->helpers->translate( 'Upvote', $translation_ident ),
					'downvote' => WPWHPRO()->helpers->translate( 'Downvote', $translation_ident ),
				),
				'label'	=> WPWHPRO()->helpers->translate( 'Trigger on selected type', $translation_ident ),
				'placeholder' => '',
				'required'  => false,
				'description' => WPWHPRO()->helpers->translate( 'Select only the vote types you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
			),
			)
		);

		return array(
			'trigger'	  => 'wpdz_vote_added',
			'name'	   => WPWHPRO()->helpers->translate( 'Vote added', $translation_ident ),
			'sentence'	   => WPWHPRO()->helpers->translate( 'a vote was added', $translation_ident ),
			'parameter'	 => $parameter,
			'settings'	 => $settings,
			'returns_code'   => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after a vote was added within wpDiscuz.', $translation_ident ),
			'description'	=> $description,
			'integration'	=> 'wpdiscuz',
			'premium'	=> true,
		);

	}

	public function ironikus_trigger_wpdz_vote_added( $voteType, $comment ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wpdz_vote_added' );
		$data_array = array(
			'voteType' => $voteType,
			'comment' => $comment,
		);
		$response_data = array();

		foreach( $webhooks as $webhook ){

			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
			foreach( $webhook['settings'] as $settings_name => $settings_data ){

				if( $settings_name === 'wpwhpro_wpdz_vote_added_trigger_on_courses' && ! empty( $settings_data ) ){
					$is_valid = false;

					if( in_array( 'upvote', $settings_data ) && $voteType > 0 ){
						$is_valid = true;
					} elseif( in_array( 'downvote', $settings_data ) && $voteType < 0 ) {
						$is_valid = true;
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

		do_action( 'wpwhpro/webhooks/trigger_wpdz_vote_added', $data_array, $response_data );
	}

	/*
	* Register the demo post delete trigger callback
	*
	* @since 1.2
	*/
	public function get_demo( $options = array() ) {

	  $data = array (
		'voteType' => '1',
		'comment' => 
		array (
			'comment_ID' => '320',
			'comment_post_ID' => '7912',
			'comment_author' => 'demouser',
			'comment_author_email' => 'demouser@demo.test',
			'comment_author_url' => '',
			'comment_author_IP' => '127.0.0.1',
			'comment_date' => '2022-04-26 12:19:52',
			'comment_date_gmt' => '2022-04-26 12:19:52',
			'comment_content' => 'This is a demo comment',
			'comment_karma' => '0',
			'comment_approved' => '1',
			'comment_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.83 Safari/537.36',
			'comment_type' => 'comment',
			'comment_parent' => '0',
			'user_id' => '1',
		),
	);

	  return $data;
	}

  }

endif; // End if class_exists check.