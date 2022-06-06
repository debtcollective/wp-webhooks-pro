<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_fluent_crm_Triggers_fcrm_contact_deleted' ) ) :

 /**
  * Load the fcrm_contact_deleted trigger
  *
  * @since 4.3.1
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_fluent_crm_Triggers_fcrm_contact_deleted {

  /**
   * Register the actual functionality of the webhook
   *
   * @param mixed $response
   * @param string $action
   * @param string $response_ident_value
   * @param string $response_api_key
   * @return mixed The response data for the webhook caller
   */
	public function get_callbacks(){

		return array(
			array(
				'type' => 'action',
				'hook' => 'fluentcrm_before_subscribers_deleted',
				'callback' => array( $this, 'prepare_fluentcrm_before_subscribers_deleted_callback' ),
				'priority' => 20,
				'arguments' => 1,
				'delayed' => false,
			),
			array(
				'type' => 'action',
				'hook' => 'fluentcrm_after_subscribers_deleted',
				'callback' => array( $this, 'fluentcrm_after_subscribers_deleted_callback' ),
				'priority' => 20,
				'arguments' => 1,
				'delayed' => true,
			),
		);
	}

	public function get_details(){

		$translation_ident = "action-fcrm_contact_deleted-description";

		$parameter = array(
			'contact_ids' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) All contact ids that have been deleted with this request.', $translation_ident ) ),
			'contacts' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) All details of the deleted contacts.', $translation_ident ) ),
		);

		$description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
			'webhook_name' => 'Contact deleted',
			'webhook_slug' => 'fcrm_contact_deleted',
			'post_delay' => true,
			'trigger_hooks' => array(
				array( 
					'hook' => 'fluentcrm_after_subscribers_deleted',
					'url' => 'https://fluentcrm.com/docs/action-hooks/',
				),
			),
		) );

		$settings = array();

		return array(
			'trigger'		   => 'fcrm_contact_deleted',
			'name'			  => WPWHPRO()->helpers->translate( 'Contact deleted', $translation_ident ),
			'sentence'			  => WPWHPRO()->helpers->translate( 'a contact was deleted', $translation_ident ),
			'parameter'		 => $parameter,
			'settings'		  => $settings,
			'returns_code'	  => $this->get_demo( array() ),
			'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a contact was deleted within FluentCRM.', $translation_ident ),
			'description'	   => $description,
			'integration'	   => 'fluent-crm',
			'premium'		   => true,
		);

	}

	/**
	 * Prepare values before deletion
	 *
	 * @param array $contact_ids A list of contact ids that will be deleted
	 */
	public function prepare_fluentcrm_before_subscribers_deleted_callback( $contact_ids ){

        if( empty( $contact_ids ) ){
            return;
        }

        $fcrm_helpers = WPWHPRO()->integrations->get_helper( 'fluent-crm', 'fcrm_helpers' );
		$contacts = $fcrm_helpers->get_contact( 'id', $contact_ids, false );

        $this->pre_action_values['fcrm_contact_deleted_contacts'] = $contacts;

    }

	/**
	 * Triggers once a contact was deleted within FluentCRM
	 *
	 * @param array $contact_ids A list of contact ids that have been deleted
	 */
	public function fluentcrm_after_subscribers_deleted_callback( $contact_ids ){

		$webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'fcrm_contact_deleted' );

		$payload = array(
			'contact_ids' => $contact_ids,
			'contacts' => isset( $this->pre_action_values['fcrm_contact_deleted_contacts'] ) ? $this->pre_action_values['fcrm_contact_deleted_contacts'] : array(),
		);

		$response_data_array = array();

		foreach( $webhooks as $webhook ){

			$webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
			$is_valid = true;

			if( $is_valid ){
				if( $webhook_url_name !== null ){
					$response_data_array[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				} else {
					$response_data_array[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $payload );
				}
			}

		}

		do_action( 'wpwhpro/webhooks/trigger_fcrm_contact_deleted', $payload, $response_data_array );
	}

	public function get_demo( $options = array() ) {

		$data = array (
            'contact_ids' => 
            array (
              0 => '2',
            ),
            'contacts' => 
            array (
              0 => 
              array (
                'id' => '2',
                'user_id' => NULL,
                'hash' => 'bffa0c582c9c84bb4d42b8XXXXXXXX',
                'contact_owner' => NULL,
                'company_id' => NULL,
                'prefix' => NULL,
                'first_name' => 'Jon',
                'last_name' => 'Doe',
                'email' => 'jon@doe.test',
                'timezone' => NULL,
                'address_line_1' => '',
                'address_line_2' => '',
                'postal_code' => '',
                'city' => '',
                'state' => '',
                'country' => '',
                'ip' => NULL,
                'latitude' => NULL,
                'longitude' => NULL,
                'total_points' => '0',
                'life_time_value' => '0',
                'phone' => '123456789',
                'status' => 'subscribed',
                'contact_type' => 'lead',
                'source' => NULL,
                'avatar' => NULL,
                'date_of_birth' => '0000-00-00',
                'created_at' => '2021-12-01 14:39:51',
                'last_activity' => NULL,
                'updated_at' => '2021-12-01 14:39:51',
                'photo' => 'https://www.gravatar.com/avatar/bffa0c582c9c84bb4d42b8d99ad46cf3?s=128',
                'full_name' => 'Jon Doe',
              ),
            ),
        );

		return $data;
	}

  }

endif; // End if class_exists check.