<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_bbpress_Helpers_bbp_helpers' ) ) :

	/**
	 * Load the WooCommerce Memberships helpers
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_bbpress_Helpers_bbp_helpers {

		private $forums_cache = null;

		public function get_forums(){

			if( $this->forums_cache !== null ){
				return $this->forums_cache;
			}

			$validated_forums = array();

			$forums = get_posts( array(
				'post_type' => bbp_get_forum_post_type(),
				'posts_perpage' => 999,
			) );
			if( ! empty( $forums ) ){
				foreach( $forums as $forum ){
					$validated_forums[ $forum->ID ] = $forum->post_title;
				}
			}

			$this->forums_cache = $validated_forums;

			return $validated_forums;
		}

	}

endif; // End if class_exists check.