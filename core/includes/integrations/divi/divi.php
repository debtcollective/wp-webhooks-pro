<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_divi Class
 *
 * This class integrates all Divi related features and endpoints
 *
 * @since 4.3.6
 */
class WP_Webhooks_Integrations_divi {

    public function is_active(){
        $theme = wp_get_theme();
        return ( ! empty( $theme ) && $theme->get_template() === 'Divi' );
    }

    public function get_details(){
        $integration_url = plugin_dir_url( __FILE__ );

        return array(
            'name' => 'Divi',
            'icon' => $integration_url . '/assets/img/icon-divi.png',
        );
    }

}
