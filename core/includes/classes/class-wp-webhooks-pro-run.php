<?php

/**
 * Class WP_Webhooks_Pro_Run
 *
 * Thats where we bring the plugin to life
 *
 * @since 1.0.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */

class WP_Webhooks_Pro_Run{

	/**
	 * The main page name for our admin page
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private $page_name;

	/**
	 * The main page title for our admin page
	 *
	 * @var string
	 * @since 1.0.0
	 */
	private $page_title;

	/**
	 * Our WP_Webhooks_Pro_Run constructor.
	 */
	function __construct(){
		$this->page_name    = WPWHPRO()->settings->get_page_name();
		$this->page_title   = WPWHPRO()->settings->get_page_title();
		$this->add_hooks();
		$this->execute_features();
	}

	/**
	 * Define all of our general hooks
	 */
	private function add_hooks(){

		add_action( 'plugin_action_links_' . WPWHPRO_PLUGIN_BASE, array( $this, 'plugin_action_links') );
		add_filter( 'admin_footer_text', array( $this, 'display_footer_information' ), 50, 2 );

		add_action( 'admin_enqueue_scripts',    array( $this, 'enqueue_scripts_and_styles' ) );
		add_action( 'admin_menu', array( $this, 'add_user_submenu' ), 150 );
		add_filter( 'submenu_file', array( $this, 'filter_active_wpwh_submenu_page' ), 150, 2 );
		add_filter( 'admin_init', array( $this, 'maybe_redirect_wpwh_submenu_items' ), 150, 2 );
		add_filter( 'wpwhpro/helpers/throw_admin_notice_bootstrap', array( $this, 'throw_admin_notice_bootstrap' ), 100, 1 );

		// Ajax related
		add_action( 'wp_ajax_ironikus_remove_webhook_trigger',  array( $this, 'ironikus_remove_webhook_trigger' ) );
		add_action( 'wp_ajax_ironikus_remove_webhook_action',  array( $this, 'ironikus_remove_webhook_action' ) );
		add_action( 'wp_ajax_ironikus_change_status_webhook_action',  array( $this, 'ironikus_change_status_webhook_action' ) );
		add_action( 'wp_ajax_ironikus_test_webhook_trigger',  array( $this, 'ironikus_test_webhook_trigger' ) );
		add_action( 'wp_ajax_ironikus_save_webhook_trigger_settings',  array( $this, 'ironikus_save_webhook_trigger_settings' ) );
		add_action( 'wp_ajax_ironikus_save_webhook_action_settings',  array( $this, 'ironikus_save_webhook_action_settings' ) );
		add_action( 'wp_ajax_wp_webhooks_validate_field_query',  array( $this, 'wp_webhooks_validate_field_query' ) );

		// Load admin page tabs
		add_filter( 'wpwhpro/admin/settings/menu_data', array( $this, 'add_main_settings_tabs' ), 10 );
		add_action( 'wpwhpro/admin/settings/menu/place_content', array( $this, 'add_main_settings_content' ), 10 );

		// Validate settings
		add_action( 'admin_init',  array( $this, 'ironikus_save_main_settings' ) );

		//Reset wp webhooks
		add_action( 'admin_init', array( $this, 'reset_wpwhpro_data' ), 10 );



	}

	/**
	 * Execute the plugin related features
	 *
	 * @since 4.2.3
	 * @return void
	 */
	private function execute_features(){

		WPWHPRO()->fields->execute();
		WPWHPRO()->integrations->execute();
		WPWHPRO()->logs->execute();
		WPWHPRO()->auth->execute();
		WPWHPRO()->data_mapping->execute();
		WPWHPRO()->extensions->execute();
		WPWHPRO()->whitelabel->execute();
		WPWHPRO()->flows->execute();
		WPWHPRO()->license->execute();
		WPWHPRO()->wizard->execute();
		WPWHPRO()->usage->execute();

	}

