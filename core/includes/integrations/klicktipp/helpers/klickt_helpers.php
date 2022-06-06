<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_klicktipp_Helpers_klickt_helpers' ) ) :

	/**
	 * Load the KlickTipp helpers
	 *
	 * @since 5.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_klicktipp_Helpers_klickt_helpers {

		public function get_klicktipp(){
			$klicktipp = null;

			if( ! class_exists('KlicktippConnector') ){
				$integration_folder = WPWHPRO()->integrations->get_integrations_folder( 'klicktipp' );
				require( $integration_folder . '/misc/klicktipp-api/klicktipp-api.php' );
			}

			if( class_exists('KlicktippConnector') ){
				$klicktipp = new KlicktippConnector();
			}

			return $klicktipp;
		}

	}

endif; // End if class_exists check.