<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_polls_Helpers_wppolls_helpers' ) ) :

	/**
	 * Load the WP Webhooks helpers
	 *
	 * @since 5.1.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_polls_Helpers_wppolls_helpers {

		public function get_polls() {
			global $wpdb;

			$validated_polls = array();

			if( ! defined('WP_POLLS_VERSION') ){
				return $validated_polls;
			}

			$polls = $wpdb->get_results( "SELECT * FROM $wpdb->pollsq  ORDER BY pollq_timestamp DESC" );

			if( ! empty( $polls ) ){
				foreach( $polls as $poll ){
					$validated_polls[ $poll->pollq_id ] = wp_kses_post( removeslashes( $poll->pollq_question ) );
				}
			}

			return $validated_polls;
		}

		public function get_query_polls( $entries, $query_args, $args ){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

            $poll_items = $this->get_polls();

			foreach( $poll_items as $name => $title ){

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

	}

endif; // End if class_exists check.