<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_wp_get_terms' ) ) :

	/**
	 * Load the wp_get_terms action
	 *
	 * @since 5.1.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_wp_get_terms {

		public function get_details(){

			$translation_ident = "action-wp_get_terms-content";

			$parameter = array(
				'arguments' => array( 
					'required' => true,
					'type' => 'repeater',
					'multiple' => true, 
					'label' => WPWHPRO()->helpers->translate( 'Arguments', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( '(String) The JSON formatted data for the WP term query. Example: {"taxonomy": "post_tag", "number": 2} - All possible filters: https://developer.wordpress.org/reference/classes/wp_term_query/__construct/', $translation_ident ),
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		   => array( 'short_description' => WPWHPRO()->helpers->translate( '(mixed) The term id, as well as the taxonomy term id on success or wp_error on failure.', $translation_ident ) ),
				'msg'			=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The taxonomy terms have been fetched successfully.',
				'data' => 
				array (
				  0 => 
				  array (
					'term_id' => 70,
					'name' => 'Sport',
					'slug' => 'sport',
					'term_group' => 0,
					'term_taxonomy_id' => 70,
					'taxonomy' => 'post_tag',
					'description' => '',
					'parent' => 0,
					'count' => 1,
					'filter' => 'raw',
					'meta_data' => array(
						'demo_field' => array(
							'Value 1',
						)
					),
				  ),
				  1 => 
				  array (
					'term_id' => 72,
					'name' => 'Male',
					'slug' => 'male',
					'term_group' => 0,
					'term_taxonomy_id' => 72,
					'taxonomy' => 'post_tag',
					'description' => '',
					'parent' => 0,
					'count' => 1,
					'filter' => 'raw',
					'meta_data' => array(
						'demo_field' => array(
							'Value 1',
						)
					),
				  ),
				),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Get taxonomy terms',
				'webhook_slug' => 'wp_get_terms',
				'steps' => array(
					WPWHPRO()->helpers->translate( "It is also required to set the arguments argument. Please set it to a JSON formatted string containing the arguments you want to seach for.", $translation_ident ),
				)
			) );

			return array(
				'action'			=> 'wp_get_terms',
				'name'			  => WPWHPRO()->helpers->translate( 'Get taxonomy terms', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'get one or multiple taxonomy terms', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Get a one or multiple taxonomy terms for all, or a specific taxonomy.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => '',
			);

			$arguments = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'arguments' );
			
			if( empty( $arguments ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please define the arguments argument.", 'action-wp_get_terms' );
				return $return_args;
			}

			$validated_arguments = array();
			if( WPWHPRO()->helpers->is_json( $arguments ) ){
				$validated_arguments = json_decode( $arguments, true );
			}

			if( empty( $validated_arguments ) || ! is_array( $validated_arguments ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Your arguments argument is empty or could not be validated.", 'action-wp_get_terms' );
				return $return_args;
			}
			
			$terms = get_terms( $validated_arguments );
 
			if( $terms && ! is_wp_error( $terms ) ) {

				//apend meta data
				foreach( $terms as $term_key => $term ){
					if( isset( $term->term_id ) ){
						$terms[ $term_key ]->meta_data = get_term_meta( $term->term_id );
					}
				}
				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The taxonomy terms have been fetched successfully.", 'action-wp_get_terms' );
				$return_args['data'] = $terms;
			} elseif( is_wp_error( $terms ) ){
				$return_args['data'] = $terms;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "An error occured while fetching the taxonomy terms.", 'action-wp_get_terms' );
			} else {
				$return_args['data'] = $terms;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "An unidentified error occured while fetching the taxonomy terms.", 'action-wp_get_terms' );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.