<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_wp_get_term' ) ) :

	/**
	 * Load the wp_get_term action
	 *
	 * @since 5.1.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_wp_get_term {

		public function get_details(){

			$translation_ident = "action-wp_get_term-content";

			$parameter = array(
				'term_id' => array( 
					'required' => true, 
					'label' => WPWHPRO()->helpers->translate( 'Term ID', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the taxonomy term you want to get.', $translation_ident ),
				),
				'taxonomy_slug' => array(
					'label' => WPWHPRO()->helpers->translate( 'Taxonomy slug', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( '(String) The slug of the taxonomy to fetch the term from.', $translation_ident ), 
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		   => array( 'short_description' => WPWHPRO()->helpers->translate( '(mixed) The term id, as well as the taxonomy term id on success or wp_error on failure.', $translation_ident ) ),
				'msg'			=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The taxonomy term has been fetched successfully.',
				'data' => 
				array (
				  'term_id' => 93,
				  'name' => 'Remotely',
				  'slug' => 'remotely',
				  'term_group' => 0,
				  'term_taxonomy_id' => 93,
				  'taxonomy' => 'category',
				  'description' => 'A short demo description.',
				  'parent' => 90,
				  'count' => 0,
				  'filter' => 'raw',
				  'meta_data' => array(
					  'demo_field' => array(
						  'Value 1',
					  )
				  ),
				),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Get taxonomy term',
				'webhook_slug' => 'wp_get_term',
				'steps' => array(
					WPWHPRO()->helpers->translate( "It is also required to set the term_id argument. Please set it to the ID of the term that you want to get.", $translation_ident ),
				)
			) );

			return array(
				'action'			=> 'wp_get_term',
				'name'			  => WPWHPRO()->helpers->translate( 'Get taxonomy term', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'get a single taxonomy term', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Get a single taxonomy term for all, or a specific taxonomy.', $translation_ident ),
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

			$term_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'term_id' ) );
			$taxonomy_slug = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'taxonomy_slug' );
			
			if( empty( $term_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please define the term_id argument.", 'action-wp_get_term' );
				return $return_args;
			}
			
			$term_data = get_term( $term_id, $taxonomy_slug, ARRAY_A );
 
			if( $term_data && ! is_wp_error( $term_data ) ) {

				//append the meta
				if( isset( $term_data['term_id'] ) ){
					$term_data['meta_data'] = get_term_meta( $term_data['term_id'] );
				}

				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The taxonomy term has been fetched successfully.", 'action-wp_get_term' );
				$return_args['data'] = $term_data;
			} elseif( is_wp_error( $term_data ) ){
				$return_args['data'] = $term_data;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "An error occured while fetching the taxonomy term.", 'action-wp_get_term' );
			} else {
				$return_args['data'] = $term_data;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "An unidentified error occured while fetching the taxonomy term.", 'action-wp_get_term' );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.