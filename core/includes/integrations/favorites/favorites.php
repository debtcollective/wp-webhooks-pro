<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_events_manager Class
 *
 * This class integrates all Favorites related features and endpoints
 *
 * @since 5.1.2
 */
class WP_Webhooks_Integrations_favorites {

    public function is_active(){
        return class_exists( 'Favorites' );
    }

    public function get_details(){
        $integration_url = plugin_dir_url( __FILE__ );

        return array(
            'name' => 'Favorites',
            'icon' => $integration_url . '/assets/img/icon-favorites.svg',
        );
    }

}
