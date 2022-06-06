<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_wp_courseware_Helpers_wpcw_helpers' ) ) :

	/**
	 * Load the WS Form helpers
	 *
	 * @since 4.3.5
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_courseware_Helpers_wpcw_helpers {

		private $courses = array();
		private $modules = array();
		private $units = array();

        public function get_courses(){

			if( ! empty( $this->courses ) ){
				return $this->courses;
			}

            $validated_courses = array();
           
			$course_args     = array(
				'status'  => 'publish',
				'number'  => - 1,
				'orderby' => 'post_title',
			);
		
			$course_objects = wpcw()->courses->get_courses( $course_args, true );

			if( ! empty( $course_objects ) ){
				foreach( $course_objects as $course_object ){
					if( ! empty( $course_object->course_title ) ){
						$validated_courses[ $course_object->course_id ] = $course_object->course_title;
					}
				}
			}

			$this->courses = $validated_courses;

            return $validated_courses;

        }

		public function get_query_courses( $entries, $query_args, $args ){
			$course_args = array(
				'number' => $entries['per_page'],
				'offset' => 0,
			);
	
			if( isset( $query_args['course_args'] ) ){
				$course_args = array_merge( $course_args, $query_args['course_args'] );
			}
	
			if( isset( $args['s'] ) ){
				$course_args['search'] = esc_sql( $args['s'] );
			}
	
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				$course_args['offset'] = ( intval( $args['paged'] ) - 1 ) * $course_args['number'];
			}
	
			if( isset( $args['selected'] ) && ! empty( $args['selected'] ) && is_array( $args['selected'] ) ){
	
				//since we work with selected values, make sure we display all
				$course_args['number'] = 0;
				$course_args['offset'] = 0;
	
				$selected_items =  array_map( 'intval', $args['selected'] );
	
				//remove empty items
				foreach( $selected_items as $item_key => $item_val ){
					if( empty( $item_val ) ){
						unset( $selected_items[ $item_key ] );
					}
				}
	
				$course_args['course_id'] = $selected_items;
			}

			$course_objects = wpcw()->courses->get_courses( $course_args, true );

			if( ! empty( $course_objects ) ){
				foreach( $course_objects as $course_object ){
					if( ! empty( $course_object->course_title ) ){
						$entries['items'][ $course_object->course_id ] = array(
							'value' => $course_object->course_id,
							'label' => $course_object->course_title,
						);
					}
				}
			}	
	
			//since there is no official total, we work with a custom counter + 1
			if( isset( $args['paged'] ) && $args['paged'] > 1 ){
				$entries['total'] = ( $args['paged'] - 1 ) * $course_args['number'];
			} else {
				$entries['total'] = count( $entries['items'] );
			}
	
			if( count( $entries['items'] ) >= $course_args['number'] ){
				//here we can assume that further entries appear, so we add one to the total
				$entries['total'] += 1;
			}
	
			return $entries;
		}

        public function get_modules(){

			if( ! empty( $this->modules ) ){
				return $this->modules;
			}

            $validated_modules = array();
           
			$modules = wpcw_get_modules(
				array(
					'number'    => - 1,
					'orderby'   => 'module_order',
					'order'     => 'ASC',
				)
			);

			if( ! empty( $modules ) ){
				foreach( $modules as $module ){
					$validated_modules[ $module->get_module_id() ] = $module->get_module_title();
				}
			}

			$this->modules = $validated_modules;

            return $validated_modules;

        }

		public function get_query_modules( $entries, $query_args, $args ){
			$module_args = array(
				'number' => $entries['per_page'],
				'offset' => 0,
			);
	
			if( isset( $query_args['module_args'] ) ){
				$module_args = array_merge( $module_args, $query_args['module_args'] );
			}
	
			if( isset( $args['s'] ) ){
				$module_args['search'] = esc_sql( $args['s'] );
			}
	
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				$module_args['offset'] = ( intval( $args['paged'] ) - 1 ) * $module_args['number'];
			}
	
			if( isset( $args['selected'] ) && ! empty( $args['selected'] ) && is_array( $args['selected'] ) ){
	
				//since we work with selected values, make sure we display all
				$module_args['number'] = 0;
				$module_args['offset'] = 0;
	
				$selected_items =  array_map( 'intval', $args['selected'] );
	
				//remove empty items
				foreach( $selected_items as $item_key => $item_val ){
					if( empty( $item_val ) ){
						unset( $selected_items[ $item_key ] );
					}
				}
	
				$module_args['module_id'] = $selected_items;
			}

			$module_objects = wpcw_get_modules( $module_args );

			if( ! empty( $module_objects ) ){
				foreach( $module_objects as $module_object ){
					$module_id = $module_object->get_module_id();

					$entries['items'][ $module_id ] = array(
						'value' => $module_id,
						'label' => $module_object->get_module_title(),
					);
				}
			}	
	
			//since there is no official total, we work with a custom counter + 1
			if( isset( $args['paged'] ) && $args['paged'] > 1 ){
				$entries['total'] = ( $args['paged'] - 1 ) * $module_args['number'];
			} else {
				$entries['total'] = count( $entries['items'] );
			}
	
			if( count( $entries['items'] ) >= $module_args['number'] ){
				//here we can assume that further entries appear, so we add one to the total
				$entries['total'] += 1;
			}
	
			return $entries;
		}

        public function get_units(){

			if( ! empty( $this->units ) ){
				return $this->units;
			}

            $validated_units = array();
           
			$query_args = array(
				'number'      => - 1,
				'orderby'     => 'post_title',
			);
	
			$units = wpcw()->units->get_units( $query_args );

			if( ! empty( $units ) ){
				foreach( $units as $unit ){
					$validated_units[ $unit->get_id() ] = $unit->get_unit_title();
				}
			}

			$this->units = $validated_units;

            return $validated_units;

        }

		public function get_query_units( $entries, $query_args, $args ){
			$unit_args = array(
				'number' => $entries['per_page'],
				'offset' => 0,
			);
	
			if( isset( $query_args['unit_args'] ) ){
				$unit_args = array_merge( $unit_args, $query_args['unit_args'] );
			}
	
			if( isset( $args['s'] ) ){
				$unit_args['search'] = esc_sql( $args['s'] );
			}
	
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				$unit_args['offset'] = ( intval( $args['paged'] ) - 1 ) * $unit_args['number'];
			}
	
			if( isset( $args['selected'] ) && ! empty( $args['selected'] ) && is_array( $args['selected'] ) ){
	
				//since we work with selected values, make sure we display all
				$unit_args['number'] = 0;
				$unit_args['offset'] = 0;
	
				$selected_items =  array_map( 'intval', $args['selected'] );
	
				//remove empty items
				foreach( $selected_items as $item_key => $item_val ){
					if( empty( $item_val ) ){
						unset( $selected_items[ $item_key ] );
					}
				}
	
				$unit_args['unit_id'] = $selected_items;
			}

			$unit_objects = wpcw()->units->get_units( $unit_args );

			if( ! empty( $unit_objects ) ){
				foreach( $unit_objects as $unit_object ){
					$unit_id = $unit_object->get_id();

					$entries['items'][ $unit_id ] = array(
						'name' => $unit_id,
						'title' => $unit_object->get_unit_title(),
					);
				}
			}	
	
			//since there is no official total, we work with a custom counter + 1
			if( isset( $args['paged'] ) && $args['paged'] > 1 ){
				$entries['total'] = ( $args['paged'] - 1 ) * $unit_args['number'];
			} else {
				$entries['total'] = count( $entries['items'] );
			}
	
			if( count( $entries['items'] ) >= $unit_args['number'] ){
				//here we can assume that further entries appear, so we add one to the total
				$entries['total'] += 1;
			}
	
			return $entries;
		}

	}

endif; // End if class_exists check.