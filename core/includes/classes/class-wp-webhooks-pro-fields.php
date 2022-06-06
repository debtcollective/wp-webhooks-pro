<?php

/**
 * WP_Webhooks_Pro_Fields Class
 *
 * This class contains all of the available fields functions
 *
 * @since 5.2.0
 */

/**
 * The Fields class of the plugin.
 *
 * @since 5.2.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Fields{

	/**
	 * Execute feature related hooks and logic to get 
	 * everything running
	 *
	 * @since 5.2.0
	 * @return void
	 */
	public function execute(){

		//Register predefined field callbacks
		add_filter( 'wpwhpro/fields/get_query_items/posts', array( $this, 'filter_query_post_items' ), 20, 3 );
		add_filter( 'wpwhpro/fields/get_query_items/users', array( $this, 'filter_query_user_items' ), 20, 3 );
		add_filter( 'wpwhpro/fields/get_query_items/comments', array( $this, 'filter_query_comment_items' ), 20, 3 );
		add_filter( 'wpwhpro/fields/get_query_items/terms', array( $this, 'filter_query_term_items' ), 20, 3 );
		add_filter( 'wpwhpro/fields/get_query_items/post_types', array( $this, 'filter_query_post_type_items' ), 20, 3 );
		add_filter( 'wpwhpro/fields/get_query_items/post_statuses', array( $this, 'filter_query_post_status_items' ), 20, 3 );
		add_filter( 'wpwhpro/fields/get_query_items/helpers', array( $this, 'filter_query_helpers_items' ), 20, 3 );
		add_filter( 'wpwhpro/fields/get_query_items/actions', array( $this, 'filter_query_action_items' ), 20, 3 );

	}

	/**
	 * The default fields that are available for 
	 * every field definition
	 *
	 * @return void
	 */
	public function get_field_defaults(){
		$defaults = array(
			'name' => '',
			'id' => '',
			'class' => 'wpwh-field',
			'type' => 'text',
			'label' => '',
			'description' => '',
			'default_value' => '',
			'required' => false,
		);

		return apply_filters( 'wpwhpro/fields/get_field_defaults', $defaults );
	}

	/**
	 * Get the validated field based on a specific type
	 *
	 * @param string $type
	 * @param array $args
	 * @return array $field
	 */
	public function get_field( $args = array(), $type = 'input' ){

		$default_args = $this->get_field_defaults();
		$args = array_merge( $default_args, $args );
		$field = null;

		switch( $type ){
			case 'select':
				$field = $this->prepare_select_field( $args );
				break;
			case 'textarea':
				$field = $this->prepare_textarea_field( $args );
				break;
			case 'input':
				$field = $this->prepare_input_field( $args );
				break;
		}

		return apply_filters( 'wpwhpro/fields/get_field', $field, $type, $args, $default_args );
	}

	public function get_query_items( $field, $args = array() ){
		$items = array(
			'total' => 0,
			'per_page' => 20,
			'item_count' => 0,
			'items' => array(),
		);

		if( is_array( $field ) && isset( $field['query'] ) && isset( $field['query']['filter'] ) && ! empty( $field['query']['filter'] ) ){
			$filter = sanitize_title( $field['query']['filter'] );
			$query_args = ( isset( $field['query']['args'] ) && is_array( $field['query']['args'] ) ) ? $field['query']['args'] : array();
			
			/**
			 * This hook allows internal and external functions
			 * to fetch and validate custom items. Within WP Webhooks, 
			 * we offer predefined filters: 
			 * 
			 * - post
			 * 
			 * The field strucutre looks like this
			 * $items = array(
			 * 		'total' => 2,
			 * 		'per_page' => 2,
			 * 		'item_count' => 2,
			 * 		'items' => array(
			 * 			'item-slug' => array(
			 * 				'value' => 'item-slug',
			 * 				'label' => 'Item Name', //you can add any other custom values
			 * 			)
			 * 		)
			 * )
			 */
			$items = apply_filters( 'wpwhpro/fields/get_query_items/' . $filter, $items, $query_args, $args );
		}

		//Maybe calculate the current page items
		if( isset( $items['items'] ) && is_array( $items['items'] ) ){
			$items['item_count'] = count( $items['items'] );
		}

		return apply_filters( 'wpwhpro/fields/get_query_items', $items, $args );
	}

	/**
	 * ######################
	 * ###
	 * #### PRIVATE FUNCTION DEFINITIONS
	 * ###
	 * #### please use the public function WPWHPRO()->fields->get_field() to access the separate field types
	 * ###
	 * ######################
	 */

	 private function prepare_input_field( $args = array() ){
		
		//field-specific definitions
		$field_defaults = array(
			'value' => '',
			'placeholder' => '',
			'copyable' => false,
		);

		$field = array_merge( $field_defaults, $args );

		return apply_filters( 'wpwhpro/fields/prepare_input_field', $field, $args, $field_defaults );
	 }

	 private function prepare_textarea_field( $args = array() ){
		
		//field-specific definitions
		$field_defaults = array(
			'type' => 'textarea',
			'value' => '',
			'placeholder' => '',
			'copyable' => false,
		);

		$field = array_merge( $field_defaults, $args );

		return apply_filters( 'wpwhpro/fields/prepare_textarea_field', $field, $args, $field_defaults );
	 }

	 private function prepare_select_field( $args = array() ){
		
		//field-specific definitions
		$field_defaults = array(
			'type'			=> 'select',
			'placeholder'	=> '',
			'choices'		=> array(),
			'query'			=> array(),
			'dependency'	=> array(),
		);

		$field = array_merge( $field_defaults, $args );

		return apply_filters( 'wpwhpro/fields/prepare_select_field', $field, $args, $field_defaults );
	 }

	 /**
	 * ######################
	 * ###
	 * #### QUERY CALLBACK FUNCTION DEFINITIONS
	 * ###
	 * #### These functions are for internal use and should not be called directly
	 * #### as the structure might change over time.
	 * #### Their main purpose is to provide a predefined filtering system 
	 * #### That can be used without writing a custom functionality.
	 * ####
	 * #### The function returns a predefined set of items with the dynamic items inside:
	 * #### array(
	 * #### 	'total' => 1,
	 * #### 	'per_page' => 1,
	 * #### 	'item_count' => 1,
	 * #### 	'items' => array(
	 * #### 		'value' => 'item-slug',
	 * #### 		'label' => 'Visual Item name',
	 * #### 	),
	 * #### )
	 * #### 
	 * ###
	 * ######################
	 */

	 /**
	  * Filter items using the default WP_Query. 
	  * This filter support all argument of the WP_Query class
	  * https://developer.wordpress.org/reference/classes/wp_query/
	  *
	  * @param array $entries - the available and found items
	  * @param array $query_args - The arguments defined within the settings query
	  * @param array $args - custom arguments that are used for the filter (e.g. paged, s - for search)
	  * @return array
	  */
	public function filter_query_post_items( $entries, $query_args, $args ){
		$default_args = array(
			'post_type' => 'post',
			'posts_per_page' => $entries['per_page'],
			'paged' => 1,
		);

		if( isset( $args['s'] ) ){
			$query_args['s'] = esc_sql( $args['s'] );
		}

		if( isset( $args['paged'] ) ){
			$query_args['paged'] = intval( $args['paged'] );
		}

		if( isset( $args['selected'] ) && ! empty( $args['selected'] ) && is_array( $args['selected'] ) ){

			//since we work with selected values, make sure we display all
			$query_args['posts_per_page'] = -1;

			$selected_items =  array_map( 'intval', $args['selected'] );

			//remove empty items
			foreach( $selected_items as $item_key => $item_val ){
				if( empty( $item_val ) ){
					unset( $selected_items[ $item_key ] );
				}
			}

			$query_args['post__in'] = $selected_items;
		}

		$query_args = array_merge( $default_args, $query_args );

		$post_query = new WP_Query( $query_args );

		if( ! empty( $post_query ) && ! is_wp_error( $post_query ) && isset( $post_query->posts ) && ! empty( $post_query->posts ) ){	

			foreach( $post_query->posts as $post ){
				$entries['items'][ $post->ID ] = array(
					'value' => $post->ID,
					'label' => $post->post_title,
				);
			}

			if( isset( $post_query->found_posts ) ){
				$entries['total'] = intval( $post_query->found_posts );
			}
		}

		return $entries;
	}

	 /**
	  * Filter items using the WP_User_Query. 
	  * This filter support all argument of the WP_User_Query class
	  * https://developer.wordpress.org/reference/classes/wp_user_query/
	  *
	  * @param array $entries - the available and found items
	  * @param array $query_args - The arguments defined within the settings query
	  * @param array $args - custom arguments that are used for the filter (e.g. paged, s - for a string search)
	  * @return array
	  */
	public function filter_query_user_items( $entries, $query_args, $args ){
		$default_args = array(
			'number' => $entries['per_page'],
			'fields' => array( 'ID', 'user_email' ),
			'paged' => 1,
		);

		if( isset( $args['s'] ) ){
			$query_args['search'] = esc_sql( $args['s'] );
		}

		if( isset( $args['paged'] ) ){
			$query_args['paged'] = intval( $args['paged'] );
		}

		if( isset( $args['selected'] ) && ! empty( $args['selected'] ) && is_array( $args['selected'] ) ){

			//since we work with selected values, make sure we display all
			$query_args['number'] = -1;

			$selected_items =  array_map( 'intval', $args['selected'] );

			//remove empty items
			foreach( $selected_items as $item_key => $item_val ){
				if( empty( $item_val ) ){
					unset( $selected_items[ $item_key ] );
				}
			}

			$query_args['include'] = $selected_items;
		}

		$query_args = array_merge( $default_args, $query_args );	

		$user_query = new WP_User_Query( $query_args );
		if( ! empty( $user_query ) ){
			$authors = $user_query->get_results();

			

			if( ! empty( $authors ) && is_array( $authors ) ){
				foreach( $authors as $author ){
					$entries['items'][ $author->ID ] = array(
						'value' => $author->ID,
						'label' => $author->user_email,
					);
				}
			}

			$entries['total'] = intval( $user_query->get_total() );
		}	

		return $entries;
	}

	 /**
	  * Filter items using the WP_Comment_Query. 
	  * This filter support all argument of the WP_Comment_Query class
	  * https://developer.wordpress.org/reference/classes/wp_comment_query/
	  *
	  * @param array $entries - the available and found items
	  * @param array $query_args - The arguments defined within the settings query
	  * @param array $args - custom arguments that are used for the filter (e.g. paged, s - for a string search)
	  * @return array
	  */
	public function filter_query_comment_items( $entries, $query_args, $args ){
		$default_args = array(
			'number' => $entries['per_page'],
			'fields' => array( 'ID', 'user_email' ),
			'paged' => 1,
		);

		if( isset( $args['s'] ) ){
			$query_args['search'] = esc_sql( $args['s'] );
		}

		if( isset( $args['paged'] ) ){
			$query_args['paged'] = intval( $args['paged'] );
		}

		if( isset( $args['selected'] ) && ! empty( $args['selected'] ) && is_array( $args['selected'] ) ){

			//since we work with selected values, make sure we display all
			$query_args['number'] = 0;

			$selected_items =  array_map( 'intval', $args['selected'] );

			//remove empty items
			foreach( $selected_items as $item_key => $item_val ){
				if( empty( $item_val ) ){
					unset( $selected_items[ $item_key ] );
				}
			}

			$query_args['comment__in'] = $selected_items;
		}

		$query_args = array_merge( $default_args, $query_args );	

		$comment_query = new WP_Comment_Query( $query_args );
		if( ! empty( $comment_query ) ){

			$comments = $comment_query->get_comments();	

			if( ! empty( $comments ) && is_array( $comments ) ){
				foreach( $comments as $comment ){
					$entries['items'][ $comment->comment_ID ] = array(
						'value' => $comment->comment_ID,
						'label' => '#' . $comment->comment_ID,
					);
				}
			}
			
		}

		//since there is no official total, we work with a custom counter + 1
		if( isset( $args['paged'] ) && $args['paged'] > 1 ){
			$entries['total'] = ( $args['paged'] - 1 ) * $query_args['number'];
		} else {
			$entries['total'] = count( $entries['items'] );
		}

		if( count( $entries['items'] ) >= $query_args['number'] ){
			//here we can assume that further entries appear, so we add one to the total
			$entries['total'] += 1;
		}

		return $entries;
	}

	 /**
	  * Filter items using the WP_Term_Query. 
	  * This filter support all argument of the WP_Term_Query class
	  * https://developer.wordpress.org/reference/classes/wp_term_query/
	  *
	  * @param array $entries - the available and found items
	  * @param array $query_args - The arguments defined within the settings query
	  * @param array $args - custom arguments that are used for the filter (e.g. paged, s - for a string search)
	  * @return array
	  */
	public function filter_query_term_items( $entries, $query_args, $args ){
		$default_args = array(
			'number' => $entries['per_page'],
			'offset' => 0,
			'hide_empty' => false,
		);

		if( isset( $args['s'] ) ){
			$query_args['search'] = esc_sql( $args['s'] );
		}

		if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
			$query_args['offset'] = ( intval( $args['paged'] ) - 1 ) * $default_args['number'];
		}

		if( isset( $args['selected'] ) && ! empty( $args['selected'] ) && is_array( $args['selected'] ) ){

			//since we work with selected values, make sure we display all
			$query_args['number'] = 0;
			$query_args['offset'] = 0;

			$selected_items =  array_map( 'intval', $args['selected'] );

			//remove empty items
			foreach( $selected_items as $item_key => $item_val ){
				if( empty( $item_val ) ){
					unset( $selected_items[ $item_key ] );
				}
			}

			$query_args['include'] = $selected_items;
		}

		$query_args = array_merge( $default_args, $query_args );	
		$terms_query = new WP_Term_Query( $query_args );

		if( ! empty( $terms_query ) ){
			$terms = $terms_query->get_terms();

			if( ! empty( $terms ) && is_array( $terms ) ){
				foreach( $terms as $term ){
					$entries['items'][ $term->term_id ] = array(
						'value' => $term->term_id,
						'label' => $term->name,
					);
				}
			}

		}	

		//since there is no official total, we work with a custom counter + 1
		if( isset( $args['paged'] ) && $args['paged'] > 1 ){
			$entries['total'] = ( $args['paged'] - 1 ) * $query_args['number'];
		} else {
			$entries['total'] = count( $entries['items'] );
		}

		if( count( $entries['items'] ) >= $query_args['number'] ){
			//here we can assume that further entries appear, so we add one to the total
			$entries['total'] += 1;
		}

		return $entries;
	}

	 /**
	  * Filter items using the get_post_types() function. 
	  * This filter support all argument of the get_post_types function
	  * https://developer.wordpress.org/reference/functions/get_post_types/
	  *
	  * @param array $entries - the available and found items
	  * @param array $query_args - The arguments defined within the settings query
	  * @param array $args - custom arguments that are used for the filter (e.g. paged, s - for a string search)
	  * @return array
	  */
	public function filter_query_post_type_items( $entries, $query_args, $args ){

		//skip the query if a secondary page is returned as everything is dumped into a single one
		if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
			return $entries;
		}

		$default_args = array();

		$query_args = array_merge( $default_args, $query_args );	

		$post_types = get_post_types( $query_args, 'objects' );

		if( ! empty( $post_types ) ){
			foreach( $post_types as $post_type_slug => $post_type_data ){

				if( isset( $post_type_data->label ) ){
					$entries['items'][ $post_type_slug ] = array(
						'value' => $post_type_slug,
						'label' => $post_type_data->label,
					);
				}
				
			}

		}

		//Validate search
		if( isset( $args['s'] ) && ! empty( $args['s'] ) ){
			foreach( $entries['items'] as $item_key => $item_data ){
				
				if(
					strpos( $item_data['value'], $args['s'] ) === false
					&& strpos( $item_data['label'], $args['s'] ) === false
				){
					unset( $entries['items'][ $item_key ] );
				}
				
			}
		}

		//validate selected fields
		if( isset( $args['selected'] ) && ! empty( $args['selected'] ) && is_array( $args['selected'] ) ){

			$selected_items =  array_map( 'sanitize_title', $args['selected'] );
			$selected_items_validated = array();

			//remove unselected items
			foreach( $entries['items'] as $item_key => $item_data ){
				if( in_array( $item_key, $selected_items ) ){
					$selected_items_validated[ $item_key ] = $item_data;
				}
			}

			$entries['items'] = $selected_items_validated;
		}

		//Get the item total
		$entries['total'] = intval( count( $entries['items'] ) );
		$entries['per_page'] = $entries['total'];

		return $entries;
	}

	 /**
	  * Filter items using the get_post_statuses() function. 
	  * This filter support all arguments of the get_post_statuses function
	  * https://developer.wordpress.org/reference/functions/get_post_statuses/
	  *
	  * @param array $entries - the available and found items
	  * @param array $query_args - The arguments defined within the settings query
	  * @param array $args - custom arguments that are used for the filter (e.g. paged, s - for a string search)
	  * @return array
	  */
	public function filter_query_post_status_items( $entries, $query_args, $args ){

		//skip the query if a secondary page is returned as everything is dumped into a single one
		if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
			return $entries;
		}

		$default_args = array();

		$query_args = array_merge( $default_args, $query_args );	

		$post_statuses = WPWHPRO()->settings->get_all_post_statuses();

		if( ! empty( $post_statuses ) ){
			foreach( $post_statuses as $post_status_slug => $post_status_label ){

				$entries['items'][ $post_status_slug ] = array(
					'value' => $post_status_slug,
					'label' => $post_status_label,
				);
				
			}

		}

		//Validate search
		if( isset( $args['s'] ) && ! empty( $args['s'] ) ){
			foreach( $entries['items'] as $item_key => $item_data ){
				
				if(
					strpos( $item_data['value'], $args['s'] ) === false
					&& strpos( $item_data['label'], $args['s'] ) === false
				){
					unset( $entries['items'][ $item_key ] );
				}
				
			}
		}

		//validate selected fields
		if( isset( $args['selected'] ) && ! empty( $args['selected'] ) && is_array( $args['selected'] ) ){

			$selected_items =  array_map( 'sanitize_title', $args['selected'] );
			$selected_items_validated = array();

			//remove unselected items
			foreach( $entries['items'] as $item_key => $item_data ){
				if( in_array( $item_key, $selected_items ) ){
					$selected_items_validated[ $item_key ] = $item_data;
				}
			}

			$entries['items'] = $selected_items_validated;
		}

		//Get the item total
		$entries['total'] = intval( count( $entries['items'] ) );
		$entries['per_page'] = $entries['total'];

		return $entries;
	}

	/**
	  * Filter items using a custom helper function from within an integration
	  * 
	  * To use this filter within a custom integration setting, you must define
	  * three arguments:
	  * 
	  * $args = array
	  * 	'integration' => 'integration-slug',
	  * 	'helper' => 'the_helper_slug',
	  * 	'function' => 'the_helper_function',
	  * )
	  * 
	  * We pass along three arguments to the helper function: $entries, $query_args, $args
	  * 
	  * @param array $entries - the available and found items
	  * @param array $query_args - The arguments defined within the settings query
	  * @param array $args - custom arguments that are used for the filter (e.g. paged, s - for a string search)
	  * @return array
	  */
	public function filter_query_helpers_items( $entries, $query_args, $args ){

		if( ! isset( $entries['total'] ) ){
			$entries['total'] = 0;
		}

		if( ! isset( $entries['items'] ) ){
			$entries['items'] = array();
		}
		
		if( 
			is_array( $query_args ) 
			&& isset( $query_args['integration'] ) 
			&& isset( $query_args['helper'] ) 
			&& isset( $query_args['function'] ) 
			&& ! empty( $query_args['integration'] )
			&& ! empty( $query_args['helper'] )
			&& ! empty( $query_args['function'] )
		){

			$integration_slug = sanitize_title( $query_args['integration'] );
			$integration_helper = sanitize_title( $query_args['helper'] );
			$integration_helper_function = sanitize_title( $query_args['function'] );

			$helper_item = WPWHPRO()->integrations->get_helper( $integration_slug, $integration_helper );

			if( is_object( $helper_item ) && method_exists( $helper_item, $integration_helper_function ) ){
				$entries = $helper_item->{$integration_helper_function}( $entries, $query_args, $args );
			}
		}

		//Maybe prevent the second page from reloading the same parameters
		if( isset( $args['paged'] ) && $args['paged'] > 1 && $entries['total'] <= count( $entries['items'] ) ){
			$entries['items'] = array();
		}

		return $entries;
	}

	/**
	  * Filter items using the WPWHPRO()->webhook->get_actions() function. 
	  * This filter support all argument of the WPWHPRO()->webhook->get_actions() function
	  *
	  * @param array $entries - the available and found items
	  * @param array $query_args - The arguments defined within the settings query
	  * @param array $args - custom arguments that are used for the filter (e.g. paged, s - for a string search)
	  * @return array
	  */
	  public function filter_query_action_items( $entries, $query_args, $args ){

		//skip the query if a secondary page is returned as everything is dumped into a single one
		if( isset( $args['paged'] ) && (int) $args['paged'] > 1 ){
			return $entries;
		}

		$validated_actions = array();

		$actions = WPWHPRO()->webhook->get_actions();

		if ( is_array( $actions ) && ! empty( $actions ) ) {
			foreach ( $actions as $action ) {

				if( isset( $action['action'] ) ){
					$action_name = $action['action'];

					if( isset( $action['name'] ) ){
						$action_name = $action['name'];
					}

					$entries['items'][ $action['action'] ] = array(
						'value' => $action['action'],
						'label' => esc_html( $action_name ),
					);
				}

			}
		}

		asort( $entries['items'] );

		//Validate search
		if( isset( $args['s'] ) && ! empty( $args['s'] ) ){
			foreach( $entries['items'] as $item_key => $item_data ){
				
				if(
					strpos( $item_data['value'], $args['s'] ) === false
					&& strpos( $item_data['label'], $args['s'] ) === false
				){
					unset( $entries['items'][ $item_key ] );
				}
				
			}
		}

		//validate selected fields
		if( isset( $args['selected'] ) && ! empty( $args['selected'] ) && is_array( $args['selected'] ) ){

			$selected_items =  array_map( 'sanitize_title', $args['selected'] );
			$selected_items_validated = array();

			//remove unselected items
			foreach( $entries['items'] as $item_key => $item_data ){
				if( in_array( $item_key, $selected_items ) ){
					$selected_items_validated[ $item_key ] = $item_data;
				}
			}

			$entries['items'] = $selected_items_validated;
		}

		//Get the item total
		$entries['total'] = intval( count( $entries['items'] ) );
		$entries['per_page'] = $entries['total'];

		return $entries;
	}

}