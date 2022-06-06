<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_bbpress_Triggers_bbp_topic_reply_created' ) ) :

 /**
  * Load the bbp_topic_reply_created trigger
  *
  * @since 5.1
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_bbpress_Triggers_bbp_topic_reply_created {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'bbp_new_reply',
				'callback' => array( $this, 'bbp_topic_reply_created_callback' ),
				'priority' => 10,
				'arguments' => 7,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-bbp_topic_reply_created-description";

		$parameter = array(
			'reply_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The Id of the newly created reply.', $translation_ident ) ),
			'topic_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The topic ID related to the reply.', $translation_ident ) ),
			'forum_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the forum the reply was created in.', $translation_ident ) ),
			'reply_author' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the author of the reply.', $translation_ident ) ),
			'reply_to' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the reply it is related.', $translation_ident ) ),
			'reply_data' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further data about the given reply.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Topic reply created',
			'webhook_slug' => 'bbp_topic_reply_created',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'bbp_new_reply',
				),
			),
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_bbp_topic_reply_created_trigger_on_forums' => array(
					'id'	 => 'wpwhpro_bbp_topic_reply_created_trigger_on_forums',
					'type'	=> 'select',
					'multiple'  => true,
					'choices'   => array(),
					'query'			=> array(
						'filter'	=> 'posts',
						'args'		=> array(
							'post_type' => ( function_exists( 'bbp_get_forum_post_type' ) ) ? bbp_get_forum_post_type() : 'forum',
						)
					),
					'label'	=> WPWHPRO()->helpers->translate( 'Trigger on selected forums', $translation_ident ),
					'placeholder' => '',
					'required'  => false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the forums you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'bbp_topic_reply_created',
			'name'			  => WPWHPRO()->helpers->translate( 'Topic reply created', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a topic reply was created', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a topic reply was created within bbPress.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'bbpress',
			'premium'		   => true,
		);

	}

	public function bbp_topic_reply_created_callback( $reply_id, $topic_id, $forum_id, $anonymous_data, $reply_author, $unusedbool, $reply_to ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'bbp_topic_reply_created' );
		$response_data_array = array();

		$payload = array(
			'reply_id' => $reply_id,
			'topic_id' => $topic_id,
			'forum_id' => $forum_id,
			'reply_author' => $reply_author,
			'reply_to' => $reply_to,
			'reply_data' => array(
				'reply_title' => bbp_get_reply_title( $reply_id ),
				'reply_status' => bbp_get_reply_status( $reply_id ),
				'published' => bbp_is_reply_published( $reply_id ),
				'excerpt' => bbp_get_reply_excerpt( $reply_id ),
				'content' => bbp_get_reply_content( $reply_id ),
				'permalink' => bbp_get_reply_permalink( $reply_id ),
				'paginated_url' => bbp_get_reply_url( $reply_id ),
				'created_date' => bbp_get_reply_post_date( $reply_id ),
				'is_spam' => bbp_is_reply_spam( $reply_id ),
				'is_trash' => bbp_is_reply_trash( $reply_id ),
				'is_pending' => bbp_is_reply_pending( $reply_id ),
				'is_private' => bbp_is_reply_private( $reply_id ),
				'is_anonymous' => bbp_is_reply_anonymous( $reply_id ),
				'author_display_name' => bbp_get_reply_author_display_name( $reply_id ),
				'author_name' => bbp_get_reply_author( $reply_id ),
				'author_id' => bbp_get_reply_author_id( $reply_id ),
				'author_email' => bbp_get_reply_author_email( $reply_id ),
				'author_avatar' => bbp_get_reply_author_avatar( $reply_id ),
				'author_url' => bbp_get_reply_author_url( $reply_id ),
				'author_link' => bbp_get_reply_author_link( $reply_id ),
				'author_role' => bbp_get_reply_author_role( $reply_id ),
				'topic_title' => bbp_get_reply_topic_title( $reply_id ),
			),
		);

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){

					if( $settings_name === 'wpwhpro_bbp_topic_reply_created_trigger_on_forums' && ! empty( $settings_data ) ){
						if( ! in_array( $forum_id, $settings_data ) ){
							$is_valid = false;
						}
					}

				}
			}

			if( $is_valid ){
				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				} else {
					$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				}
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_bbp_topic_reply_created', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'reply_id' => 9252,
			'topic_id' => 9249,
			'forum_id' => 9231,
			'anonymous_data' => 
			array (
			),
			'reply_author' => 1,
			'reply_to' => 0,
			'reply_data' => 
			array (
			  'reply_title' => 'Reply To: Demo Topic',
			  'reply_status' => 'publish',
			  'published' => true,
			  'excerpt' => 'This is a demo comment.',
			  'content' => '<p>This is a demo comment.</p>
		  ',
			  'permalink' => 'https://yourdomain.test/forums/reply/9252/',
			  'paginated_url' => 'https://yourdomain.test/forums/topic/demo-topic/#post-9252',
			  'created_date' => 'April 14, 2022 at 10:05 am',
			  'is_spam' => false,
			  'is_trash' => false,
			  'is_pending' => false,
			  'is_private' => false,
			  'is_anonymous' => false,
			  'author_display_name' => 'admin',
			  'author_name' => 'admin',
			  'author_id' => 1,
			  'author_email' => 'admin@yourdomain.test',
			  'author_avatar' => '<img alt=\'\' src=\'https://secure.gravatar.com/avatar/xxxxxxxxxx?s=40&#038;d=mm&#038;r=g\' srcset=\'https://secure.gravatar.com/avatar/xxxxxxxxxx?s=80&#038;d=mm&#038;r=g 2x\' class=\'avatar avatar-40 photo\' height=\'40\' width=\'40\' loading=\'lazy\'/>',
			  'author_url' => 'https://yourdomain.test/forums/users/admin/',
			  'author_link' => '<a href="https://yourdomain.test/forums/users/admin/" title="View admin&#039;s profile" class="bbp-author-link"><span  class="bbp-author-avatar"><img alt=\'\' src=\'https://secure.gravatar.com/avatar/xxxxxxxxxx?s=80&#038;d=mm&#038;r=g\' srcset=\'https://secure.gravatar.com/avatar/xxxxxxxxxx?s=160&#038;d=mm&#038;r=g 2x\' class=\'avatar avatar-80 photo\' height=\'80\' width=\'80\' loading=\'lazy\'/></span><span  class="bbp-author-name">admin</span></a>',
			  'author_role' => '<div class="bbp-author-role">Keymaster</div>',
			  'topic_title' => 'Demo Topic',
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.