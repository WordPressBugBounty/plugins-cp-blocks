<?php
/**
 * Widgets Classes and related code
 */

if ( ! class_exists( 'CodePeople_Blocks_Dashboard_Widget' ) ) {
	class CodePeople_Blocks_Dashboard_Widget {

		private $main;
		public function __construct( $main ) {
			$this->main = $main;
			wp_add_dashboard_widget(
				'CodePeople_Blocks_Dashboard_Widget',
				'New Blocks and Tips',
				array( $this, 'dashboard_widget' )
			);
		} // End __construct

		/**
		 * Generates html code to display in the dashboard with the information of new blocks and tips.
		 * Prints the HMTL code directly to the browser.
		 */
		public function dashboard_widget() {
			$mssg   = '';
			$btn    = '';
			$domain = $this->main->get_domain();
			$body   = array(
				'get'    => 'promo',
				'key'    => get_option( 'CPBLOCKSXT_BLOCKS_LICENSE', '' ),
				'domain' => $domain,
			);

			$response = wp_remote_post(
				$this->main->get_service_url(),
				array(
					'sslverify' => false,
					'timeout'   => 300,
					'body'      => $body,
				)
			);

			if ( ! is_wp_error( $response ) ) {
				$json_obj = wp_remote_retrieve_body( $response );
				if ( ( $obj = json_decode( $json_obj ) ) != false ) {
					$mssg = $obj->text;
					if ( ! $obj->paid ) {
						$btn = '<div><input type="button" class="button-primary" value="' . __( 'Get Full Version', 'cp-blocks' ) . '" onclick="document.location.href=\'' . esc_attr( $this->main->get_settings_page_of_plugin() . '#cp_blocks_purchase_license' ) . '\'" /></div>';
					}
				}
			}
			?>
			<style scoped>.cp-blocks-promo .language-markup{overflow:auto;padding:10px 0;}.cp-blocks-promo .language-markup code{padding:10px;}</style>
			<div class="cp-blocks-promo"><?php print $mssg; ?></div>
			<?php
			print $btn;
			// Checks if there is a license valid to display the link to purchase the license
		} // End dashboard_widget
	} // End CodePeople_Blocks_Dashboard_Widget
}
