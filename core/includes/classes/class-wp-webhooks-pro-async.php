<?php

/**
 * WP_Webhooks_Pro_Async Class
 *
 * This class contains all of the available async functions
 *
 * @since 4.3.0
 */

/**
 * The async class of the plugin.
 *
 * @since 4.3.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Async {

	public function new_process( $args = array() ){
        $return = null;

        if( class_exists( 'WP_Webhooks_Pro_Async_Process' ) ){

            if( isset( $args['action'] ) ){
                $return = new WP_Webhooks_Pro_Async_Process( $args );
            }
            
        }

        return $return;
    }

}
