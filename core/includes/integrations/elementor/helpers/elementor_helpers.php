<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_elementor_Helpers_elementor_helpers' ) ) :

	class WP_Webhooks_Integrations_elementor_Helpers_elementor_helpers {

		protected $forms = null;

		public function get_forms(){

			if( $this->forms !== null ){
				return $this->forms;
			}

			$sql = "SELECT pm.meta_value FROM {postmeta} pm JOIN {posts} p on p.ID = pm.post_id WHERE pm.meta_key LIKE '_elementor_data' AND p.post_status = 'publish' AND pm.meta_value LIKE '%form_fields%';";
			$results = WPWHPRO()->sql->run( $sql );
			$forms = array();
			
			if( ! empty( $results ) ){
				foreach( $results as $meta ){
					$elementor_data = json_decode( $meta->meta_value );
					$forms = array_merge( $forms, $this->find_forms( $elementor_data ) );
				}
			}

			return $forms;
		}

		public function get_elementor_forms( $entries, $query_args, $args ){

			//bail for paged values as everything is returned at once
			if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
				return $entries;
			}

			$form_items = $this->get_forms();
			$validated_forms = array();

			foreach( $form_items as $form_id => $form ){
				$form_name = $form_id;
				if( isset( $form->form_name ) ){
					$form_name = $form->form_name;
				}
	
				$validated_forms[ $form_id ] = $form_name;
			}

			foreach( $validated_forms as $name => $title ){

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

		public function find_forms( $elementor_data ){
			$return = array();

			if( ! empty( $elementor_data ) ){
				foreach( $elementor_data as $element ){

					if ( 
						property_exists( $element, 'widgetType' ) 
						&& property_exists( $element, 'elType' ) 
						&& $element->widgetType === 'form' 
						&& $element->elType === 'widget' 
					) {
						$return[ $element->id ] = $element->settings;
					}

					if ( ! empty( $element->elements ) ) {
						$sub_elements = $this->find_forms( $element->elements );
						if ( ! empty( $sub_elements ) ) {
							$return = array_merge( $return, $sub_elements );
						}
					}

				}
			}

			return $return;
		}

    }

endif; // End if class_exists check.