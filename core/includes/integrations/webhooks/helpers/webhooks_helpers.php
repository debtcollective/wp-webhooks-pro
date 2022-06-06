<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_webhooks_Helpers_webhooks_helpers' ) ) :

	/**
	 * Load the Webhooks helpers
	 *
	 * @since 4.3.6
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_webhooks_Helpers_webhooks_helpers {

        public function get_action_urls(){
            $validated_webhooks = array();
           
			$webhook_actions = WPWHPRO()->webhook->get_hooks( 'action' );
            if( ! empty( $webhook_actions ) ){
				foreach( $webhook_actions as $slug => $webhook_data ){

					if( ! isset( $webhook_data['api_key'] ) || ! is_string( $webhook_data['api_key'] ) ){
						foreach( $webhook_data as $action_slug => $action_data ){
							//Skip flow URLs
							if( strpos( $action_slug, 'wpwh-flow-' ) !== FALSE && substr( $action_slug, 0, 10 ) === 'wpwh-flow-' ){
								continue;
							}

							$validated_webhooks[ $action_slug ] = $action_slug;
						}
					}

					
				}
            }

            return $validated_webhooks;
        }

	}

endif; // End if class_exists check.