<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_multisite_assign_user' ) ) :

	/**
	 * Load the multisite_assign_user action
	 *
	 * @since 5.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_multisite_assign_user {

		public function is_active(){
			$return = false;

			if( function_exists('is_multisite') ){
				$return = is_multisite();
			}

			return $return;
		}

	public function get_details(){

		$translation_ident = "action-multisite_assign_user-description";

		$parameter = array(
			'user' => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The user email or ID of the user you want to assign to the specific sub sites.', $translation_ident ) ),
			'blog_ids' => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'A comma-separated list of the IDs of the specific sub sites you want to assign the user to. Or "all" for all sub sites.', $translation_ident ) ),
			'role' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The role you want to assign to the user. Default: The default role of your blog.', $translation_ident ) ),
			'do_action' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook.', $translation_ident ) )
		);

		$returns = array(
			'success' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg' => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			'data' => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) The adjusted meta data, includnig the response of the related WP function." )', $translation_ident ) ),
		);

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "In case you want to add the user to multiple sub-sites, you can either comma-separate them like <code>2,3,12,44</code>, or you can add them via a JSON construct:", $translation_ident ); ?>
<pre>{
  23,
  3,
  44
}</pre>
<?php echo WPWHPRO()->helpers->translate( "Set this argument to <strong>all</strong> to assign the user to all sub-sits of the network.", $translation_ident ); ?>
		<?php
		$parameter['blog_ids']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>manage_term_meta</strong> action was fired.", $translation_ident ); ?>
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
		<strong>$return_args</strong> (array)
		<?php echo WPWHPRO()->helpers->translate( "Contains all the data we send back to the webhook action caller. The data includes the following key: msg, success, data", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'The user has assigned to the sites successfully.',
			'data' => 
			array (
			  'user_id' => 123,
			  'role' => 'editor',
			  'sites' => 
			  array (
				0 => '1',
				1 => '2',
			  ),
			  'errors' => 
			  array (
			  ),
			),
		);

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Multisite assign user',
				'webhook_slug' => 'multisite_assign_user',
				'steps' => array(
					WPWHPRO()->helpers->translate( "Please also set the <strong>user</strong> argument. Set it to the ID or the email of the user.", $translation_ident ),
					WPWHPRO()->helpers->translate( "The third argument you need to set is <strong>manage_meta_data</strong>. It accepts a JSON formatted string or array as seen within the details section of the argument.", $translation_ident ),
				),
				'tipps' => array(
					WPWHPRO()->helpers->translate( "To create post meta values visually, you can use our meta value generator at: <a title=\"Visit our meta value generator\" href=\"https://wp-webhooks.com/blog/how-to-update-custom-post-meta-values-with-wp-webhooks/\" target=\"_blank\">https://wp-webhooks.com/blog/how-to-update-custom-post-meta-values-with-wp-webhooks/</a>.", $translation_ident ),
				)
			) );

			return array(
				'action'			=> 'multisite_assign_user',
				'name'			  => WPWHPRO()->helpers->translate( 'Multisite assign user', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'assign a user to a multisite sub-site', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Assign a user to one, multiple, or all blogs within a WordPress multisite.', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$translation_ident = "action-multisite_assign_user-execute";
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(),
			);
	
			$user = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$blog_ids = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'blog_ids' );
			$role = sanitize_title( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'role' ) );
			$do_action = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $blog_ids ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the blog_ids argument first.", $translation_ident );
				return $return_args;
			}

			$user_id = 0;

            if( ! empty( $user ) && is_numeric( $user ) ){
                $user_id = intval( $user );
            } elseif( ! empty( $user ) && is_email( $user ) ) {
                $user_data = get_user_by( 'email', $user );
                if( ! empty( $user_data ) && isset( $user_data->ID ) && ! empty( $user_data->ID ) ){
                    $user_id = $user_data->ID;
                }
            }

            if( empty( $user_id ) ){
                $return_args['msg'] = WPWHPRO()->helpers->translate( "We could not find a user for your given user argument value.", 'action-rcp_disable_membership-error' );
				return $return_args;
            }

			if( empty( $role ) ){
				$role = get_option( 'default_role' );
			}

			$errors = array();

			if( $blog_ids === 'all' ){

				$blog_ids_array = array();
				$blogs = get_sites();
				foreach( $blogs as $b ){
					if( isset( $b->blog_id  ) && ! empty( $b->blog_id  ) ){
						$blog_ids_array[] = $b->blog_id;
					}
				}

			} else {
				if( WPWHPRO()->helpers->is_json( $blog_ids ) ){
					$blog_ids_array = json_decode( $blog_ids, true );
				} else {
					$blog_ids_array = array_map( "trim", explode( ',', $blog_ids ) );
				}
			}
			
			
			if( ! empty( $blog_ids_array ) && is_array( $blog_ids_array ) ){
				foreach( $blog_ids_array as $blog_id ){

					$blog_id = intval( $blog_id );

					$result = add_user_to_blog( $blog_id, $user_id, $role );
					if( is_wp_error( $result ) ){
						$error[] = $result->get_error_message();
					}
				}
			}

			$return_args['data']['user_id'] = $user_id;
			$return_args['data']['role'] = $role;
			$return_args['data']['sites'] = $blog_ids_array;
			$return_args['data']['errors'] = $errors;

			if( empty( $errors ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The user has assigned to the sites successfully.", 'action-plugin_activate-success' );
				$return_args['success'] = true;
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "One or more errors occured while adding the user to the multisite sites.", 'action-plugin_activate-success' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}
	
			return $return_args;
	
		}

	}

endif; // End if class_exists check.