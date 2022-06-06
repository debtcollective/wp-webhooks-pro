<?php

/**
 * WP_Webhooks_Pro_Integrations Class
 *
 * This class contains all of the webhook integrations
 *
 * @since 4.2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The webhook integration class of the plugin.
 *
 * @since 4.2.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_Integrations {

	/**
	 * If an action call is present, this var contains the webhook
	 *
	 * @since 4.2.0
	 * @var - The currently present action webhook
	 */
	public $integrations = array();

	/**
	 * A cached version of all the available actions
	 *
	 * @since 5.0
	 * @var - The currently present action webhook
	 */
	private $actions = null;

    /**
	 * Execute feature related hooks and logic to get 
	 * everything running
	 *
	 * @since 5.0
	 * @return void
	 */
	public function execute(){

		add_action( 'plugins_loaded', array( $this, 'load_integrations' ), 10 );
        add_action( 'plugins_loaded', array( $this, 'register_trigger_callbacks' ), 10 );

	}

    /**
	 * ######################
	 * ###
	 * #### INTEGRATION AUTOLOADER
	 * ###
	 * ######################
	 */

     /**
      * Initialize all default integrations
      *
      * @return void
      */
     public function load_integrations(){
         $integration_folder = $this->get_integrations_folder();
         $integration_folders = $this->get_integrations_directories();
         if( is_array( $integration_folders ) ){
             foreach( $integration_folders as $integration ){
                 $file_path = $integration_folder . DIRECTORY_SEPARATOR . $integration . DIRECTORY_SEPARATOR . $integration . '.php';
                 $this->register_integration( array(
                     'slug' => $integration,
                     'path' => $file_path,
                 ) );
             }
         }
     }

     /**
      * Get an array contianing all of the currently given default integrations
      * The directory folder name acts as well as the integration slug.
      *
      * @return array The available default integrations
      */
    public function get_integrations_directories() {

        $integrations = array();
		
        try {
            $integrations = WPWHPRO()->helpers->get_folders( $this->get_integrations_folder() );
        } catch ( Exception $e ) {
            throw WPWHPRO()->helpers->log_issue( $e->getTraceAsString() );
        }

		return apply_filters( 'wpwhpro/integrations/get_integrations_directories', $integrations );
	}

    /**
     * Get the main integration folder
     *
     * @return void
     */
    public function get_integrations_folder( $integration = '' ){

        $integration = sanitize_title( $integration );

        $folder = WPWHPRO_PLUGIN_DIR . 'core' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'integrations';

        if( $integration ){
            $folder .= DIRECTORY_SEPARATOR . $integration;
        }

        return apply_filters( 'wpwhpro/integrations/get_integrations_folder', $folder );
    }

    /**
     * Register an integration 
     * 
     * This function can also be used to register third-party extensions. 
     * The following parameters are required: 
     * 
     * "path" => contains the integrations full path + file name + file extension
     * "slug" => contains the slug (folder name) of the integration
     * 
     * All other values are dynamically included (in case you define them.)
     *
     * @param array $integration
     * @return bool Whether the integration was added or not
     */
    public function register_integration( $integration = array() ){
        $return = false;
        $default_dependencies = WPWHPRO()->settings->get_default_integration_dependencies();

        if( is_array( $integration ) && isset( $integration['slug'] ) && isset( $integration['path'] ) ){
            $path = $integration['path'];
            $slug = $integration['slug'];

            if( file_exists( $path ) ){
                require_once $path;
                
                $directory = dirname( $path );
                $class = $this->get_integration_class( $slug );
                if( ! empty( $class ) && class_exists( $class ) && ! isset( $this->integrations[ $slug ] ) ){
                    $integration_class = new $class();
        
                    $is_active = ( ! method_exists( $integration_class, 'is_active' ) || method_exists( $integration_class, 'is_active' ) && $integration_class->is_active() ) ? true : false;
                    $is_active = apply_filters( 'wpwhpro/integrations/integration/is_active', $is_active, $slug, $class, $integration_class );

                    if( $is_active ) {
                        $this->integrations[ $slug ] = $integration_class;

                        //Since v5.2, we pre-load the details within the integration for performance and to centralize
                        $integration_details = ( method_exists( $integration_class, 'get_details' ) ) ? $integration_class->get_details() : null;
                        if( $integration_details !== null ){
                            $this->integrations[ $slug ]->details = $integration_details;
                        }
        
                        //Register Depenencies
                        foreach( $default_dependencies as $default_dependency ){

                            //Make sure the default dependencies exists
                            if( ! property_exists( $this->integrations[ $slug ], $default_dependency ) ){
                                $this->integrations[ $slug ]->{$default_dependency} = new stdClass();
                            }

                            if( ! is_array( $this->integrations[ $slug ]->{$default_dependency} ) ){
                                $this->integrations[ $slug ]->{$default_dependency} = new stdClass();
                            }

                            $dependency_path = $directory . DIRECTORY_SEPARATOR . $default_dependency;
                            if( is_dir( $dependency_path ) ){
                                $dependencies = array();
    
                                try {
                                    $dependencies = WPWHPRO()->helpers->get_files( $dependency_path, array(
                                        'index.php'
                                    ) );
                                } catch ( Exception $e ) {
                                    throw WPWHPRO()->helpers->log_issue( $e->getTraceAsString() );
                                }
    
                                if( is_array( $dependencies ) && ! empty( $dependencies ) ){

                                    foreach( $dependencies as $dependency ){
                                        $basename = basename( $dependency );
                                        $basename_clean = basename( $dependency, ".php" );
    
                                        $ext = pathinfo( $basename, PATHINFO_EXTENSION );
                                        if ( (string) $ext !== 'php' ) {
                                            continue;
                                        }
    
                                        require_once $dependency_path . DIRECTORY_SEPARATOR . $dependency;
    
                                        $dependency_class = $this->get_integration_class( $slug, $default_dependency, $basename_clean );

                                        if( class_exists( $dependency_class ) ){
                                            $dependency_class_object = new $dependency_class();
    
                                            $is_active = ( ! method_exists( $dependency_class_object, 'is_active' ) || method_exists( $dependency_class_object, 'is_active' ) && $dependency_class_object->is_active() ) ? true : false;
                                            $is_active = apply_filters( 'wpwhpro/integrations/dependency/is_active', $is_active, $slug, $basename_clean, $dependency_class, $dependency_class_object );

                                            if( $is_active ){
                                                $this->integrations[ $slug ]->{$default_dependency}->{$basename_clean} = $dependency_class_object;

                                                //Since v5.2, we pre-load the details within the integration for performance and to centralize
                                                $details = ( method_exists( $dependency_class_object, 'get_details' ) ) ? $dependency_class_object->get_details() : null;
                                                if( $details !== null ){
                                                    $this->integrations[ $slug ]->{$default_dependency}->{$basename_clean}->details = $details;
                                                }
                                            }
    
                                        }
                                    }
                                }
                            }
                        }
        
                    }
        
                    $return = true;{

                    }
                }
    
            }
        }

        return $return;
    }

    /**
     * Builds the dynamic class based on the integration name and a sub file name
     *
     * @param string $integration The integration slug
     * @param string $type The type fetched from WPWHPRO()->settings->get_default_integration_dependencies()
     * @param string $sub_class A sub file name in case we add something from te default dependencies
     * @return string The integration class
     */
    public function get_integration_class( $integration, $type = '', $sub_class = '' ){
        $class = false;

        if( ! empty( $integration ) ){
            $class = 'WP_Webhooks_Integrations_' . $this->validate_class_name( $integration );
        }

        if( ! empty( $type ) && ! empty( $sub_class ) ){
            $validate_class_type = ucfirst( strtolower( $type ) );
            $class .= '_' . $validate_class_type . '_' . $this->validate_class_name( $sub_class );
        }
        
        return apply_filters( 'wpwhpro/integrations/get_integration_class', $class );
    }

    /**
     * Format the class name to make it compatible with our
     * dynamic structure
     *
     * @param string $class_name
     * @return string The class name
     */
    public function validate_class_name( $class_name ){

        $class_name = str_replace( ' ', '_', $class_name );
        $class_name = str_replace( '-', '_', $class_name );

        return apply_filters( 'wpwhpro/integrations/validate_class_name', $class_name );
    }

    /**
     * Grab the details of a given integration
     *
     * @param string $slug
     * @return array The integration details
     */
    public function get_details( $slug ){
        $return = array();

        if( ! empty( $slug ) ){
            if( isset( $this->integrations[ $slug ] ) ){
                if( isset( $this->integrations[ $slug ]->details ) ){
                    $return = $this->integrations[ $slug ]->details;
                }
            }
        }

        return apply_filters( 'wpwhpro/integrations/get_details', $return );
    }

    /**
     * Get all available integrations
     *
     * @param string $slug
     * @return array The integration details
     */
    public function get_integrations( $slug = false ){
        $return = $this->integrations;

        if( $slug !== false ){
            if( isset( $this->integrations[ $slug ] ) ){
                $return = $this->integrations[ $slug ];
            } else {
                $return = false;
            }
        }

        return apply_filters( 'wpwhpro/integrations/get_integrations', $return );
    }

    /**
     * Grab a specific helper from the given integration
     *
     * @param string $integration The integration slug (folder name)
     * @param string $helper The helper slug (file name)
     * @return object|stdClass The helper class
     */
    public function get_helper( $integration, $helper ){
        $return = new stdClass();

        if( ! empty( $integration ) && ! empty( $helper ) ){
            if( isset( $this->integrations[ $integration ] ) ){
                if( property_exists( $this->integrations[ $integration ], 'helpers' ) ){
                    if( property_exists( $this->integrations[ $integration ]->helpers, $helper ) ){
                        $return = $this->integrations[ $integration ]->helpers->{$helper};
                    }
                }
            }
        }

        return apply_filters( 'wpwhpro/integrations/get_helper', $return );
    }

    /**
     * Get a list of all available actions
     * 
     * @since 5.0
     *
     * @param mixed $integration_slug - The slug of a single integration
     * @param mixed $integration_action - The slug of a single action
     * 
     * @return array A list of actions or a single action
     */
    public function get_actions( $integration_slug = false, $integration_action = false ){

        $actions = array();

        if( ! empty( $this->integrations ) ){
            foreach( $this->integrations as $si ){
                if( property_exists( $si, 'actions' ) ){
                    foreach( $si->actions as $action_slug => $action ){

                        if( isset( $this->actions[ $action_slug ] ) ){
                            $actions[ $action_slug ] = $this->actions[ $action_slug ];
                        } else {
                            if( isset( $action->details ) ){
                                $details = $action->details;
                                if( is_array( $details ) && isset( $details['action'] ) && ! empty( $details['action'] ) ){
        
                                    //Validate parameter globally
                                    if( isset( $details['parameter'] ) && is_array( $details['parameter'] ) ){

                                        foreach( $details['parameter'] as $arg => $arg_data ){
        
                                            //Add name
                                            if( ! isset( $details['parameter'][ $arg ]['id'] ) ){
                                                $details['parameter'][ $arg ]['id'] = $arg;
                                            }
        
                                            //Add label
                                            if( ! isset( $details['parameter'][ $arg ]['label'] ) ){
                                                $details['parameter'][ $arg ]['label'] = $arg;
                                            }
        
                                            //Add type
                                            if( ! isset( $details['parameter'][ $arg ]['type'] ) ){
                                                $details['parameter'][ $arg ]['type'] = 'text';
                                            }
        
                                            //Add required
                                            if( ! isset( $details['parameter'][ $arg ]['required'] ) ){
                                                $details['parameter'][ $arg ]['required'] = false;
                                            }
        
                                            //Add variable
                                            if( ! isset( $details['parameter'][ $arg ]['variable'] ) ){
                                                $details['parameter'][ $arg ]['variable'] = true;
                                            }
        
                                            //Verify choices to the new structure
                                            if( isset( $details['parameter'][ $arg ]['choices'] ) ){
                                                foreach( $details['parameter'][ $arg ]['choices'] as $single_choice_key => $single_choice_data ){

                                                    //Make sure we always serve the same values
                                                    if( is_array( $single_choice_data ) ){

                                                        if( ! isset( $single_choice_data['value'] ) ){
                                                            $details['parameter'][ $arg ]['choices'][ $single_choice_key ]['value'] = $single_choice_key;
                                                        }

                                                        if( ! isset( $single_choice_data['label'] ) ){
                                                            $details['parameter'][ $arg ]['choices'][ $single_choice_key ]['label'] = $single_choice_key;
                                                        }

                                                    } elseif( is_string( $single_choice_data ) ){
                                                        $details['parameter'][ $arg ]['choices'][ $single_choice_key ] = array(
                                                            'label' => $single_choice_data,
                                                            'value' => $single_choice_key,
                                                        );
                                                    }

                                                }
                                            }
                                            
                                        }

                                        //Dynamically append the new action callback parameter
                                        $details['parameter']['wpwh_call_action'] = array(
                                            'id' => 'wpwh_call_action',
                                            'type' => 'text',
                                            'required' => false,
                                            'variable' => true,
                                            'label' => WPWHPRO()->helpers->translate( 'WordPress action callback (Advanced)', 'wpwhpro-integration-action-parameter' ), 
                                            'short_description' => WPWHPRO()->helpers->translate( '(String) Register a custom WordPress hook callback. The value will be called as followed: do_action( $yourdefinedaction, $action_return_data, $request_data )', 'wpwhpro-integration-action-parameter' ),
                                        );
                                    }

                                    $actions[ $details['action'] ] = $details;
                                    $this->actions[ $action_slug ] = $details;
                                }
                            }
                        }
                        
                    }
                }
            }
        }

        $actions = apply_filters( 'wpwhpro/integrations/get_actions', $actions, $integration_slug, $integration_action );

        $actions_output = $actions;

        if( $integration_slug !== false ){
            $actions_output = array();

            foreach( $actions as $action_slug => $action_data ){

                //Continue only if the integration matches
                if( 
                    ! is_array( $action_data ) 
                    || ! isset( $action_data['integration'] ) 
                    || $action_data['integration'] !== $integration_slug 
                ){
                    continue;
                }

                $actions_output[ $action_slug ] = $action_data;

            }
        }
        
        if( $integration_action !== false ){
            if( isset( $actions_output[ $integration_action ] ) ){
                $actions_output = $actions_output[ $integration_action ];
            }
        }

        return apply_filters( 'wpwhpro/integrations/get_actions/output', $actions_output, $actions, $integration_slug, $integration_action );
    }

    /**
     * Execute the acion logic
     *
     * @param array $default_return_data
     * @param string $action
     * @return array The data we return to the webhook caller
     */
    public function execute_actions( $default_return_data, $action ){
        $return_data = $default_return_data;
        $response_body = WPWHPRO()->http->get_current_request();

        if( ! empty( $this->integrations ) ){
            foreach( $this->integrations as $si ){
                if( property_exists( $si, 'actions' ) ){
                    $actions = $si->actions;
                    if( is_object( $actions ) && isset( $actions->{$action} ) ){
                        if( method_exists( $actions->{$action}, 'execute' ) ){
                            $return_data = $actions->{$action}->execute( $return_data, $response_body );
                        }
                    }
                }
            }
        }

        return apply_filters( 'wpwhpro/integrations/execute_actions', $return_data );
    }

    /**
     * Get all available triggers
     *
     * @return array Te triggers
     */
    public function get_triggers( $integration_slug = false, $integration_trigger = false ){
        $triggers = array();

        if( ! empty( $this->integrations ) ){
            foreach( $this->integrations as $si ){
                if( property_exists( $si, 'triggers' ) ){
                    foreach( $si->triggers as $trigger ){
                        if( isset( $trigger->details ) ){
                            $details = $trigger->details;
                            if( is_array( $details ) && isset( $details['trigger'] ) && ! empty( $details['trigger'] ) ){
                                $triggers[ $details['trigger'] ] = $details;
                            }
                        }
                    }
                }
            }
        }

        $triggers_output = $triggers;

        if( $integration_slug !== false ){
            $triggers_output = array();

            foreach( $triggers as $action_slug => $action_data ){

                //Continue only if the integration matches
                if( 
                    ! is_array( $action_data ) 
                    || ! isset( $action_data['integration'] ) 
                    || $action_data['integration'] !== $integration_slug 
                ){
                    continue;
                }

                $triggers_output[ $action_slug ] = $action_data;

            }
        }
        
        if( $integration_trigger !== false ){
            if( isset( $triggers_output[ $integration_trigger ] ) ){
                $triggers_output = $triggers_output[ $integration_trigger ];
            }
        }

        return apply_filters( 'wpwhpro/integrations/get_triggers', $triggers_output, $triggers, $integration_slug, $integration_trigger );
    }

    /**
     * Execute the receivable triggers
     *
     * @param array $default_return_data
     * @param string $trigger - the trigger name
     * @param string $trigger_url_name - The name of the trigger URL
     * @return array The data we return to the webhook caller
     */
    public function execute_receivable_triggers( $default_return_data, $trigger, $trigger_url_name = null ){
        $return_data = $default_return_data;
        $response_body = WPWHPRO()->http->get_current_request();

        if( ! empty( $this->integrations ) ){
            foreach( $this->integrations as $si ){
                if( property_exists( $si, 'triggers' ) ){
                    $triggers = $si->triggers;
                    if( is_object( $triggers ) && isset( $triggers->{$trigger} ) ){
                        if( method_exists( $triggers->{$trigger}, 'execute' ) ){
                            $return_data = $triggers->{$trigger}->execute( $return_data, $response_body, $trigger_url_name );
                            break; //shorten the circle
                        }
                    }
                }
            }
        }

        return apply_filters( 'wpwhpro/integrations/execute_receivable_triggers', $return_data );
    }

    /**
     * Get demo data from a given trigger
     *
     * @param string $trigger
     * @param array $options
     * @return array The demo data
     */
    public function get_trigger_demo( $trigger, $options = array() ){
        $demo_data = array(); 

        if( ! empty( $this->integrations ) ){
            foreach( $this->integrations as $si ){
                if( property_exists( $si, 'triggers' ) ){
                    $triggers = get_object_vars( $si->triggers );
                    
                    if( is_array( $triggers ) && isset( $triggers[ $trigger ] ) ){
                        if( is_object( $triggers[ $trigger ] ) && method_exists( $triggers[ $trigger ], 'get_demo' ) ){
                            $demo_data = $triggers[ $trigger ]->get_demo( $options );
                            break;
                        }
                    }
                }
            }
        }  

        return apply_filters( 'wpwhpro/integrations/get_trigger_demo', $demo_data );
    }

    /**
     * Register the callbacks for all available triggers
     *
     * @return void
     */
    public function register_trigger_callbacks(){
        $default_callback_vars = apply_filters( 'wpwhpro/integrations/default_callback_vars', array(
            'priority' => 10,
            'arguments' => 1,
            'delayed' => false,
        ) );

        if( ! empty( $this->integrations ) ){
            foreach( $this->integrations as $si ){
                if( property_exists( $si, 'triggers' ) ){
                    $triggers = get_object_vars( $si->triggers );
                    if( is_array( $triggers ) ){
                        foreach( $triggers as $trigger_name => $trigger ){
                            if( is_object( $trigger ) && method_exists( $trigger, 'get_callbacks' ) && ! empty( WPWHPRO()->webhook->get_hooks( 'trigger', $trigger_name ) ) ){
                                $callbacks = $trigger->get_callbacks();
                                if( ! empty( $callbacks ) && is_array( $callbacks ) ){
                                    foreach( $callbacks as $callback ){
                                        if( 
                                            isset( $callback['type'] ) 
                                            && isset( $callback['hook'] ) 
                                            && isset( $callback['callback'] )
                                        ){
                                            $type = $callback['type'];
                                            $hook = $callback['hook'];
                                            $hook_callback = $callback['callback'];
                                            $priority = isset( $callback['priority'] ) ? $callback['priority'] : $default_callback_vars['priority'];
                                            $arguments = isset( $callback['arguments'] ) ? $callback['arguments'] : $default_callback_vars['arguments'];
                                            $delayed = isset( $callback['delayed'] ) ? $callback['delayed'] : $default_callback_vars['delayed'];

                                            $callback_func = $hook_callback;

                                            if( $delayed ){
                                                $callback_func = function() use ( $type, $hook_callback, $trigger_name, $trigger ) {
                                                    $func_args = func_get_args();
                                                    WPWHPRO()->delay->add_post_delayed_trigger( $hook_callback, $func_args, array(
                                                        'trigger_name' => $trigger_name,
                                                        'trigger' => $trigger,
                                                    ) );

                                                    if( $type === 'filter' || $type === 'shortcode' ){
                                                        $return ='';

                                                        if( is_array( $func_args ) && isset( $func_args[0] ) ){
                                                            $return = $func_args[0];
                                                        }

                                                        return $return;
                                                    }
                                                };
                                            }

                                            switch( $type ){
                                                case 'filter':
                                                    add_filter( $hook, $callback_func, $priority, $arguments );
                                                    break;
                                                case 'action':
                                                    add_action( $hook, $callback_func, $priority, $arguments );
                                                    break;
                                                case 'shortcode':
                                                    add_shortcode( $hook, $callback_func, $priority, $arguments );
                                                    break;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        do_action( 'wpwhpro/integrations/callbacks_registered' );
    }

}
