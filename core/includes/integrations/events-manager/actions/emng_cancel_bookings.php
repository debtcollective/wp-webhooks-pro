<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_events_manager_Actions_emng_cancel_bookings' ) ) :

	/**
	 * Load the emng_cancel_bookings action
	 *
	 * @since 4.3.6
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_events_manager_Actions_emng_cancel_bookings {

	public function get_details(){

		$translation_ident = "action-emng_cancel_bookings-content";

			$parameter = array(
				'user'		=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Set this argument to the id of the user. In case you do not have the user id, you can also assign the user via a given email.', $translation_ident ) ),
				'event_ids'	=> array( 'required' => true, 'short_description' => WPWHPRO()->helpers->translate( 'Add the event ids you want to adjust the user bookings for. This argument accepts a comma-separated string, as well as a JSON construct.', $translation_ident ) ),
				'prevent_emails'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this argument to yes if you want to prevent the status email to be sent. Default: no', $translation_ident ) ),
				'ignore_spaces'	=> array( 'short_description' => WPWHPRO()->helpers->translate( 'Set this argument to yes if you want to ignore the available slots/spaces for the given event. Default: no', $translation_ident ) ),
				'do_action'	  => array( 'short_description' => WPWHPRO()->helpers->translate( 'Advanced: Register a custom action after Webhooks Pro fires this webhook. More infos are in the description.', $translation_ident ) )
			);

			$returns = array(
				'success'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(Bool) True if the action was successful, false if not. E.g. array( \'success\' => true )', $translation_ident ) ),
				'data'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(array) Further data about the fired actions.', $translation_ident ) ),
				'msg'		=> array( 'short_description' => WPWHPRO()->helpers->translate( '(string) A message with more information about the current request. E.g. array( \'msg\' => "This action was successful." )', $translation_ident ) ),
			);

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "In case you want to add multiple event IDs, you can either comma-separate them like <code>2,3,12,44</code>, or you can add them via a JSON construct:", $translation_ident ); ?>
<pre>{
  23,
  3,
  44
}</pre>
<?php echo WPWHPRO()->helpers->translate( "You can also target all bookings of the user, regardless of the event. To do that, simply set the field to:", $translation_ident ); ?>
<pre>all</pre>
		<?php
		$parameter['event_ids']['description'] = ob_get_clean();

			ob_start();
		?>
<?php echo WPWHPRO()->helpers->translate( "The <strong>do_action</strong> argument is an advanced webhook for developers. It allows you to fire a custom WordPress hook after the <strong>emng_cancel_bookings</strong> action was fired.", $translation_ident ); ?>
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
			'msg' => 'The bookings have been cancelled.',
			'data' => 
			array (
			  'user_id' => 148,
			  'events' => 
			  array (
				0 => 1,
			  ),
			),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'action', array(
			'webhook_name' => 'Add tags',
			'webhook_slug' => 'emng_cancel_bookings',
			'steps' => array(
				WPWHPRO()->helpers->translate( 'It is also required to set the <strong>user</strong> argument. Please set it to the user id or user email.', $translation_ident ),
				WPWHPRO()->helpers->translate( 'Please also set the <strong>event_ids</strong> argument. This argument accepts a comma-separated list of event ids, as well as a JSON with each id on a separate line. Please see the argument definition for further information.', $translation_ident ),
			),
		) );

		return array(
			'action'			=> 'emng_cancel_bookings', //required
			'name'			   => WPWHPRO()->helpers->translate( 'Cancel bookings', $translation_ident ),
			'sentence'			   => WPWHPRO()->helpers->translate( 'cancel one or multiple bookings', $translation_ident ),
			'parameter'		 => $parameter,
			'returns'		   => $returns,
			'returns_code'	  => $returns_code,
			'short_description' => WPWHPRO()->helpers->translate( 'Cancel one, multiple, or all bookings for a user within Events Manager.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'events-manager',
			'premium'	   	=> true,
		);


		}

		public function execute( $return_data, $response_body ){

			$return_args = array(
				'success' => false,
				'msg' => '',
				'data' => array(
					'user_id' => 0,
					'events' => '',
				)
			);

			$user		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'user' );
			$event_ids		= WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'event_ids' );
			$prevent_emails		= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'prevent_emails' ) === 'yes' ) ? true : false;
			$ignore_spaces		= ( WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'ignore_spaces' ) === 'yes' ) ? true : false;
			$do_action	  = WPWHPRO()->helpers->validate_request_value( $response_body['content'], 'do_action' );

			if( empty( $user ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the user argument to either the user id or user email.", 'action-emng_cancel_bookings-error' );
				return $return_args;
			}

			if( empty( $event_ids ) ){
				$return_args['msg'] = WPWHPRO()->helpers->translate( "Please set the event_ids argument.", 'action-emng_cancel_bookings-error' );
				return $return_args;
			}

			$trigger_all_events = ( is_string( $event_ids ) && $event_ids === 'all' ) ? true : false;
			$validated_events = array();

			if( ! $trigger_all_events ){
				if( WPWHPRO()->helpers->is_json( $event_ids ) ){
					$validated_events = json_decode( $event_ids, true );
				} else {
					$validated_events = explode( ',', $event_ids );
				}
			}

            if( ! is_array( $validated_events ) && ! empty( $validated_events ) ){
                $validated_events = array( $validated_events );
            }

			foreach( $validated_events as $tk => $tv ){
				$validated_events[ $tk ] = intval( $tv );
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
                $return_args['msg'] = WPWHPRO()->helpers->translate( "We could not find a user for your given user data.", 'action-emng_cancel_bookings-error' );
				return $return_args;
            }

            $em_person = new EM_Person( array( 'user_id' => $user_id ) );

			if( ! empty( $em_person ) ){
				$em_bookings = $em_person->get_bookings( false );
				if( count( $em_bookings->bookings ) > 0 ){
					
					foreach( $em_bookings as $em_booking ){
						if( $trigger_all_events || in_array( $em_booking->event_id, $validated_events ) ){

							if( $trigger_all_events ){
								$validated_events[] = intval( $em_booking->event_id );
							}

							$send_email = ( ! $prevent_emails ) ? true : false;
							$ignore_spaces = ( $ignore_spaces ) ? true : false;
							$em_booking->set_status( 3, $send_email, $ignore_spaces );
						}
					}

					$return_args['success'] = true;
					$return_args['msg'] = WPWHPRO()->helpers->translate( "The bookings have been cancelled.", 'action-emng_cancel_bookings-success' );
					$return_args['data']['user_id'] = $user_id;
					$return_args['data']['events'] = $validated_events;

					if( ! empty( $do_action ) ){
						do_action( $do_action, $return_args );
					}
	
				} else {
					$return_args['msg'] = WPWHPRO()->helpers->translate( "The given user has no bookings.", 'action-emng_cancel_bookings-success' );
				}
			} else {
				$return_args['msg'] = WPWHPRO()->helpers->translate( "We could not find a booking person for the given user.", 'action-emng_cancel_bookings-success' );
			}

			return $return_args;
	
		}

	}

endif; // End if class_exists check.