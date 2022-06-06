<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_automate_io Class
 *
 * This class integrates all Automate.io related features and endpoints
 *
 * @since 5.0
 */
class WP_Webhooks_Integrations_automate_io {

    public function is_active(){
        return true;
    }

    public function get_details(){
        $integration_url = plugin_dir_url( __FILE__ );

        return array(
            'name' => 'Automate.io',
            'icon' => $integration_url . '/assets/img/icon-automate-io.svg',
        );
    }

}