	/**
	 * Plugin action links.
	 *
	 * Adds action links to the plugin list table
	 *
	 * Fired by `plugin_action_links` filter.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $links An array of plugin action links.
	 *
	 * @return array An array of plugin action links.
	 */
	public function plugin_action_links( $links ) {
		$settings_link = sprintf( '<a href="%s">%s</a>', admin_url( 'options-general.php?page=' . $this->page_name ), WPWHPRO()->helpers->translate('Settings', 'plugin-page') );

		array_unshift( $links, $settings_link );

		$links['our_shop'] = sprintf( '<a href="%s" target="_blank" style="font-weight:700;color:#f1592a;">%s</a>', 'https://wp-webhooks.com/?utm_source=wp-webhooks-pro&utm_medium=plugin-overview-shop-button&utm_campaign=WP%20Webhooks%20Pro', WPWHPRO()->helpers->translate('Our Shop', 'plugin-page') );

		return $links;
	}

	/**
	 * Add footer information about our plugin
	 *
	 * @since 4.2.1
	 * @access public
	 *
	 * @param string The current footer text
	 *
	 * @return string Our footer text
	 */
	public function display_footer_information( $text ) {

		if( WPWHPRO()->helpers->is_page( $this->page_name ) ){
			$text = sprintf(
				WPWHPRO()->helpers->translate( '%1$s version %2$s', 'admin-footer-text' ),
				'<strong>' . $this->page_title . '</strong>',
				'<strong>' . WPWHPRO_VERSION . '</strong>'
			);
		}

		return $text;
	}

	/**
	 * ######################
	 * ###
	 * #### SCRIPTS & STYLES
	 * ###
	 * ######################
	 */

	/**
	 * Register all necessary scripts and styles
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts_and_styles() {
		if( WPWHPRO()->helpers->is_page( $this->page_name ) && is_admin() ) {
			$is_dev_mode = defined( 'WPWH_DEV' ) && WPWH_DEV === true;
			$is_flow = ( isset( $_GET['wpwhprovrs'] ) && $_GET['wpwhprovrs'] === 'flows' && isset( $_GET['flow_id'] ) ) ? true : false;
			$is_flows_main = ( isset( $_GET['wpwhprovrs'] ) && $_GET['wpwhprovrs'] === 'flows' && ! isset( $_GET['flow_id'] ) ) ? true : false;
			wp_enqueue_style( 'wpwhpro-google-fonts', 'https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;700&family=Poppins:wght@500&display=swap', array(), null );

			// wp_enqueue_style( 'wpwhpro-admin-styles-old', WPWHPRO_PLUGIN_URL . 'core/includes/assets/dist/css/styles.min.css', array(), WPWHPRO_VERSION, 'all' );

			if( $is_flow ){
				wp_enqueue_style( 'wpwhpro-codemirror', WPWHPRO_PLUGIN_URL . 'core/includes/assets/dist/css/codemirror' . ( $is_dev_mode ? '' : '.min' ) . '.css', array(), WPWHPRO_VERSION, 'all' );
				wp_enqueue_style( 'wpwhpro-sweetalert2', WPWHPRO_PLUGIN_URL . 'core/includes/assets/dist/css/sweetalert2.min.css', array(), WPWHPRO_VERSION, 'all' );
				wp_enqueue_style( 'wpwhpro-vue-select', WPWHPRO_PLUGIN_URL . 'core/includes/assets/dist/css/vue-select.css', array(), WPWHPRO_VERSION, 'all' );
			}

			wp_enqueue_style( 'wpwhpro-admin-styles', WPWHPRO_PLUGIN_URL . 'core/includes/assets/dist/css/admin-styles' . ( $is_dev_mode ? '' : '.min' ) . '.css', array(), WPWHPRO_VERSION, 'all' );

			wp_enqueue_script( 'jquery-ui-sortable');
			wp_enqueue_editor();
			wp_enqueue_media();

			if( $is_flow ){
				wp_enqueue_script( 'wpwhpro-flows-vendor', WPWHPRO_PLUGIN_URL . 'core/includes/assets/dist/js/flows-vendor' . ( $is_dev_mode ? '' : '.min' ) . '.js', array( 'jquery' ), WPWHPRO_VERSION, true );
				wp_enqueue_script( 'wpwhpro-flows', WPWHPRO_PLUGIN_URL . 'core/includes/assets/dist/js/flows' . ( $is_dev_mode ? '' : '.min' ) . '.js', array( 'jquery' ), WPWHPRO_VERSION, true, true );
			} else {
				wp_enqueue_script( 'wpwhpro-admin-vendors', WPWHPRO_PLUGIN_URL . 'core/includes/assets/dist/js/admin-vendor' . ( $is_dev_mode ? '' : '.min' ) . '.js', array( 'jquery' ), WPWHPRO_VERSION, true );
				wp_enqueue_script( 'wpwhpro-admin-scripts', WPWHPRO_PLUGIN_URL . 'core/includes/assets/dist/js/admin-scripts' . ( $is_dev_mode ? '' : '.min' ) . '.js', array( 'jquery' ), WPWHPRO_VERSION, true );
			}

			wp_localize_script( 'wpwhpro-admin-scripts', 'ironikus', array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( md5( $this->page_name ) ),
				'language' => '',
			));

			if( $is_flow ) {
				wp_localize_script( 'wpwhpro-flows', 'ironikusflows', array(
					'ajax_url'   => admin_url( 'admin-ajax.php' ),
					'ajax_nonce' => wp_create_nonce( md5( $this->page_name ) ),
					'plugin_url' => WPWHPRO_PLUGIN_URL,
					'language' => get_locale(),
				));
			}

			if( $is_flows_main ) {
				wp_localize_script( 'wpwhpro-admin-scripts', 'ironikusflows', array(
					'ajax_url'   => admin_url( 'admin-ajax.php' ),
					'ajax_nonce' => wp_create_nonce( md5( $this->page_name ) ),
					'plugin_url' => WPWHPRO_PLUGIN_URL,
					'language' => get_locale(),
				));
			}

			// wp_enqueue_script( 'wpwhpro-admin-scripts-old', WPWHPRO_PLUGIN_URL . 'core/includes/assets-old/dist/js/admin-scripts.js', array( 'jquery' ), WPWHPRO_VERSION, true );
		}
	}

	/**
	 * Register the bootstrap styling for posts on our own settings page
	 *
	 * @since    1.0.0
	 */
	public function throw_admin_notice_bootstrap( $bool ) {
		if( WPWHPRO()->helpers->is_page( $this->page_name ) && is_admin() ) {
			$bool = true;
		}

		return $bool;
	}

