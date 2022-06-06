<?php

use Groundhogg\Contact;

if ( ! class_exists( 'WP_Webhooks_Integrations_groundhogg_Helpers_ghogg_helpers' ) ) :

	/**
	 * Load the WS Form helpers
	 *
	 * @since 4.3.5
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_groundhogg_Helpers_ghogg_helpers {

        public function get_contact( $contact_value, $get_by_user_id = false ){
            return new Contact( $contact_value, $get_by_user_id );

        }

	}

endif; // End if class_exists check.