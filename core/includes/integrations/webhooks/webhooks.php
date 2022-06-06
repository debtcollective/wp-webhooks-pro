<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_webhooks Class
 *
 * This class integrates all Webhooks related features and endpoints
 *
 * @since 4.3.6
 */
class WP_Webhooks_Integrations_webhooks {

    public function is_active(){
        return true;
    }

    public function get_details(){
        $integration_url = plugin_dir_url( __FILE__ );

        return array(
            'name' => 'Webhooks',
            'icon' => $integration_url . '/assets/img/icon-webhooks.svg',
        );
    }

}