	/*
     * Functionality to save the main settings of the settings page
     */
	public function ironikus_save_main_settings(){

        if( ! is_admin() || ! WPWHPRO()->helpers->is_page( $this->page_name ) ){
			return;
		}

		if( ! isset( $_POST['wpwh_settings_submit'] ) ){
			return;
		}

		$settings_nonce_data = WPWHPRO()->settings->get_settings_nonce();

		if ( ! check_admin_referer( $settings_nonce_data['action'], $settings_nonce_data['arg'] ) ){
			return;
		}

		if( ! WPWHPRO()->helpers->current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwh-save-settings' ), 'wpwhpro-page-settings-save' ) ){
			return;
		}

		$current_url = WPWHPRO()->helpers->get_current_url();

		WPWHPRO()->settings->save_settings( $_POST );

		wp_redirect( $current_url );
		exit;

    }

	/**
	 * ######################
	 * ###
	 * #### AJAX
	 * ###
	 * ######################
	 */

    /*
     * Remove the action via ajax
     */
	public function ironikus_remove_webhook_action(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $webhook_group = isset( $_REQUEST['webhook_group'] ) ? sanitize_title( $_REQUEST['webhook_group'] ) : '';
        $webhook        = isset( $_REQUEST['webhook'] ) ? sanitize_title( $_REQUEST['webhook'] ) : '';
		$response       = array( 'success' => false );

		$check = WPWHPRO()->webhook->unset_hooks( $webhook, 'action', $webhook_group );
		if( $check ){
			$response['success'] = true;
		}

        echo json_encode( $response );
		die();
    }

