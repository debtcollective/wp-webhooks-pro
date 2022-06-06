<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_givewp_Actions_give_create_donor_note' ) ) :

	/**
	 * Load the give_create_donor_note action
	 *
	 * @since 4.3.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_givewp_Actions_give_create_donor_note {

	public function get_details(){

		$translation_ident = "action-give_create_donor_note-content";

			$parameter = array(
				'donor'			=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Set either the email for the donor or the user_id.', $translation_ident ) ),
				'donor_note'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set the note for the donor.', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after WP Webhooks fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data about the fired triggers.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>give_create_donor_note</strong> action was fired.", $translation_ident ); ?>
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
			'msg' => 'The donor note has been successfully created.',
			'data' => 
			array (
			  'donor' => 'jondoe@democustomer.test',
			  'donor_note' => 'This is a sample note for the given donor.',
			  'formatted_note' => 'January 24, 2022 06:00:49 - This is a sample note for the given donor.',
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Create donor note',
			'webhook_slug' => 'give_create_donor_note',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>donor</strong> argument. Please set it to either the donor email or the user id.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'give_create_donor_note', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Create donor note', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'create a donor note', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Create a donor note within GiveWP.', $translation_ident ),
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

			$donor		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'donor' );
			$donor_note		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'donor_note' );
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $donor ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the donor argument with either a valid email address or the user id.", 'action-give_create_donor_note-error' );
				return $return_args;
			}

			if( is_email( $donor ) ){
				$donor = sanitize_email( $donor );
				$donor_object = new Give_Donor( $donor, false );
			} else {
				$donor_object = new Give_Donor( $donor );
			}

            
			$formatted_note = $donor_object->add_note( $donor_note );

			
			if( ! empty( $formatted_note ) ){
				
				$return_args['success'] = true;
				$return_args['msg'] = WPWHPRO()->helpers->translate( "The donor note has been successfully created.", 'action-give_create_donor_note-success' );
				$return_args['data']['donor'] = $donor;
				$return_args['data']['donor_note'] = $donor_note;
				$return_args['data']['formatted_note'] = $formatted_note;
				
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Error: There was an issue creating the donor note.", 'action-give_create_donor_note-error' );
			}

			if( ! empty( $do_action ) ){
				do_action( $do_action, $return_args );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.