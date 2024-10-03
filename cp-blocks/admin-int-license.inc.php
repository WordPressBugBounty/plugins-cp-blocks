<?php

$current_user        = wp_get_current_user();
$current_user_access = current_user_can( 'edit_pages' );

if ( ! is_admin() || ( ! $current_user_access && ! @in_array( $current_user->ID, unserialize( $this->get_option( 'cp_user_access', '' ) ) ) ) ) {
	esc_html_e( 'Direct access not allowed.', 'cp-blocks' );
	exit;
}

$message = '';
$cp_block_http_host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
$cp_block_request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
if ( ! empty( $_GET['ac'] ) ) {
	if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'uname_cpblocks' ) ) {
		$message = esc_html__( 'Access verification error. Cannot update settings.', 'cp-blocks' );
	} else {
		update_option( 'CPBLOCKSXT_BLOCKS_LICENSE', sanitize_text_field( wp_unslash( $_GET['ac'] ) ) );
		$message = esc_html__( 'License ID updated', 'cp-blocks' );
	}
}

$nonce_un = wp_create_nonce( 'uname_cpblocks' );

$license = get_option( 'CPBLOCKSXT_BLOCKS_LICENSE', '' );

if ( $message ) {
	echo "<div id='setting-error-settings_updated' class='updated settings-error'><p><strong>" . $message . '</strong></p></div>';
}

?>
<script type="text/javascript">
 function cpverifyStatus()
 {
	 jQuery.ajax({
			crossDomain: true,
			contentType: "application/json; charset=utf-8",
			url: "<?php echo esc_js( CPBLOCKSXT_SERVICE_URL ); ?>code/verifylicense.php?callback=?",
			data: {licenseid:document.getElementById("cplicense").value,website:'<?php echo esc_js( $cp_block_http_host ); ?>'},
			dataType: "jsonp",
			jsonpCallback: 'cplincense_successcallback'
		});
 }
 function cplincense_successcallback(json){
	 if (json.result == 1)
		 document.getElementById("cpblockslicencestatusbtn").style.display='none';
	 document.getElementById("cpblockslicencestatus").innerHTML = json.verbose;
 }
 function cp_updateLicenseId(id)
 {
	 document.location = 'admin.php?page=<?php echo esc_js( $this->menu_parameter ); ?>&_wpnonce=<?php echo esc_js( $nonce_un ); ?>&ac='+document.getElementById("cplicense").value+'&r='+Math.random();
 }
</script>
<div class="wrap">
<h1><?php echo esc_html( $this->plugin_name ); ?> - License</h1>
<form method="post" action="" name="cpformconf" id="cpformconf" >
<input name="<?php echo esc_attr( $this->prefix ); ?>_post_options" type="hidden" value="1" />
<input name="<?php echo esc_attr( $this->prefix ); ?>_id" type="hidden" value="<?php echo esc_attr( $this->item ); ?>" />
 <div class="postbox" >
  <h3 class='hndle' style="padding:5px;"><span><?php esc_html_e( 'Inserting Blocks', 'cp-blocks' ); ?></span></h3>
  <div class="inside">
	<div style="width:calc( 100% - 20px );width:-webkit-calc( 100% - 20px );width:-moz-calc( 100% - 20px );width:-o-calc( 100% - 20px );border:4px solid #1582AB;background:#FFF;display:table;padding-left: 10px; padding-right: 10px;padding-top:0px;padding-bottom: 10px;background-color:#ffffc6">
	<p>To insert blocks in post or pages use the "Insert Block" button located on the post/pages edition. For inserting blocks in other plugins and more instructions please read the instructions at <a href="https://services.dwbooster.com/documentation#insertingblocks">https://services.dwbooster.com/documentation#insertingblocks</a></p>
	</div>
  </div>
 </div>