    /*
     * Change the status of the action via ajax
     */
	public function ironikus_change_status_webhook_action(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $webhook        = isset( $_REQUEST['webhook'] ) ? sanitize_title( $_REQUEST['webhook'] ) : '';
        $webhook_group = isset( $_REQUEST['webhook_group'] ) ? sanitize_text_field( $_REQUEST['webhook_group'] ) : '';
        $webhook_status = isset( $_REQUEST['webhook_status'] ) ? sanitize_title( $_REQUEST['webhook_status'] ) : '';
        $webhook_type = isset( $_REQUEST['webhook_type'] ) ? sanitize_title( $_REQUEST['webhook_type'] ) : '';
		$response       = array( 'success' => false, 'new_status' => '', 'new_status_name' => '' );

		$new_status = null;
		$new_status_name = null;
		switch( $webhook_status ){
			case 'active':
				$new_status = 'inactive';
				$new_status_name = 'Inactive';
				break;
			case 'inactive':
				$new_status = 'active';
				$new_status_name = 'Active';
				break;
		}

		if( ! empty( $webhook ) ){

			if( $webhook_type === 'send' ){
				$check = WPWHPRO()->webhook->update( $webhook, 'trigger', $webhook_group, array(
					'status' => $new_status
				) );
			} else {
				$check = WPWHPRO()->webhook->update( $webhook, 'action', $webhook_group, array(
					'status' => $new_status
				) );
			}

			if( $check ){
				$response['success'] = true;
				$response['new_status'] = $new_status;
				$response['new_status_name'] = $new_status_name;
			}
		}

        echo json_encode( $response );
		die();
    }

    /*
     * Remove the trigger via ajax
     */
	public function ironikus_remove_webhook_trigger(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $webhook        = isset( $_REQUEST['webhook'] ) ? sanitize_title( $_REQUEST['webhook'] ) : '';
        $webhook_group  = isset( $_REQUEST['webhook_group'] ) ? sanitize_text_field( $_REQUEST['webhook_group'] ) : '';
		$webhooks       = WPWHPRO()->webhook->get_hooks( 'trigger', $webhook_group );
		$response       = array( 'success' => false );

		if( isset( $webhooks[ $webhook ] ) ){
			$check = WPWHPRO()->webhook->unset_hooks( $webhook, 'trigger', $webhook_group );
			if( $check ){
			    $response['success'] = true;
            }
		}


        echo json_encode( $response );
		die();
    }

    /*
     * Functionality to load all of the available demo webhook triggers
     */
	public function ironikus_test_webhook_trigger(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $webhook            = isset( $_REQUEST['webhook'] ) ? sanitize_title( $_REQUEST['webhook'] ) : '';
        $webhook_group      = isset( $_REQUEST['webhook_group'] ) ? sanitize_text_field( $_REQUEST['webhook_group'] ) : '';
        $webhook_callback   = isset( $_REQUEST['webhook_callback'] ) ? sanitize_text_field( $_REQUEST['webhook_callback'] ) : '';
		$webhooks           = WPWHPRO()->webhook->get_hooks( 'trigger', $webhook_group );
        $response           = array( 'success' => false );

		if( isset( $webhooks[ $webhook ] ) ){
			$data = WPWHPRO()->integrations->get_trigger_demo( $webhook_group, array(
				'webhook' => $webhook,
				'webhooks' => $webhooks,
				'webhook_group' => $webhook_group,
			) );

			if( ! empty( $webhook_callback ) ){
				$data = apply_filters( 'ironikus_demo_' . $webhook_callback, $data, $webhook, $webhook_group, $webhooks[ $webhook ] );
			}

			$response_data = WPWHPRO()->webhook->post_to_webhook( $webhooks[ $webhook ], $data, array( 'blocking' => true ), true );

			if ( ! empty( $response_data ) ) {
				$response['data']       = $response_data;
				$response['success']    = true;
			}
		}

        echo json_encode( $response );
		die();
    }

    /*
     * Functionality to load all of the available demo webhook triggers
     */
	public function ironikus_save_webhook_trigger_settings(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $webhook            = isset( $_REQUEST['webhook_id'] ) ? sanitize_title( $_REQUEST['webhook_id'] ) : '';
        $webhook_group      = isset( $_REQUEST['webhook_group'] ) ? sanitize_text_field( $_REQUEST['webhook_group'] ) : '';
		$trigger_settings   = ( isset( $_REQUEST['trigger_settings'] ) && ! empty( $_REQUEST['trigger_settings'] ) ) ? $_REQUEST['trigger_settings'] : '';
        $response           = array( 'success' => false );

		parse_str( $trigger_settings, $trigger_settings_data );

		if( ! empty( $webhook_group ) && ! empty( $webhook ) ){
		    $check = WPWHPRO()->webhook->update( $webhook, 'trigger', $webhook_group, array(
                'settings' => $trigger_settings_data
            ) );

		    if( ! empty( $check ) ){
		        $response['success'] = true;
            }
        }

        echo json_encode( $response );
		die();
    }

