<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_events_manager_Triggers_emng_booking_rejected' ) ) :

 /**
  * Load the emng_booking_rejected trigger
  *
  * @since 4.3.6
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_events_manager_Triggers_emng_booking_rejected {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'em_booking_set_status',
				'callback' => array( $this, 'emng_booking_rejected_callback' ),
				'priority' => 20,
				'arguments' => 2,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-emng_booking_rejected-description";

		$parameter = array(
			'event_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the event.', $translation_ident ) ),
			'booking' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The full booking data.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Booking rejected',
			'webhook_slug' => 'emng_booking_rejected',
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
			'trigger'		   => 'emng_booking_rejected',
			'name'			  => WPWHPRO()->helpers->translate( 'Booking rejected', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a booking was rejected', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a booking was rejected within Events Manager.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'events-manager',
			'premium'		   => true,
		);

	}

	public function emng_booking_rejected_callback( $result, $booking ){

		if( empty( $booking ) || $booking->booking_status !== 2 || $booking->previous_status === $booking->booking_status ){
			return;
		}

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'emng_booking_rejected' );
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

		do_action( 'wpwhpro/webhooks/trigger_emng_booking_rejected', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'event_id' => '1',
			'booking' => 
			array (
			  'booking_id' => '10',
			  'event_id' => '1',
			  'person_id' => '148',
			  'booking_price' => '0.0000',
			  'booking_spaces' => '1',
			  'booking_comment' => 'Some demo comment',
			  'booking_tax_rate' => 0,
			  'booking_taxes' => NULL,
			  'booking_meta' => 
			  array (
				'consent' => true,
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
			  'feedback_message' => 'Booking rejected.',
			  'errors' => 
			  array (
			  ),
			  'mails_sent' => 0,
			  'custom' => 
			  array (
			  ),
			  'previous_status' => '3',
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
			  'event' => NULL,
			  'tickets_bookings' => NULL,
			  'manage_override' => NULL,
			  'mime_types' => 
			  array (
				1 => 'gif',
				2 => 'jpg',
				3 => 'png',
			  ),
			  'id' => '10',
			  'price' => '0.0000',
			  'spaces' => '1',
			  'comment' => 'Some demo comment',
			  'status' => '3',
			  'tax_rate' => '0.0000',
			  'meta' => 
			  array (
				'consent' => true,
			  ),
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.