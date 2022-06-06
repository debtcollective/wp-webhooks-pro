<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_events_manager_Triggers_emng_booking_pending' ) ) :

 /**
  * Load the emng_booking_pending trigger
  *
  * @since 4.3.6
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_events_manager_Triggers_emng_booking_pending {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'em_booking_set_status',
				'callback' => array( $this, 'emng_booking_pending_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-emng_booking_pending-description";

		$parameter = array(
			'event_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the event.', $translation_ident ) ),
			'booking' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The full booking data.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Booking pending',
			'webhook_slug' => 'emng_booking_pending',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'em_booking_set_status',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'You can fire this trigger as well on specific events only. To do that, simply specify the event id(s) within the webhook URL settings.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_ws_form_trigger_on_selected_events' => array(
					'id'		  => 'wpwhpro_ws_form_trigger_on_selected_events',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'posts',
						'args'		=> array(
							'post_type' => ( defined('EM_POST_TYPE_EVENT') ) ? EM_POST_TYPE_EVENT : 'event',
						)
					),
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected events', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the events you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'emng_booking_pending',
			'name'			  => WPWHPRO()->helpers->translate( 'Booking pending', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a booking was set to pending', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a booking was set to pending within Events Manager.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'events-manager',
			'premium'		   => true,
		);

	}

	public function emng_booking_pending_callback( $result, $booking ){

		if( empty( $booking ) || $booking->booking_status !== 0 || $booking->previous_status === $booking->booking_status ){
			return;
		}

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'emng_booking_pending' );
		$response_data_array = array();
		$payload = array(
			'event_id' => $booking->event_id,
			'booking' => $booking,
		);

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){
	  
				  if( $settings_name === 'wpwhpro_ws_form_trigger_on_selected_events' && ! empty( $settings_data ) ){
					if( ! in_array( $payload['event_id'], $settings_data ) ){
					  $is_valid = false;
					}
				  }
	  
				}
			}

			if( $is_valid ){
				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				} else {
					$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				}
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_emng_booking_pending', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'event_id' => '1',
			'booking' => 
			array (
			  'booking_id' => '4',
			  'event_id' => '1',
			  'person_id' => '1',
			  'booking_price' => '0.0000',
			  'booking_spaces' => '1',
			  'booking_comment' => 'nother',
			  'booking_tax_rate' => 0,
			  'booking_taxes' => NULL,
			  'booking_meta' => 
			  array (
			  ),
			  'fields' => 
			  array (
				'booking_id' => 
				array (
				  'name' => 'id',
				  'type' => '%d',
				),
				'event_id' => 
				array (
				  'name' => 'event_id',
				  'type' => '%d',
				),
				'person_id' => 
				array (
				  'name' => 'person_id',
				  'type' => '%d',
				),
				'booking_price' => 
				array (
				  'name' => 'price',
				  'type' => '%f',
				),
				'booking_spaces' => 
				array (
				  'name' => 'spaces',
				  'type' => '%d',
				),
				'booking_comment' => 
				array (
				  'name' => 'comment',
				  'type' => '%s',
				),
				'booking_status' => 
				array (
				  'name' => 'status',
				  'type' => '%d',
				),
				'booking_tax_rate' => 
				array (
				  'name' => 'tax_rate',
				  'type' => '%f',
				  'null' => 1,
				),
				'booking_taxes' => 
				array (
				  'name' => 'taxes',
				  'type' => '%f',
				  'null' => 1,
				),
				'booking_meta' => 
				array (
				  'name' => 'meta',
				  'type' => '%s',
				),
			  ),
			  'notes' => NULL,
			  'required_fields' => 
			  array (
				0 => 'booking_id',
				1 => 'event_id',
				2 => 'person_id',
				3 => 'booking_spaces',
			  ),
			  'feedback_message' => 'Booking pending.',
			  'errors' => 
			  array (
			  ),
			  'mails_sent' => 0,
			  'custom' => 
			  array (
			  ),
			  'previous_status' => '1',
			  'status_array' => 
			  array (
				0 => 'Pending',
				1 => 'Approved',
				2 => 'Rejected',
				3 => 'Cancelled',
				4 => 'Awaiting Online Payment',
				5 => 'Awaiting Payment',
			  ),
			  'tickets' => NULL,
			  'event' => 
			  array (
				'event_id' => 1,
				'post_id' => 1351,
				'event_parent' => NULL,
				'event_slug' => 'demo-event',
				'event_owner' => 1,
				'event_name' => 'Demo Event',
				'event_all_day' => NULL,
				'post_content' => '',
				'event_rsvp' => '1',
				'event_rsvp_spaces' => '0',
				'event_spaces' => '0',
				'event_private' => 0,
				'location_id' => 0,
				'event_location_type' => NULL,
				'recurrence_id' => NULL,
				'event_status' => 1,
				'blog_id' => '1',
				'group_id' => NULL,
				'event_language' => NULL,
				'event_translation' => 0,
				'event_attributes' => 
				array (
				  'project_name' => '',
				  'project_start' => '',
				  'project_target' => '',
				  'project_next_step' => '',
				  'percent_completed' => '0',
				  'project_completed_date' => '',
				  'project_description' => '',
				  'project_strategy' => '',
				  'project_comments' => '',
				  'project_trello_card_link' => '',
				  'project_partner' => '',
				  'project_client' => '',
				  'project_pid' => '',
				  'hours_partner' => '',
				  'project_priority' => '',
				  'project_tasks_0_task_item' => '',
				  'project_tasks_0_complete_item' => '0',
				  'project_tasks' => '1',
				),
				'recurrence' => 0,
				'recurrence_interval' => NULL,
				'recurrence_freq' => NULL,
				'recurrence_byday' => NULL,
				'recurrence_days' => 0,
				'recurrence_byweekno' => NULL,
				'recurrence_rsvp_days' => NULL,
				'event_owner_anonymous' => NULL,
				'event_owner_name' => NULL,
				'event_owner_email' => NULL,
				'fields' => 
				array (
				  'event_id' => 
				  array (
					'name' => 'id',
					'type' => '%d',
				  ),
				  'post_id' => 
				  array (
					'name' => 'post_id',
					'type' => '%d',
				  ),
				  'event_parent' => 
				  array (
					'type' => '%d',
					'null' => true,
				  ),
				  'event_slug' => 
				  array (
					'name' => 'slug',
					'type' => '%s',
					'null' => true,
				  ),
				  'event_owner' => 
				  array (
					'name' => 'owner',
					'type' => '%d',
					'null' => true,
				  ),
				  'event_name' => 
				  array (
					'name' => 'name',
					'type' => '%s',
					'null' => true,
				  ),
				  'event_timezone' => 
				  array (
					'type' => '%s',
					'null' => true,
				  ),
				  'event_start_time' => 
				  array (
					'name' => 'start_time',
					'type' => '%s',
					'null' => true,
				  ),
				  'event_end_time' => 
				  array (
					'name' => 'end_time',
					'type' => '%s',
					'null' => true,
				  ),
				  'event_start' => 
				  array (
					'type' => '%s',
					'null' => true,
				  ),
				  'event_end' => 
				  array (
					'type' => '%s',
					'null' => true,
				  ),
				  'event_all_day' => 
				  array (
					'name' => 'all_day',
					'type' => '%d',
					'null' => true,
				  ),
				  'event_start_date' => 
				  array (
					'name' => 'start_date',
					'type' => '%s',
					'null' => true,
				  ),
				  'event_end_date' => 
				  array (
					'name' => 'end_date',
					'type' => '%s',
					'null' => true,
				  ),
				  'post_content' => 
				  array (
					'name' => 'notes',
					'type' => '%s',
					'null' => true,
				  ),
				  'event_rsvp' => 
				  array (
					'name' => 'rsvp',
					'type' => '%d',
				  ),
				  'event_rsvp_date' => 
				  array (
					'name' => 'rsvp_date',
					'type' => '%s',
					'null' => true,
				  ),
				  'event_rsvp_time' => 
				  array (
					'name' => 'rsvp_time',
					'type' => '%s',
					'null' => true,
				  ),
				  'event_rsvp_spaces' => 
				  array (
					'name' => 'rsvp_spaces',
					'type' => '%d',
					'null' => true,
				  ),
				  'event_spaces' => 
				  array (
					'name' => 'spaces',
					'type' => '%d',
					'null' => true,
				  ),
				  'location_id' => 
				  array (
					'name' => 'location_id',
					'type' => '%d',
					'null' => true,
				  ),
				  'event_location_type' => 
				  array (
					'type' => '%s',
					'null' => true,
				  ),
				  'recurrence_id' => 
				  array (
					'name' => 'recurrence_id',
					'type' => '%d',
					'null' => true,
				  ),
				  'event_status' => 
				  array (
					'name' => 'status',
					'type' => '%d',
					'null' => true,
				  ),
				  'event_private' => 
				  array (
					'name' => 'status',
					'type' => '%d',
					'null' => true,
				  ),
				  'blog_id' => 
				  array (
					'name' => 'blog_id',
					'type' => '%d',
					'null' => true,
				  ),
				  'group_id' => 
				  array (
					'name' => 'group_id',
					'type' => '%d',
					'null' => true,
				  ),
				  'event_language' => 
				  array (
					'type' => '%s',
					'null' => true,
				  ),
				  'event_translation' => 
				  array (
					'type' => '%d',
				  ),
				  'recurrence' => 
				  array (
					'name' => 'recurrence',
					'type' => '%d',
					'null' => false,
				  ),
				  'recurrence_interval' => 
				  array (
					'name' => 'interval',
					'type' => '%d',
					'null' => true,
				  ),
				  'recurrence_freq' => 
				  array (
					'name' => 'freq',
					'type' => '%s',
					'null' => true,
				  ),
				  'recurrence_days' => 
				  array (
					'name' => 'days',
					'type' => '%d',
					'null' => true,
				  ),
				  'recurrence_byday' => 
				  array (
					'name' => 'byday',
					'type' => '%s',
					'null' => true,
				  ),
				  'recurrence_byweekno' => 
				  array (
					'name' => 'byweekno',
					'type' => '%d',
					'null' => true,
				  ),
				  'recurrence_rsvp_days' => 
				  array (
					'name' => 'recurrence_rsvp_days',
					'type' => '%d',
					'null' => true,
				  ),
				),
				'post_fields' => 
				array (
				  0 => 'event_slug',
				  1 => 'event_owner',
				  2 => 'event_name',
				  3 => 'event_private',
				  4 => 'event_status',
				  5 => 'event_attributes',
				  6 => 'post_id',
				  7 => 'post_content',
				),
				'recurrence_fields' => 
				array (
				  0 => 'recurrence',
				  1 => 'recurrence_interval',
				  2 => 'recurrence_freq',
				  3 => 'recurrence_days',
				  4 => 'recurrence_byday',
				  5 => 'recurrence_byweekno',
				  6 => 'recurrence_rsvp_days',
				),
				'image_url' => '',
				'location' => NULL,
				'event_location' => NULL,
				'event_location_deleted' => NULL,
				'bookings' => NULL,
				'contact' => NULL,
				'categories' => NULL,
				'tags' => NULL,
				'errors' => 
				array (
				),
				'feedback_message' => NULL,
				'warnings' => NULL,
				'required_fields' => 
				array (
				  0 => 'event_name',
				  1 => 'event_start_date',
				),
				'mime_types' => 
				array (
				  1 => 'gif',
				  2 => 'jpg',
				  3 => 'png',
				),
				'previous_status' => false,
				'recurring_reschedule' => false,
				'recurring_recreate_bookings' => NULL,
				'recurring_delete_bookings' => false,
				'just_added_event' => false,
				'ID' => 1351,
				'post_author' => '1',
				'post_date' => '2021-08-26 12:41:39',
				'post_date_gmt' => '2021-08-26 12:41:39',
				'post_title' => 'Demo Event',
				'post_excerpt' => '',
				'post_status' => 'publish',
				'comment_status' => 'open',
				'ping_status' => 'closed',
				'post_password' => '',
				'post_name' => 'demo-event',
				'to_ping' => '',
				'pinged' => '',
				'post_modified' => '2021-09-08 13:39:54',
				'post_modified_gmt' => '2021-09-08 13:39:54',
				'post_content_filtered' => '',
				'post_parent' => 0,
				'guid' => 'https://demodomain.test/?post_type=event&#038;p=1351',
				'menu_order' => 0,
				'post_type' => 'event',
				'post_mime_type' => '',
				'comment_count' => '0',
				'ancestors' => NULL,
				'filter' => 'raw',
				'id' => '1',
				'slug' => 'demo-event',
				'owner' => '1',
				'name' => 'Demo Event',
				'start_time' => '00:00:00',
				'end_time' => '00:00:00',
				'start_date' => '2022-08-31',
				'end_date' => '2022-09-30',
				'rsvp' => '1',
				'rsvp_date' => '2022-08-31',
				'rsvp_time' => '00:00:00',
				'status' => 1,
				'status_array' => 
				array (
				  0 => 'Pending',
				  1 => 'Approved',
				),
			  ),
			  'tickets_bookings' => NULL,
			  'manage_override' => NULL,
			  'mime_types' => 
			  array (
				1 => 'gif',
				2 => 'jpg',
				3 => 'png',
			  ),
			  'id' => '4',
			  'price' => '0.0000',
			  'spaces' => '1',
			  'comment' => 'nother',
			  'status' => '1',
			  'tax_rate' => '0.0000',
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.