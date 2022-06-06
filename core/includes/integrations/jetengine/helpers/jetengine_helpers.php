<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_jetengine_Helpers_jetengine_helpers' ) ) :

	/**
	 * Load the JetEngine helpers
	 *
	 * @since 4.3.7
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_jetengine_Helpers_jetengine_helpers {

		private $forms_cache = array();

        public function get_notification_types(){
			$validated_types = jet_engine()->forms->get_notification_types();

            return $validated_types;

        }

        public function get_forms(){

            $validated_forms = array();

			$forms = get_posts( array(
				'posts_per_page' => - 1,
				'post_status'    => 'publish',
				'post_type'      => jet_engine()->forms->slug(),
			) );

			if( ! empty( $forms ) ){
				foreach( $forms as $form ){
					$validated_forms[ $form->ID ] = $form->post_title;
				}
			}

            return $validated_forms;

        }

		public function get_query_types( $entries, $query_args, $args ){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

            $type_items = $this->get_notification_types();

			foreach( $type_items as $name => $title ){

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