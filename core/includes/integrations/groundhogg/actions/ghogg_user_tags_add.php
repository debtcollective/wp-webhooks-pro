<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_groundhogg_Actions_ghogg_user_tags_add' ) ) :

	/**
	 * Load the ghogg_user_tags_add action
	 *
	 * @since 4.3.5
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_groundhogg_Actions_ghogg_user_tags_add {

	public function get_details(){

		$translation_ident = "action-ghogg_user_tags_add-content";

			$parameter = array(
				'contact_value'		=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Set this argument to either the contact id or the contact/user email. You can also set it to the user id if you set the value_type argument to user_id.', $translation_ident ) ),
				'tags'	=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Add the tags you want to add to the contact. This argument accepts a comma-separated string, as well as a JSON construct.', $translation_ident ) ),
				'value_type'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this argument to user_id to use the id of the user within the contact value argument. Default: default.', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data about the fired actions.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "In case you want to add multiple tags to the contact, you can either comma-separate them like <code>2,3,12,44</code>, or you can add them via a JSON construct:", $translation_ident ); ?>
<pre>{
  23,
  3,
  44
}</pre>
		<?php
		$parameter['tags']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>ghogg_user_tags_add</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 3 );
function my_custom_callback_function( $return_args, $contact_value, $value_type ){
	//run your custom logic in here
}
</pre>
<?php echo WPWHPRO()->helpers->translate( "Here's an explanation to each of the variables that are sent over within the custom function.", $translation_ident ); ?>
<ol>
	<li>
		<strong>$return_args</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "All the values that are sent back as a response to the initial webhook action caller.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$contact_value</strong> (string)<br>
		<?php echo WPWHPRO()->helpers->translate( "The value used to identify the contact.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$value_type</strong> (mixed)<br>
		<?php echo WPWHPRO()->helpers->translate( "Either string or bool. String if set to user_id.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'Tags have been added to the respective contact.',
			'data' => 
			array (
			  'user_id' => 1,
			  'tags' => 
			  array (
				0 => 12,
				1 => 4,
			  ),
			  'contact_id' => 1,
			),
		  );

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Add user tags',
			'webhook_slug' => 'ghogg_user_tags_add',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>contact_value</strong> argument. Please set it to the contact id or contact/user email of the contact you want to add the tags to.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Please also set the <strong>tags</strong> argument. This argument accepts a comma-separated list of tag ids, as well as a JSON with each id on a separate line. Please see the argument definition for further information.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'ghogg_user_tags_add', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Add user tags', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'add one or multiple tags to a user', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Add one or multiple tags to a user within Groundhogg.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'groundhogg',
			'premium'	   	=> true,
		);


		}

		public function execute( $return_data, $response_body ){

			$ghogg_helpers = WPWHPRO()->integrations->get_helper( 'groundhogg', 'ghogg_helpers' );
			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'user_id' => 0,
					'tags' => '',
				)
			);

			$contact_value		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'contact_value' );
			$value_type		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'value_type' );
			$tags		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tags' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $contact_value ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the contact_value argument.", 'action-ghogg_user_tags_add-error' );
				return $return_args;
			}

			if( empty( $tags ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the tags argument.", 'action-ghogg_user_tags_add-error' );
				return $return_args;
			}

			$validated_tags = array();
			if( WPWHPRO()->helpers->is_json( $tags ) ){
                $validated_tags = json_decode( $tags, true );
            } else {
				$validated_tags = explode( ',', $tags );
			}

            if( ! is_array( $validated_tags ) && ! empty( $validated_tags ) ){
                $validated_tags = array( $validated_tags );
            }

			foreach( $validated_tags as $tk => $tv ){
				$validated_tags[ $tk ] = intval( $tv );
			}

            if( $value_type === 'user_id' ){
				$contact = $ghogg_helpers->get_contact( $contact_value, true );
			} else {
				$contact = $ghogg_helpers->get_contact( $contact_value );
			}

			if( ! $contact->exists() ) {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The contact you try to update does not exist.", 'action-ghogg_user_tags_add-error' );
				return $return_args;
			}

			$contact->apply_tag( $validated_tags );
			
			$return_args['success'] = true;
			$return_args['msg'] = WPWHPRO()->helpers->translate( "Tags have been added to the respective contact.", 'action-ghogg_user_tags_add-success' );
			$return_args['data']['contact_id'] = $contact->get_id();
			$return_args['data']['user_id'] = $contact->get_user_id();
			$return_args['data']['tags'] = $validated_tags;

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $contact_value, $value_type );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.