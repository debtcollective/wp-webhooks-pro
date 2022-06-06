<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Actions_wp_manage_user_meta_data' ) ) :

	/**
	 * Load the wp_manage_user_meta_data action
	 *
	 * @since 4.3.7
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Actions_wp_manage_user_meta_data {

	public function get_details(){

		$translation_ident = "action-wp_manage_user_meta_data-description";

		$parameter = array(
			'user_id' => array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'The ID (or the email) of the user you want to perform the action for.', $translation_ident ) ),
			'meta_update' => array( 
				'type' => 'repeater',
				'label' => WPWHPRO()->helpers->translate( 'Add/Update Meta', $translation_ident ),
				'short_description' => WPWHPRO()->helpers->translate( 'Update (or add) meta keys/values.', $translation_ident ),
			),
			'manage_meta_data' => array( 
				'label' => WPWHPRO()->helpers->translate( 'Manage Meta Data (Advanced)', $translation_ident ),
				'short_description' => WPWHPRO()->helpers->translate( 'In case you want to add more complex meta data, this field is for you. Check out some examples within our post meta blog post.', $translation_ident )
			),
			'do_action' => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after the plugin fires this webhook.', $translation_ident ) )
		);

		$returns = array(
			'success' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'msg' => array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			'data' => array( 'short_description' => WPWHPRO()->helpers->translate( '(array) The adjusted meta data, includnig the response of the related WP function." )', $translation_ident ) ),
		);

		$returns_code = array (
			'success' => true,
			'msg' => 'The meta data was successfully executed.',
			'data' => 
			array (
			  'update_user_meta' => 
			  array (
				0 => 
				array (
				  'meta_key' => 'demo_field',
				  'meta_value' => 'Some custom value',
				  'prev_value' => false,
				  'response' => 4941,
				),
			  ),
			),
		);

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This arguments accepts a JSON formatted string with the meta key as the key and the meta value as the value.", $translation_ident ); ?>
<br>
<pre>
{
	"meta_key": "Meta Value"
}
</pre>
		<?php
		$parameter['meta_update']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument integrates the full features of managing user related meta values.", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "<strong>Please note</strong>: This argument is very powerful and requires some good understanding of JSON. It is integrated with the commonly used functions for managing user meta within WordPress. You can find a list of all avaialble functions here: ", $translation_ident ); ?>
<ul>
	<li><strong>add_user_meta()</strong>: <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/add_user_meta/">https://developer.wordpress.org/reference/functions/add_user_meta/</a></li>
	<li><strong>update_user_meta()</strong>: <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/update_user_meta/">https://developer.wordpress.org/reference/functions/update_user_meta/</a></li>
	<li><strong>delete_user_meta()</strong>: <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/delete_user_meta/">https://developer.wordpress.org/reference/functions/delete_user_meta/</a></li>
</ul>
<br>
<?php echo WPWHPRO()->helpers->translate( "Down below you will find a complete JSON example that shows you how to use each of the functions above.", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "We also offer JSON to array/object serialization for single user meta values. This means, you can turn JSON into a serialized array or object.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "This argument accepts a JSON construct as an input. This construct contains each available function as a top-level key within the first layer and the assigned data respectively as a value. If you want to learn more about each line, please take a closer look at the bottom of the example.", $translation_ident ); ?>
<?php echo WPWHPRO()->helpers->translate( "Down below you will find a list that explains each of the top level keys.", $translation_ident ); ?>
<ol>
	<li>
		<strong><?php echo WPWHPRO()->helpers->translate( "add_user_meta", $translation_ident ); ?></strong>
		<pre>{
   "add_user_meta":[
	  {
		"meta_key": "first_custom_key",
		"meta_value": "Some custom value"
	  },
	  {
		"meta_key": "second_custom_key",
		"meta_value": { "some_array_key": "Some array Value" },
		"unique": true
	  }
	]
}</pre>
		<?php echo WPWHPRO()->helpers->translate( "This key refers to the <strong>add_user_meta()</strong> function of WordPress:", $translation_ident ); ?> <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/add_user_meta/">https://developer.wordpress.org/reference/functions/add_user_meta/</a><br>
		<?php echo WPWHPRO()->helpers->translate( "In the example above, you will find two entries within the add_user_meta key. The first one shows the default behavior using only the meta key and the value. This causes the meta key to be created without checking upfront if it exists - that allows you to create the meta value multiple times.", $translation_ident ); ?><br>
		<?php echo WPWHPRO()->helpers->translate( "As seen in the second entry, you will find a third key called <strong>unique</strong> that allows you to check upfront if the meta key exists already. If it does, the meta entry is neither created, nor updated. Set the value to <strong>true</strong> to check against existing ones. Default: false", $translation_ident ); ?><br>
		<?php echo WPWHPRO()->helpers->translate( "If you look closely to the second entry again, the value included is not a string, but a JSON construct, which is considered as an array and will therefore be serialized. The given value will be saved to the database in the following format: <code>a:1:{s:14:\"some_array_key\";s:16:\"Some array Value\";}</code>", $translation_ident ); ?>
	</li>
	<li>
		<strong><?php echo WPWHPRO()->helpers->translate( "update_user_meta", $translation_ident ); ?></strong>
		<pre>{
   "update_user_meta":[
	  {
		"meta_key": "first_custom_key",
		"meta_value": "Some custom value"
	  },
	  {
		"meta_key": "second_custom_key",
		"meta_value": "The new value",
		"prev_value": "The previous value"
	  }
	]
}</pre>
		<?php echo WPWHPRO()->helpers->translate( "This key refers to the <strong>update_user_meta()</strong> function of WordPress:", $translation_ident ); ?> <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/update_user_meta/">https://developer.wordpress.org/reference/functions/update_user_meta/</a><br>
		<?php echo WPWHPRO()->helpers->translate( "The example above shows you two entries for this function. The first one is the default set up thats used in most cases. Simply define the meta key and the meta value and the key will be updated if it does exist and if it does not exist, it will be created.", $translation_ident ); ?><br>
		<?php echo WPWHPRO()->helpers->translate( "The third argument, as seen in the second entry, allows you to check against a previous value before updating. That causes that the meta value will only be updated if the previous key fits to whats currently saved within the database. Default: ''", $translation_ident ); ?>
	</li>
	<li>
		<strong><?php echo WPWHPRO()->helpers->translate( "delete_user_meta", $translation_ident ); ?></strong>
		<pre>{
   "delete_user_meta":[
	  {
		"meta_key": "first_custom_key"
	  },
	  {
		"meta_key": "second_custom_key",
		"meta_value": "Target specific value"
	  }
	]
}</pre>
		<?php echo WPWHPRO()->helpers->translate( "This key refers to the <strong>delete_user_meta()</strong> function of WordPress:", $translation_ident ); ?> <a title="Go to WordPress" target="_blank" href="https://developer.wordpress.org/reference/functions/delete_user_meta/">https://developer.wordpress.org/reference/functions/delete_user_meta/</a><br>
		<?php echo WPWHPRO()->helpers->translate( "Within the example above, you will see that only the meta key is required for deleting an entry. This will cause all meta keys on this user with the same key to be deleted.", $translation_ident ); ?><br>
		<?php echo WPWHPRO()->helpers->translate( "The second argument allows you to target only a specific meta key/value combination. This gets important if you want to target a specific meta key/value combination and not delete all available entries for the given user. Default: ''", $translation_ident ); ?>
	</li>
</ol>
<strong><?php echo WPWHPRO()->helpers->translate( "Some tipps:", $translation_ident ); ?></strong>
<ol>
	<li><?php echo WPWHPRO()->helpers->translate( "You can include the value for this argument as a simple string to your webhook payload or you integrate it directly as JSON into your JSON payload (if you send a raw JSON response).", $translation_ident ); ?></li>
	<li><?php echo WPWHPRO()->helpers->translate( "Changing the order of the functions within the JSON causes the user meta to behave differently. If you, for example, add the <strong>delete_user_meta</strong> key before the <strong>update_user_meta</strong> key, the meta values will first be deleted and then added/updated.", $translation_ident ); ?></li>
	<li><?php echo WPWHPRO()->helpers->translate( "The webhook response contains a validted array that shows each initialized meta entry, as well as the response from its original WordPress function. This way you can see if the meta value was adjusted accordingly.", $translation_ident ); ?></li>
</ol>
		<?php
		$parameter['manage_meta_data']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The do_action argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the wp_manage_user_meta_data action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $manage_meta_data, $return_args, $meta_update ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$manage_meta_data</strong> (String)<br>
		<?php echo WPWHPRO()->helpers->translate( "The WP data that was sent by the webhook caller.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "An array containing the information we will send back as the response to the initial webhook caller.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$meta_update</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "An array containing further data about the simplified meta data that should be added/updated.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

			$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
				'webhook_name' => 'Manage WP user meta',
				'webhook_slug' => 'wp_manage_user_meta_data',
				'steps' => array(
					WPWHPRO()->helpers->translate( "You also need to set the <strong>user_id</strong> argument. Please set it to either the user ID or the user email of the user you want to adjust the metadata for.", $translation_ident ),
					WPWHPRO()->helpers->translate( "The third argument you need to set is <strong>manage_meta_data</strong>. It accepts a JSON formatted string or array as seen within the details section of the argument.", $translation_ident ),
				),
				'tipps' => array(
					WPWHPRO()->helpers->translate( "To create user meta values visually, you can use our meta value generator at: <a title=\"Visit our meta value generator\" href=\"https://wp-webhooks.com/blog/how-to-update-custom-post-meta-values-with-wp-webhooks/\" target=\"_blank\">https://wp-webhooks.com/blog/how-to-update-custom-post-meta-values-with-wp-webhooks/</a>.", $translation_ident ),
				)
			) );

			return array(
				'action'			=> 'wp_manage_user_meta_data',
				'name'			  => WPWHPRO()->helpers->translate( 'Manage WP user meta', $translation_ident ),
				'sentence'			  => WPWHPRO()->helpers->translate( 'add, update, or delete WP user meta', $translation_ident ),
				'parameter'		 => $parameter,
				'returns'		   => $returns,
				'returns_code'	  => $returns_code,
				'short_description' => WPWHPRO()->helpers->translate( 'Add, update, or delete custom user meta data within "WordPress".', $translation_ident ),
				'description'	   => $description,
				'integration'	   => 'wordpress',
				'premium' 			=> true,
			);

		}

		public function execute( $return_data, $response_body ){

			$translation_ident = "action-wp_manage_user_meta_data-execute";
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(),
			);
	
			$user_id = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' );
			$meta_update = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'meta_update' );
			$manage_meta_data = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'manage_meta_data' );
			$do_action = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $user_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the user_id argument first.", $translation_ident );
				return $return_args;
			}

			if( is_numeric( $user_id ) ){
				$user_id = intval( $user_id );
			} elseif( is_email( $user_id ) ){
				$user = get_user_by( 'email', $user_id );
				if( ! empty( $user ) ){
					$user_id = $user->ID;
				}
			}

			if( empty( $user_id ) || ! is_numeric( $user_id ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The user you try to update does not exist.", $translation_ident );
				return $return_args;
			}

			if( empty( $meta_update ) && empty( $manage_meta_data ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set either the manage_meta_data or the meta_update argument.", $translation_ident );
				return $return_args;
			}

			$user_helpers = WPWHPRO()->integrations->get_helper( 'wordpress', 'user_helpers' );
			
			if( ! empty( $meta_update ) ){
				$manage_meta_data = $user_helpers->merge_repeater_meta_data( $manage_meta_data, $meta_update );
			}
			
			$return_args = $user_helpers->manage_user_meta_data( $user_id, $manage_meta_data );

			if( ! empty( $do_action ) ){
				do_action( $do_action, $manage_meta_data, $return_args, $meta_update );
			}
	
			return $return_args;
	
		}

	}

endif; // End if class_exists check.