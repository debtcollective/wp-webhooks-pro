<?php

/**
 * WP_Webhooks_Pro_System_Report Class
 *
 * @since 5.0
 */

/**
 * The reports class of the plugin.
 *
 * @since 5.0
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_System_Report {

	public function generate_report(){

		$report_data = array(
			'site_ident'         => WPWHPRO()->settings->get_site_ident(),
			'environment'        => $this->get_environment_info(),
			'database'           => $this->get_database_info(),
			'active_plugins'     => $this->get_active_plugins(),
			'inactive_plugins'   => $this->get_inactive_plugins(),
			'dropins_mu_plugins' => $this->get_dropins_mu_plugins(),
			'theme'              => $this->get_theme_info(),
			'wpwhsettings'           => $this->get_settings(),
			'security'           => $this->get_security_info(),
		);
		
		return $report_data;
	}

	public function get_environment_info() {

		$curl_version = '';
		if( function_exists( 'curl_version' ) ){
			$curl_version = curl_version();
			$curl_version = $curl_version['version'] . ', ' . $curl_version['ssl_version'];
		} elseif( extension_loaded( 'curl' ) ){
			$curl_version = WPWHPRO()->helpers->translate( 'cURL installed but unable to retrieve version.', 'wpwh-system-report' );
		}

		$wp_memory_limit = 0;
		if( defined( 'WP_MEMORY_LIMIT' ) ){
			$wp_memory_limit = $this->phpini_to_num( WP_MEMORY_LIMIT );
			if( function_exists( 'memory_get_usage' ) ){
				$wp_memory_limit = max( $wp_memory_limit, $this->phpini_to_num( @ini_get( 'memory_limit' ) ) );
			}
		}
		

		//Perform a POST request to our site for testing
		$post_response_code = get_transient( 'wpwh_system_report_test_remote_post' );
		if( false === $post_response_code || is_wp_error( $post_response_code ) ){
			$response = wp_safe_remote_post(
				'https://status.wp-webhooks.com/?wpwhcheck=yes',
				array(
					'timeout'     => 5,
					'user-agent'  => 'WPWHPRO/' . WPWHPRO_VERSION,
					'httpversion' => '1.1',
					'body'        => array(),
				)
			);
			if( ! is_wp_error( $response ) ){
				$post_response_code = $response['response']['code'];
			}
			set_transient( 'wpwh_system_report_test_remote_post', $post_response_code, HOUR_IN_SECONDS );
		}

		$post_response_successful = ! is_wp_error( $post_response_code ) && $post_response_code >= 200 && $post_response_code < 300;

		//Perform a GET requests.
		$get_response_code = get_transient( 'wpwh_system_report_test_remote_get' );

			if( false === $get_response_code || is_wp_error( $get_response_code ) ){
				$response = wp_safe_remote_get( 'https://status.wp-webhooks.com/?wpwhcheck=yes&is_multisite=' . ( is_multisite() ? '1' : '0' ) );
				if( ! is_wp_error( $response ) ) {
					$get_response_code = $response['response']['code'];
				}
				set_transient( 'wpwh_system_report_test_remote_get', $get_response_code, HOUR_IN_SECONDS );
			}

			$get_response_successful = ! is_wp_error( $get_response_code ) && $get_response_code >= 200 && $get_response_code < 300;

		$database_version = $this->get_server_database_version();

		// Return all environment info. Described by JSON Schema.
		return array(
			'home_url'                  => get_option( 'home' ),
			'site_url'                  => get_option( 'siteurl' ),
			'version'                   => WPWHPRO_VERSION,
			'wp_version'                => get_bloginfo( 'version' ),
			'wp_multisite'              => is_multisite(),
			'wp_memory_limit'           => $wp_memory_limit,
			'wp_debug_mode'             => ( defined( 'WP_DEBUG' ) && WP_DEBUG ),
			'wp_cron'                   => ! ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ),
			'language'                  => get_locale(),
			'external_object_cache'     => wp_using_ext_object_cache(),
			'server_info'               => isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '',
			'php_version'               => phpversion(),
			'php_post_max_size'         => $this->phpini_to_num( ini_get( 'post_max_size' ) ),
			'php_max_execution_time'    => (int) ini_get( 'max_execution_time' ),
			'php_max_input_vars'        => (int) ini_get( 'max_input_vars' ),
			'curl_version'              => $curl_version,
			'suhosin_installed'         => extension_loaded( 'suhosin' ),
			'max_upload_size'           => wp_max_upload_size(),
			'mysql_version'             => $database_version['number'],
			'mysql_version_string'      => $database_version['string'],
			'default_timezone'          => date_default_timezone_get(),
			'fsockopen_or_curl_enabled' => ( function_exists( 'fsockopen' ) || function_exists( 'curl_init' ) ),
			'soapclient_enabled'        => class_exists( 'SoapClient' ),
			'domdocument_enabled'       => class_exists( 'DOMDocument' ),
			'gzip_enabled'              => is_callable( 'gzopen' ),
			'mbstring_enabled'          => extension_loaded( 'mbstring' ),
			'remote_post_successful'    => $post_response_successful,
			'remote_post_response'      => is_wp_error( $post_response_code ) ? $post_response_code->get_error_message() : $post_response_code,
			'remote_get_successful'     => $get_response_successful,
			'remote_get_response'       => is_wp_error( $get_response_code ) ? $get_response_code->get_error_message() : $get_response_code,
		);
	}

	public function phpini_to_num( $size ) {

		$l   = substr( $size, -1 );
		$ret = (int) substr( $size, 0, -1 );
		switch ( strtoupper( $l ) ) {
			case 'P':
				$ret *= 1024;
			case 'T':
				$ret *= 1024;
			case 'G':
				$ret *= 1024;
			case 'M':
				$ret *= 1024;
			case 'K':
				$ret *= 1024;
		}

		return $ret;
	}

	public function get_server_database_version(){
		global $wpdb;
	
		if( empty( $wpdb->is_mysql ) ){
			return array(
				'string' => '',
				'number' => '',
			);
		}
	
		if( $wpdb->use_mysqli ){
			$server_info = mysqli_get_server_info( $wpdb->dbh );
		} else {
			$server_info = mysql_get_server_info( $wpdb->dbh );
		}
		
		return array(
			'string' => $server_info,
			'number' => preg_replace( '/([^\d.]+).*/', '', $server_info ),
		);
	}

	public function get_database_info() {
		global $wpdb;

		$tables        = array();
		$database_size = array();

		// It is not possible to get the database name from some classes that replace wpdb (e.g., HyperDB)
		// and that is why this if condition is needed.
		if( defined( 'DB_NAME' ) ){
			$database_table_information = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT
					    table_name AS 'name',
						engine AS 'engine',
					    round( ( data_length / 1024 / 1024 ), 2 ) 'data',
					    round( ( index_length / 1024 / 1024 ), 2 ) 'index'
					FROM information_schema.TABLES
					WHERE table_schema = %s
					ORDER BY name ASC;",
					DB_NAME
				)
			);

			// WP Webhooks Core tables to check existence of.
			$core_tables = apply_filters(
				'wpwh_database_tables',
				array(
					'wpwhpro_data_mapping',
					'wpwhpro_authentication',
					'wpwhpro_flows',
					'wpwhpro_flows_logs',
					'wpwhpro_logs',
				)
			);

			/**
			 * Adding the prefix to the tables array, for backwards compatibility.
			 *
			 * If we changed the tables above to include the prefix, then any filters against that table could break.
			 */
			foreach( $core_tables as $table_key => $table_value ){
				$core_tables[ $table_key ] = $wpdb->prefix . $table_value;
			}

			/**
			 * Organize WP Webhooks and non-WP Webhooks tables separately for display purposes later.
			 *
			 * To ensure we include all Wocoommerce tables, even if they do not exist, pre-populate the WP Webhooks array with all the tables.
			 */
			$tables = array(
				'wpwh' => array_fill_keys( $core_tables, false ),
				'other'       => array(),
			);

			$database_size = array(
				'data'  => 0,
				'index' => 0,
			);

			$site_tables_prefix = $wpdb->get_blog_prefix( get_current_blog_id() );
			$global_tables      = $wpdb->tables( 'global', true );
			foreach ( $database_table_information as $table ) {
				// Only include tables matching the prefix of the current site, this is to prevent displaying all tables on a MS install not relating to the current.
				if ( is_multisite() && 0 !== strpos( $table->name, $site_tables_prefix ) && ! in_array( $table->name, $global_tables, true ) ) {
					continue;
				}
				$table_type = in_array( $table->name, $core_tables, true ) ? 'wpwh' : 'other';

				$tables[ $table_type ][ $table->name ] = array(
					'data'   => $table->data,
					'index'  => $table->index,
					'engine' => $table->engine,
				);

				$database_size['data']  += $table->data;
				$database_size['index'] += $table->index;
			}
		}

		// Return all database info. Described by JSON Schema.
		return array(
			'database_prefix'        => $wpdb->prefix,
			'database_tables'        => $tables,
			'database_size'          => $database_size,
		);
	}

	public function get_active_plugins() {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		if( ! function_exists( 'get_plugin_data' ) ){
			return array();
		}

		$active_plugins = (array) get_option( 'active_plugins', array() );
		if( is_multisite() ){
			$network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
			$active_plugins            = array_merge( $active_plugins, $network_activated_plugins );
		}

		$active_plugins_data = array();

		foreach( $active_plugins as $plugin ){
			$data                  = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
			$active_plugins_data[] = $this->format_plugin_data( $plugin, $data );
		}

		return $active_plugins_data;
	}

	public function get_inactive_plugins() {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		if( ! function_exists( 'get_plugins' ) ){
			return array();
		}

		$plugins        = get_plugins();
		$active_plugins = (array) get_option( 'active_plugins', array() );

		if( is_multisite() ){
			$network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
			$active_plugins            = array_merge( $active_plugins, $network_activated_plugins );
		}

		$plugins_data = array();

		foreach( $plugins as $plugin => $data ){
			if( in_array( $plugin, $active_plugins, true ) ){
				continue;
			}
			$plugins_data[] = $this->format_plugin_data( $plugin, $data );
		}

		return $plugins_data;
	}

	private function format_plugin_data( $plugin, $data ) {
		require_once ABSPATH . 'wp-admin/includes/update.php';

		if ( function_exists( 'get_plugin_updates' ) ) {
			// Use WP API to lookup latest updates for plugins.
			if ( empty( $this->available_updates ) ) {
				$this->available_updates = get_plugin_updates();
			}

			$version_latest = $data['Version'];

			// Find latest version.
			if( isset( $this->available_updates[ $plugin ]->update->new_version ) ){
				$version_latest = $this->available_updates[ $plugin ]->update->new_version;
			}
		} else {
			$version_latest = $data['Version'];
		}

		return array(
			'plugin'            => $plugin,
			'name'              => $data['Name'],
			'version'           => $data['Version'],
			'version_latest'    => $version_latest,
			'url'               => $data['PluginURI'],
			'author_name'       => $data['AuthorName'],
			'author_url'        => esc_url_raw( $data['AuthorURI'] ),
			'network_activated' => $data['Network'],
		);
	}

	public function get_dropins_mu_plugins() {
		$dropins = get_dropins();
		$plugins = array(
			'dropins'    => array(),
			'mu_plugins' => array(),
		);
		foreach ( $dropins as $key => $dropin ) {
			$plugins['dropins'][] = array(
				'plugin' => $key,
				'name'   => $dropin['Name'],
			);
		}

		$mu_plugins = get_mu_plugins();
		foreach ( $mu_plugins as $plugin => $mu_plugin ) {
			$plugins['mu_plugins'][] = array(
				'plugin'      => $plugin,
				'name'        => $mu_plugin['Name'],
				'version'     => $mu_plugin['Version'],
				'url'         => $mu_plugin['PluginURI'],
				'author_name' => $mu_plugin['AuthorName'],
				'author_url'  => esc_url_raw( $mu_plugin['AuthorURI'] ),
			);
		}
		return $plugins;
	}

	public function get_theme_info() {
		$active_theme = wp_get_theme();

		// Get parent theme info if this theme is a child theme, otherwise
		// pass empty info in the response.
		if ( is_child_theme() ) {
			$parent_theme      = wp_get_theme( $active_theme->template );
			$parent_theme_info = array(
				'parent_name'           => $parent_theme->name,
				'parent_version'        => $parent_theme->version,
				'parent_author_url'     => $parent_theme->{'Author URI'},
			);
		} else {
			$parent_theme_info = array(
				'parent_name'           => '',
				'parent_version'        => '',
				'parent_version_latest' => '',
				'parent_author_url'     => '',
			);
		}

		$active_theme_info = array(
			'name'                    => $active_theme->name,
			'version'                 => $active_theme->version,
			'author_url'              => esc_url_raw( $active_theme->{'Author URI'} ),
			'is_child_theme'          => is_child_theme(),
		);

		return array_merge( $active_theme_info, $parent_theme_info );
	}

	public function get_settings(){
		$validated_settings = array();

		foreach( WPWHPRO()->settings->get_settings() as $setting_key => $setting_data ){
			$validated_settings[ $setting_key ] = ( isset( $setting_data['value'] ) ) ? $setting_data['value'] : '';
		}

		return $validated_settings;
	}

	public function get_security_info() {
		return array(
			'secure_connection' => ( isset( $_SERVER['HTTPS'] ) && ( $_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == '1' || $_SERVER['HTTPS'] === true ) ) ? true : false,
			'server_port' 		=> ( isset( $_SERVER['SERVER_PORT'] ) ) ? $_SERVER['SERVER_PORT'] : 0,
			'hide_errors'       => ! ( defined( 'WP_DEBUG' ) && defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG && WP_DEBUG_DISPLAY ) || 0 === intval( ini_get( 'display_errors' ) ),
		);
	}

}
