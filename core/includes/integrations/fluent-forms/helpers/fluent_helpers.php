<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_forms_Helpers_fluent_helpers' ) ) :

	/**
	 * Load the FuentCRM helpers
	 *
	 * @since 5.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_fluent_forms_Helpers_fluent_helpers {

        public function get_query_forms( $entries, $query_args, $args ){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

            $form_items = array();
			$forms = wpFluent()->table( 'fluentform_forms' )
							   ->select( [ 'id', 'title' ] )
							   ->orderBy( 'id', 'DESC' )
							   ->get();

			if( ! empty( $forms ) ) {
				foreach ( $forms as $form ) {
					$form_items[ $form->id ] = esc_html( $form->title );
				}
			}

			foreach( $form_items as $name => $title ){

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