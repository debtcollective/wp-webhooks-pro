<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_groundhogg Class
 *
 * This class integrates all Groundhogg related features and endpoints
 *
 * @since 4.3.5
 */
class WP_Webhooks_Integrations_groundhogg {

    public function is_active(){
        return defined( 'GROUNDHOGG_VERSION' );
    }

    public function get_details(){
        $integration_url = plugin_dir_url( __FILE__ );

        return array(
            'name' => 'Groundhogg',
            'icon' => $integration_url . '/assets/img/icon-groundhogg.png',
        );
    }

}
