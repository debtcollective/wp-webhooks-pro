<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_ifttt Class
 *
 * This class integrates all IFTTT related features and endpoints
 *
 * @since 5.0
 */
class WP_Webhooks_Integrations_ifttt {

    public function is_active(){
        return true;
    }

    public function get_details(){
        $integration_url = plugin_dir_url( __FILE__ );

        return array(
            'name' => 'IFTTT',
            'icon' => $integration_url . '/assets/img/icon-ifttt.png',
        );
    }

}
