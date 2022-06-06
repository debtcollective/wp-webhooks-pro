<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_ws_form_Helpers_wsform_helpers' ) ) :

	/**
	 * Load the WS Form helpers
	 *
	 * @since 4.3.5
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_ws_form_Helpers_wsform_helpers {

        public function get_forms(){
            $validated_forms = array();
           
            if( class_exists( 'WS_Form_Common' ) ){
                $validated_forms = WS_Form_Common::get_forms_array();
            }

            return $validated_forms;

        }

		public function get_query_forms( $entries, $query_args, $args ){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

            $form_items = $this->get_forms();

			foreach( $form_items as $name => $title ){

				//skip empty value fields
				if( empty( $name ) ){
					continue;
				}

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