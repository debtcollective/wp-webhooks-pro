<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_create_term' ) ) :

	/**
	 * Load the create_term action
	 *
	 * @since 4.2.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_create_term {

		public function get_details(){

			$translation_ident = "action-create_term-content";

			$parameter = array(
				'term_name'			=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(String) The name for the specific taxonomy term.', $translation_ident ) ),
				'taxonomy'			=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( '(String) The slug of the taxonomy to relate the term with.', $translation_ident ) ),
				'alias_of'			=> array( 'short_description' => WPWHPRO()->helpers->translate( '(String) Slug of the term to make this term an alias of. Default empty string. Accepts a term slug.', $translation_ident ) ),
				'description'			=> array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The term description. Default empty string.', $translation_ident ) ),
				'parent'			=> array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The id of the parent term. Default 0.', $translation_ident ) ),
				'slug'			=> array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The term slug to use. Default empty string.', $translation_ident ) ),
				'name'			=> array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The term name to use. Default empty string.', $translation_ident ) ),
				'do_action'		  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		   => array( 'short_description' => WPWHPRO()->helpers->translate( '(mixed) The term id, as well as the taxonomy term id on success or wp_error on failure.', $translation_ident ) ),
				'msg'			=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>create_term</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $return_args, $term_name, $taxonomy, $term_args ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "Contains all the data we send back to the webhook action caller. The data includes the following key: msg, success, data", $translation_ident ); ?>
	</li>
	<li>
		<strong>$term_name</strong> (string)<br>
		<?php echo WPWHPRO()->helpers->translate( "Contains the name of the term that was just added.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$taxonomy</strong> (string)<br>
		<?php echo WPWHPRO()->helpers->translate( "Contains the taxonomy slug of the taxonomy you added the term to.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$term_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "Contains the additional information you set within the create_term webhook action.", $translation_ident ); ?>
	</li>
</ol>
			<?php
			$parameter['do_action']['description'] = ob_get_clean();

			$returns_code = array (
				'success' => true,
				'msg' => 'Taxonomy terms were set successfully.',
				'data' => 
				array (
					'term_id' => 93,
					'term_taxonomy_id' => 93,
				),
			);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Create a taxonomy term',
				'webhook_slug' => 'create_term',
				'steps' => array(
					WPWHPRO()->helpers->translate( "It is also required to set the term_name argument. Please add the name you want to see for your term. E.g. Sport", $translation_ident ),
					WPWHPRO()->helpers->translate( "The last required argument is <strong>taxonomy</strong>. Please set it to the slug of the taxonomy you would like to assign the term to.", $translation_ident ),
				)
			) );

			return array(
				'action'			=> 'create_term',
				'name'			  => WPWHPRO()->helpers->translate( 'Create taxonomy term', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'create a taxonomy term', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Create a taxonomy term for a specific taxonomy.', $translation_ident ),
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

			$term_name = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'term_name' );
			$taxonomy = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'taxonomy' );
			$alias_of = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'alias_of' );
			$description = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'description' );
			$parent = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'parent' ) );
			$slug = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'slug' );
			$name = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'name' );

			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $term_name ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please define the term_name argument first.", 'action-create_term' );
				return $return_args;
			}

			if( empty( $taxonomy ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please define the taxonomy argument first.", 'action-create_term' );
				return $return_args;
			}

			$term_args = array();

			if( ! empty( $alias_of ) ){
				$term_args['alias_of'] = $alias_of;
			}

			if( ! empty( $description ) ){
				$term_args['description'] = $description;
			}

			if( ! empty( $parent ) ){
				$term_args['parent'] = $parent;
			}

			if( ! empty( $slug ) ){
				$term_args['slug'] = $slug;
			}

			if( ! empty( $name ) ){
				$term_args['name'] = $name;
			}

			$term_data = wp_insert_term( $term_name, $taxonomy, $term_args );
 
			if ( ! is_wp_error( $term_data ) ) {
				$return_args['success'] = true;
				$return_args['data'] = $term_data;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Taxonomy terms were set successfully.", 'action-create_term' );
			} else {
				$return_args['data'] = $term_data;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Error while setting taxonomy terms", 'action-create_term' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $term_name, $taxonomy, $term_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.