    /*
     * Functionality to save all available webhook actions
     */
	public function ironikus_save_webhook_action_settings(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $webhook            = isset( $_REQUEST['webhook_id'] ) ? sanitize_title( $_REQUEST['webhook_id'] ) : '';
		$webhook_group      = isset( $_REQUEST['webhook_group'] ) ? sanitize_text_field( $_REQUEST['webhook_group'] ) : '';
        $action_settings   = ( isset( $_REQUEST['action_settings'] ) && ! empty( $_REQUEST['action_settings'] ) ) ? $_REQUEST['action_settings'] : '';
        $response           = array( 'success' => false );

		parse_str( $action_settings, $action_settings_data );

		if( ! empty( $webhook ) ){
		    $check = WPWHPRO()->webhook->update( $webhook, 'action', $webhook_group, array(
                'settings' => $action_settings_data
            ) );

		    if( ! empty( $check ) ){
		        $response['success'] = true;
            }
        }

        echo json_encode( $response );
		die();
    }

    /*
     * Functionality to save all available webhook actions
     */
	public function wp_webhooks_validate_field_query(){
        check_ajax_referer( md5( $this->page_name ), 'ironikus_nonce' );

        $webhook_type = isset( $_REQUEST['webhook_type'] ) ? sanitize_title( $_REQUEST['webhook_type'] ) : '';
		$webhook_group      = isset( $_REQUEST['webhook_group'] ) ? sanitize_text_field( $_REQUEST['webhook_group'] ) : '';
        $webhook_integration   = ( isset( $_REQUEST['webhook_integration'] ) && ! empty( $_REQUEST['webhook_integration'] ) ) ? sanitize_title( $_REQUEST['webhook_integration'] ) : '';
        $webhook_field   = ( isset( $_REQUEST['webhook_field'] ) && ! empty( $_REQUEST['webhook_field'] ) ) ? sanitize_title( $_REQUEST['webhook_field'] ) : '';
        $field_search   = ( isset( $_REQUEST['field_search'] ) && ! empty( $_REQUEST['field_search'] ) ) ? esc_sql( $_REQUEST['field_search'] ) : '';
        $paged = ( isset( $_REQUEST['page'] ) && ! empty( $_REQUEST['page'] ) ) ? intval( $_REQUEST['page'] ) : 1;
        $selected = ( isset( $_REQUEST['selected'] ) && ! empty( $_REQUEST['selected'] ) ) ? $_REQUEST['selected'] : '';
        $response           = array(
			'success' => false,
			'data' => array(
				'total' => 0,
				'choices' => array(),
			)
		);
		$endpoint = null;

		if( ! empty( $webhook_type ) && ! empty( $webhook_group ) && ! empty( $webhook_integration ) && ! empty( $webhook_field ) ){
		    switch( $webhook_type ){
				case 'action':
					$endpoint = WPWHPRO()->integrations->get_actions( $webhook_integration, $webhook_group );
					break;
				case 'trigger':
					$endpoint = WPWHPRO()->integrations->get_triggers( $webhook_integration, $webhook_group );
					break;
			}

			if( ! empty( $endpoint ) ){

				if(
					isset( $endpoint[ $webhook_group ]['settings']['data'] )
					&& is_array( $endpoint[ $webhook_group ]['settings']['data'] )
					&& isset( $endpoint[ $webhook_group ]['settings']['data'][ $webhook_field ] )
				){
					$query_items = WPWHPRO()->fields->get_query_items( $endpoint[ $webhook_group ]['settings']['data'][ $webhook_field ], $args = array(
						's' => $field_search,
						'paged' => $paged,
						'selected' => $selected,
					) );

					$response['data']['total'] = $query_items['total'];
					$response['data']['per_page'] = $query_items['per_page'];
					$response['data']['item_count'] = $query_items['item_count'];

					if( ! empty( $query_items ) && is_array( $query_items ) && isset( $query_items['items'] ) ){
						$response['success'] = true;

						//validate items to make them compatible with select2
						foreach( $query_items['items'] as $item_name => $item_value ){

							if( ! is_array( $item_value ) || ! isset( $item_value['label'] ) ){
								continue;
							}

							$response['data']['choices'][] = array(
								'id' => $item_value['value'],
								'text' => $item_value['label'],
							);

						}

					}
				}

			}
        }

        echo json_encode( $response );
		die();
    }

