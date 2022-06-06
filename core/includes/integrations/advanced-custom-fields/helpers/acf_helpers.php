<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_advanced_custom_fields_Helpers_acf_helpers' ) ) :

	/**
	 * Load the Advanced Custom Fields helpers
	 *
	 * @since 4.3.7
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_advanced_custom_fields_Helpers_acf_helpers {

		private $meta_field_cache = array();

        public function is_acf_meta_field( $post_id, $meta_key, $type = 'post' ){
			$return = false;

			if( empty( $post_id ) ){
				return $return;
			}

			switch( $type ){
				case 'term':
					$post_id = 'term_' . $post_id;
					break;
				case 'comment':
					$post_id = 'comment_' . $post_id;
					break;
				case 'user':
					$post_id = 'user_' . $post_id;
					break;
			}

			if( isset( $this->meta_field_cache[ $post_id ] ) ){

				if( isset( $this->meta_field_cache[ $post_id ][ $meta_key ] ) ){
					$return = true;
				} else {
					$return = false;
				}

				return $return;
			}

            $acf_fields = get_fields( $post_id );
           
            if( ! is_array( $acf_fields ) ){
				$acf_fields = array();
			}

			$this->meta_field_cache[ $post_id ] = $acf_fields;

			if( isset( $acf_fields[ $meta_key ] ) ){
				$return = true;
			} else {
				$return = false;
			}

            return $return;

        }

	}

endif; // End if class_exists check.