<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_jetengine_Triggers_jetengine_form_submit' ) ) :

 /**
  * Load the jetengine_form_submit trigger
  *
  * @since 4.3.7
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_jetengine_Triggers_jetengine_form_submit {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'jet-engine/forms/notifications/before-send',
				'callback' => array( $this, 'preregister_form_collectors' ),
				'priority' => 20,
				'arguments' => 1,
				'delayed' => false,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-jetengine_form_submit-description";

		$parameter = array(
			'form_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of the form that was submitted.', $translation_ident ) ),
			'form' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further data about the form.', $translation_ident ) ),
			'data' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The submitted data variables.', $translation_ident ) ),
			'email_data' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Further data about a submitted email.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Form submitted',
			'webhook_slug' => 'jetengine_form_submit',
			'post_delay' => false,
			'trigger_hooks' => array(
				array( 
					'hook' => 'jet-engine/forms/notifications/before-send',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'You can fire this trigger as well on specific statuses only. To do that, simply specify the status slugs within the webhook URL settings.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_ws_form_trigger_on_selected_forms' => array(
					'id'		  => 'wpwhpro_ws_form_trigger_on_selected_forms',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'posts',
						'args'		=> array(
							'post_type' => ( function_exists('jet_engine') && isset( jet_engine()->forms ) && method_exists( jet_engine()->forms, 'slug' ) ) ? jet_engine()->forms->slug() : 'jet-engine-booking',
						)
					),
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected forms', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the forms you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
				'wpwhpro_ws_form_trigger_on_selected_types' => array(
					'id'		  => 'wpwhpro_ws_form_trigger_on_selected_types',
					'type'		=> 'select',
					'multiple'	=> true,
					'choices'	  => array(),
					'query'			=> array(
						'filter'	=> 'helpers',
						'args'		=> array(
							'integration' => 'jetengine',
							'helper' => 'jetengine_helpers',
							'function' => 'get_query_types',
						)
					),
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected notification types', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the notification types you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'jetengine_form_submit',
			'name'			  => WPWHPRO()->helpers->translate( 'Form submitted', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a form was submitted', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a form was submitted within WS Form.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'jetengine',
			'premium'		   => false,
		);

	}

	public function preregister_form_collectors( $notification ){

		$jetengine_helpers = WPWHPRO()->integrations->get_helper( 'jetengine', 'jetengine_helpers' );
		$validated_types = $jetengine_helpers->get_notification_types();

		foreach( $validated_types as $single_type => $single_label ){
			add_filter( 'jet-engine/forms/booking/notification/' . $single_type, array( $this, 'jetengine_form_submit_callback' ), 500, 2 );
		}

	}

	public function jetengine_form_submit_callback( $notification, $notifications ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'jetengine_form_submit' );
		$response_data_array = array();
		$form_id = intval( $notifications->form );
		$form_data = isset( $notifications->data ) ? $notifications->data : array();
		$email_data = isset( $notifications->email_data ) ? $notifications->email_data : array();

		if( empty( $form_id ) ){
			return;
		}	

		$notification_type = isset( $notification['type'] ) ? $notification['type'] : '';
		$payload = array(
			'form_id' => $form_id,
			'form' => $notification,
			'data' => $form_data,
			'email_data' => $email_data,
		);

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){
		
					if( $settings_name === 'wpwhpro_ws_form_trigger_on_selected_forms' && ! empty( $settings_data ) ){
					if( ! in_array( $form_id, $settings_data ) ){
						$is_valid = false;
					}
					}
		
					if( $is_valid && $settings_name === 'wpwhpro_ws_form_trigger_on_selected_types' && ! empty( $settings_data ) ){
					if( ! in_array( $notification_type, $settings_data ) ){
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

		do_action( 'wpwhpro/webhooks/trigger_jetengine_form_submit', $notifications, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'form_id' => 9129,
			'form' => 
			array (
			  'type' => 'email',
			  'mail_to' => 'admin',
			  'hook_name' => '',
			  'custom_email' => '',
			  'from_field' => '',
			  'post_type' => '',
			  'fields_map' => 
			  array (
			  ),
			  'meta_fields_map' => 
			  array (
			  ),
			  'log_in' => '',
			  'email' => 
			  array (
				'content' => 'Hi admin!
		  
		  There are new order on your website.
		  
		  Order details:
		  - Post ID: %post_id%',
				'subject' => 'New order on website',
				'content_type' => 'text/html',
			  ),
			  'mailchimp' => 
			  array (
				'fields_map' => 
				array (
				),
				'data' => 
				array (
				),
			  ),
			  'activecampaign' => 
			  array (
				'fields_map' => 
				array (
				),
				'lists' => 
				array (
				),
			  ),
			  'getresponse' => 
			  array (
				'fields_map' => 
				array (
				),
				'data' => 
				array (
				),
			  ),
			  'default_meta' => 
			  array (
			  ),
			  'redirect_args' => 
			  array (
			  ),
			),
			'data' => 
			array (
			  'post_id' => '9130',
			  'Submit' => '',
			  'field_name' => 'demofield',
			  'last_name' => 'Doe',
			),
			'email_data' => 
			array (
			  'content' => 'Hi admin!
		  
		  There are new order on your website.
		  
		  Order details:
		  - Post ID: %post_id%',
			  'subject' => 'New order on website',
			  'content_type' => 'text/html',
			  'reply_email' => false,
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.