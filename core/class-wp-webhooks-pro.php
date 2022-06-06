<?php
if ( ! class_exists( 'WP_Webhooks_Pro' ) ) :

	/**
	 * Main WP_Webhooks_Pro Class.
	 *
	 * @since 1.0.0
	 * @package WPWHPRO
	 * @author Ironikus <info@ironikus.com>
	 */
	final class WP_Webhooks_Pro {

		/**
		 * The real instance
		 *
		 * @var WP_Webhooks_Pro
		 * @since 1.0.0
		 */
		private static $instance;

		/**
		 * WPWHPRO updater Object.
		 *
		 * @var object|Ironikus_Webhook_Pro_Updater
		 * @since 1.0.0
		 */
		public $updater;

		/**
		 * WPWHPRO helpers Object.
		 *
		 * @var object|WP_Webhooks_Pro_Helpers
		 * @since 1.0.0
		 */
		public $helpers;

		/**
		 * WPWHPRO Fields Object.
		 *
		 * @var object|WP_Webhooks_Pro_Fields
		 * @since 5.2.0
		 */
		public $fields;

		/**
		 * WPWHPRO settings Object.
		 *
		 * @var object|WP_Webhooks_Pro_Settings
		 * @since 1.0.0
		 */
		public $settings;

		/**
		 * WPWHPRO helpers Object.
		 *
		 * @var object|WP_Webhooks_Pro_License
		 * @since 1.0.0
		 */
		public $license;

		/**
		 * WPWHPRO SQL Object.
		 *
		 * @var object|WP_Webhooks_Pro_SQL
		 * @since 1.6.3
		 */
		public $sql;

		/**
		 * WPWHPRO Async Object.
		 *
		 * @var object|WP_Webhooks_Pro_Async
		 * @since 4.3.0
		 */
		public $async;

		/**
		 * WPWHPRO Log Object.
		 *
		 * @var object|WP_Webhooks_Pro_Logs
		 * @since 1.6.3
		 */
		public $logs;

		/**
		 * WPWHPRO API Object.
		 *
		 * @var object|WP_Webhooks_Pro_API
		 * @since 1.0.0
		 */
		public $api;

		/**
		 * WPWHPRO HTTP Object.
		 *
		 * @var object|WP_Webhooks_Pro_HTTP
		 * @since 5.0
		 */
		public $http;

		/**
		 * WPWHPRO Webhook Object.
		 *
		 * @var object|WP_Webhooks_Pro_Webhook
		 * @since 1.0.0
		 */
		public $webhook;

		/**
		 * WPWHPRO Integrations Object.
		 *
		 * @var object|WP_Webhooks_Pro_Integrations
		 * @since 4.2.0
		 */
		public $integrations;

		/**
		 * WPWHPRO Extensions Object.
		 *
		 * @var object|WP_Webhooks_Pro_Extensions
		 * @since 4.2.3
		 */
		public $extensions;

		/**
		 * WPWHPRO Whitelist Object.
		 *
		 * @var object|WP_Webhooks_Pro_Whitelist
		 * @since 1.0.0
		 */
		public $whitelist;

		/**
		 * WPWHPRO Whitelabel Object.
		 *
		 * @var object|WP_Webhooks_Pro_Whitelabel
		 * @since 3.0.6
		 */
		public $whitelabel;

		/**
		 * WPWHPRO Polling Object.
		 *
		 * @var object|WP_Webhooks_Pro_Polling
		 * @since 2.1.2
		 */
		public $polling;

		/**
		 * WPWHPRO Data Mapping Object.
		 *
		 * @var object|WP_Webhooks_Pro_Data_Mapping
		 * @since 2.0.0
		 */
		public $data_mapping;

		/**
		 * WPWHPRO Post Delay Object.
		 *
		 * @var object|WP_Webhooks_Pro_Post_Delay
		 * @since 2.1.4
		 */
		public $delay;

		/**
		 * WPWHPRO Authentication Object.
		 *
		 * @var object|WP_Webhooks_Pro_Authentication
		 * @since 3.0.0
		 */
		public $auth;

		/**
		 * WPWHPRO Flows Object.
		 *
		 * @var object|WP_Webhooks_Pro_flows
		 * @since 4.3.0
		 */
		public $flows;

		/**
		 * WPWHPRO Advanced Custom Fields Object.
		 *
		 * @var object|WP_Webhooks_Pro_ACF
		 * @since 3.0.8
		 */
		public $acf;

		/**
		 * WPWHPRO Tools Object.
		 *
		 * @var object|WP_Webhooks_Pro_Tools
		 * @since 5.0
		 */
		public $tools;

		/**
		 * WPWHPRO Wizard Object.
		 *
		 * @var object|WP_Webhooks_Pro_Wizard
		 * @since 5.0
		 */
		public $wizard;

		/**
		 * WPWHPRO System Report Object.
		 *
		 * @var object|WP_Webhooks_Pro_System_Report
		 * @since 5.0
		 */
		public $system;

		/**
		 * WPWHPRO Usage Report Object.
		 *
		 * @var object|WP_Webhooks_Pro_Usage_Report
		 * @since 5.0
		 */
		public $usage;

		/**
		 * Throw error on object clone.
		 *
		 * Cloning instances of the class is forbidden.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'ironikus' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'ironikus' ), '1.0.0' );
		}

		/**
		 * Main WP_Webhooks_Pro Instance.
		 *
		 * Insures that only one instance of WP_Webhooks_Pro exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 * @static
		 * @staticvar array $instance
		 * @return object|WP_Webhooks_Pro The one true WP_Webhooks_Pro
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WP_Webhooks_Pro ) ) {
				self::$instance                 = new WP_Webhooks_Pro;
				self::$instance->base_hooks();
				self::$instance->includes();
				self::$instance->helpers        = new WP_Webhooks_Pro_Helpers();
				self::$instance->fields         = new WP_Webhooks_Pro_Fields();
				self::$instance->settings       = new WP_Webhooks_Pro_Settings();
				self::$instance->sql            = new WP_Webhooks_Pro_SQL();
				self::$instance->async          = new WP_Webhooks_Pro_Async();
				self::$instance->logs           = new WP_Webhooks_Pro_Logs();
				self::$instance->data_mapping   = new WP_Webhooks_Pro_Data_Mapping();
				self::$instance->delay			= new WP_Webhooks_Pro_Post_Delay();
				self::$instance->auth			= new WP_Webhooks_Pro_Authentication();
				self::$instance->api            = new WP_Webhooks_Pro_API();
				self::$instance->license        = new WP_Webhooks_Pro_License();
				self::$instance->http        	= new WP_Webhooks_Pro_HTTP();
				self::$instance->webhook        = new WP_Webhooks_Pro_Webhook();
				self::$instance->integrations   = new WP_Webhooks_Pro_Integrations();
				self::$instance->extensions		= new WP_Webhooks_Pro_Extensions();
				self::$instance->whitelist      = new WP_Webhooks_Pro_Whitelist();
				self::$instance->whitelabel     = new WP_Webhooks_Pro_Whitelabel();
				self::$instance->polling      	= new WP_Webhooks_Pro_Polling();
				self::$instance->flows      	= new WP_Webhooks_Pro_flows();
				self::$instance->acf      		= new WP_Webhooks_Pro_ACF();
				self::$instance->tools      	= new WP_Webhooks_Pro_Tools();
				self::$instance->wizard      	= new WP_Webhooks_Pro_Wizard();
				self::$instance->system      	= new WP_Webhooks_Pro_System_Report();
				self::$instance->usage      	= new WP_Webhooks_Pro_Usage_Report();
				self::$instance->load_updater( WPWHPRO_PLUGIN_FILE, array(
						'version' => WPWHPRO_VERSION,
						'item_id' => WPWHPRO_PLUGIN_ID
					)
				);

				/**
				 * Used to launch our integrations
				 */
				do_action( 'wpwh_plugin_configured' );

				//Run plugin
				new WP_Webhooks_Pro_Run();

				//Schedule our daily maintenance event
				if( ! wp_next_scheduled( 'wpwh_daily_maintenance' ) ){
					wp_schedule_event( time(), 'daily', 'wpwh_daily_maintenance' );
				}

				/**
				 * Fire a custom action to allow extensions to register
				 * after WP Webhooks Pro was successfully registered
				 */
				do_action( 'wpwhpro_plugin_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Include required files.
		 *
		 * @access private
		 * @since 1.0.0
		 * @return void
		 */
		private function includes() {

			//Libraries
			if( ! class_exists( 'WP_Async_Request' ) ){
				require_once WPWHPRO_PLUGIN_DIR . 'core/includes/libraries/wp-background-processing/wp-async-request.php';
			}
			if( ! class_exists( 'WP_Background_Process' ) ){
				require_once WPWHPRO_PLUGIN_DIR . 'core/includes/libraries/wp-background-processing/wp-background-process.php';
			}

			//Core
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-helpers.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-fields.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-settings.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-sql.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-async.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-async-process.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-logs.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-data-mapping.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-post-delay.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-auth.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-whitelist.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-whitelabel.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-api.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-http.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-webhook.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-integrations.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-extensions.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-updater.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-polling.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-license.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-flows.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-acf.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-tools.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-wizard.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-system-report.php';
			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-usage-report.php';

			require_once WPWHPRO_PLUGIN_DIR . 'core/includes/classes/class-wp-webhooks-pro-run.php';
		}

		/**
		 * Add base hooks for the core functionality
		 *
		 * @access private
		 * @since 1.0.0
		 * @return void
		 */
		private function base_hooks() {
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
			register_deactivation_hook( WPWHPRO_PLUGIN_FILE, array( self::$instance, 'register_deactivation_hook_callback' ) );
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access public
		 * @since 1.0.0
		 * @return void
		 */
		public function load_textdomain() {
			load_plugin_textdomain( WPWHPRO_TEXTDOMAIN, FALSE, dirname( plugin_basename( WPWHPRO_PLUGIN_FILE ) ) . '/language/' );
		}

		/**
		 * Register the plugin deactivation callback
		 * 
		 * This function is called on plugin deactivation
		 *
		 * @access public
		 * @since 4.3.3
		 * @return void
		 */
		public function register_deactivation_hook_callback() {
			//Remove wpwh_daily_maintenance cron job
			$next_event = wp_next_scheduled( 'wpwh_daily_maintenance' );

			if( $next_event ){
				wp_unschedule_event( $next_event, 'wpwh_daily_maintenance' );
			}
		}

		/**
		 * Plugin Updater class for external extension (shop related)
		 *
		 * The following values should always get defined from the plugin
		 * that loads the updater class:
		 * 1. version
		 * 2. item_id
		 *
		 * @param $plugin_file - The plugin file ( __FILE__ )
		 * @param $settings - An array of the given settings
		 * @access public
		 * @since 1.5.5
		 * @return void
		 */
		public function load_updater( $plugin_file, $settings = array() ) {
			$default_args = array(
				'version'   => '1.0.0',
				'item_id'   => 0,
				'license'   => trim( WPWHPRO()->settings->get_license('key') ),
				'author'    => 'Ironikus',
				'url'       => home_url()
			);

			$settings = array_merge( $default_args, $settings );

			new Ironikus_Webhook_Pro_Updater( IRONIKUS_STORE, $plugin_file, $settings );
		}

	}

endif; // End if class_exists check.