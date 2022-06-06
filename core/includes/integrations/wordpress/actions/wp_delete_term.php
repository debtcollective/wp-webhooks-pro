<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_wp_delete_term' ) ) :

	/**
	 * Load the wp_delete_term action
	 *
	 * @since 5.1.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_wp_delete_term {

		public function get_details(){

			$translation_ident = "action-wp_delete_term-content";

			$parameter = array(
				'term_id' => array( 
					'required' => true, 
					'label' => WPWHPRO()->helpers->translate( 'Term ID', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the taxonomy term you want to delete.', $translation_ident ),
				),
				'taxonomy_slug' => array( 
					'required' => true, 
					'label' => WPWHPRO()->helpers->translate( 'Taxonomy slug', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( '(String) The slug of the taxonomy to delete the term from.', $translation_ident ), 
				),
				'default_id' => array( 
					'label' => WPWHPRO()->helpers->translate( 'Default ID', $translation_ident ), 
					'short_description' => WPWHPRO()->helpers->translate( '(Integer) The term ID to make the default term. This will only override the terms found if there is only one term found. Any other and the found terms are used.', $translation_ident ),
				),
				'force_default' => array( 
					'type' => 'select', 
					'default_value' => 'yes', 
					'label' => WPWHPRO()->helpers->translate( 'Force default', $translation_ident ), 
					'choices' => array( 
						'yes' => WPWHPRO()->helpers->translate( 'Yes', $translation_ident ),
						'no' => WPWHPRO()->helpers->translate( 'No', $translation_ident ),
					), 
					'short_description' => WPWHPRO()->helpers->translate( '(String) Whether to force the supplied term as default to be assigned even if the object was not going to be term-less. Default: no', $translation_ident ),
				),
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		   => array( 'short_description' => WPWHPRO()->helpers->translate( '(mixed) The term id, as well as the taxonomy term id on success or wp_error on failure.', $translation_ident ) ),
				'msg'			=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			$returns_code = array (
				'success' => true,
				'msg' => 'The taxonomy term has been deleted successfully.',
				'data' => '',
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Delete a taxonomy term',
				'webhook_slug' => 'wp_delete_term',
				'steps' => array(
					WPWHPRO()->helpers->translate( "It is also required to set the term_id argument. Please set it to the ID of the term that you want to delete.", $translation_ident ),
					WPWHPRO()->helpers->translate( "The last required argument is <strong>taxonomy_slug</strong>. Please set it to the slug of the taxonomy you would like to remove the term from.", $translation_ident ),
				)
			) );

			return array(
				'action'			=> 'wp_delete_term',
				'name'			  => WPWHPRO()->helpers->translate( 'Delete taxonomy term', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'delete a taxonomy term', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Delete a taxonomy term for a specific taxonomy.', $translation_ident ),
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
			$default_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'default_id' ) );
			$force_default = ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'force_default' ) === 'yes' ) ? true : false;

			if( empty( $term_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please define the term_id argument.", 'action-wp_delete_term' );
				return $return_args;
			}

			if( empty( $taxonomy_slug ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please define the taxonomy_slug argument.", 'action-wp_delete_term' );
				return $return_args;
			}

			$args = array();

			if( ! empty( $default_id ) ){
				$args['default'] = $default_id;
			}

			if( ! empty( $force_default ) ){
				$args['force_default'] = $force_default;
			}
			
			$term_data = wp_delete_term( $term_id, $taxonomy_slug, $args );
 
			if( $term_data === true ) {
				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The taxonomy term has been deleted successfully.", 'action-wp_delete_term' );
			} elseif( $term_data === false ){
				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The term was not deleted as it did not exist in the first place.", 'action-wp_delete_term' );
			} elseif( $term_data === 0 ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Error deleting the taxonomy term as it is set as the default term.", 'action-wp_delete_term' );
			} elseif( is_wp_error( $term_data ) ){
				$return_args['data'] = $term_data;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "An error occured deleting the taxonomy term.", 'action-wp_delete_term' );
			} else {
				$return_args['data'] = $term_data;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "An unidentified error occured while deleting the taxonomy term.", 'action-wp_delete_term' );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.