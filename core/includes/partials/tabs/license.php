<?php
/**
 * Main Template
 */

$license_key        = WPWHPRO()->settings->get_license('key');
$license_status     = WPWHPRO()->settings->get_license('status');
$license_expires    = WPWHPRO()->settings->get_license('expires');
$license_expired    = '';
$license_option_key = WPWHPRO()->settings->get_license_option_key();
$license_nonce_data = WPWHPRO()->settings->get_license_nonce();
$home_url = home_url();

if ( ! empty( $license_expires ) ) {
	$license_is_expired = WPWHPRO()->license->is_expired( $license_expires );
	if ( $license_is_expired ) {
		$license_expired = WPWHPRO()->helpers->translate('Your license key has expired.', 'admin-settings-license');

		//Check for renewal on expired license
		$license_is_renewed = WPWHPRO()->license->verify_renewal();
		if( $license_is_renewed ){
			$license_expired = '';
			$license_status     = $license_is_renewed['status'];
			$license_expires    = $license_is_renewed['expires'];
		} elseif( $license_is_renewed === false ) {
			WPWHPRO()->license->update( 'status', 'expired' );
			$license_status = 'expired';
		}
	}
}

// Check on submit and update the license.
if ( isset( $_REQUEST['submit'] ) ) {
	if( 
		check_admin_referer( $license_nonce_data['action'], $license_nonce_data['arg'] )
		&& WPWHPRO()->helpers->current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-license-manage-license' ), 'wpwhpro-page-license-manage-license' )
	) {
		if ( isset( $_REQUEST['ironikus_wpwhpro_license_key' ] ) && !empty( $_REQUEST['ironikus_wpwhpro_license_key'] ) ) {

			$license_activation = false;
			if( empty( $license_key ) ){
				$license_activation = true;
			}

			if( $license_key !== $_REQUEST['ironikus_wpwhpro_license_key' ] ){
				$license_activation = true;

				//triggers in case the license was adjusted 
				if( $license_status == 'valid' ){
					$response = WPWHPRO()->license->deactivate( array( 'license' => $license_key ) );
		
					if ( ! is_wp_error( $response ) ) {
						if( is_array( $response ) && isset( $response['body'] ) ){
							$response_data = json_decode( $response['body'], true );
							if( 
								is_array( $response_data ) 
								&& isset( $response_data['success'] ) 
								&& $response_data['success']  
								&& isset( $response_data['license'] )
								&& $response_data['license'] == 'deactivated'
							){
								WPWHPRO()->license->update( 'status' );
								WPWHPRO()->license->update( 'expires' );
								WPWHPRO()->license->update( 'whitelabel' );
								$license_status = false;
								echo WPWHPRO()->helpers->create_admin_notice( 'Previous license successfully deactivated.', 'success', true );
							}
						}
					}
				}
			}

			//Update License
			$license_key = $_REQUEST['ironikus_wpwhpro_license_key'];
			WPWHPRO()->license->update( 'key', trim( $license_key ) );
			echo WPWHPRO()->helpers->create_admin_notice( 'Saved successfully.', 'success', true );
	
			// Activate license.
			if( isset( $_POST['ironikus_activate_license'] ) && ! empty( $_POST['ironikus_activate_license'] ) || $license_activation ) {
				if( $license_status !== 'valid' ){
					$response = WPWHPRO()->license->activate( array( 'license' => $license_key	) );
	
					if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
						$message =  ( is_wp_error( $response ) && ! empty( $response->get_error_message() ) ) ? $response->get_error_message() : WPWHPRO()->helpers->translate( 'An error occurred, please try again.', 'admin-settings-license' );
						echo WPWHPRO()->helpers->create_admin_notice( $message, 'error', true );
					} else {
						$license_data = json_decode( wp_remote_retrieve_body( $response ) );
		
						if( ! empty( $license_data ) && $license_data->license == 'valid' ){
							WPWHPRO()->license->update( 'status', $license_data->license );
							WPWHPRO()->license->update( 'expires', $license_data->expires );
							$license_status = $license_data->license;
							$license_expires = $license_data->expires;
							$license_expired = false;
							echo WPWHPRO()->helpers->create_admin_notice( 'License successfully activated.', 'success', true );
						} elseif( ! empty( $license_data ) && ! empty( $license_data->site_count ) && ! empty( $license_data->license_limit ) ) {
							if( $license_data->site_count >= $license_data->license_limit ){
								echo sprintf(WPWHPRO()->helpers->create_admin_notice( 'We are sorry, but you reached the maximum of active installations for your subscription. Please go to your <a href="%s" target="_blank" rel="noopener">account page</a> and manage your active sites or upgrade your current plan.', 'error', true ), 'https://wp-webhooks.com/account/?utm_source=wp-webhooks-pro&utm_medium=notice-reached-activation-limit&utm_campaign=WP%20Webhooks%20Pro');
							}
						} elseif( ! empty( $license_data ) && ! empty( $license_data->error ) && $license_data->error == 'expired' ){
							echo WPWHPRO()->helpers->create_admin_notice( 'Sorry, but your license is expired. Please renew it first.', 'error', true );
						} else {
							echo WPWHPRO()->helpers->create_admin_notice( 'Unfortunately we could not activate your license.', 'error', true );
						}
		
					}
				}
	
			} else {
				$response = WPWHPRO()->license->deactivate( array( 'license' => $license_key ) );
	
				if ( ! is_wp_error( $response ) ) {
					if( is_array( $response ) && isset( $response['body'] ) ){
						$response_data = json_decode( $response['body'], true );
						if( 
							is_array( $response_data ) 
							&& isset( $response_data['success'] ) 
							&& $response_data['success']  
							&& isset( $response_data['license'] )
							&& $response_data['license'] == 'deactivated'
						){
							WPWHPRO()->license->update( 'status' );
							WPWHPRO()->license->update( 'expires' );
							WPWHPRO()->license->update( 'whitelabel' );
							$license_status = false;
							echo WPWHPRO()->helpers->create_admin_notice( 'Deactivated license successfully.', 'warning', true );
						}
					}
				}
			}
	
		} else {
			$response = WPWHPRO()->license->deactivate( array( 'license' => $license_key ) );
	
			if ( ! is_wp_error( $response ) ) {
				if( is_array( $response ) && isset( $response['body'] ) ){
					$response_data = json_decode( $response['body'], true );
					if( 
						is_array( $response_data ) 
						&& isset( $response_data['success'] ) 
						&& $response_data['success']  
						&& isset( $response_data['license'] )
						&& $response_data['license'] == 'deactivated'
					){
						WPWHPRO()->license->update( 'status' );
						WPWHPRO()->license->update( 'expires' );
						WPWHPRO()->license->update( 'whitelabel' );
						WPWHPRO()->license->update( 'key' );

						$license_status = false;
						$license_key = '';
						$license_status = '';
						$license_expires = '';
						$license_expired = '';

						echo WPWHPRO()->helpers->create_admin_notice( 'License has been removed and deactivated successfully.', 'success', true );
					}
				}
			} else {
				echo WPWHPRO()->helpers->create_admin_notice( 'An error occured while deactivating your license.', 'success', true );
			}
		}
	}
}

