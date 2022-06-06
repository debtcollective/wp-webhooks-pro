<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_favorites_Triggers_fvrt_post_favored' ) ) :

    /**
     * Load the fvrt_post_favored trigger
     *
     * @since 5.1.2
     * @author Ironikus <info@ironikus.com>
     */
    class WP_Webhooks_Integrations_favorites_Triggers_fvrt_post_favored {

        public function get_callbacks(){

            return array(
                array(
                    'type' => 'action',
                    'hook' => 'favorites_after_favorite',
                    'callback' => array( $this, 'wpwh_trigger_fvrt_post_favored' ),
                    'priority' => 10,
                    'arguments' => 4,
                    'delayed' => true,
                ),
            );
        }

        public function get_details(){

            $translation_ident = "action-fvrt_post_got_favorite-description";

            $parameter = array(
                'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The user id.', $translation_ident ) ),
                'post_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The post id.', $translation_ident ) ),
                'site_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The site id.', $translation_ident ) ),
            );

            $description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
                'webhook_name' => 'Post favored',
                'webhook_slug' => 'fvrt_post_favored',
                'post_delay' => true,
                'trigger_hooks' => array(
                    array(
                        'hook' => 'favorites_after_favorite'
                    ),
                )
            ) );

            $settings = array(
                'load_default_settings' => true,
                'data' => array(
                    'wpwhpro_fvrt_users_post_favorite_trigger_on_users' => array(
                        'id'			=> 'wpwhpro_fvrt_users_post_favorite_trigger_on_users',
                        'type'			=> 'select',
                        'multiple'		=> true,
                         'choices'		=> array(),
                         'query'			=> array(
                             'filter'	=> 'users',
                             'args'		=> array()
                         ),
                        'label'			=> WPWHPRO()->helpers->translate( 'Trigger on users', $translation_ident ),
                        'placeholder'	=> '',
                        'required'		=> false,
                        'description'	=> WPWHPRO()->helpers->translate( 'Select only the users you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
                    ),
                )
            );

            return array(
                'trigger'		  => 'fvrt_post_favored',
                'name'			  => WPWHPRO()->helpers->translate( 'Post favored', $translation_ident ),
                'sentence'		  => WPWHPRO()->helpers->translate( 'a post got favored', $translation_ident ),
                'parameter'		  => $parameter,
                'settings'		  => $settings,
                'returns_code'	  => $this->get_demo( array() ),
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires once a post was favored.', $translation_ident ),
                'description'	   => $description,
                'integration'	  => 'favorites',
            );

        }

        /**
         * Triggers once a Post got favorite
         *
         * @param  integer $user_id User ID
         * @param  integer $post_id Post ID
         * @param  integer $site_id Site ID
         * @param  string $status Favorites status
         */
        public function wpwh_trigger_fvrt_post_favored( $post_id, $status, $site_id, $user_id ){
            $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'fvrt_post_favored' );

            // Only active favorites
            if( $status !== 'active' ) {
                return;
            }

            $payload = array(
                'user_id' => $user_id,
                'post_id' => $post_id,
                'site_id' => $site_id
            );

            foreach( $webhooks as $webhook ){

                $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
                $is_valid = true;

                if( isset( $webhook['settings'] ) ){
                    foreach( $webhook['settings'] as $settings_name => $settings_data ){

                        if( $settings_name === 'wpwhpro_fvrt_users_post_favorite_trigger_on_users' && ! empty( $settings_data ) ){
                        if( ! in_array( $user_id, $settings_data ) ){
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

            do_action( 'wpwhpro/webhooks/trigger_fvrt_post_got_favorite', $post_id, $status, $site_id, $user_id, $response_data_array );
        }

        public function get_demo( $options = array() ) {

            $data = array(
                'user_id' => 1,
                'post_id' => 1,
                'site_id' => 1
            );

            return $data;
        }

    }

endif; // End if class_exists check.