<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_integrately Class
 *
 * This class integrates all Integrately related features and endpoints
 *
 * @since 5.0
 */
class WP_Webhooks_Integrations_integrately {

    public function is_active(){
        return true;
    }

    public function get_details(){
        $integration_url = plugin_dir_url( __FILE__ );

        return array(
            'name' => 'Integrately',
            'icon' => $integration_url . '/assets/img/icon-integrately.svg',
        );
    }

}
