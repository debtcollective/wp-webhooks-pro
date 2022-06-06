<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_givewp_Actions_give_create_donor' ) ) :

	/**
	 * Load the give_create_donor action
	 *
	 * @since 4.3.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_givewp_Actions_give_create_donor {

	public function get_details(){

		$translation_ident = "action-give_create_donor-content";

			$parameter = array(
				'email'			=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Set the email for the donor.', $translation_ident ) ),
				'name'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set the donor name. In case you leave this field empty, we try to fetch the name from the first_name and last_name arguments.', $translation_ident ) ),
				'user_id'		=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The id of a WordPress user that you want to connect. If you leave it empty, we try to fetch the user from the given email.', $translation_ident ) ),
				'donor_company'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The company of the donor.', $translation_ident ) ),
				'first_name'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The first name of the donor.', $translation_ident ) ),
				'last_name'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'The last name of the donor.', $translation_ident ) ),
				'title_prefix'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'A title prefix for the donor name.', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data about the fired triggers.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>give_create_donor</strong> action was fired.", $translation_ident ); ?>
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
		<?php echo WPWHPRO()->helpers->translate( "All the values that are sent back as a response to the initial webhook action caller.", $translation_ident ); ?>
	</li>
</ol>
		<?php
		$parameter['do_action']['description'] = ob_get_clean();

		$returns_code = array (
			'success' => true,
			'msg' => 'The donor has been successfully created.',
			'data' => 
			array (
			  'donor_id' => '3',
			  'donor_data' => 
			  array (
				'email' => 'jondoe@democustomer.test',
				'name' => 'Jon Doe',
				'user_id' => 154,
			  ),
			  'donor_company' => 'Demo Corp',
			  'first_name' => 'Jon',
			  'last_name' => 'Doe',
			  'title_prefix' => 'Dr. Dr.',
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Create donor',
			'webhook_slug' => 'give_create_donor',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>email</strong> argument. Please set it to the donor email.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'give_create_donor', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Create donor', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'create a donor', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Create a donor within GiveWP.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'givewp',
            'premium'		   => true,
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array()
			);

			$name		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'name' );
			$email		= sanitize_email( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'email' ) );
			$user_id		= intval( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user_id' ) );
			$donor_company		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'donor_company' );
			$first_name		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'first_name' );
			$last_name		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'last_name' );
			$title_prefix		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'title_prefix' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $email ) || ! is_email( $email ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the email argument with a valid email address.", 'action-give_create_donor-error' );
				return $return_args;
			}

			$donor_data = array(
				'email' => $email,
			);

			if( ! empty( $name ) ){
				$donor_data['name'] = $name;
			} elseif( ! empty( $first_name ) || ! empty( $last_name ) ){
				$donor_data['name'] = $first_name . ' ' . $last_name;
				$donor_data['name'] = trim( $donor_data['name'], ' ' );
			}

            if( empty( $user_id ) ){
                $user_data = get_user_by( 'email', $email );
                if( ! empty( $user_data ) && isset( $user_data->ID ) && ! empty( $user_data->ID ) ){
                    $user_id = $user_data->ID;
                }
            }

            if( ! empty( $user_id ) ){
                $donor_data['user_id'] = $user_id;
            }

            $donor    = new Give_Donor();
			$donor_id = $donor->create( $donor_data );

			
			if( ! empty( $donor_id ) ){
				$return_args['data']['donor_id'] = $donor_id;
				$return_args['data']['donor_data'] = $donor_data;
				
				if( ! empty( $donor_company ) ){
					Give()->donor_meta->update_meta( $donor_id, '_give_donor_company', $donor_company );
					$return_args['data']['donor_company'] = $donor_company;
				}
				
				if( ! empty( $first_name ) ){
					Give()->donor_meta->update_meta( $donor_id, '_give_donor_first_name', $first_name );
					$return_args['data']['first_name'] = $first_name;
				}
				
				if( ! empty( $last_name ) ){
					Give()->donor_meta->update_meta( $donor_id, '_give_donor_last_name', $last_name );
					$return_args['data']['last_name'] = $last_name;
				}
				
				if( ! empty( $title_prefix ) ){
					Give()->donor_meta->update_meta( $donor_id, '_give_donor_title_prefix', $title_prefix );
					$return_args['data']['title_prefix'] = $title_prefix;
				}

				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The donor has been successfully created.", 'action-give_create_donor-success' );
				
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Error: There was an issue creating the donor.", 'action-give_create_donor-error' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.