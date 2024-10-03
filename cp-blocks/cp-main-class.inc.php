<?php

class CodePeople_Blocks extends CP_MetaSystem_BaseClass {

	private $menu_parameter = 'cp_blocks';
	private $prefix         = 'cp_blocks';
	private $plugin_name    = 'CP Blocks';
	private $plugin_URL     = 'https://wordpress.dwbooster.com';
	private $service_URL    = 'https://services.dwbooster.com/api.php';
	private $nonce_name     = 'cp_blocks_import_server_side_nonce';
	private $nonce_seed;
	private $domain;
	private $settings_page_of_plugin;

	public function __construct() {
		if ( ! is_admin() ) {
			add_action( 'plugins_loaded', array( $this, 'load_server_side_blocks' ) );
		} else {
			$this->nonce_seed = 'cp_blocks_' . md5( __FILE__ );
			add_action( 'admin_init', array( $this, 'import_block' ) );
			add_filter( 'tiny_mce_before_init', array( $this, 'tinymce_settings' ) );
		}
	} // End __construct

	public function _install() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	} // End _install

	/* Code for the admin area */
	public function tinymce_settings( $settings ) {
		$settings['valid_elements']          = '*[*]';
		$settings['extended_valid_elements'] = '*[*]';
		return $settings;
	} // End tinymce_settings

	public function plugin_page_links( $links ) {
		$help_link = '<a href="https://wordpress.dwbooster.com/support">' . __( 'Support' ) . '</a>';
		array_unshift( $links, $help_link );
		return $links;
	} // End plugin_page_links

	public function dashboard_widget() {
		if ( current_user_can( 'manage_options' ) ) {
			require_once __DIR__ . '/cp-dashboard-widget.inc.php';
			if ( class_exists( 'CodePeople_Blocks_Dashboard_Widget' ) ) {
				new CodePeople_Blocks_Dashboard_Widget( $this );
			}
		}
	} // End dashboard_widget

	public function admin_menu() {
		add_menu_page(
			$this->plugin_name . ' Options',
			$this->plugin_name,
			'edit_pages',
			$this->menu_parameter,
			array( $this, 'settings_page' )
		);
	} // End admin_menu

	public function set_media_insert_button() {
		 print '<a href="javascript:jQuery(document).trigger(\'load_blocks_module\',\'wpcf7\');" title="' . esc_html__( 'Insert Block', 'cp-blocks' ) . '" class="button button-primary">Insert Block</a>';
	} // End set_media_insert_button

	public function settings_page() {
		global $wpdb;
		include_once dirname( __FILE__ ) . '/admin-int-license.inc.php';
	} // End settings_page

	public function insert_adminScripts( $hook ) {
		wp_enqueue_script( 'jquery' );
		$enqueue_api = false;

		if ( strpos( $hook, 'cp_calculated_fields_form' ) !== false ) {
			if ( isset( $_REQUEST['cal'] ) ) {
				$enqueue_api = true;

				// Load the resources of complementary-blocks related to "CFF"
				wp_enqueue_script( 'complementary_blocks_cff_connector', plugins_url( '/js/cff.connector.js', __FILE__ ), array( 'jquery' ), CPBLOCKSXT_BLOCKS_VERSION, true );
			}
		} elseif ( strpos( $hook, 'page_wpcf7' ) !== false ) {
			$enqueue_api = true;

			// Load the resources of complementary-blocks related to "WPCF7"
			wp_enqueue_script( 'complementary_blocks_cff_connector', plugins_url( '/js/wpcf7.connector.js', __FILE__ ), array( 'jquery' ), CPBLOCKSXT_BLOCKS_VERSION, true );
		} elseif ( strpos( $hook, 'cp_contactformtoemail' ) !== false ) {
			if ( isset( $_REQUEST['cal'] ) ) {
				$enqueue_api = true;

				// Load the resources of complementary-blocks related to "CFF"
				wp_enqueue_script( 'complementary_blocks_cfte_connector', plugins_url( '/js/cfte.connector.js', __FILE__ ), array( 'jquery' ), CPBLOCKSXT_BLOCKS_VERSION, true );
			}
		} elseif ( 'post.php' == $hook || 'post-new.php' == $hook ) {
			$enqueue_api = true;
			wp_enqueue_script( 'complementary_blocks_page_connector', plugins_url( '/js/page.connector.js', __FILE__ ), array( 'jquery' ), CPBLOCKSXT_BLOCKS_VERSION, true );
		}

		if ( $enqueue_api ) {
			wp_enqueue_script(
				'cp_blocks_api',
				$this->service_URL . '?key=' . urlencode( get_option( 'CPBLOCKSXT_BLOCKS_LICENSE', '' ) ),
				array( 'jquery' ),
				CPBLOCKSXT_BLOCKS_VERSION,
				true
			);

			wp_localize_script(
				'cp_blocks_api',
				'cp_blocks_data',
				array(
					'register_url'               => $this->get_settings_page_of_plugin(),
					'plugin_url'                 => wp_nonce_url( $this->get_settings_page_of_plugin(), $this->nonce_seed, $this->nonce_name ),
					'accept_server_side_plugins' => 1,
				)
			);
		}
	} // End insert_adminScripts

	/**
		Integrates the plugin with the Gutenberg Editor
	 */
	public function gutenberg_editor() {
		wp_enqueue_script(
			'cp-blocks-gutenberg-editor',
			plugins_url( '/js/gutenberg.js', __FILE__ ),
			array( 'wp-blocks', 'wp-element' ),
			CPBLOCKSXT_BLOCKS_VERSION,
			true
		);
	} // End gutenbergEditor

	public function get_domain() {
		if ( empty( $this->domain ) ) {
			$blog_id      = get_current_blog_id();
			$site_url     = get_home_url( $blog_id, '' );
			$this->domain = parse_url( $site_url, PHP_URL_HOST );
		}
		return $this->domain;
	} // End get_domain

	public function get_service_url() {
		 return $this->service_URL;
	} // End get_service_url

	public function get_settings_page_of_plugin() {
		if ( empty( $this->settings_page_of_plugin ) ) {
			$this->settings_page_of_plugin = admin_url() . 'admin.php?page=cp_blocks';
		}
		return $this->settings_page_of_plugin;
	} // End get_settings_page_of_plugin
	/***************************************** SERVER SIDE BLOCKS *****************************************/

	public function import_block() {
		$excluding_params = array( 'page', $this->nonce_name, 'block', 'language', 'plugin' );
		if (
			! empty( $_REQUEST[ $this->nonce_name ] ) &&
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST[ $this->nonce_name ] ) ), $this->nonce_seed ) &&
			! empty( $_REQUEST['block'] ) &&
			! empty( $_REQUEST['plugin'] ) &&
			! empty( $_REQUEST['language'] )
		) {
			$result = new stdClass();
			if ( ( $path = $this->_create_server_side_blocks_directory() ) !== false ) {
				$file_name = sanitize_text_field( wp_unslash( $_REQUEST['block'] ) ) . '.zip';
				$file_path = $path . '/' . $file_name;
				$domain    = $this->get_domain();
				$body      = array(
					'get'      => 'zip',
					'block'    => sanitize_text_field( wp_unslash( $_REQUEST['block'] ) ),
					'callback' => 'xxx',
					'key'      => get_option( 'CPBLOCKSXT_BLOCKS_LICENSE', '' ),
					'domain'   => $domain,
					'language' => sanitize_text_field( wp_unslash( $_REQUEST['language'] ) ),
					'plugin'   => sanitize_text_field( wp_unslash( $_REQUEST['plugin'] ) ),
				);

				foreach ( $_REQUEST as $key => $value ) {
					if ( ! in_array( $key, $excluding_params ) ) {
						$body[ $key ] = sanitize_text_field( wp_unslash( $value ) );
					}
				}

				$response = wp_remote_post(
					$this->service_URL,
					array(
						'sslverify' => false,
						'timeout'   => 300,
						'stream'    => true,
						'filename'  => $file_path,
						'body'      => $body,
					)
				);

				if ( ! is_wp_error( $response ) ) {
					WP_Filesystem();
					$old_level    = error_reporting( E_ERROR | E_PARSE ); // To prevent warnings if block was inserted previously
					$unzip_action = @unzip_file( $file_path, $path );
					if ( ! is_wp_error( $unzip_action ) ) {
						// Now that the zip file has been used, destroy it
						@unlink( $file_path );
						$result->ok = true;
					} else {
						$result->error = $unzip_action->get_error_message();
					}
					error_reporting( $old_level );
				} else {
					$result->error = $response->get_error_message();
				}
			}
			print json_encode( $result );
			exit;
		}
	} // End import_block

	public function load_server_side_blocks() {
		if ( defined( 'CPBLOCKSXT_LOCAL_PATH' ) && file_exists( CPBLOCKSXT_LOCAL_PATH ) ) {
			// Applies a require_once to all PHP scripts into the directory
			$blocks = dir( CPBLOCKSXT_LOCAL_PATH );
			while ( false !== ( $entry = $blocks->read() ) ) {
				if ( strlen( $entry ) > 3 && strtolower( pathinfo( $entry, PATHINFO_EXTENSION ) ) == 'php' ) {
					require_once $blocks->path . '/' . $entry;
				}
			}
		}
	} // End load_server_side_blocks

	private function _create_server_side_blocks_directory() {
		if ( defined( 'CPBLOCKSXT_LOCAL_PATH' ) ) {
			$path = rtrim( CPBLOCKSXT_LOCAL_PATH, '/' );
			if ( ! file_exists( $path ) ) {
				if ( ! @mkdir( $path ) ) {
					return false;
				}
			}

			if ( ! file_exists( $path . '/.htaccess' ) ) {
				file_put_contents( $path . '/.htaccess', 'deny from all' );
			}
			return $path;
		}
		return false;
	} // End _create_server_side_blocks_directory

} // end class
