<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wp_webhooks_Triggers_wpwh_button' ) ) :

	/**
	 * Load the wpwh_button trigger
	 *
	 * @since 4.3.1
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wp_webhooks_Triggers_wpwh_button {

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
                    'type' => 'shortcode',
                    'hook' => 'wpwh_button',
                    'callback' => array( $this, 'ironikus_trigger_wpwh_button' ),
                    'priority' => 10,
                    'arguments' => 2,
                    'delayed' => false,
                ),
                array(
                    'type' => 'action',
                    'hook' => 'wp',
                    'callback' => array( $this, 'ironikus_trigger_wpwh_button_callback' ),
                    'priority' => 10,
                    'arguments' => 1,
                    'delayed' => false,
                ),
            );

		}

        /*
        * Register the user login trigger as an element
        */
        public function get_details(){

            $translation_ident = "trigger-wpwh_button-description";
            $wpwh_helpers = WPWHPRO()->integrations->get_helper( 'wp-webhooks', 'wpwh_helpers' );
            $parameter = array(
                'custom_data'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'Your custom data construct build out of the shortcode arguments, as well as the data mapping.', $translation_ident ) ),
            );

            ob_start();
?>
<p><?php echo WPWHPRO()->helpers->translate( "The trigger will be fired whenever someone clicks the button that was added with the shortcode <code>[wpwh_button]</code>", $translation_ident ); ?>
</p>
<p>
<?php echo WPWHPRO()->helpers->translate( "<strong>Please note:</strong> If the shortcode is not executed and you see the shortcode itself within the frontend, please add a webhook URL first withn this trigger. We only load the shortcode if a trigger URL is added to save performance for your site.", $translation_ident ); ?>
</p>
<?php echo WPWHPRO()->helpers->translate( "While the shortcode itself does not do much except of displaying a custom button on your page, you might want to add some data to it. To do that, you have two different ways of doing so:", $translation_ident ); ?>
<ol>
    <li><?php echo WPWHPRO()->helpers->translate( "You can add the data using the data mapping feature ba assigning a data mapping template to your webhook URL.", $translation_ident ); ?></li>
    <li>
        <?php echo WPWHPRO()->helpers->translate( "You can also add the data using the shortcode parameters. E.g. <code>[wpwh_button param=\"some value\"]</code>", $translation_ident ); ?>
        <br>
        <?php echo WPWHPRO()->helpers->translate( "While <strong>param</strong> is the key within the data response, <strong>some value</strong> is the value. The example above will cause an output similar to:", $translation_ident ); ?>
        <pre>
{
    "param": "some value"
}
</pre>

<?php echo WPWHPRO()->helpers->translate( "We also support a variety os special attributes within the shortcode tag (e.g. <code>[wpwh_button wpwh_new_window=\"yes\"]</code> ). Down below you will find a list of those, as well as a short description of what they are good for.", $translation_ident ); ?>
<table class="wpwh-table wpwh-text-small">
    <thead>
        <tr>
            <td><?php echo WPWHPRO()->helpers->translate( "Special tag name", $translation_ident ); ?></td>
            <td><?php echo WPWHPRO()->helpers->translate( "Special tag description", $translation_ident ); ?></td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>wpwh_id</td>
            <td><?php echo WPWHPRO()->helpers->translate( "Use this attribute to set a custom id for the button form. it is set for the <strong>form</strong> element.", $translation_ident ); ?></td>
        </tr>
        <tr>
            <td>wpwh_class</td>
            <td><?php echo WPWHPRO()->helpers->translate( "Set custom CSS classes for the button form. If you want to set multiple ones, simply leave a space in between: class-1 class-2", $translation_ident ); ?></td>
        </tr>
        <tr>
            <td>wpwh_button_label</td>
            <td><?php echo WPWHPRO()->helpers->translate( "Customizes the text of the button.", $translation_ident ); ?></td>
        </tr>
        <tr>
            <td>wpwh_new_window</td>
            <td><?php echo WPWHPRO()->helpers->translate( "Set this argument to \"yes\" if you want the button click to open within a new window.", $translation_ident ); ?></td>
        </tr>
        <tr>
            <td>wpwh_method</td>
            <td><?php echo WPWHPRO()->helpers->translate( "By default, the button data is sent via the POST method (recommended). If you want to use the GET method, set this attribute value to \"get\".", $translation_ident ); ?></td>
        </tr>
        <tr>
            <td>wpwh_trigger_names</td>
            <td><?php echo WPWHPRO()->helpers->translate( "If you want this button to only fire specific webhook URLs, you can define their names here. If you want to use multiple ones, simply separate them via a comma: webhook-1,url-2,demo-3", $translation_ident ); ?></td>
        </tr>
    </tbody>
