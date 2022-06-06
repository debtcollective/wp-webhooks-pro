<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_jetengine Class
 *
 * This class integrates all JetEngine related features and endpoints
 *
 * @since 4.3.7
 */
class WP_Webhooks_Integrations_jetengine {

    public function is_active(){
        return function_exists( 'jet_engine' );
    }

    public function get_details(){
        $integration_url = plugin_dir_url( __FILE__ );

        return array(
            'name' => 'JetEngine',
            'icon' => $integration_url . '/assets/img/icon-jetengine.svg',
        );
    }

}
