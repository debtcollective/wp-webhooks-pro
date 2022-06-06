<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_kadence_blocks_Triggers_kdbl_submit' ) ) :

 /**
  * Load the kdbl_submit trigger
  *
  * @since 4.3.4
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_kadence_blocks_Triggers_kdbl_submit {

	public function get_callbacks(){

		return array(
			array(
				'type' => 'filter',
				'hook' => 'kadence_blocks_form_submission_success',
				'callback' => array( $this, 'kdbl_submit_callback' ),
				'priority' => 999,
				'arguments' => 5,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-kdbl_submit-description";

		$parameter = array(
			'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The id of user who submitted the form (In case given).', $translation_ident ) ),
			'form_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(String) The form id of the submitted form.', $translation_ident ) ),
			'form_fields' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) All of the form fields that have been submitted.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Form submitted',
			'webhook_slug' => 'kdbl_submit',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'kadence_blocks_form_submission_success',
				),
			),
			'tipps' => array(
				WPWHPRO()->helpers->translate( 'You can fire this trigger as well on specific statuses only. To do that, simply specify the status slugs within the webhook URL settings.', $translation_ident ),
			)
		) );

		$settings = array(
			'load_default_settings' => true,
			'data' => array(
				'wpwhpro_kadence_blocks_trigger_on_selected_forms' => array(
					'id'		  => 'wpwhpro_kadence_blocks_trigger_on_selected_forms',
					'type'		=> 'text',
					'multiple'	=> true,
					'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected forms', $translation_ident ),
					'placeholder' => '',
					'required'	=> false,
					'description' => WPWHPRO()->helpers->translate( 'Add your forms using the form id (You will find the form id of a form by inspecting it within the frontend. You will find it by searching for "kadence-form-" within the frontend code - the text after it is the id which looks something like: _39ce56-48). You can also add multiple ones separated by comma. If you don\'t add any, all are triggered.', $translation_ident )
				),
			)
		);

		return array(
			'trigger'		   => 'kdbl_submit',
			'name'			  => WPWHPRO()->helpers->translate( 'Form submitted', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a form was submitted', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a form was submitted within Kadence Blocks.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'kadence-blocks',
			'premium'		   => true,
		);

	}

	public function kdbl_submit_callback( $success, $form_args, $fields, $form_id, $post_id ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'kdbl_submit' );
		$response_data_array = array();

		$formatted_fields = array();
		if( ! empty( $fields ) && is_array( $fields ) ){
			foreach( $fields as $field_key => $field_value ){
				if( isset( $field_value['label'] ) ){
					$field_key = sanitize_title( $field_value['label'] );

					if( ! isset( $formatted_fields[ $field_key ] ) ){
						$formatted_fields[ $field_key ] = $field_value['value'];
					} else {
						$counter = 1;
						$is_set = false;
						while( ! $is_set ){

							if( $counter > 100 ){
								break;
							}

							$new_key = $field_key . '_' . $counter;

							if( ! isset( $formatted_fields[ $new_key ] ) ){
								$formatted_fields[ $new_key ] = $field_value['label'];
								break;
							}

							$counter++;

						}
					}
					
				}
			}
		}

		$payload = array(
			'success' => $success,
			'form_id' => $form_id,
			'post_id' => $post_id,
			'fields' => $fields,
			'fields_formatted' => $formatted_fields,
			'form_args' => $form_args,
		);

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( isset( $webhook['settings'] ) ){
				foreach( $webhook['settings'] as $settings_name => $settings_data ){
	  
				  if( $settings_name === 'wpwhpro_kadence_blocks_trigger_on_selected_forms' && ! empty( $settings_data ) ){
					$is_valid = false;

					$settings_data = explode( ',', $settings_data );
					if( ! is_array( $settings_data ) ){
						$settings_data = array( $settings_data );
					}
					
					if( in_array( $form_id, $settings_data ) ){
					  $is_valid = true;
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

		do_action( 'wpwhpro/webhooks/trigger_kdbl_submit', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array(
			"success" => true,
			"form_id" => "_39ce56-48",
			"post_id" => "9114",
			"fields" => array(
				array(
					"type" => "text",
					"label" => "Name",
					"value" => "Jon Doe"
				),
				array(
					"type" => "email",
					"label" => "Email",
					"value" => "jondoe@demo.test"
				),
				array(
					"type" => "textarea",
					"label" => "Message",
					"value" => "This is a test message."
				),
			),
			"fields_formatted" => array(
				"name" => "Jon Doe",
				"email" => "jondoe@demo.test",
				"message" => "This is a test message.",
			),
			"form_args" => array(
				"uniqueID" => "_39ce56-48",
				"postID" => "9114",
				"fields" => array(
					array(
						"label" => "Name",
						"showLabel" => true,
						"placeholder" => "",
						"default" => "",
						"description" => "",
						"rows" => 4,
						"options" => array(
							array(
								"value" => "",
								"label" => ""
							)
						),
						"multiSelect" => false,
						"inline" => false,
						"showLink" => false,
						"min" => "",
						"max" => "",
						"type" => "text",
						"required" => false,
						"width" => array(
							"100",
							"",
							""
						),
						"auto" => "",
						"errorMessage" => "",
						"requiredMessage" => "",
						"slug" => "",
						"ariaLabel" => ""
					),
					array(
						"label" => "Email",
						"showLabel" => true,
						"placeholder" => "",
						"default" => "",
						"description" => "",
						"rows" => 4,
						"options" => array(
							array(
								"value" => "",
								"label" => ""
							)
						),
						"multiSelect" => false,
						"inline" => false,
						"showLink" => false,
						"min" => "",
						"max" => "",
						"type" => "email",
						"required" => true,
						"width" => array(
							"100",
							"",
							""
						),
						"auto" => "",
						"errorMessage" => "",
						"requiredMessage" => "",
						"slug" => "",
						"ariaLabel" => ""
					),
					array(
						"label" => "Message",
						"showLabel" => true,
						"placeholder" => "",
						"default" => "",
						"description" => "",
						"rows" => 4,
						"options" => array(
							array(
								"value" => "",
								"label" => ""
							)
						),
						"multiSelect" => false,
						"inline" => false,
						"showLink" => false,
						"min" => "",
						"max" => "",
						"type" => "textarea",
						"required" => true,
						"width" => array(
							"100",
							"",
							""
						),
						"auto" => "",
						"errorMessage" => "",
						"requiredMessage" => "",
						"slug" => "",
						"ariaLabel" => ""
					),
				),
				"actions" => array(
					"email"
				),
				"email" => array(
					array(
						"emailTo" => "admin@yoursite.test",
						"subject" => "[yourtitle Submission]",
						"replyTo" => "email_field",
						"html" => true
					)
				)
			)
		);

		return $data;
	}

  }

endif; // End if class_exists check.