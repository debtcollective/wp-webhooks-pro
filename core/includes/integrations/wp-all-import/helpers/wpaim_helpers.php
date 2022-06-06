<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_wp_all_import_Helpers_wpaim_helpers' ) ) :

	/**
	 * Load the WP All Import helpers
	 *
	 * @since 5.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_all_import_Helpers_wpaim_helpers {

		public function get_query_imports( $entries, $query_args, $args ){

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

			$list = new PMXI_Import_List();

			if( $selected ){
				$items = $list->getBy('id', $selected);
			} else {
				$by = array(
					'parent_import_id' => 0,
				);
	
				if( '' != $search ){
					$like = '%' . preg_replace( '%\s+%', '%', preg_replace( '/[%?]/', '\\\\$0', $search ) ) . '%';
					$by[] = array( array( 'name LIKE' => $like, 'type LIKE' => $like, 'path LIKE' => $like, 'friendly_name LIKE' => $like ), 'OR' );
				}
	
				$items = $list->setColumns(
					$list->getTable() . '.*'
				)->getBy($by, "friendly_name DESC", $paged, $entries['per_page'], $list->getTable() . '.id');
			}

			foreach( $items->convertRecords() as $item ){
				$entries['items'][ $item->id ] = array(
					'value' => $item->id,
					'label' => empty($item->friendly_name) ? $item->name : $item->friendly_name,
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