	/**
	 * ######################
	 * ###
	 * #### MENU TEMPLATE ITEMS
	 * ###
	 * ######################
	 */

	/**
	 * Add our custom admin user page
	 */
	public function add_user_submenu(){
		$menu_position = get_option( 'wpwhpro_show_sub_menu' );

		if( ! empty( $menu_position ) && $menu_position == 'yes' ){
			add_submenu_page(
				'options-general.php',
				WPWHPRO()->helpers->translate( $this->page_title, 'admin-add-submenu-page-title' ),
				WPWHPRO()->helpers->translate( $this->page_title, 'admin-add-submenu-page-site-title' ),
				WPWHPRO()->settings->get_admin_cap( 'admin-add-submenu-page-item' ),
				$this->page_name,
				array( $this, 'render_admin_submenu_page' )
			);
		} else {
			add_menu_page(
				WPWHPRO()->helpers->translate( $this->page_title, 'admin-add-menu-page-title' ),
				WPWHPRO()->helpers->translate( $this->page_title, 'admin-add-menu-page-site-title' ),
				WPWHPRO()->settings->get_admin_cap( 'admin-add-menu-page-item' ),
				$this->page_name,
				array( $this, 'render_admin_submenu_page' ) ,
				WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/logo-menu-wp-webhooks.svg',
				'81.025'
			);

			/**
			 * Originally called within /core/includes/partials/wpwhpro-page-display.php,
			 * but used here to re-validate the available menu items dynamically
			 */
			$menu_endpoints = apply_filters( 'wpwhpro/admin/settings/menu_data', array() );
			if( is_array( $menu_endpoints ) && ! empty( $menu_endpoints ) ){
				foreach( $menu_endpoints as $endpoint_slug => $endpoint_data ){

					//Skip the whitelabel tab
					if( $endpoint_slug === 'whitelabel' ){
						continue;
					}

					$sub_page_title = ( is_array( $endpoint_data ) ) ? $endpoint_data['label'] : $endpoint_data;

					add_submenu_page(
						$this->page_name,
						WPWHPRO()->helpers->translate( $sub_page_title, 'admin-add-submenu-page-title' ),
						WPWHPRO()->helpers->translate( $sub_page_title, 'admin-add-submenu-page-site-title' ),
						WPWHPRO()->settings->get_admin_cap( 'admin-add-submenu-page-item' ),
						$this->page_name . '-' . sanitize_title( $endpoint_slug ),
						array( $this, 'render_admin_submenu_page' )
					);
				}
			}

			//Remove its duplicate sub menu item
			remove_submenu_page( $this->page_name, $this->page_name);
		}

	}

	/**
	 * Mark our dynamic sub menu item as active
	 *
	 * @param string $submenu_file
	 * @param string $parent_file
	 * @return string The submenu item in case given
	 */
	public function filter_active_wpwh_submenu_page( $submenu_file, $parent_file ){

		if( $parent_file === $this->page_name ){
			if( isset( $_REQUEST['wpwhprovrs'] ) && ! empty( $_REQUEST['wpwhprovrs'] ) ){

				$sub_menu_slug = $_REQUEST['wpwhprovrs'];

				/**
				 * Originally called within /core/includes/partials/wpwhpro-page-display.php,
				 * but used here to re-validate the available menu items dynamically
				 */
				$menu_endpoints = apply_filters( 'wpwhpro/admin/settings/menu_data', array() );
				if( is_array( $menu_endpoints ) && ! empty( $menu_endpoints ) ){

					//Set the parent slug in case a child item is given
					if( ! isset( $menu_endpoints[ $sub_menu_slug ] ) ){
						foreach( $menu_endpoints as $endpoint_slug => $endpoint_data ){

							// Skip non sub menus
							if( ! isset( $endpoint_data['items'] ) ){
								continue;
							}

							if( isset( $endpoint_data['items'][ $sub_menu_slug ] ) ){
								$sub_menu_slug = $endpoint_slug;
							}

						}
					}

				}

				$submenu_file = $this->page_name . '-' . sanitize_title( $sub_menu_slug );
			}
		}

		return $submenu_file;
	}

