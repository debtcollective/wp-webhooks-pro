<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_woocommerce_Helpers_wc_helpers' ) ) :

	/**
	 * Load the Woocommerce helpers
	 *
	 * @since 4.3.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_woocommerce_Helpers_wc_helpers {

		private $api_version_cache = null;
		private $api_tax_cache = null;
		private $api_product_cache = null;

		/**
		 * Get all Woocommerce webhook API versions
		 *
		 * @return array A list of the available types 
		 */
		public function get_wc_api_versions(){

			$versions = array();

			if( function_exists( 'wc_get_webhook_rest_api_versions' ) ){
				$versions = wc_get_webhook_rest_api_versions();
			} else {
				$versions = array(
					'wp_api_v1',
					'wp_api_v2',
					'wp_api_v3',
				);
			}

			$validated_versions = array();
			foreach( $versions as $version ){
				$validated_versions[ $version ] = esc_html( sprintf( WPWHPRO()->helpers->translate( 'WP REST API v%d', 'trigger-wc_helpers-get_types' ), str_replace( 'wp_api_v', '', $version ) ) );
			}

			$validated_versions = apply_filters( 'wpwhpro/webhooks/wc_helpers/get_wc_api_versions', $validated_versions );

			return $validated_versions;
		}

		public function get_query_wc_api_versions( $entries, $query_args, $args ){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

            $version_items = $this->get_wc_api_versions();

			foreach( $version_items as $name => $title ){

				//skip search values that don't occur if set
				if( isset( $args['s'] ) && $args['s'] !== '' ){
					if( 
						strpos( $name, $args['s'] ) === false
						&& strpos( $title, $args['s'] ) === false
					){
						continue;
					}
				}

				//skip unselected values in a selected statement
				if( isset( $args['selected'] ) && ! empty( $args['selected'] ) ){
					if( ! in_array( $name, $args['selected'] ) ){
						continue;
					}
				}

				$entries['items'][ $name ] = array(
					'value' => $name,
					'label' => $title,
				);
			}

			//calculate total
			$entries['total'] = count( $entries['items'] );

			//set all items to be visible on one page
			$entries['per_page'] = count( $entries['items'] );

			return $entries;
		}

		public function get_query_statuses( $entries, $query_args, $args ){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

            $status_items = array();

			if( function_exists( 'wc_get_order_statuses' ) ){
				$status_items = wc_get_order_statuses();
			}

			foreach( $status_items as $name => $title ){

				//skip search values that don't occur if set
				if( isset( $args['s'] ) && $args['s'] !== '' ){
					if( 
						strpos( $name, $args['s'] ) === false
						&& strpos( $title, $args['s'] ) === false
					){
						continue;
					}
				}

				//skip unselected values in a selected statement
				if( isset( $args['selected'] ) && ! empty( $args['selected'] ) ){
					if( ! in_array( $name, $args['selected'] ) ){
						continue;
					}
				}

				$entries['items'][ $name ] = array(
					'value' => $name,
					'label' => $title,
				);
			}

			//calculate total
			$entries['total'] = count( $entries['items'] );

			//set all items to be visible on one page
			$entries['per_page'] = count( $entries['items'] );

			return $entries;
		}

		/**
		 * Get an array of assigned taxonomies for a given post
		 *
		 * @param int $post_id
		 * @since 4.3.3
		 * @return array
		 */
		public function get_validated_taxonomies( $post_id ){

			if( $this->api_tax_cache !== null ){
				return $this->api_tax_cache;
			}
			
			$tax_output = array();

			if( ! empty( $post_id ) ){
				$tax_output = array();
                $taxonomies = get_taxonomies( array(),'names' );
                if( ! empty( $taxonomies ) ){
                    $tax_terms = wp_get_post_terms( $post_id, $taxonomies );
                    foreach( $tax_terms as $sk => $sv ){

                        if( ! isset( $sv->taxonomy ) || ! isset( $sv->slug ) ){
                            continue;
                        }

                        if( ! isset( $tax_output[ $sv->taxonomy ] ) ){
                            $tax_output[ $sv->taxonomy ] = array();
                        }

                        if( ! isset( $tax_output[ $sv->taxonomy ][ $sv->slug ] ) ){
                            $tax_output[ $sv->taxonomy ][ $sv->slug ] = array();
                        }

                        $tax_output[ $sv->taxonomy ][ $sv->slug ] = $sv;

                    }
                }
			}

			if( ! empty( $tax_output ) ){
				$this->api_tax_cache = $tax_output;
			}

			return $tax_output;
		}

		public function get_products(){

			if( $this->api_product_cache !== null ){
				return $this->api_product_cache;
			}

			$validated_products = array();
			$products = wc_get_products(
				array(
					'status'  => array( 'private', 'publish' ),
					'limit'   => 9999,
					'orderby' => array(
						'title' => 'ASC',
					),
					'return'  => 'objects',
				)
			);

			if( ! empty( $products ) ){
				foreach( $products as $product ){
					$validated_products[ $product->get_id() ] = $product->get_title();
				}
			}

			if( ! empty( $validated_products ) ){
				$this->api_product_cache = $validated_products;
			}

			return $validated_products;
		}

	}

endif; // End if class_exists check.