<div id="normal-sortables" class="meta-box-sortables">
 <div class="postbox" >
  <h3 class='hndle' style="padding:5px;"><span><?php esc_html_e( 'CP Blocks Service License', 'cp-blocks' ); ?></span></h3>
  <div class="inside">
	 <table class="form-table">
		<tr valign="top">
			  <th scope="row"><?php esc_html_e( 'License ID', 'cp-blocks' ); ?></th><td><input type="text" size="80" value="<?php echo esc_attr( $license ); ?>" name="cplicense" id="cplicense"/></td>
		</tr>
		<tr valign="top">
			  <th scope="row"><?php esc_html_e( 'Website ID', 'cp-blocks' ); ?></th><td><?php echo esc_html( $cp_block_http_host ); ?></td>
		</tr>
		<tr valign="top">
			  <th scope="row"><?php esc_html_e( 'License Status', 'cp-blocks' ); ?></th>
			  <td>
				<div id="cpblockslicencestatus"></div>
				<div id="cpblockslicencestatusbtn"><a href="javascript:cpverifyStatus();"><?php esc_html_e( 'Click to display the License Status.' ); ?></a></div>
			  </td>
		</tr>
		<tr><th></th><td><input type="button" name="licensebtn" id="licensebtn" onclick="cp_updateLicenseId();" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'cp-blocks' ); ?>"  /></td></tr>
	 </table>
  </div>
 </div>
</div>
</form>
<form method="post" action="<?php echo esc_attr( CPBLOCKSXT_SERVICE_URL ); ?>code/get-pack.php" name="cpformconf" id="cpformconf" >
<input name="<?php echo esc_attr( $this->prefix ); ?>_post_options" type="hidden" value="1" />
<input name="<?php echo esc_attr( $this->prefix ); ?>_id" type="hidden" value="<?php echo esc_attr( $this->item ); ?>" />
<input name="r" id="rpage"  type="hidden" value="<?php echo esc_attr( ( isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . "://{$cp_block_http_host}{$cp_block_request_uri}" ); ?>" />
<div class="meta-box-sortables">
 <div id="cp_blocks_purchase_license" class="postbox" >
  <h3 class='hndle' style="padding:5px;"><span><?php esc_html_e( 'Purchase a Premium License', 'cp-blocks' ); ?></span></h3>
  <div class="inside">
	<div style="width:calc( 100% - 20px );width:-webkit-calc( 100% - 20px );width:-moz-calc( 100% - 20px );width:-o-calc( 100% - 20px );border:4px solid #1582AB;background:#FFF;display:table;padding-left: 10px; padding-right: 10px;padding-top:0px;padding-bottom: 10px;background-color:#ffffc6">
		<p>If the free blocks are not sufficient to your project, you can purchase a Premium License and access to all the blocks (A premium license can be used from the development and productions versions of the website).</p>
		<p>You can cancel the subscription to the CP Blocks premium service at any time directly from your PayPal account.</p>
		<p>The code blocks inserted into your website will continue working for an unlimited time even after cancelling the subscription to the CP Blocks service.</p>
		<p>With the license for the CP Blocks service you get the full set of premium blocks, updates and access the premium support service.</p>
		<strong>Details about plans:</strong> <a href="https://services.dwbooster.com/pricing">https://services.dwbooster.com/pricing</a>
	</div>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">Website ID</th>
			<td>
				<input type="text" size="30" value="<?php echo esc_attr( $cp_block_http_host ); ?>" name="website" id="website" required /><br />
				<em>Note: Must match the website host name, this mean the exact domain or subdomain. Leave the auto-detected value "<strong><?php echo esc_html( $cp_block_http_host ); ?></strong>" for this website.</em>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">Your email address</th>
			<td><input size="40" type="text" value="<?php echo esc_attr( $current_user->user_email ); ?>" name="email" id="email" required /></td>
		</tr>
		<tr valign="top">
			<th scope="row">Plan</th>
			<td>
				<select name="plan">
					<option value="2">Monthly</option>
					<option value="3">Yearly</option>
				</select> <br />
			</td>
		</tr>
		<tr>
			<th></th>
			<td><input type="submit" name="submit" id="submit" class="button-primary" value="<?php esc_attr_e( 'Purchase License', 'cp-blocks' ); ?>"  /></td>
		</tr>
	 </table>
  </div>
 </div>
</div>
</form>
</div>
