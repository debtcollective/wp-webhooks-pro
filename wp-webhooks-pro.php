<?php
/**
 * Plugin Name: WP Webhooks Pro
 * Plugin URI: https://wp-webhooks.com/
 * Description: Automate your WordPress system with Webhooks and Workflows
 * Version: 5.2.1
 * Author: Ironikus
 * Author URI: https://wp-webhooks.com/about/
 * License: GPL2
 *
 * You should have received a copy of the GNU General Public License
 * along with TMG User Filter. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly.
if ( !defined( 'ABSPATH' ) ) exit;

//Preload Whitelabel data
$wpwh_whitelabel_data = get_option( 'ironikus_webhook_whitelabel' );
if( 
	! empty( $wpwh_whitelabel_data ) 
	&& isset( $wpwh_whitelabel_data['wpwhpro_whitelabel_name'] ) 
	&& isset( $wpwh_whitelabel_data['wpwhpro_whitelabel_activate'] ) 
	&& $wpwh_whitelabel_data['wpwhpro_whitelabel_activate'] === 'yes' 
	&& ! empty( $wpwh_whitelabel_data['wpwhpro_whitelabel_name'] ) 
){
	// Plugin name.
	define( 'WPWHPRO_NAME',		  $wpwh_whitelabel_data['wpwhpro_whitelabel_name'] );
} else {
	// Plugin name.
	define( 'WPWHPRO_NAME',		  'WP Webhooks Pro' );
}



// Plugin version.
define( 'WPWHPRO_VERSION',        '5.2.1' );

// Determines if the plugin is loaded
define( 'WPWHPRO_SETUP',          true );

// Plugin Root File.
define( 'WPWHPRO_PLUGIN_FILE',    __FILE__ );

// Plugin base.
define( 'WPWHPRO_PLUGIN_BASE',    plugin_basename( WPWHPRO_PLUGIN_FILE ) );

// Plugin Folder Path.
define( 'WPWHPRO_PLUGIN_DIR',     plugin_dir_path( WPWHPRO_PLUGIN_FILE ) );

// Plugin Folder URL.
define( 'WPWHPRO_PLUGIN_URL',     plugin_dir_url( WPWHPRO_PLUGIN_FILE ) );

// Plugin Root File.
//Defined within /core/includes/classes/class-wp-webhooks-pro-helpers.php
//Please check the translation function for the original definition
define( 'WPWHPRO_TEXTDOMAIN',     'wp-webhooks-pro' );

// Plugin Store URL
define( 'IRONIKUS_STORE',        'https://wp-webhooks.com' );

// Plugin Store ID
define( 'WPWHPRO_PLUGIN_ID',    183 );

// News ID
define( 'WPWHPRO_NEWS_FEED_ID', 6 );

/*
 * Load the main instance for our core functions
 */
if( ! defined( 'WPWH_SETUP' ) ){

	/**
	 * Load the main instance for our core functions
	 */
	require_once WPWHPRO_PLUGIN_DIR . 'core/class-wp-webhooks-pro.php';

	/**
	 * The main function to load the only instance
	 * of our master class.
	 *
	 * @return object|WP_Webhooks_Pro
	 */
	function WPWHPRO() {
		return WP_Webhooks_Pro::instance();
	}

	WPWHPRO();

} else {

	add_action( 'admin_notices', 'wpwhpro_free_version_custom_notice', 100 );
	function wpwhpro_free_version_custom_notice(){

		ob_start();
		?>
		<div class="notice notice-warning">
			<p><?php echo 'To use <strong>' . WPWHPRO_NAME . '</strong> properly, please deactivate <strong>WP Webhooks</strong>.'; ?></p>
		</div>
		<?php
		echo ob_get_clean();

	}
}