	/**
	 * Maybe redirect a menu item from a submenu URL
	 *
	 * @since 5.0
	 *
	 * @return void
	 */
	public function maybe_redirect_wpwh_submenu_items(){

		if( ! isset( $_GET['page'] ) ){
			return;
		}

		//shorten the circle if nothing was set.
		if( isset( $_GET['wpwhprovrs'] ) ){
			return;
		}

		$page = $_GET['page'];
		$ident = $this->page_name;

		//Only redirect if it differs
		if( $ident === $page ){
			return;
		}

		if( strlen( $page ) < strlen( $ident ) ){
			return;
		}

		if( substr( $page, 0, strlen( $ident ) ) !== $ident ){
			return;
		}

		$page_slug = str_replace( $this->page_name, '', $page );

		$url = WPWHPRO()->helpers->get_current_url( false );
		$redirect_uri = WPWHPRO()->helpers->built_url( $url, array(
			'page' => $this->page_name,
			'wpwhprovrs' => sanitize_title( $page_slug ),
		) );

		wp_redirect( $redirect_uri );
		exit;

	}

	/**
	 * Render the admin submenu page
	 *
	 * You need the specified capability to edit it.
	 */
	public function render_admin_submenu_page(){
		if( ! current_user_can( WPWHPRO()->settings->get_admin_cap('admin-submenu-page') ) ){
			wp_die( WPWHPRO()->helpers->translate( WPWHPRO()->settings->get_default_string( 'sufficient-permissions' ), 'admin-submenu-page-sufficient-permissions' ) );
		}

		$wpwh_page = WPWHPRO_PLUGIN_DIR . 'core/includes/partials/wpwhpro-page-display.php';

		/*
		 * Filter the core display page
		 *
		 * @param $wpwh_page The page template
		 */
		$wpwh_page = apply_filters( 'wpwhpro/admin/page_template_file', $wpwh_page );

		if( file_exists( $wpwh_page ) ){
			include( $wpwh_page );
		}

	}

	/**
	 * Register all of our default tabs to our plugin page
	 *
	 * @param $tabs - The previous tabs
	 *
	 * @return array - Return the array of all available tabs
	 */
	public function add_main_settings_tabs( $tabs ){

		$tabs['home']           = WPWHPRO()->helpers->translate( 'Home', 'admin-menu' );

		if( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_flows' ) !== 'yes' ){
			$tabs['flows'] = array(
				'label' => WPWHPRO()->helpers->translate( 'Automations (Flows)', 'admin-menu' ),
				'title' => WPWHPRO()->helpers->translate( 'An automated workflow that can fire multiple actions consecutively.', 'admin-menu' ),
			);
		}

		$tabs['send-data']      = array(
			'label' => WPWHPRO()->helpers->translate( 'Webhooks', 'admin-menu' ),
			'title' => WPWHPRO()->helpers->translate( 'A single trigger or action that can receive or sent data from/to a single source.', 'admin-menu' ),
			'items' => array(
				'send-data'  	=> WPWHPRO()->helpers->translate( 'Send Data (Triggers)', 'admin-menu' ),
				'receive-data'  	=> WPWHPRO()->helpers->translate( 'Receive Data (Actions)', 'admin-menu' ),
			)
		);

		$tabs['features']   = array(
			'label' => WPWHPRO()->helpers->translate( 'Features', 'admin-menu' ),
			'items' => array(),
		);

		if( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_authentication' ) !== 'yes' ){
			$tabs['features']['items']['authentication'] = WPWHPRO()->helpers->translate( 'Authentication', 'admin-menu' );
		}

		if( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_data_mapping' ) !== 'yes' ){
			$tabs['features']['items']['data-mapping'] = WPWHPRO()->helpers->translate( 'Data Mapping', 'admin-menu' );
		}

		if( WPWHPRO()->whitelist->is_active() ){
			if( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_ip_whitelist' ) !== 'yes' ){
				$tabs['features']['items']['whitelist']  = WPWHPRO()->helpers->translate( 'IP Whitelist', 'admin-menu' );
			}
		}

		if( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_logs' ) !== 'yes' ){
			$tabs['features']['items']['logs']  = WPWHPRO()->helpers->translate( 'Logs', 'admin-menu' );
		}

		if( isset( $_GET['wpwh_whitelabel_settings'] ) && $_GET['wpwh_whitelabel_settings'] === 'visible' ){
			$tabs['whitelabel']  = WPWHPRO()->helpers->translate( 'Whitelabel', 'admin-menu' );
		}

		if( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_settings' ) !== 'yes' ){
			$tabs['settings']   = array(
				'label' => WPWHPRO()->helpers->translate( 'Settings', 'admin-menu' ),
				'items' => array(
					'settings'  		=> WPWHPRO()->helpers->translate( 'All Settings', 'admin-menu' ),
				),
			);
		}

		if( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_extensions' ) !== 'yes' ){

			if( isset( $tabs['settings'] ) && is_array( $tabs['settings'] ) && isset( $tabs['settings']['items'] ) ){
				$tabs['settings']['items']['extensions'] = WPWHPRO()->helpers->translate( 'Extensions', 'admin-menu' );
			} else {
				$tabs['extensions'] = WPWHPRO()->helpers->translate( 'Extensions', 'admin-menu' );
			}

		}

		if( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_tools' ) !== 'yes' ){

			if( isset( $tabs['settings'] ) && is_array( $tabs['settings'] ) && isset( $tabs['settings']['items'] ) ){
				$tabs['settings']['items']['tools'] = WPWHPRO()->helpers->translate( 'Tools', 'admin-menu' );
			} else {
				$tabs['tools'] = WPWHPRO()->helpers->translate( 'Tools', 'admin-menu' );
			}

		}

		if( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_licensing' ) !== 'yes' ){

			if( isset( $tabs['settings'] ) && is_array( $tabs['settings'] ) && isset( $tabs['settings']['items'] ) ){
				$tabs['settings']['items']['license'] = WPWHPRO()->helpers->translate( 'License', 'admin-menu' );
			} else {
				$tabs['license'] = WPWHPRO()->helpers->translate( 'License', 'admin-menu' );
			}

		}

		//Remove the features tab if no child items are available
		if( empty( $tabs['features']['items'] ) ){
			unset( $tabs['features'] );
		}

		return $tabs;

	}

