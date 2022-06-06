<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_formatter_Helpers_wpwhf_helpers' ) ) :

	/**
	 * Load the WP Webhooks helpers
	 *
	 * @since 5.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_formatter_Helpers_wpwhf_helpers {

		public function capitalize_first_character( $string, $encoding = "UTF-8", $lower_string_end = false ) {

			if( ! is_string( $string ) || $string === '' ){
				return '';
			}

			$first_letter = mb_strtoupper( mb_substr( $string, 0, 1, $encoding ), $encoding );
			$string_end = "";

			if( $lower_string_end ){
			  $string_end = mb_strtolower( mb_substr( $string, 1, mb_strlen( $string, $encoding ), $encoding ), $encoding );
			} else {
			  $string_end = mb_substr( $string, 1, mb_strlen( $string, $encoding ), $encoding );
			}

			$string = $first_letter . $string_end;

			return $string;
		}

	}

endif; // End if class_exists check.