<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_jetengine_Actions_jetengine_get_relations' ) ) :

	/**
	 * Load the jetengine_get_relations action
	 *
	 * @since 4.3.7
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_jetengine_Actions_jetengine_get_relations {

	public function get_details(){

		$translation_ident = "action-jetengine_get_relations-description";

		$parameter = array(
			'post_id' => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The ID of the post you want to get the active relations from.', $translation_ident ) ),
			'do_action' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook.', $translation_ident ) )
		);

		$returns = array(
			'success' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg' => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			'data' => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) The adjusted meta data, includnig the response of the related ACF function." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The relations have been returned successfully.',
			'data' => 
			array (
			  'post_id' => 9114,
			  'post_type' => 'page',
			  'type_name' => 'posts::page',
			  'relations' => 
			  array (
				3 => 
				array (
				  'relation_id' => '3',
				  'relation_type' => 'children',
				  'relation_object' => 'posts::post',
				  'relations' => 
				  array (
					0 => '7914',
				  ),
				),
				2 => 
				array (
				  'relation_id' => '2',
				  'relation_type' => 'children',
				  'relation_object' => 'posts::post',
				  'relations' => 
				  array (
					0 => '7914',
				  ),
				),
				1 => 
				array (
				  'relation_id' => '1',
				  'relation_type' => 'children',
				  'relation_object' => 'posts::post',
				  'relations' => 
				  array (
					0 => '7914',
				  ),
				),
			  ),
			),
		);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the jetengine_get_relations action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 1 );
function my_custom_callback_function( $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "An array containing the information we will send back as the response to the initial webhook caller.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Get post relations',
				'webhook_slug' => 'jetengine_get_relations',
				'steps' => array(
					WPWHPRO()->helpers->translate( "The second argument you need to set is <strong>post_id</strong>. Please set it to the ID of the post you want to get the relations from.", $translation_ident ),
				),
			) );

			return array(
				'action'			=> 'jetengine_get_relations',
				'name'			  => WPWHPRO()->helpers->translate( 'Get active post relations', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'get all active relations for a post', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Get all active relations for a post within "JetEngine".', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'jetengine',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$translation_ident = "action-jetengine_get_relations-execute";
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(),
			);
	
			$post_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'post_id' ) );
			$do_action = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $post_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the post_id argument first.", $translation_ident );
				return $return_args;
			}

			$post_type = get_post_type( $post_id );

			if( ! $post_type || ! isset( jet_engine()->relations->types_helper ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "We could not determine the given post type for your post id.", $translation_ident );
				return $return_args;
			}
	
			$type_name = jet_engine()->relations->types_helper->type_name_by_parts( 'posts', $post_type );
			$relations = jet_engine()->relations->get_active_relations();
			$relations_validated = array();

			if( ! empty( $relations ) ){
				foreach ( $relations  as $relation ) {

					$relation_id           = 0;
					$relation_ids           = array();
					$relation_object        = null;
					$relation_object_type = null;
		
					if( $relation->get_args( 'parent_object' ) === $type_name ){
						$relation_id = $relation->get_id();
						$relation_object = $relation->get_args( 'child_object' );
						$relation_object_type = 'children';
						$relation_ids = $relation->get_children( $post_id, 'ids' );
					} elseif( $relation->get_args( 'child_object' ) === $type_name ){
						$relation_id = $relation->get_id();
						$relation_object = $relation->get_args( 'parent_object' );
						$relation_object_type = 'parent';
						$relation_ids = $relation->get_parents( $post_id, 'ids' );
					}
		
					if( $relation_object && $relation_id ){
						$relations_validated[ $relation_id ] = array(
							'relation_id' => $relation_id,
							'relation_type' => $relation_object_type,
							'relation_object' => $relation_object,
							'relations' => $relation_ids,
						);
					}
		
				}
			}

			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "The relations have been returned successfully.", $translation_ident );
			$return_args['data']['post_id'] = $post_id;
			$return_args['data']['post_type'] = $post_type;
			$return_args['data']['type_name'] = $type_name;
			$return_args['data']['relations'] = $relations_validated;

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}
	
			return $return_args;
	
		}

	}

endif; // End if class_exists check.