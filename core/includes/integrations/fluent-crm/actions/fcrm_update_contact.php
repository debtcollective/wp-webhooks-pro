<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_crm_Actions_fcrm_update_contact' ) ) :

	/**
	 * Load the fcrm_update_contact action
	 *
	 * @since 4.3.5
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_fluent_crm_Actions_fcrm_update_contact {

	public function get_details(){

		$translation_ident = "action-fcrm_update_contact-content";
		$validated_statuses = array();
		$validated_countries = array();

		if( defined( 'FLUENTCRM' ) ){
			$fcrm_helpers = WPWHPRO()->integrations->get_helper( 'fluent-crm', 'fcrm_helpers' );
		
			$validated_statuses = $fcrm_helpers->get_statuses();
			$validated_countries = $fcrm_helpers->get_countries();
		}

		$parameter = array(
			'email'			=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Set this field to the email (or the user id) of the contact you want to update.', $translation_ident ) ),
			'name_prefix'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'A prefix for the contact name.', $translation_ident ) ),
			'first_name'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The first name of the contact.', $translation_ident ) ),
			'last_name'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The last name of the contact.', $translation_ident ) ),
			'full_name'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'A separate field for the full user name.', $translation_ident ) ),
			'address_line_1'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The first address line.', $translation_ident ) ),
			'address_line_2'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The second address line.', $translation_ident ) ),
			'city'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The city name.', $translation_ident ) ),
			'state'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The country state.', $translation_ident ) ),
			'postal_code'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The postal code.', $translation_ident ) ),
			'country'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The country code.', $translation_ident ) ),
			'ip'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The contact IP address.', $translation_ident ) ),
			'phone'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The phone number for the contact.', $translation_ident ) ),
			'source'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Where the subscriber was acquired from. Standard values are: wp_users or web', $translation_ident ) ),
			'date_of_birth'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The birthday of the contact.', $translation_ident ) ),
			'status'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The status of the contact.', $translation_ident ) ),
			'tags'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'A comma-separated list or JSON construct of the tags you want to assign to the contact.', $translation_ident ) ),
			'lists'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'A comma-separated list or JSON construct of the lists you want to assign to the contact.', $translation_ident ) ),
			'timezone'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The timezone of the contact. E.g.: UTC', $translation_ident ) ),
			'custom_values'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'A JSON construct containing further meta values for the contact.', $translation_ident ) ),
			'remove_tags'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'A comma-separated list or JSON construct of the tags you want to remove from the contact.', $translation_ident ) ),
			'remove_lists'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'A comma-separated list or JSON construct of the lists you want to remove from the contact.', $translation_ident ) ),
			'create_if_none'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this argument to yes to create the contact in case it does not exist yet. Default: no', $translation_ident ) ),
			'send_pending_mail'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this to "yes" to send a pending email to the contact in case the status is set to pending. Default: no', $translation_ident ) ),
			'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More infos are in the description.', $translation_ident ) )
		);

		$returns = array(
			'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
			'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data about the fired action.', $translation_ident ) ),
			'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
		);

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "To add a country, please add it via the given country code. Down below you will find a list with all country codes available within FluentCRM (Written in bold):", $translation_ident ); ?>
<ul>
	<?php foreach( $validated_countries as $country_slug => $country_name ){
		echo '<li><strong>' . esc_html( $country_slug ) . '</strong>: ' . esc_html( $country_name ) . '</li>';
	} ?>
</ul>
		<?php
		$parameter['country']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "Using this argument, you can customize the status of the contact. Down below, you will find a list of all available statuses. To use a specific status, please use the status slug (the bold value):", $translation_ident ); ?>
<ul>
	<?php foreach( $validated_statuses as $status_slug => $status_name ){
		echo '<li><strong>' . esc_html( $status_slug ) . '</strong>: ' . esc_html( $status_name ) . '</li>';
	} ?>
</ul>
		<?php
		$parameter['status']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "This argument allows you to add custom meta values to the contact, apartm from the data added with the other arguments. It accepts a JSON formatted string that contains each of your custom meta values and keys. Down below is an example that adds two custom meta values:", $translation_ident ); ?>
<pre>{
  "first_meta_key": "My custom data",
  "second_meta_key": "More custom data"
}</pre>
<?php echo WPWHPRO()->helpers->translate( "Please note: The values of this argument are not naturally shown within the Contact inside of FluentCRM.", $translation_ident ); ?>
		<?php
		$parameter['custom_values']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "With the tags argument, you can assign one or multiple tags to the contact. To do that, you have two options:", $translation_ident ); ?>
<ol>
	<li>
		<strong><?php echo WPWHPRO()->helpers->translate( "Comma-separated list", $translation_ident ); ?></strong>
		<p><?php echo WPWHPRO()->helpers->translate( "This is the simplest way to assign the tags. Simply add the id of the tags you want to add and separate them by a comma.", $translation_ident ); ?></p>
		<pre>12,3,44</pre>
	</li>
	<li>
		<strong><?php echo WPWHPRO()->helpers->translate( "JSON formatted string", $translation_ident ); ?></strong>
		<p><?php echo WPWHPRO()->helpers->translate( "You can also use a JSON formatted string or direct JSON data for this value. Here is an example to update multiple values:", $translation_ident ); ?></p>
		<pre>[
  123,
  12,
  44
]</pre>
	</li>
</ol>
		<?php
		$parameter['tags']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "With the lists argument, you can assign one or multiple lists to the contact. To do that, you have two options:", $translation_ident ); ?>
<ol>
	<li>
		<strong><?php echo WPWHPRO()->helpers->translate( "Comma-separated list", $translation_ident ); ?></strong>
		<p><?php echo WPWHPRO()->helpers->translate( "This is the simplest way to assign the lists. Simply add the id of the lists you want to add and separate them by a comma.", $translation_ident ); ?></p>
		<pre>12,3,44</pre>
	</li>
	<li>
		<strong><?php echo WPWHPRO()->helpers->translate( "JSON formatted string", $translation_ident ); ?></strong>
		<p><?php echo WPWHPRO()->helpers->translate( "You can also use a JSON formatted string or direct JSON data for this value. Here is an example to update multiple values:", $translation_ident ); ?></p>
		<pre>[
  123,
  12,
  44
]</pre>
	</li>
</ol>
		<?php
		$parameter['lists']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "With the tags argument, you can remove one or multiple tags from the contact. To do that, you have two options:", $translation_ident ); ?>
<ol>
	<li>
		<strong><?php echo WPWHPRO()->helpers->translate( "Comma-separated list", $translation_ident ); ?></strong>
		<p><?php echo WPWHPRO()->helpers->translate( "This is the simplest way to remove the tags. Simply add the id of the tags you want to remove and separate them by a comma.", $translation_ident ); ?></p>
		<pre>12,3,44</pre>
	</li>
	<li>
		<strong><?php echo WPWHPRO()->helpers->translate( "JSON formatted string", $translation_ident ); ?></strong>
		<p><?php echo WPWHPRO()->helpers->translate( "You can also use a JSON formatted string or direct JSON data for this value. Here is an example to update multiple values:", $translation_ident ); ?></p>
		<pre>[
  123,
  12,
  44
]</pre>
	</li>
</ol>
		<?php
		$parameter['remove_tags']['description'] = ob_get_clean();

		ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "With the lists argument, you can remove one or multiple lists from the contact. To do that, you have two options:", $translation_ident ); ?>
<ol>
	<li>
		<strong><?php echo WPWHPRO()->helpers->translate( "Comma-separated list", $translation_ident ); ?></strong>
		<p><?php echo WPWHPRO()->helpers->translate( "This is the simplest way to remove the lists. Simply add the id of the lists you want to remove and separate them by a comma.", $translation_ident ); ?></p>
		<pre>12,3,44</pre>
	</li>
	<li>
		<strong><?php echo WPWHPRO()->helpers->translate( "JSON formatted string", $translation_ident ); ?></strong>
		<p><?php echo WPWHPRO()->helpers->translate( "You can also use a JSON formatted string or direct JSON data for this value. Here is an example to update multiple values:", $translation_ident ); ?></p>
		<pre>[
  123,
  12,
  44
]</pre>
	</li>
</ol>
		<?php
		$parameter['remove_lists']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>fcrm_update_contact</strong> action was fired.", $translation_ident ); ?>
<br>
<?php echo WPWHPRO()->helpers->translate( "You can use it to trigger further logic after the webhook action. Here's an example:", $translation_ident ); ?>
<br>
<br>
<?php echo WPWHPRO()->helpers->translate( "Let's assume you set for the <strong>do_action</strong> parameter <strong>fire_this_function</strong>. In this case, we will trigger an action with the hook name <strong>fire_this_function</strong>. Here's how the code would look in this case:", $translation_ident ); ?>
<pre>add_action( 'fire_this_function', 'my_custom_callback_function', 20, 4 );
function my_custom_callback_function( $return_args, $contact, $contact_data, $send_pending_mail, $contact_exists ){
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
		<strong>$contact</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "Further data about the contact.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$contact_data</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "The validated data used to update (or create) the contact.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$send_pending_mail</strong> (bool)<br>
		<?php echo WPWHPRO()->helpers->translate( "True if the argument was set to yes, false if it was so to no.", $translation_ident ); ?>
	</li>
	<li>
		<strong>$contact_exists</strong> (array)<br>
		<?php echo WPWHPRO()->helpers->translate( "The contact data in case a contact was already given.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'The contact has been successfully updated.',
			'data' => 
			array (
			  'contact' => 
			  array (
				'id' => '6',
				'user_id' => NULL,
				'hash' => '9580eafd182f47axxxxxxx71e8',
				'contact_owner' => NULL,
				'company_id' => NULL,
				'prefix' => NULL,
				'first_name' => 'Jon',
				'last_name' => 'Doe',
				'email' => 'jondoe@yourdomain.test',
				'timezone' => NULL,
				'address_line_1' => NULL,
				'address_line_2' => NULL,
				'postal_code' => NULL,
				'city' => NULL,
				'state' => NULL,
				'country' => NULL,
				'ip' => NULL,
				'latitude' => NULL,
				'longitude' => NULL,
				'total_points' => '0',
				'life_time_value' => '0',
				'phone' => NULL,
				'status' => 'subscribed',
				'contact_type' => 'lead',
				'source' => NULL,
				'avatar' => NULL,
				'date_of_birth' => NULL,
				'created_at' => '2022-02-14 08:19:21',
				'last_activity' => NULL,
				'updated_at' => '2022-02-14 08:19:21',
				'photo' => 'https://www.gravatar.com/avatar/9580eafdxxxxxxx71e8?s=128',
				'full_name' => 'Jon Doe',
				'tags' => 
				array (
				  0 => 
				  array (
					'id' => '2',
					'title' => 'Demo Tag 2',
					'slug' => 'demo-tag-2',
					'description' => '',
					'created_at' => '2021-12-01 10:22:44',
					'updated_at' => '2021-12-01 10:22:44',
					'pivot' => 
					array (
					  'subscriber_id' => '6',
					  'object_id' => '2',
					  'object_type' => 'FluentCrm\\App\\Models\\Tag',
					  'created_at' => '2022-02-14 08:19:21',
					  'updated_at' => '2022-02-14 08:19:21',
					),
				  ),
				  1 => 
				  array (
					'id' => '3',
					'title' => 'Demo Tag 3',
					'slug' => 'demo-tag-3',
					'description' => '',
					'created_at' => '2021-12-01 10:22:54',
					'updated_at' => '2021-12-01 10:22:54',
					'pivot' => 
					array (
					  'subscriber_id' => '6',
					  'object_id' => '3',
					  'object_type' => 'FluentCrm\\App\\Models\\Tag',
					  'created_at' => '2022-02-14 08:19:21',
					  'updated_at' => '2022-02-14 08:19:21',
					),
				  ),
				),
				'lists' => 
				array (
				  0 => 
				  array (
					'id' => '2',
					'title' => 'Demo List 2',
					'slug' => 'demo-list-2',
					'description' => '',
					'is_public' => '0',
					'created_at' => '2021-12-01 09:10:00',
					'updated_at' => '2021-12-01 09:10:00',
					'pivot' => 
					array (
					  'subscriber_id' => '6',
					  'object_id' => '2',
					  'object_type' => 'FluentCrm\\App\\Models\\Lists',
					  'created_at' => '2022-02-14 08:19:21',
					  'updated_at' => '2022-02-14 08:19:21',
					),
				  ),
				  1 => 
				  array (
					'id' => '3',
					'title' => 'Demo List 3',
					'slug' => 'demo-list-3',
					'description' => '',
					'is_public' => '0',
					'created_at' => '2021-12-01 09:10:06',
					'updated_at' => '2021-12-01 09:10:06',
					'pivot' => 
					array (
					  'subscriber_id' => '6',
					  'object_id' => '3',
					  'object_type' => 'FluentCrm\\App\\Models\\Lists',
					  'created_at' => '2022-02-14 08:19:21',
					  'updated_at' => '2022-02-14 08:19:21',
					),
				  ),
				),
			  ),
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Update contact',
			'webhook_slug' => 'fcrm_update_contact',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>email</strong> argument to the email (or contact id) of the person you want to update (or create) as a contact.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'fcrm_update_contact', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Update contact', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'update a contact', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Update or create a contact within FluentCRM.', $translation_ident ),
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

			$email		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'email' );
			$name_prefix		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'name_prefix' );
			$first_name		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'first_name' );
			$last_name		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'last_name' );
			$full_name		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'full_name' );
			$address_line_1		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'address_line_1' );
			$address_line_2		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'address_line_2' );
			$city		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'city' );
			$state		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'state' );
			$postal_code		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'postal_code' );
			$country		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'country' );
			$ip		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'ip' );
			$phone		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'phone' );
			$source		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'source' );
			$date_of_birth		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'date_of_birth' );
			$status		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'status' );
			$remove_tags		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'remove_tags' );
			$remove_lists		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'remove_lists' );
			$tags		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'tags' );
			$lists		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'lists' );
			$timezone		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'timezone' );
			$custom_values		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'custom_values' );
			$create_if_none	= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'create_if_none' ) === 'yes' ) ? true : false;
			$send_pending_mail	= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'send_pending_mail' ) === 'yes' ) ? true : false;
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $email ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set email argument to the contact email.", 'action-fcrm_update_contact-error' );
				return $return_args;
			}

			$contact_api = FluentCrmApi( 'contacts' );
			$contact_exists = $contact_api->getContact( $email );

			if( empty( $contact_exists ) && empty( $create_if_none ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The contact was not updated as it does not exist. If you want to create it, set the create_if_none argument to yes.", 'action-fcrm_update_contact-error' );
				return $return_args;
			}

			//Make sure we fetch the email in case a contact id was given
			if( is_numeric( $email ) ){
				$email = isset( $contact_exists->email ) ? $contact_exists->email : false;
			}

			if( empty( $email ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "There was an issue while fetching the contact based on your given contact id.", 'action-fcrm_update_contact-error' );
				return $return_args;
			}

			$contact_data = array(
				'email' => $email,
			);

			if( ! empty( $name_prefix ) ){
				$contact_data['prefix'] = $name_prefix;
			}

			if( ! empty( $first_name ) ){
				$contact_data['first_name'] = $first_name;
			}

			if( ! empty( $last_name ) ){
				$contact_data['last_name'] = $last_name;
			}

			if( ! empty( $full_name ) ){
				$contact_data['full_name'] = $full_name;
			}

			if( ! empty( $address_line_1 ) ){
				$contact_data['address_line_1'] = $address_line_1;
			}

			if( ! empty( $address_line_2 ) ){
				$contact_data['address_line_2'] = $address_line_2;
			}

			if( ! empty( $city ) ){
				$contact_data['city'] = $city;
			}

			if( ! empty( $state ) ){
				$contact_data['state'] = $state;
			}

			if( ! empty( $country ) ){
				$contact_data['country'] = $country;
			}

			if( ! empty( $ip ) ){
				$contact_data['ip'] = $ip;
			}

			if( ! empty( $phone ) ){
				$contact_data['phone'] = $phone;
			}

			if( ! empty( $source ) ){
				$contact_data['source'] = $source;
			}

			if( ! empty( $date_of_birth ) ){
				$contact_data['date_of_birth'] = WPWHPRO()->helpers->get_formatted_date( $date_of_birth, 'Y-m-d' );
			}

			if( ! empty( $timezone ) ){
				$contact_data['timezone'] = $timezone;
			}

			if( ! empty( $status ) ){
				$contact_data['status'] = $status;
			}

			if( ! empty( $postal_code ) ){
				$contact_data['postal_code'] = $postal_code;
			}

			if( ! empty( $custom_values ) ){
				$contact_data['custom_values'] = WPWHPRO()->helpers->force_array( $custom_values );
			}

			if( ! empty( $tags ) ){

				if( WPWHPRO()->helpers->is_json( $tags ) ){
					$tags = json_decode( $tags, true );
				} elseif( is_array( $tags ) || is_object( $tags ) ){
					$tags = json_decode( json_encode( $tags ), true ); //streamline data
				} else {
					$tags = array_map( 'trim', explode( ',', $tags ) );
				}

				if( ! is_array( $tags ) ){
					$tags = array( $tags );
				}

				$contact_data['tags'] = $tags;
			}

			if( ! empty( $lists ) ){

				if( WPWHPRO()->helpers->is_json( $lists ) ){
					$lists = json_decode( $lists, true );
				} elseif( is_array( $lists ) || is_object( $lists ) ){
					$lists = json_decode( json_encode( $lists ), true ); //streamline data
				} else {
					$lists = array_map( 'trim', explode( ',', $lists ) );
				}

				if( ! is_array( $lists ) ){
					$lists = array( $lists );
				}

				$contact_data['lists'] = $lists;
			}
		
			$contact = $contact_api->createOrUpdate( $contact_data );
			
			if( ! empty( $contact ) ){

				// send a double opt-in email if the status is pending
				if( $send_pending_mail && $contact->status == 'pending' ){
					$contact->sendDoubleOptinEmail();
				}

				if( ! empty( $remove_tags ) ){
					if( WPWHPRO()->helpers->is_json( $remove_tags ) ){
						$remove_tags = json_decode( $remove_tags, true );
					} elseif( is_array( $remove_tags ) || is_object( $remove_tags ) ){
						$remove_tags = json_decode( json_encode( $remove_tags ), true ); //streamline data
					} else {
						$remove_tags = array_map( 'trim', explode( ',', $remove_tags ) );
					}
	
					if( ! is_array( $remove_tags ) ){
						$remove_tags = array( $remove_tags );
					}
	
					$contact->detachTags( $remove_tags );
				}

				if( ! empty( $remove_lists ) ){
					if( WPWHPRO()->helpers->is_json( $remove_lists ) ){
						$remove_lists = json_decode( $remove_lists, true );
					} elseif( is_array( $remove_lists ) || is_object( $remove_lists ) ){
						$remove_lists = json_decode( json_encode( $remove_lists ), true ); //streamline data
					} else {
						$remove_lists = array_map( 'trim', explode( ',', $remove_lists ) );
					}
	
					if( ! is_array( $remove_lists ) ){
						$remove_lists = array( $remove_lists );
					}
	
					$contact->detachLists( $remove_lists );
				}

				$return_args['success'] = true;

				if( $contact_exists ){
					$return_args['msg'] = WPWHPRO()->helpers->translate( "The contact has been successfully updated.", 'action-fcrm_update_contact-success' );
				} else {
					$return_args['msg'] = WPWHPRO()->helpers->translate( "The contact has been successfully created.", 'action-fcrm_update_contact-success' );
				}
				
				$return_args['data']['contact'] = $contact;
			} else {

				if( $contact_exists ){
					$return_args['msg'] = WPWHPRO()->helpers->translate( "Error: There was an issue updating the contact.", 'action-fcrm_update_contact-error' );
				} else {
					$return_args['msg'] = WPWHPRO()->helpers->translate( "Error: There was an issue creating the contact.", 'action-fcrm_update_contact-error' );
				}
				
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args, $contact, $contact_data, $send_pending_mail, $contact_exists );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.