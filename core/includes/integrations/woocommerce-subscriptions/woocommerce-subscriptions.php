<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP_Webhooks_Integrations_woocommerce_subscriptions Class
 *
 * This class integrates all WooCommerce Subscriptions related features and endpoints
 *
 * @since 5.2
 */
class WP_Webhooks_Integrations_woocommerce_subscriptions {

    public function is_active(){
        return class_exists( 'WC_Subscriptions' );
    }

    public function get_details(){
        $integration_url = plugin_dir_url( __FILE__ );

        return array(
            'name' => 'WooCommerce Subscriptions',
            'icon' => $integration_url . '/assets/img/icon-woocommerce-subscriptions.svg',
        );
    }

}
