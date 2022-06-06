<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_formidable_forms_Helpers_formidable_helpers' ) ) :

	/**
	 * Load the FuentCRM helpers
	 *
	 * @since 5.2
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_formidable_forms_Helpers_formidable_helpers {

        public function get_query_forms( $entries, $query_args, $args ){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

            $type_items = array();
			$forms = FrmForm::getAll( array(
				'is_template' => 0,
				'or'               => 1,
				'parent_form_id'   => null,
				'parent_form_id <' => 1,
				'status !' => 'trash',
			), '', ' 0, 999' );

			if( ! empty( $forms ) ) {
				foreach( $forms as $form ) {
					$type_items[ $form->id ] = esc_html( $form->name );
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