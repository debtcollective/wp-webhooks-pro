<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_wp_webhooks_formatter Class
 *
 * This class integrates all WP Webhooks Formatter related features and endpoints
 *
 * @since 5.1
 */
class WP_Webhooks_Integrations_wp_webhooks_formatter {

    public function is_active(){
        return true;
    }

    public function get_details(){
        $integration_url = plugin_dir_url( __FILE__ );

        return array(
            'name' => 'WP Webhooks Formatter',
            'icon' => $integration_url . '/assets/img/icon-wp-webhooks-formatter.svg',
        );
    }

}
