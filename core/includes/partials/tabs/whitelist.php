<?php

$whitelist = WPWHPRO()->whitelist->get_list();
$whitelist_requests = WPWHPRO()->whitelist->get_request_list();
$current_url = WPWHPRO()->helpers->get_current_url( false, true );
$clear_form_url = WPWHPRO()->helpers->get_current_url( true, true );
$whitelist_nonce_data = WPWHPRO()->settings->get_whitelist_nonce();
$shitelist_requests_count = 0;

// START Add IP
if( isset( $_POST['ironikus_WP_Webhooks_Pro_whitelist_url'] ) ) {
	if ( check_admin_referer( $whitelist_nonce_data['action'], $whitelist_nonce_data['arg'] ) ) {

		if( WPWHPRO()->helpers->current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-whitelist-add-ip' ), 'wpwhpro-page-whitelist-add-ip' ) ){
			$check = WPWHPRO()->whitelist->add_item( esc_html( $_POST['ironikus_WP_Webhooks_Pro_whitelist_url'] ) );
			if( $check ) {
				echo WPWHPRO()->helpers->create_admin_notice( array(
					'IP successfully added: %s',
					esc_html( $_POST['ironikus_WP_Webhooks_Pro_whitelist_url'] )
				), 'success', true );
	
				$whitelist = WPWHPRO()->whitelist->get_list();
			}
		}
		
	}
}
// END ADd IP

//START Delete IP
if( isset( $_GET['wpwhpro_whitelist_delete'] ) ){
	$check = WPWHPRO()->whitelist->delete_item( esc_html( $_GET['wpwhpro_whitelist_delete'] ) );
	if( $check ){

		if( isset( $whitelist[ $_GET['wpwhpro_whitelist_delete'] ] ) ){
			$whitelist_name = $whitelist[ $_GET['wpwhpro_whitelist_delete'] ];
		} else {
			$whitelist_name = $_GET['wpwhpro_whitelist_delete'];
		}

		echo WPWHPRO()->helpers->create_admin_notice( array( 'The following IP was successfully removed: %s', esc_html( $whitelist_name ) ), 'success', true );

		$whitelist = WPWHPRO()->whitelist->get_list();
	}
	unset( $_GET[ 'wpwhpro_whitelist_delete' ] );
	$clear_form_url = WPWHPRO()->helpers->built_url( $current_url, $_GET );
}
//END Delete IP

