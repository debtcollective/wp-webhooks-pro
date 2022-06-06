<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_ws_form_Triggers_wsform_submit' ) ) :

 /**
  * Load the wsform_submit trigger
  *
  * @since 4.3.4
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_ws_form_Triggers_wsform_submit {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'wsf_submit_post_complete',
				'callback' => array( $this, 'wsform_submit_callback' ),
				'priority' => 20,
				'arguments' => 1,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-wsform_submit-description";

		$parameter = array(
			'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of user who submitted the form (In case given).', $translation_ident ) ),
			'form_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The form id of the submitted form.', $translation_ident ) ),
			'form_fields' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) All of the form fields that have been submitted.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Form submitted',
			'webhook_slug' => 'wsform_submit',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'wsf_submit_post_complete',
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
						'filter'	=> 'helpers',
						'args'		=> array(
							'integration' => 'ws-form',
							'helper' => 'wsform_helpers',
							'function' => 'get_query_forms',
						)
					),
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected forms', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Select only the forms you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'wsform_submit',
			'name'			  => WPWHPRO()->helpers->translate( 'Form submitted', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a form was submitted', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a form was submitted within WS Form.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'ws-form',
			'premium'		   => false,
		);

	}

	public function wsform_submit_callback( $ws_form_submit ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wsform_submit' );
		$response_data_array = array();

		$payload = array(
			'user_id' => isset( $ws_form_submit->user_id ) ? intval( $ws_form_submit->user_id ) : 0,
			'form_id' => isset( $ws_form_submit->form_id ) ? intval( $ws_form_submit->form_id ) : 0,
			'form_fields' => array(),
		);

		$form_fields = wsf_form_get_fields( $ws_form_submit->form_object );
        $field_types = wsf_config_get_field_types();

		foreach( $form_fields as $form_field ){

			if( ! isset( $form_field->type ) || ! isset( $field_types[ $form_field->type ] ) ){
				continue;
			}

			if( ! isset( $field_types[ $form_field->type ]['submit_save'] ) ) {
				continue;
			}
	
			$field_prefix = ( defined('WS_FORM_FIELD_PREFIX') ? WS_FORM_FIELD_PREFIX : 'field_' );
			$field_id = esc_html( $form_field->id );
			$field_name = $field_prefix . $field_id;
			$field_value = wsf_submit_get_value( $ws_form_submit, $field_name );
	
			$payload['form_fields'][ $field_name ] = $field_value;
	
		}

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){
	  
				  if( $settings_name === 'wpwhpro_ws_form_trigger_on_selected_forms' && ! empty( $settings_data ) ){
					if( ! in_array( $payload['form_id'], $settings_data ) ){
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

		do_action( 'wpwhpro/webhooks/trigger_wsform_submit', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
			'user_id' => 1,
			'form_id' => 1,
			'form_fields' => 
			array (
			  'field_1' => 'Jon',
			  'field_2' => 'Doe',
			  'field_3' => 'jondoe@demo.test',
			  'field_4' => '+123456789',
			  'field_5' => 'This is a demo message.',
			  'field_6' => 
			  array (
				0 => 'I consent to WEBSITE storing my submitted information so they can respond to my inquiry',
			  ),
			  'field_7' => '',
			),
		);

		return $data;
	}

  }

endif; // End if class_exists check.