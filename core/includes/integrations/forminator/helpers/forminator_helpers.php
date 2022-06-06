<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_forminator_Helpers_forminator_helpers' ) ) :

	/**
	 * Load the FuentCRM helpers
	 *
	 * @since 5.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_forminator_Helpers_forminator_helpers {

        public function get_query_forms( $entries, $query_args, $args ){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

            $type_items = array();
			
			if( class_exists( 'Forminator_API' ) ){
				$forms = Forminator_API::get_forms( null, 1, 999 );
	
				if ( ! empty( $forms ) ) {
					foreach ( $forms as $form ) {
						
						$form_name = $form->name;
						if( isset( $form->settings ) && is_array( $form->settings ) && isset( $form->settings['form_name'] ) ){
							$form_name = $form->settings['form_name'];
						}
							 
						$type_items[ $form->id ] = esc_html( $form_name );
					}
				}
			}

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