<?php
/*
Plugin Name: CP Blocks
Plugin URI: https://services.dwbooster.com/pricing
Description: CP Blocks helps to insert blocks of code and styles
Version: 1.1.0
Author: CodePeople
Author URI: https://wordpress.dwbooster.com
License: GPL
Text Domain: cp-blocks
*/

/* initialization / install */
require_once 'banner.php';
$codepeople_promote_banner_plugins['codepeople-cp-block'] = array(
	'plugin_name' => 'CP Blocks',
	'plugin_url'  => 'https://wordpress.org/support/plugin/cp-blocks/reviews/#new-post',
);

add_filter( 'option_sbp_settings', 'cp_blocks_troubleshoot' );
if ( ! function_exists( 'cp_blocks_troubleshoot' ) ) {
	function cp_blocks_troubleshoot( $option ) {
		if ( ! is_admin() ) {
			// Solves a conflict caused by the "Speed Booster Pack" plugin
			if ( is_array( $option ) && isset( $option['jquery_to_footer'] ) ) {
				unset( $option['jquery_to_footer'] );
			}
		}
		return $option;
	} // End cp_blocks_troubleshoot
}

define( 'CPBLOCKSXT_SERVICE_URL', 'https://services.dwbooster.com/' );
define( 'CPBLOCKSXT_BLOCKS_VERSION', '1.1.0' );
define( 'CPBLOCKSXT_LOCAL_PATH', plugin_dir_path( __DIR__ ) . 'server-side-blocks' );

// Feedback system
require_once 'feedback/cp-feedback.php';
new CP_FEEDBACK( plugin_basename( dirname( __FILE__ ) ), __FILE__, 'https://services.dwbooster.com/contact' );

require_once dirname( __FILE__ ) . '/classes/cp-base-class.inc.php';
require_once dirname( __FILE__ ) . '/cp-main-class.inc.php';

$codepeople_blocks_plugin = new CodePeople_Blocks();

register_activation_hook( __FILE__, array( $codepeople_blocks_plugin, '_install' ) );

function cpblocksservice_plugin_init() {
	load_plugin_textdomain( 'cp-blocks', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action('after_setup_theme', 'cpblocksservice_plugin_init');

if ( is_admin() ) {
	add_action( 'admin_enqueue_scripts', array( $codepeople_blocks_plugin, 'insert_adminScripts' ), 1 );
	add_action( 'admin_menu', array( $codepeople_blocks_plugin, 'admin_menu' ) );
	add_action( 'media_buttons', array( $codepeople_blocks_plugin, 'set_media_insert_button' ), 100 );
	add_action( 'enqueue_block_editor_assets', array( $codepeople_blocks_plugin, 'gutenberg_editor' ) );
	add_action( 'media_buttons', array( $codepeople_blocks_plugin, 'set_media_insert_button' ), 100 );
	// Load the dashboard widgets
	add_action( 'wp_dashboard_setup', array( $codepeople_blocks_plugin, 'dashboard_widget' ) );
}
