<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WP_Webhooks_Integrations_paid_memberships_pro_Triggers_pmpro_order_deleted' ) ) :

 /**
  * Load the pmpro_order_deleted trigger
  *
  * @since 4.2.2
  * @author Ironikus <info@ironikus.com>
  */
  class WP_Webhooks_Integrations_paid_memberships_pro_Triggers_pmpro_order_deleted {

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
                'hook' => 'pmpro_delete_order',
                'callback' => array( $this, 'pmpro_delete_order_callback' ),
                'priority' => 20,
                'arguments' => 2,
                'delayed' => true,
            ),
        );
    }

    public function get_details(){

        $translation_ident = "action-pmpro_order_deleted-description";

        $parameter = array(
            'user_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the user of which the order was deleted.', $translation_ident ) ),
            'membership_id' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Integer) The ID of the membership level.', $translation_ident ) ),
            'order' => array( 'short_description' => WPWHPRO()->helpers->translate( '(Array) The full order data of the deleted order.', $translation_ident ) ),
        );

        $description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
            'webhook_name' => 'Order deleted',
            'webhook_slug' => 'pmpro_order_deleted',
            'post_delay' => true,
            'trigger_hooks' => array(
                array( 
                    'hook' => 'pmpro_delete_order',
                    'url' => 'https://www.paidmembershipspro.com/hook/pmpro_delete_order/',
                ),
            )
        ) );

        $settings = array(
            'load_default_settings' => true,
            'data' => array(
                'wpwhpro_pmpro_trigger_on_membership_level' => array(
                    'id'		  => 'wpwhpro_pmpro_trigger_on_membership_level',
                    'type'		=> 'select',
                    'multiple'	=> true,
                    'choices'	  => array(),
                    'query'			=> array(
                        'filter'	=> 'helpers',
                        'args'		=> array(
                            'integration' => 'paid-memberships-pro',
                            'helper' => 'pmpro_helpers',
                            'function' => 'get_query_levels',
                        )
                    ),
                    'label'	   => WPWHPRO()->helpers->translate( 'Trigger on selected membership level', $translation_ident ),
                    'placeholder' => '',
                    'required'	=> false,
                    'description' => WPWHPRO()->helpers->translate( 'Select only the membership levels you want to fire the trigger on. You can also choose multiple ones. If none is selected, all are triggered.', $translation_ident )
                ),
            )
        );

        return array(
            'trigger'           => 'pmpro_order_deleted',
            'name'              => WPWHPRO()->helpers->translate( 'Order deleted', $translation_ident ),
            'sentence'              => WPWHPRO()->helpers->translate( 'an order was deleted', $translation_ident ),
            'parameter'         => $parameter,
            'settings'          => $settings,
            'returns_code'      => $this->get_demo( array() ),
            'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as an order was deleted within Paid Memberships Pro.', $translation_ident ),
            'description'       => $description,
            'integration'       => 'paid-memberships-pro',
            'premium'           => true,
        );

    }

    /**
     * Triggers once a new Paid Membership Pro membership was updated
     *
	 * @param object|MemberOrder $order The data about the current order
     */
    public function pmpro_delete_order_callback( $order_id, MemberOrder $order ){

        $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'pmpro_order_deleted' );
		$membership_level = $order->getMembershipLevel();
		$membership_id = $membership_level->id;
        $user = $order->getUser();
		$user_id = $user->ID;

        $payload = array(
            'user_id' => $user_id,
            'membership_id' => $membership_id,
            'order' => $order,
        );

        $response_data_array = array();

        foreach( $webhooks as $webhook ){

            $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
            $is_valid = true;

            if( isset( $webhook['settings'] ) ){
                if( isset( $webhook['settings']['wpwhpro_pmpro_trigger_on_membership_level'] ) && ! empty( $webhook['settings']['wpwhpro_pmpro_trigger_on_membership_level'] ) ){
                    if( ! in_array( $membership_id, $webhook['settings']['wpwhpro_pmpro_trigger_on_membership_level'] ) ){
                        $is_valid = false;
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

        do_action( 'wpwhpro/webhooks/trigger_pmpro_order_deleted', $payload, $response_data_array );
    }

    public function get_demo( $options = array() ) {

        $data = array (
            'user_id' => '1',
            'membership_id' => '1',
            'order' => 
            array (
              'gateway' => '',
              'Gateway' => 
              array (
                'gateway' => '',
              ),
              'id' => '1',
              'code' => 'ACF9DBE74E',
              'session_id' => 'k0rqir9dkh4rkfh5ghs2uqptes',
              'user_id' => '1',
              'membership_id' => '1',
              'paypal_token' => '',
              'billing' => 
              array (
                'name' => '',
                'street' => '',
                'city' => '',
                'state' => '',
                'zip' => '',
                'country' => '',
                'phone' => '',
              ),
              'FirstName' => '',
              'LastName' => '',
              'Address1' => '',
              'Email' => 'jon@doe.test',
              'subtotal' => '0',
              'tax' => '0',
              'couponamount' => '',
              'certificate_id' => '0',
              'certificateamount' => '',
              'total' => '0',
              'payment_type' => '',
              'cardtype' => '',
              'accountnumber' => '',
              'expirationmonth' => '',
              'expirationyear' => '2032',
              'ExpirationDate' => '2032',
              'ExpirationDate_YdashM' => '2032-',
              'status' => 'success',
              'gateway_environment' => 'sandbox',
              'payment_transaction_id' => '',
              'subscription_transaction_id' => '',
              'timestamp' => 1626948840,
              'affiliate_id' => '',
              'affiliate_subid' => '',
              'notes' => '',
              'checkout_id' => '1',
              'datetime' => '2021-07-22 10:14:00',
              'sqlQuery' => 'UPDATE wp_pmpro_membership_orders
                                              SET `code` = \'ACF9DBE74E\',
                                              `session_id` = \'k0rqir9dkh4rkfh5ghs2uqptes\',
                                              `user_id` = 1,
                                              `membership_id` = 1,
                                              `paypal_token` = \'\',
                                              `billing_name` = \'\',
                                              `billing_street` = \'\',
                                              `billing_city` = \'\',
                                              `billing_state` = \'\',
                                              `billing_zip` = \'\',
                                              `billing_country` = \'\',
                                              `billing_phone` = \'\',
                                              `subtotal` = \'0\',
                                              `tax` = \'0\',
                                              `couponamount` = \'\',
                                              `certificate_id` = 0,
                                              `certificateamount` = \'\',
                                              `total` = \'0\',
                                              `payment_type` = \'\',
                                              `cardtype` = \'\',
                                              `accountnumber` = \'\',
                                              `expirationmonth` = \'\',
                                              `expirationyear` = \'2032\',
                                              `status` = \'success\',
                                              `gateway` = \'\',
                                              `gateway_environment` = \'sandbox\',
                                              `payment_transaction_id` = \'\',
                                              `subscription_transaction_id` = \'\',
                                              `timestamp` = \'2021-07-22 10:14:00\',
                                              `affiliate_id` = \'\',
                                              `affiliate_subid` = \'\',
                                              `notes` = \'\',
                                              `checkout_id` = 1
                                              WHERE id = \'1\'
                                              LIMIT 1',
              'discount_code' => NULL,
              'membership_level' => 
              array (
                'level_id' => '1',
                'name' => 'First Level',
                'description' => 'This is a demo level',
                'allow_signups' => '1',
                'expiration_number' => '0',
                'expiration_period' => '',
                'id' => '1',
                'user_id' => '1',
                'membership_id' => '1',
                'code_id' => '0',
                'initial_payment' => 0,
                'billing_amount' => 0,
                'cycle_number' => '0',
                'cycle_period' => '',
                'billing_limit' => '0',
                'trial_amount' => 0,
                'trial_limit' => '0',
                'status' => 'active',
                'startdate' => '1626948898',
                'enddate' => NULL,
                'modified' => '2021-07-22 12:14:58',
              ),
              'user' => 
              array (
                'ID' => '1',
                'user_login' => 'jondoe',
                'user_pass' => '$P$B4B1t8fCUMXXXXXXXXXC7EbzY1',
                'user_nicename' => 'jondoe',
                'user_email' => 'jon@doe.test',
                'user_url' => '',
                'user_registered' => 1501199891,
                'user_activation_key' => '',
                'user_status' => '0',
                'display_name' => 'jondoe',
                'spam' => '0',
                'deleted' => '0',
              ),
            ),
        );

        return $data;
    }

  }

endif; // End if class_exists check.