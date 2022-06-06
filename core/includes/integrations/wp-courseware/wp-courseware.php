<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_wp_courseware Class
 *
 * This class integrates all WP Courseware related features and endpoints
 *
 * @since 4.3.5
 */
class WP_Webhooks_Integrations_wp_courseware {

    public function is_active(){
        return defined( 'WPCW_VERSION' );
    }

    public function get_details(){
        $integration_url = plugin_dir_url( __FILE__ );

        return array(
            'name' => 'WP Courseware',
            'icon' => $integration_url . '/assets/img/icon-wp-courseware.svg',
        );
    }

}