	/**
	 * Load the content for our plugin page based on a specific tab
	 *
	 * @param $tab - The currently active tab
	 */
	public function add_main_settings_content( $tab ){

		switch($tab){
			case 'license':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/license.php' );
				break;
			case 'send-data':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/send-data.php' );
				break;
			case 'recieve-data': // Keep it backwards compatible
			case 'receive-data':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/receive-data.php' );
				break;
			case 'settings':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/settings.php' );
				break;
			case 'whitelist':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/whitelist.php' );
				break;
			case 'flows':
				if( isset( $_GET['flow_id'] ) && current_user_can( WPWHPRO()->settings->get_admin_cap( 'flow-edit-single' ) ) ){

					$flow_id = intval( $_GET['flow_id'] );

					include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/flows-single.php' );
				} else {
					include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/flows.php' );
				}
				break;
			case 'logs':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/logs.php' );
				break;
			case 'data-mapping':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/data-mapping.php' );
				break;
			case 'authentication':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/authentication.php' );
				break;
			case 'extensions':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/extensions.php' );
				break;
			case 'whitelabel':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/whitelabel.php' );
				break;
			case 'home':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/home.php' );
				break;
			case 'flows-add-new':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/flows/add-new.php' );
				break;
			case 'flows-add-new-trigger':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/flows/add-new-trigger.php' );
				break;
			case 'features':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/features.php' );
				break;
			case 'tools':
				include( WPWHPRO_PLUGIN_DIR . 'core/includes/partials/tabs/tools.php' );
				break;
		}

	}

	/**
	 * ######################
	 * ###
	 * #### SETTINGS EXTENSIONS
	 * ###
	 * ######################
	 */

	/*
	 * Reset the settings and webhook data
	 */
	public function reset_wpwhpro_data(){

	    if( ! is_admin() || ! is_user_logged_in() ){
	        return;
        }

		$current_url_full = WPWHPRO()->helpers->get_current_url();
		$reset_all = get_option( 'wpwhpro_reset_data' );
		if( $reset_all && $reset_all === 'yes' ){
			delete_option( 'wpwhpro_reset_data' );

			WPWHPRO()->webhook->reset_wpwhpro();

			wp_redirect( $current_url_full );
			die();
		}
    }

}