?>

<div class="wpwh-container">
	<div class="row align-items-start">
		<div class="wpwh-welcome__left col-md-6">
			<div class="wpwh-content wpwh-text-small mb-4 pb-3">
				<?php if( WPWHPRO()->whitelabel->is_active() && ! empty( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_license' ) ) ) : ?>
					<?php echo WPWHPRO()->helpers->translate( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_license' ), 'admin-settings-license' ); ?>
				<?php else : ?>
					<h1><?php echo WPWHPRO()->helpers->translate( 'Plugin License Options', 'admin-settings-license' ); ?></h1>
					<p>
					<?php echo sprintf(WPWHPRO()->helpers->translate( 'If you have any trouble activating your license, please check your available license slots and your subscription on your <a class="text-secondary" href="%s" target="_blank" >account page</a>.', 'wpwhpro-page-actions' ), 'https://wp-webhooks.com/'); ?>
					</p>
					<p><?php echo WPWHPRO()->helpers->translate( 'To activate your license, please follow the steps below:', 'admin-settings-license' ); ?></p>
					<ol>
						<li><?php echo WPWHPRO()->helpers->translate( 'Add the license key into the input field and press "Save license"', 'admin-settings-license' ); ?></li>
						<li><?php echo WPWHPRO()->helpers->translate( 'Once saved, please make sure the license is displayed as active.', 'admin-settings-license' ); ?></li>
						<li><?php echo WPWHPRO()->helpers->translate( 'That\'s it, you are ready to automate!', 'admin-settings-license' ); ?></li>
					</ol>
				<?php endif; ?>
			</div>
			<form method="post" action="">
				<div class="wpwh-form-field mb-4">
					<div class="d-flex align-items-start justify-content-between">
						<label class="wpwh-form-label" for="ironikus_wpwhpro_license_key"><?php echo WPWHPRO()->helpers->translate('License Key', 'admin-settings-license'); ?></label>
						<?php if( ! empty( $license_key ) || false != $license_key ) : 

							$is_active = false;
							if ( $license_status !== false && $license_status == 'valid' ){
								if ( ! $license_expired ){
									$is_active = true;
								}
							}
							
						?>
							<div class="wpwh-toggle wpwh-toggle--on-off">
								<?php if ( false !== $license_status && $license_status == 'valid' ) : ?>
									<?php if ( ! $license_expired ) : ?>
										<label class="mb-0 mr-2 wpwh-text-small" for="ironikus_activate_license">
											<span style="color:green">
												<strong><?php echo WPWHPRO()->helpers->translate( 'Active', 'admin-settings-license' ); ?></strong>
											</span>
										</label>
									<?php else : ?>
										<label class="mb-0 mr-2 wpwh-text-small" for="ironikus_activate_license">
											<span style="color:red">
												<strong><?php echo WPWHPRO()->helpers->translate( 'Expired', 'admin-settings-license' ); ?></strong>
											</span>
										</label>
									<?php endif; ?>

								<?php else : ?>
									<?php if ( ! $license_expired ) : ?>
										<label class="mb-0 mr-2 wpwh-text-small" for="ironikus_activate_license">
											<span style="color:red">
												<strong><?php echo WPWHPRO()->helpers->translate( 'Inactive', 'admin-settings-license' ); ?></strong>
											</span>
										</label>
									<?php else : ?>
										<label class="mb-0 mr-2 wpwh-text-small" for="ironikus_activate_license">
											<span style="color:red">
												<strong><?php echo WPWHPRO()->helpers->translate( 'Expired', 'admin-settings-license' ); ?></strong>
											</span>
										</label>
									<?php endif; ?>
								<?php endif; ?>
								<input type="checkbox" id="ironikus_activate_license" name="ironikus_activate_license" class="wpwh-toggle__input"<?php echo ( $is_active ) ? ' checked="checked"' : ''; ?>>
								<label class="wpwh-toggle__btn d-inline-block" for="ironikus_activate_license"></label>
							</div>
						<?php endif; ?>
					</div>
					<input class="wpwh-form-input w-100" id="ironikus_wpwhpro_license_key" name="ironikus_wpwhpro_license_key" value="<?php esc_attr_e( $license_key ); ?>" type="text" class="regular-text form-control" placeholder="License key" aria-label="License Key" aria-describedby="ironikus_wpwhpro_license_key_label">
				</div>
				<div class="wpwh-form-field d-flex align-items-start">
					<label class="wpwh-form-label w-25 pb-0">
						<?php echo WPWHPRO()->helpers->translate( 'License Status', 'admin-settings-license' ); ?>
					</label>
					<p class="wpwh-text-small">
						<?php if ( false !== $license_status && $license_status == 'valid' ) : ?>
							<?php if ( ! $license_expired ) : ?>
								<span class="wpwh-text-success"><?php echo WPWHPRO()->helpers->translate( 'Active.', 'admin-settings-license' ); ?></span>
							<?php else : ?>
								<span class="wpwh-text-danger"><?php echo WPWHPRO()->helpers->translate( 'Expired.', 'admin-settings-license' ); ?></span>
							<?php endif; ?>
							<?php if ( ! $license_expired ) : ?>
								<?php echo sprintf( WPWHPRO()->helpers->translate( 'Expires on %s', 'admin-settings-license'), $license_expires ); ?>
							<?php endif; ?>
						<?php else : ?>
							<?php if ( ! $license_expired ) : ?>
								<span class="wpwh-text-danger"><?php echo WPWHPRO()->helpers->translate( 'Please activate your license down below.', 'admin-settings-license' ); ?></span>
							<?php else : ?>
								<span class="wpwh-text-success"><?php echo WPWHPRO()->helpers->translate( 'Expired.', 'admin-settings-license' ); ?></span>
							<?php endif; ?>
						<?php endif; ?>
					</p>
				</div>
				<?php echo WPWHPRO()->helpers->get_nonce_field( $license_nonce_data ); ?>
				<input type="submit" name="submit" id="submit" class="wpwh-btn wpwh-btn--secondary w-100" value="<?php echo WPWHPRO()->helpers->translate( 'Save License', 'admin-settings-license' ); ?>">
			</form>
		</div>
		<div class="wpwh-welcome__right col-md-6 text-right">
			<img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/license-illustration.svg'; ?>" alt="Plugin License Options">
		</div>
	</div>
</div>