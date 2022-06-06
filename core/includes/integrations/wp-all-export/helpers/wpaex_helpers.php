<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_wp_all_export_Helpers_wpaex_helpers' ) ) :

	/**
	 * Load the WP All Export helpers
	 *
	 * @since 5.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_all_export_Helpers_wpaex_helpers {

		public function get_query_exports( $entries, $query_args, $args ){

			$paged = 1;
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				$paged = intval( $args['paged'] );
			}

			$search = '';
			if( isset( $args['s'] ) && ! empty( $args['s'] ) ){
				$search = esc_sql( $args['s'] );
			}

			$selected = array();
			if( isset( $args['selected'] ) && ! empty( $args['selected'] ) ){
				foreach( $args['selected'] as $selected_id ){
					$selected[] = intval( $selected_id );
				}
			}

			$list = new PMXE_Export_List();

			if( $selected ){
				$items = $list->getBy('id', $selected);
			} else {
				$by = array(
					'parent_id' => 0,
				);
	
				if( '' != $search ){
					$like = '%' . preg_replace( '%\s+%', '%', preg_replace( '/[%?]/', '\\\\$0', $search ) ) . '%';
					$by[] = array( array( 'friendly_name LIKE' => $like, 'registered_on LIKE' => $like ), 'OR' );
				}
	
				$items = $list->setColumns(
					$list->getTable() . '.*'
				)->getBy($by, "friendly_name DESC", $paged, $entries['per_page'], $list->getTable() . '.id');
			}

			

			foreach( $items->convertRecords() as $item ){
				$entries['items'][ $item->id ] = array(
					'value' => $item->id,
					'label' => $item->friendly_name,
				);
			}

			//calculate total
			$entries['total'] = $list->total();

			//set all items to be visible on one page
			$entries['per_page'] = $list->count();

			return $entries;
		}

	}

endif; // End if class_exists check.