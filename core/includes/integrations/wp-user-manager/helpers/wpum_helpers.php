<?php

if ( ! class_exists( 'WP_Webhooks_Integrations_wp_user_manager_Helpers_wpum_helpers' ) ) :

	/**
	 * Load the WP User Manager helpers
	 *
	 * @since 4.3.5
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_user_manager_Helpers_wpum_helpers {

		private $form_cache = array();

        public function get_forms(){

			if( ! empty( $this->form_cache ) ){
				return $this->form_cache;
			}

			global $wpdb;

            $validated_forms = array();

			// Get the forms
			$forms = $wpdb->get_results( $wpdb->prepare(
				"SELECT form.id AS form_id, form.name AS form_name
				FROM {$wpdb->prefix}wpum_registration_forms AS form
				LIMIT %d",
				99999
			) );

			foreach( $forms as $form ){
				if( isset( $form->form_id ) && isset( $form->form_name ) ){
					$validated_forms[ $form->form_id ] = $form->form_name;
				}
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

		public function remove_clean_password( $values = array() ){

			if( is_array( $values ) ){
				foreach( $values as $value_name => $value ){
					if( is_array( $value ) ){
						$values[ $value_name ] = $this->remove_clean_password( $value );
					} elseif( is_string( $value ) && strpos( $value_name, 'password' ) !== FALSE ){
						unset( $values[ $value_name ] );
					}
				}
			}

			return $values;
		}

	}

endif; // End if class_exists check.