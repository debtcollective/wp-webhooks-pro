<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_paid_memberships_pro_Helpers_pmpro_helpers' ) ) :

	class WP_Webhooks_Integrations_paid_memberships_pro_Helpers_pmpro_helpers {

		public function get_membership_levels( $include_hidden = false, $use_cache = true, $force = false ) {
	
            $levels = array();
            if( function_exists( 'pmpro_getAllLevels' ) ){
                $levels = pmpro_getAllLevels( $include_hidden, $use_cache, $force );
            }
			
            return $levels;
		}

        public function get_query_levels( $entries, $query_args, $args ){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

            $level_items = array();
			
			$membership_levels = $this->get_membership_levels( true );
            if( ! empty( $membership_levels ) && is_array( $membership_levels ) ){
                foreach( $membership_levels as $level_id => $single_level ){
                    $level_items[ $level_id ] = isset( $single_level->name ) ? sanitize_text_field( $single_level->name ) : WPWHPRO()->helpers->translate( 'undefined', $translation_ident );
                }
            }

			foreach( $level_items as $name => $title ){

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