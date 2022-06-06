<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_advanced_custom_fields_Actions_acf_manage_comment_meta_data' ) ) :

	/**
	 * Load the acf_manage_comment_meta_data action
	 *
	 * @since 4.3.7
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_advanced_custom_fields_Actions_acf_manage_comment_meta_data {

	public function get_details(){

		$translation_ident = "action-acf_manage_comment_meta_data-description";

		$parameter = array(
			'comment_id' => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The ID of the comment you want to perform the action for.', $translation_ident ) ),
			'meta_update' => array( 
				'type' => 'repeater',
				'label' => WPWHPRO()->helpers->translate( 'Add/Update ACF Meta', $translation_ident ),
				'short_description' => WPWHPRO()->helpers->translate( 'Update (or add) ACF meta keys/values.', $translation_ident ),
			),
			'manage_acf_data' => array( 
				'label' => WPWHPRO()->helpers->translate( 'Manage ACF Data (Advanced)', $translation_ident ),
				'short_description' => WPWHPRO()->helpers->translate( 'In case you want to add more complex ACF data, this field is for you. Check out some examples within our post meta blog post.', $translation_ident )
			),
			'do_action' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook.', $translation_ident ) )
		);

		$returns = array(
			'success' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg' => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			'data' => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) The adjusted meta data, includnig the response of the related ACF function." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The given ACF data has been successfully executed.',
			'data' => 
			array (
			  'update_field' => 
			  array (
				0 => 
				array (
				  'selector' => 'your_text_field',
				  'value' => 'Some custom value',
				  'response' => 123,
				),
			  ),
			),
		);

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This arguments accepts a JSON formatted string with the field key as the key and the ACF value as the value.", $translation_ident ); ?>
<br>
<pre>
{
	"meta_key": "Meta Value"
}
</pre>
		<?php
		$parameter['meta_update']['description'] = ob_get_clean();

		ob_start();
		WPWHPRO()->acf->load_acf_description( $translation_ident );
		$parameter['manage_acf_data']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the acf_manage_comment_meta_data action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 2 );
function my_custom_callback_function( $manage_acf_data, $return_args ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$manage_acf_data</strong> (String)<br>
		<?php echo WPWHPRO()->helpers->translate( "The ACF data that was sent by the webhook caller.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "An array containing the information we will send back as the response to the initial webhook caller.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Manage ACF comment meta',
				'webhook_slug' => 'acf_manage_comment_meta_data',
				'steps' => array(
					WPWHPRO()->helpers->translate( "Please also set the <strong>comment_id</strong> argument. Set it to the ID of your chosen comment.", $translation_ident ),
					WPWHPRO()->helpers->translate( "The third argument you need to set is <strong>manage_acf_data</strong>. It accepts a JSON formatted string or array as seen within the details section of the argument.", $translation_ident ),
				),
				'tipps' => array(
					WPWHPRO()->helpers->translate( "To create comment meta values visually, you can use our meta value generator at: <a title=\"Visit our meta value generator\" href=\"https://wp-webhooks.com/blog/how-to-update-custom-post-meta-values-with-wp-webhooks/\" target=\"_blank\">https://wp-webhooks.com/blog/how-to-update-custom-post-meta-values-with-wp-webhooks/</a>.", $translation_ident ),
				)
			) );

			return array(
				'action'			=> 'acf_manage_comment_meta_data',
				'name'			  => WPWHPRO()->helpers->translate( 'Manage ACF comment meta', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'add, update, or delete ACF comment meta', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Add, update, or delete custom comment meta data within "Advanced Custom Fields".', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'advanced-custom-fields',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$translation_ident = "action-acf_manage_comment_meta_data-execute";
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(),
			);
	
			$comment_id = intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'comment_id' ) );
			$meta_update = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'meta_update' );
			$manage_acf_data = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'manage_acf_data' );
			$do_action = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $comment_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the comment_id argument first.", $translation_ident );
				return $return_args;
			}

			$comment = get_comment( $comment_id );
			if( empty( $comment ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The comment you try to update does not exist.", $translation_ident );
				return $return_args;
			}

			if( empty( $manage_acf_data ) && empty( $meta_update ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set either the manage_acf_data or the meta_update argument.", $translation_ident );
				return $return_args;
			}

			if( ! empty( $meta_update ) ){
				$manage_acf_data = WPWHPRO()->acf->merge_repeater_meta_data( $manage_acf_data, $meta_update );
			}

			$return_args = WPWHPRO()->acf->manage_acf_meta( $comment_id, $manage_acf_data, 'comment' );

			if( ! empty( $do_action ) ){
				do_action( $do_action, $manage_acf_data, $return_args );
			}
	
			return $return_args;
	
		}

	}

endif; // End if class_exists check.