</table>

        <?php echo WPWHPRO()->helpers->translate( "We do also support custom tags, meaning you can add dynamic values from the currently given data. E.g. <code>email=\"%user_email%\"</code> - This will add the email of the currently logged in user. For a full list of the dynamic arguments, please take a look at the list down below.", $translation_ident ); ?>

        <table class="wpwh-table wpwh-text-small">
            <thead>
                <tr>
                    <td><?php echo WPWHPRO()->helpers->translate( "Tag name", $translation_ident ); ?></td>
                    <td><?php echo WPWHPRO()->helpers->translate( "Tag description", $translation_ident ); ?></td>
                </tr>
            </thead>
            <tbody>
                <?php foreach( $wpwh_helpers->get_shortcode_tags() as $tag ) : 
                
                if( ! isset( $tag['tag_name'] ) ){
                    continue;
                }

                $title = '';
                if( isset( $tag['title'] ) ){
                    $title = '<strong>' . $tag['title'] . '</strong><br>';
                }

                $description = '';
                if( isset( $tag['description'] ) ){
                    $description = $tag['description'];
                }
                
                ?>
                <tr>
                    <td><?php echo '%' . $tag['tag_name'] . '%'; ?></td>
                    <td><?php echo $title . $description; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </li>
</ol>
<?php
            $how_to = ob_get_clean();

            $description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
				'webhook_name' => 'Custom button clicked',
				'webhook_slug' => 'wpwh_button',
				'post_delay' => false,
				'how_to' => $how_to,
				'trigger_hooks' => array(
					array( 
                        'hook' => 'init',
                        'url' => 'https://developer.wordpress.org/reference/hooks/init/',
                    ),
				)
			) );

            return array(
                'trigger'           => 'wpwh_button',
                'name'              => WPWHPRO()->helpers->translate( 'Custom button clicked', $translation_ident ),
                'sentence'              => WPWHPRO()->helpers->translate( 'a custom button was clicked', $translation_ident ),
                'parameter'         => $parameter,
                'returns_code'      => $this->get_demo( array() ),
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires as soon as a custom button was clicked.', $translation_ident ),
                'description'       => $description,
                'integration'       => 'wp-webhooks',
                'premium'           => true,
            );

        }

        public function ironikus_trigger_wpwh_button( $attr = array(), $content = '' ){

            $translation_ident = "trigger-wpwh_button-description";

            $id_out = '';
            if( isset( $attr['wpwh_id'] ) ){
                $id_out = 'id="wpwh_button_id_' . intval( $attr['wpwh_id'] ) . '" ';
            }

            $button_label_out = 'Send data';
            if( isset( $attr['wpwh_button_label'] ) ){
                $button_label_out = sanitize_text_field( $attr['wpwh_button_label'] );
            }

            $setting_new_window = '';
            if( isset( $attr['wpwh_new_window'] ) && $attr['wpwh_new_window'] == 'yes' ){
                $setting_new_window = ' target="_blank"';
            }

            $setting_method = 'post';
            if( isset( $attr['wpwh_method'] ) && $attr['wpwh_method'] == 'get' ){
                $setting_method = 'get';
            }

            $html_classes = array(
                'wpwh_button'
            );
            if( isset( $attr['wpwh_class'] ) ){
                $class_array = explode( ' ', $attr['wpwh_class'] );

                if( is_array( $class_array ) ){
                    $html_classes = array_merge( $html_classes, $class_array );
                }
            }

            $button_html = '';

            $button_html .= '<form ' . $id_out . 'class="' . esc_attr( implode( ' ', $html_classes ) ) . '" method="' . $setting_method . '"' . $setting_new_window . '>';
            
            $signature_string = '';
            $secret = wp_create_nonce( 'trigger_wpwh_button' );

            foreach( $attr as $attr_name => $attr_value ){

                $validated_value = $attr_value;
                if( is_array( $attr_value ) || is_object( $attr_value ) ){
                    $validated_value = json_encode( $attr_value );
                }

                $validated_value = htmlspecialchars( $validated_value );
                $signature_string .= $validated_value;
                $attr_name = str_replace( '-', '_', sanitize_title( $attr_name ) );

                $button_html .= '<input type="hidden" name="wpwh_button[' . $attr_name . ']" value="' . $validated_value . '"/>';

            }

            $signature = WPWHPRO()->helpers->generate_signature( $signature_string, $secret );
            $button_html .= '<input type="hidden" name="wpwh_button_signature" value="' . $signature . '"/>';
            $button_html .= '<input type="hidden" name="wpwh_button_signature_key" value="' . base64_encode( $secret ) . '"/>';

            $button_html .= WPWHPRO()->helpers->get_nonce_field( array(
                'action' => 'trigger_wpwh_button',
			    'arg'    => 'trigger_wpwh_button_nonce'
            ) );
            
            $button_html .= '<button type="submit" class="wpwh_button_submit" name="wpwh_button_submit">' . WPWHPRO()->helpers->translate( $button_label_out, $translation_ident ) . '</button>';
            $button_html .= '</form>';

            $button_html = apply_filters( 'wpwhpro/webhooks/wpwh_button', $button_html, $attr );

            return $button_html;
            
        }

        public function ironikus_trigger_wpwh_button_callback(){

            if( 
                ! isset( $_POST['wpwh_button_submit'] ) 
                || ! isset( $_POST['wpwh_button'] ) 
                || ! isset( $_POST['wpwh_button_signature'] ) 
                || ! isset( $_POST['wpwh_button_signature_key'] ) 
            ){
                return;
            }

            if( ! check_admin_referer( 'trigger_wpwh_button', 'trigger_wpwh_button_nonce' ) ){
                return;
            }

            $testing_signature = '';
            foreach( $_POST['wpwh_button'] as $signature_part ){
                $testing_signature .= $signature_part;
            }
            $form_key = base64_decode( $_POST['wpwh_button_signature_key'] );

            if( $_POST['wpwh_button_signature'] !== WPWHPRO()->helpers->generate_signature( $testing_signature, $form_key ) ){
                return;
            }
   
            $attr = $_POST['wpwh_button'];
            $response_data = array();
            $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wpwh_button' );
            $wpwh_helpers = WPWHPRO()->integrations->get_helper( 'wp-webhooks', 'wpwh_helpers' );
            $special_arguments = array(
                'wpwh_trigger_names' => 'all',
                'wpwh_id' => 0,
                'wpwh_button_label' => '',
                'wpwh_new_window' => '',
                'wpwh_method' => '',
                'wpwh_class' => '',
            );

            foreach( $special_arguments as $ak => $dv ){
                if( isset( $attr[ $ak ] ) ){
                    $special_arguments[ $ak ] = $attr[ $ak ];
                    unset( $attr[ $ak ] );
                }
            }

            $shortcode_tags = $wpwh_helpers->get_shortcode_tags();
            $attr_validated = $wpwh_helpers->validate_data( $attr, $shortcode_tags );

            $trigger_name_whitelist = array();
            if( $special_arguments['wpwh_trigger_names'] !== 'all' ){
                $trigger_names_array = explode( ',', $special_arguments['wpwh_trigger_names'] );
                if( is_array( $trigger_names_array ) ){
                    foreach( $trigger_names_array as $single_trigger ){
                        $trigger_name_whitelist[] = trim( $single_trigger );
                    }
                }
            } 

            foreach( $webhooks as $webhook ){

                $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;
                
                if( ! empty( $trigger_name_whitelist ) && ! in_array( $webhook_url_name, $trigger_name_whitelist ) ){
                    continue;
                }

                if( $webhook_url_name !== null ){
                    $response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $attr_validated );
                } else {
                    $response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $attr_validated );
                }

            }

            do_action( 'wpwhpro/webhooks/wpwh_button', $attr_validated, $attr, $response_data );
            
        }

        /*
        * Register the demo data response
        *
        * @param $data - The default options
        *
        * @return array - The demo data
        */
        public function get_demo( $options = array() ){

            $data = array (
                'your custom data construct'
            );

            return $data;
        }

    }

endif; // End if class_exists check.