?>
<div class="wpwh-container">
  <div class="wpwh-title-area mb-4">
		<h2><?php echo WPWHPRO()->helpers->translate( 'IP Whitelist', 'wpwhpro-page-whitelist' ); ?></h2>
		<?php if( WPWHPRO()->whitelabel->is_active() && ! empty( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_whitelist' ) ) ) : ?>
			<p><?php echo WPWHPRO()->helpers->translate( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_whitelist' ), 'admin-settings-license' ); ?></p>
		<?php else : ?>
			<p><?php echo sprintf( WPWHPRO()->helpers->translate( 'Down below you wil find a list of all permitted IPs that are able to access and send data to %s. You can also see the last 20 IP addresses which are not whitelisted but tried to access the endpoints. This feature also supports wildcard whitelisting - if you want to learn more about it, please click on the "Add IP address button". <br>This feature protects specifically any incoming data of webhook actions URLs, as well as the data sent to receivable trigger URLs.', 'wpwhpro-page-whitelist' ), WPWHPRO()->settings->get_page_title() ); ?></p>
		<?php endif; ?>
  </div>

  <div class="wpwh-table-container mb-5">
    <div class="wpwh-table-header d-flex align-items-center justify-content-between">
      <h2 class="mb-0"><?php echo WPWHPRO()->helpers->translate( 'Allowed IPs', 'whitelist' ); ?></h2>
			<button class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm" data-toggle="modal" data-target="#addIpAddressModal"><?php echo WPWHPRO()->helpers->translate( 'Add IP address', 'wpwhpro-page-whitelist' ); ?></button>
    </div>
    <table class="wpwh-table">
      <thead>
        <tr>
          <th><?php echo WPWHPRO()->helpers->translate( 'IP', 'wpwhpro-page-whitelist' ); ?></th>
          <th><?php echo WPWHPRO()->helpers->translate( 'Action', 'wpwhpro-page-whitelist' ); ?></th>
        </tr>
      </thead>
      <tbody>
				<?php foreach( $whitelist as $key => $url ): ?>
					<?php if ( ! is_string( $url ) ) { continue; } ?>
					<tr>
						<td><?php echo $url; ?></td>
						<td class="wpwh-w-auto align-middle" style="width: 120px;">
							<a class="wpwh-text-danger" href="<?php echo WPWHPRO()->helpers->built_url( WPWHPRO()->helpers->get_current_url( true, true ), array_merge( $_GET, array( 'wpwhpro_whitelist_delete' => $key, ) ) ); ?>" title="<?php echo WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-whitelist' ); ?>" >
								<strong><?php echo WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-whitelist' ); ?></strong>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
      </tbody>
    </table>
	</div>

	<div class="wpwh-title-area mb-4">
		<h2><?php echo WPWHPRO()->helpers->translate( 'Requested IP\'s', 'wpwhpro-page-whitelist' ); ?></h2>
		<p class="wpwh-text-small"><?php echo sprintf( WPWHPRO()->helpers->translate( 'Below you will find a list of all IP adresses that send requests to your %s API without being whitelisted. We always save the last 20 requests.', 'wpwhpro-page-whitelist' ), WPWHPRO()->settings->get_page_title() ); ?></p>
	</div>

	<?php if( ! empty( $whitelist_requests ) ) : ?>
		<div class="wpwh-accordion wpwh-accordion--style-2" id="wpwh_accordion_requested_ips">
			<?php $i=0; foreach( $whitelist_requests as $s_time => $data ) :
				$i++;

				//validate data
				$validated_data = ( is_array( $data['data'] ) ) ? $data['data'] : json_decode( base64_decode( $data['data'] ), true );
				
				?>
				<div class="wpwh-accordion__item border-top-0 pt-0">
					<button class="wpwh-accordion__heading wpwh-btn wpwh-btn--link wpwh-btn--block text-left collapsed" type="button" data-toggle="collapse" data-target="#wpwh_accordion_ip_<?php echo $i; ?>" aria-expanded="true" aria-controls="wpwh_accordion_ip_<?php echo $i; ?>">
					<div>
								<span><?php echo $data['ip']; ?></span>
								<span class="wpwh-accordion__heading-info"><?php echo date( 'F j, Y, g:i a', $s_time ); ?></span>
							</div>
					<span class="text-secondary">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="9" fill="none" class="ml-1">
						<defs />
						<path stroke="#F1592A" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1l7 7 7-7" />
						</svg>
					</span>
					</button>
					<div id="wpwh_accordion_ip_<?php echo $i; ?>" class="wpwh-accordion__content collapse" aria-labelledby="headingOne">
						<pre class="wpwh-code wpwh-code--expand-accordion"><?php echo htmlspecialchars( json_encode( $validated_data, JSON_PRETTY_PRINT ) ); ?></pre>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

</div>

<div class="modal fade" id="addIpAddressModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title"><?php echo WPWHPRO()->helpers->translate( 'Whitelist IP Address', 'wpwhpro-page-whitelist' ); ?></h3>
        <div class="wpwh-content">
            <p class="mt-4">
            	<?php echo WPWHPRO()->helpers->translate( 'This feature supports wildcard whitelisting. If you want to whitelist a range of IP addresses, you can define them as <code>127.0.*</code>', 'wpwhpro-page-whitelist' ); ?>
            </p>
        </div>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
        </button>
      </div>
      <form action="<?php echo $clear_form_url; ?>" method="post">
        <div class="modal-body">
          <label class="wpwh-form-label" for="ironikus_WP_Webhooks_Pro_whitelist_url"><?php echo WPWHPRO()->helpers->translate( 'IP Address', 'wpwhpro-page-whitelist' ); ?></label>
					<input class="wpwh-form-input w-100" type="text" id="ironikus_WP_Webhooks_Pro_whitelist_url" name="ironikus_WP_Webhooks_Pro_whitelist_url" placeholder="<?php echo WPWHPRO()->helpers->translate( '127.0.0.1', 'wpwhpro-page-whitelist' ); ?>" />
        </div>
        <div class="modal-footer">
					<?php echo WPWHPRO()->helpers->get_nonce_field( $whitelist_nonce_data ); ?>
					<input type="submit" name="submit" id="submit" class="wpwh-btn wpwh-btn--secondary w-100" value="<?php echo WPWHPRO()->helpers->translate( 'Add IP address', 'wpwhpro-page-whitelist' ); ?>">
        </div>
      </form>
    </div>
  </div>
</div>