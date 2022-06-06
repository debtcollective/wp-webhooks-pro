<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_divi_Triggers_divi_form_submit' ) ) :

 /**
  * Load the divi_form_submit trigger
  *
  * @since 4.3.6
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_divi_Triggers_divi_form_submit {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'et_pb_contact_form_submit',
				'callback' => array( $this, 'divi_form_submit_callback' ),
				'priority' => 20,
				'arguments' => 3,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-divi_form_submit-description";

		$parameter = array(
			'form_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The form id of the submitted form.', $translation_ident ) ),
			'submission' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The data submitted with the form.', $translation_ident ) ),
			'errors' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) Any kind of errors that appeared during the form submission.', $translation_ident ) ),
			'form_info' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) All of the form fields that have been submitted.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Form submitted',
			'webhook_slug' => 'divi_form_submit',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'et_pb_contact_form_submit',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'You can fire this trigger as well on specific forms only. To do that, simply specify the form ids within the webhook URL settings.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_divi_trigger_on_selected_forms' => array(
					'id'		  => 'wpwhpro_divi_trigger_on_selected_forms',
					'type'		=> 'text',
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected forms', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Set the IDs of the forms you would like to fire this trigger on. Comma-separate them in case you want to add multiple ones. If none are set, all are triggered. To get the ID for your form, please head on the frontend page of your form, right-click it, and search for data-form_unique_id - this field contains an ID, which is your unique form id.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'divi_form_submit',
			'name'			  => WPWHPRO()->helpers->translate( 'Form submitted', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a form was submitted', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a form was submitted within Divi.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'divi',
			'premium'		   => false,
		);

	}

	/**
	 * Fires after contact form is submitted.
	 *
	 * Use $et_contact_error variable to check whether there is an error on the form
	 * entry submit process or not.
	 *
	 * @param array $processed_fields_values Processed fields values.
	 * @param array $et_contact_error        Whether there is an error on the form
	 *                                       entry submit process or not.
	 * @param array $contact_form_info       Additional contact form info.
	 */
	public function divi_form_submit_callback( $processed_fields_values, $et_contact_error, $contact_form_info ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'divi_form_submit' );
		$response_data_array = array();

		$payload = array(
			'form_id' => ( isset( $contact_form_info['contact_form_unique_id'] ) ) ? $contact_form_info['contact_form_unique_id'] : 0,
			'submission' => $processed_fields_values,
			'errors' => $et_contact_error,
			'form_info' => $contact_form_info,
		);

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){
	  
				  if( $settings_name === 'wpwhpro_divi_trigger_on_selected_forms' && ! empty( $settings_data ) ){

					$forms = array_map( 'trim', explode( ',', $settings_data ) );

					if( is_array( $forms ) && ! in_array( $payload['form_id'], $forms ) ){
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

		do_action( 'wpwhpro/webhooks/trigger_divi_form_submit', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'form_id' => 'a556daba-4757-48a4-8f83-229842ada23d',
			'submission' => 
			array (
			  'name' => 
			  array (
				'value' => 'Jon Doe',
				'label' => 'Name',
			  ),
			  'email' => 
			  array (
				'value' => 'demo@email.test',
				'label' => 'Email Address',
			  ),
			  'message' => 
			  array (
				'value' => 'This is a demo message.',
				'label' => 'Message',
			  ),
			),
			'errors' => false,
			'form_info' => 
			array (
			  'contact_form_id' => 'et_pb_contact_form_1',
			  'contact_form_number' => 0,
			  'contact_form_unique_id' => 'a556daba-4757-48a4-8f83-229842ada23d',
			  'module_slug' => 'et_pb_contact_form',
			  'post_id' => 9107,
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.