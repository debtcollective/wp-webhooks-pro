<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_kadence_blocks Class
 *
 * This class integrates all Kadence Blocks related features and endpoints
 *
 * @since 4.3.7
 */
class WP_Webhooks_Integrations_kadence_blocks {

    public function is_active(){
        return defined( 'KADENCE_BLOCKS_VERSION' );
    }

    public function get_details(){
        $integration_url = plugin_dir_url( __FILE__ );

        return array(
            'name' => 'Kadence Blocks',
            'icon' => $integration_url . '/assets/img/icon-kadence-blocks.png',
        );
    }

}
