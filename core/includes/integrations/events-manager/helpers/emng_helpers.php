<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_events_manager_Helpers_emng_helpers' ) ) :

	/**
	 * Load the Events Manager helpers
	 *
	 * @since 4.3.6
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_events_manager_Helpers_emng_helpers {

        public function get_events(){
            $validated_forms = array();
           
            if( defined( 'EM_POST_TYPE_EVENT' ) ){
                $forms = get_posts( array( 
					'post_type' => EM_POST_TYPE_EVENT,
					'posts_per_page' => -1,
					'numberposts' => -1,
				) );
				
				if( ! empty( $forms ) ){
					foreach( $forms as $form ){
						$validated_forms[ $form->ID ] = $form->post_title;
					}
				}
				
            }

            return $validated_forms;

        }

	}

endif; // End if class_exists check.