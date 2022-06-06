<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_crm_Actions_fcrm_get_contact' ) ) :

	/**
	 * Load the fcrm_get_contact action
	 *
	 * @since 4.3.5
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_fluent_crm_Actions_fcrm_get_contact {

	public function get_details(){

		$translation_ident = "action-fcrm_get_contact-content";

			$parameter = array(
				'contact_value'			=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Set this field to either the contact email or contact id.', $translation_ident ) ),
				'value_type'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'If you want to fetch the contact by a given user id, set this field do user_id. Default: default', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data about the fired triggers.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>fcrm_get_contact</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $return_args, $contact_value, $contact, $value_type ){
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
		<?php echo WPWHPRO()->helpers->translate( "The value set with the contact_value argument.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$contact</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "Further data about the contact.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$value_type</strong> (string)<br>
		<?php echo WPWHPRO()->helpers->translate( "The value set with the value_type argument.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'The contact has been successfully retrieved.',
			'data' => 
			array (
			  'contact' => 
			  array (
				'id' => '3',
				'user_id' => NULL,
				'hash' => 'eb87ffdba30105af809xxxxxx64b5fc7',
				'contact_owner' => NULL,
				'company_id' => NULL,
				'prefix' => NULL,
				'first_name' => 'Demo',
				'last_name' => 'contact',
				'email' => 'demoemail@demo.test',
				'timezone' => NULL,
				'address_line_1' => '',
				'address_line_2' => '',
				'postal_code' => '',
				'city' => '',
				'state' => '',
				'country' => '',
				'ip' => NULL,
				'latitude' => NULL,
				'longitude' => NULL,
				'total_points' => '0',
				'life_time_value' => '0',
				'phone' => '',
				'status' => 'subscribed',
				'contact_type' => 'lead',
				'source' => NULL,
				'avatar' => NULL,
				'date_of_birth' => '0000-00-00',
				'created_at' => '2022-01-11 07:04:02',
				'last_activity' => NULL,
				'updated_at' => '2022-01-23 12:03:39',
				'photo' => 'https://www.gravatar.com/avatar/eb87ffdba301xxxxxxxxxx5fc7?s=128',
				'full_name' => 'Jon Doe',
			  ),
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Get contact',
			'webhook_slug' => 'fcrm_get_contact',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>contact_value</strong> argument. Please set it to either the contact email or contact id. Please note: You can also retrieve a contact based on the user id. to make that work, simply set the <strong>value_type</strong> argument to <strong>user_id</strong>.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'fcrm_get_contact', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Get contact', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'retrieve a contact', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Retrieve a contact within FluentCRM.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'fluent-crm',
			'premium'	   		=> true,
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'contact' => array()
				)
			);

			$contact_value		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'contact_value' );
			$value_type		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $contact_value ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set contact_value argument.", 'action-fcrm_get_contact-error' );
				return $return_args;
			}

			if( is_numeric( $contact_value ) ){
				$contact_value = intval( $contact_value );
			} else {
				$contact_value = sanitize_email( $contact_value );
			}

			if( empty( $value_type ) ){
				$value_type = 'default';
			}

			$contact_api = FluentCrmApi( 'contacts' );

			if( $value_type === 'user_id' ){
				$contact = $contact_api->getContactByUserRef( $contact_value );
			} else {
				$contact = $contact_api->getContact( $contact_value );
			}

			
			if( ! empty( $contact ) ){
				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The contact has been successfully retrieved.", 'action-fcrm_get_contact-success' );
				$return_args['data']['contact'] = $contact;
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Error: There was an issue retrieveing the contact.", 'action-fcrm_get_contact-error' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $contact_value, $contact, $value_type );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.