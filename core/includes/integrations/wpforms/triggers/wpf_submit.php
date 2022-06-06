<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_wpforms_Triggers_wpf_submit' ) ) :

 /**
  * Load the wpf_submit trigger
  *
  * @since 4.1.0
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_wpforms_Triggers_wpf_submit {

  public function get_callbacks(){

   return array(
	array(
		'type' => 'action',
		'hook' => 'wpforms_process_complete',
		'callback' => array( $this, 'ironikus_trigger_wpf_submit' ),
		'priority' => 20,
		'arguments' => 4,
		'delayed' => true,
	  ),
	);

  }

	public function get_details(){

	  $translation_ident = "trigger-wpf_submit-description";

	  $parameter = array(
		'form_id' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The id of the form that was currently submitted.', $translation_ident ) ),
		'entry_id' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The id of the current form submission.', $translation_ident ) ),
		'entry' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The full data that was submitted within the form.', $translation_ident ) ),
		'fields' => array( 'short_description' => WPWHPRO()->helpers->translate( 'The full form data, including field definitions, etc.', $translation_ident ) ),
	  );

	  	$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Form submitted',
			'webhook_slug' => 'wpf_submit',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'wpforms_process_complete',
					'url' => 'https://wpforms.com/developers/wpforms_process_complete/',
				),
			)
		) );

	  	$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_wpf_submit_trigger_on_forms' => array(
					'id'	 => 'wpwhpro_wpf_submit_trigger_on_forms',
					'type'	=> 'select',
					'multiple'  => true,
					'choices'   => array(),
					'query'			=> array(
						'filter'	=> 'posts',
						'args'		=> array(
							'post_type' 	=> 'wpforms',
							'orderby'       => 'id',
							'order'         => 'ASC',
						)
					),
					'label'	=> WPWHPRO()->helpers->translate( 'Trigger on selected forms', $translation_ident ),
					'placeholder' => '',
					'required'  => false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the forms you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
			)
		);

	  return array(
		'trigger'	  => 'wpf_submit',
		'name'	   => WPWHPRO()->helpers->translate( 'Form submitted', $translation_ident ),
		'sentence'	   => WPWHPRO()->helpers->translate( 'a form was submitted', $translation_ident ),
		'parameter'	 => $parameter,
		'settings'	 => $settings,
		'returns_code'   => $this->get_demo( array() ),
		'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires after a WPForms form submission.', $translation_ident ),
		'description'	=> $description,
		'callback'	 => 'test_wpf_submit',
		'integration'	=> 'wpforms',
		'premium'	=> true,
	  );

	}

	public function ironikus_trigger_wpf_submit( $fields, $entry, $form_data, $entry_id ){

	  $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wpf_submit' );
	  $form_id = ( is_array( $form_data ) && isset( $form_data['id'] ) ) ? $form_data['id'] : 0;
	  $data_array = array(
		'form_id' => $form_id,
		'entry_id' => $entry_id,
		'entry' => $entry,
		'fields' => $fields,
	  );
	  $response_data = array();

	  foreach( $webhooks as $webhook ){

		$is_valid = true;

		if( isset( $webhook['settings'] ) ){
		  foreach( $webhook['settings'] as $settings_name => $settings_data ){

			if( $settings_name === 'wpwhpro_wpf_submit_trigger_on_forms' && ! empty( $settings_data ) ){
			  if( ! in_array( $form_id, $settings_data ) ){
				$is_valid = false;
			  }
			}

		  }
		}

		if( $is_valid ) {
		  $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

		  if( $webhook_url_name !== null ){
			$response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
		  } else {
			$response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data_array );
		  }
		}
	  }

	  do_action( 'wpwhpro/webhooks/trigger_wpf_submit', $data_array, $response_data );
	}

	/*
	* Register the demo post delete trigger callback
	*
	* @since 1.2
	*/
	public function get_demo( $options = array() ) {

	  $data = array (
		'form_id' => '717',
		'entry_id' => 2,
		'entry' => 
		array (
		  'fields' => 
		  array (
			0 => 
			array (
			  'first' => 'Jon',
			  'last' => 'Doe',
			),
			1 => 'demo@email.test',
			2 => '(123) 456-7890',
			3 => 
			array (
			  'address1' => 'Demo  Street',
			  'address2' => '',
			  'city' => 'Demo City',
			  'state' => 'AL',
			  'postal' => '12345',
			),
			4 => '2',
			5 => '$ 20.00',
			6 => 'This is a demo message',
		  ),
		  'hp' => '',
		  'id' => '717',
		  'author' => '1',
		  'submit' => 'wpforms-submit',
		),
		'fields' => 
		array (
		  0 => 
		  array (
			'name' => 'Name',
			'value' => 'Jon Doe',
			'id' => 0,
			'type' => 'name',
			'first' => 'Jon',
			'middle' => '',
			'last' => 'Doe',
		  ),
		  1 => 
		  array (
			'name' => 'Email',
			'value' => 'demo@email.test',
			'id' => 1,
			'type' => 'email',
		  ),
		  2 => 
		  array (
			'name' => 'Phone',
			'value' => '(123) 456-7890',
			'id' => 2,
			'type' => 'phone',
		  ),
		  3 => 
		  array (
			'name' => 'Address',
			'value' => 'Demo  Street
	  Demo City, AL
	  12345',
			'id' => 3,
			'type' => 'address',
			'address1' => 'Demo Street',
			'address2' => '',
			'city' => 'Demo City',
			'state' => 'AL',
			'postal' => '12345',
			'country' => '',
		  ),
		  4 => 
		  array (
			'name' => 'Available Items',
			'value' => 'Second Item - &#36; 20.00',
			'value_choice' => 'Second Item',
			'value_raw' => '2',
			'amount' => '20.00',
			'amount_raw' => '20.00',
			'currency' => 'USD',
			'image' => '',
			'id' => 4,
			'type' => 'payment-multiple',
		  ),
		  5 => 
		  array (
			'name' => 'Total Amount',
			'value' => '&#36; 20.00',
			'amount' => '20.00',
			'amount_raw' => '20.00',
			'id' => 5,
			'type' => 'payment-total',
		  ),
		  6 => 
		  array (
			'name' => 'Comment or Message',
			'value' => 'This is a demo message',
			'id' => 6,
			'type' => 'textarea',
		  ),
		),
	  );

	  return $data;
	}

  }

endif; // End if class_exists check.