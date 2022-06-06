<?php
if ( ! class_exists( 'WP_Webhooks_Integrations_wordpress_Triggers_wordpress_hook' ) ) :

	/**
	 * Load the wordpress_hook trigger
	 *
	 * @since 4.1.0
	 * @author Ironikus <info@ironikus.com>
	 */
	class WP_Webhooks_Integrations_wordpress_Triggers_wordpress_hook {

		public function get_callbacks(){

            return array(
                array(
                    'type' => 'action',
                    'hook' => 'wpwhpro/integrations/callbacks_registered',
                    'callback' => array( $this, 'register_wrodpress_hook_callbacks' ),
                    'priority' => 5,
                    'arguments' => 0,
                    'delayed' => false,
                ),
            );

		}

        public function get_details(){

            $translation_ident = "action-wordpress_hook-description";

            $parameter = array(
                'none'   => array( 'short_description' => WPWHPRO()->helpers->translate( 'No default values given. Send over whatever you like.', 'trigger-login-user-content' ) ),
            );

            $description = WPWHPRO()->webhook->get_endpoint_description( 'trigger', array(
				'webhook_name' => 'WordPress hook',
				'webhook_slug' => 'wordpress_hook',
				'post_delay' => false,
				'trigger_hooks' => array(
					array( 
                        'hook' => 'wpwhpro/integrations/callbacks_registered',
                    ),
					array( 
                        'hook' => 'your_custom_hook',
                        'url' => 'https://developer.wordpress.org/plugins/hooks/',
                    ),
				)
			) );

            $settings = array(
                'load_default_settings' => true,
                'data' => array(
                    'wpwhpro_wordpress_hook_definition_type' => array(
						'id'          => 'wpwhpro_wordpress_hook_definition_type',
						'type'        => 'select',
						'multiple'    => false,
						'choices'      => array(
                            'action' => WPWHPRO()->helpers->translate('Action', $translation_ident),
                            'filter' => WPWHPRO()->helpers->translate('Filter', $translation_ident),
                        ),
						'label'       => WPWHPRO()->helpers->translate('WordPress hook type', $translation_ident),
						'placeholder' => '',
						'required'    => false,
						'description' => WPWHPRO()->helpers->translate('Select whether your defined hook is a filter or an action.', $translation_ident)
					),
					'wpwhpro_wordpress_hook_definition' => array(
						'id'          => 'wpwhpro_wordpress_hook_definition',
						'type'        => 'text',
						'label'       => WPWHPRO()->helpers->translate('WordPress hook name', $translation_ident),
						'placeholder' => '',
						'required'    => false,
						'description' => WPWHPRO()->helpers->translate('Add the WordPress hook name that you want to use to fire this trigger on.', $translation_ident)
					),
					'wpwhpro_wordpress_hook_definition_priority' => array(
						'id'          => 'wpwhpro_wordpress_hook_definition_priority',
						'type'        => 'text',
						'label'       => WPWHPRO()->helpers->translate('WordPress hook priority', $translation_ident),
						'placeholder' => '',
						'required'    => false,
						'description' => WPWHPRO()->helpers->translate('Add a custom WordPress hook priority. Default: 10', $translation_ident)
					),
					'wpwhpro_wordpress_hook_definition_arguments' => array(
						'id'          => 'wpwhpro_wordpress_hook_definition_arguments',
						'type'        => 'text',
						'label'       => WPWHPRO()->helpers->translate('WordPress hook arguments', $translation_ident),
						'placeholder' => '',
						'required'    => false,
						'description' => WPWHPRO()->helpers->translate('Define the number of arguments this hook has. Default: 1', $translation_ident)
					),
				)
            );

            return array(
                'trigger'           => 'wordpress_hook',
                'name'              => WPWHPRO()->helpers->translate( 'WordPress hook fired', 'trigger-custom-action' ),
                'sentence'              => WPWHPRO()->helpers->translate( 'a WordPress hook was fired', 'trigger-custom-action' ),
                'parameter'         => $parameter,
                'settings'          => $settings,
                'returns_code'      => $this->get_demo(),
                'short_description' => WPWHPRO()->helpers->translate( 'This webhook fires once your selected WordPress hook (filter or action) has been called.', 'trigger-custom-action' ),
                'description'       => $description,
                'integration'       => 'wordpress',
                'premium'           => true,
            );

        }


        public function register_wrodpress_hook_callbacks(){

            $webhooks = WPWHPRO()->webhook->get_hooks( 'trigger', 'wordpress_hook' );

            foreach( $webhooks as $webhook_key => $webhook ){

                $hook_type = null;
                $hook = null;
                $priority = 10;
                $arguments = 1;

                if( isset( $webhook['settings'] ) ){
					foreach( $webhook['settings'] as $settings_name => $settings_data ){

						if( $settings_name === 'wpwhpro_wordpress_hook_definition_type' && ! empty( $settings_data ) ){
                            $hook_type = $settings_data;
						}

                        if( $settings_name === 'wpwhpro_wordpress_hook_definition' && ! empty( $settings_data ) ){
                            $hook = $settings_data;
                        }

                        if( $settings_name === 'wpwhpro_wordpress_hook_definition_priority' && ! empty( $settings_data ) ){
                            $priority = $settings_data;
                        }

                        if( $settings_name === 'wpwhpro_wordpress_hook_definition_arguments' && ! empty( $settings_data ) ){
                            $arguments = $settings_data;
                        }
					}
				}

                if( ! empty( $hook_type ) && ! empty( $hook ) ){ 

                    $callback_func = function() use ( $webhook, $hook_type ) {
                        
                        $data = func_get_args();
                        $webhook_url_name = ( is_array($webhook) && isset( $webhook['webhook_url_name'] ) ) ? $webhook['webhook_url_name'] : null;

                        if( $webhook_url_name !== null ){
                            $response_data[ $webhook_url_name ] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data );
                        } else {
                            $response_data[] = WPWHPRO()->webhook->post_to_webhook( $webhook, $data );
                        }

                        if( $hook_type === 'filter' ){
                            if( is_array( $data ) ){
                                foreach( $data as $return ){
                                    return $return; //whatever comes first
                                }
                            } else {
                                return $data;
                            }
                        }
                        
                    };  

                    switch( $hook_type ){
                        case 'action':
                            add_action( $hook, $callback_func, $priority, $arguments );
                            break;
                        case 'filter':
                            add_filter( $hook, $callback_func, $priority, $arguments );
                            break;
                    }
                }
                
            }

        }

        /*
        * Register the demo post delete trigger callback
        *
        * @since 1.6.4
        */
        public function get_demo( $options = array() ) {

            return array( WPWHPRO()->helpers->translate( 'The data construct of your given hook callback.', 'trigger-custom-action' ) ); // Custom content from the action
        }

    }

endif; // End if class_exists check.