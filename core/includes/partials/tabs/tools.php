<?php

/*
 * Settings Template
 */

$settings = WPWHPRO()->settings->get_settings();
$clear_page_url = WPWHPRO()->helpers->get_current_url( true, true );
$tools_export_url = WPWHPRO()->helpers->built_url( $clear_page_url, array_merge( $_GET, array( 'create_plugin_export' => 'yes', ) ) );
$tools_import_nonce_data = WPWHPRO()->settings->get_tools_import_nonce();
$wizard_nonce_data = WPWHPRO()->settings->get_wizard_nonce();
$plugin_export = null;

//Import/Export logic
if( isset( $_POST['wpwhpro_tools_import_plugin'] ) ){
	if ( check_admin_referer( $tools_import_nonce_data['action'], $tools_import_nonce_data['arg'] ) ) {
  
	  if( WPWHPRO()->helpers->current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-tools-import-plugin-data' ), 'wpwhpro-page-tools-import-plugin-data' ) ){
		$plugin_import_data = isset( $_POST['wpwh_plugin_import_data'] ) ? base64_decode( $_POST['wpwh_plugin_import_data'] ) : false;
  
		if( ! empty( $plugin_import_data ) ){
			$import_errors = WPWHPRO()->tools->import_plugin_export( $plugin_import_data );
	
			if( empty( $import_errors ) ){
				echo WPWHPRO()->helpers->create_admin_notice( 'The plugin import was successful. Down below in the left field, you will find the old export as a reference.', 'success', true );
			} else {
			  echo WPWHPRO()->helpers->create_admin_notice( 'One or multiple errors occured. Please see the notices down below.', 'warning', true );
			  foreach( $import_errors as $error ){
				echo WPWHPRO()->helpers->create_admin_notice( esc_html( $error ), 'warning', true );
			  }
			}
	
		} else {
			echo WPWHPRO()->helpers->create_admin_notice( 'The import data cannot be empty.', 'warning', true );
		}

	  } else {
		echo WPWHPRO()->helpers->create_admin_notice( 'You don\'t have permission to import data.', 'warning', true );
	  }
  
	}
}

if( isset( $_GET['create_plugin_export'] ) ){
	if( WPWHPRO()->helpers->current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-tools-export-plugin-data' ), 'wpwhpro-page-tools-export-plugin-data' ) ){
		$plugin_export = WPWHPRO()->tools->generate_plugin_export();
	}
}

$plugin_system_report = '';
if( isset( $_POST['wpwhpro_tools_create_system_report'] ) ){
	if ( check_admin_referer( $tools_import_nonce_data['action'], $tools_import_nonce_data['arg'] ) ){
		if( WPWHPRO()->helpers->current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-tools-create-system-report' ), 'wpwhpro-page-tools-create-system-report' ) ){
			$plugin_system_report = WPWHPRO()->system->generate_report();
		}
	}
}

?>
<div class="wpwh-container">

		<div class="wpwh-title-area mb-4">
			<h2><?php echo WPWHPRO()->helpers->translate( 'Tools', 'wpwhpro-page-tools' ); ?></h2>
			<?php if( WPWHPRO()->whitelabel->is_active() && ! empty( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_tools_custom_text_settings' ) ) ) : ?>
				<p><?php echo WPWHPRO()->helpers->translate( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_tools_custom_text_settings' ), 'admin-settings-license' ); ?></p>
			<?php else : ?>
				<p><?php echo sprintf( WPWHPRO()->helpers->translate( 'Down below you will find a list of all available tools we offer for %s.', 'wpwhpro-page-tools' ), WPWHPRO()->settings->get_page_title() ); ?></p>
			<?php endif; ?>
		</div>

		<div class="wpwh-box wpwh-box--big mb-3">
			<div class="wpwh-box__body">
				<h2><?php echo WPWHPRO()->helpers->translate( 'Relaunch wizard', 'wpwhpro-page-tools' ); ?></h2>
				<p class="mb-4">
					<?php echo sprintf( WPWHPRO()->helpers->translate( 'Using the button below, you can relaunch the setup wizard.', 'wpwhpro-page-tools' ), $this->page_title ); ?>
				</p>
				<form id="wpwh-relaunch-wizard-form" method="post" action="">
					<button type="submit" class="wpwh-btn wpwh-btn--secondary" name="wpwhpro_tools_relaunch_wizard">Relaunch wizard</button>

					<?php echo WPWHPRO()->helpers->get_nonce_field( $wizard_nonce_data ); ?>
				</form>
			</div>
		</div>

		<div class="wpwh-box wpwh-box--big mb-3">
			<div class="wpwh-box__body">
				<h2><?php echo WPWHPRO()->helpers->translate( 'Import / Export plugin data', 'wpwhpro-page-tools' ); ?></h2>
				<p class="mb-4">
					<?php echo sprintf( WPWHPRO()->helpers->translate( 'This tool allows you to import or export plugin data for %s. Down below is a list of what the export file includes and what not:', 'wpwhpro-page-tools' ), $this->page_title ); ?>
				</p>
				<h4><?php echo WPWHPRO()->helpers->translate( 'Included in export', 'wpwhpro-page-tools' ); ?></h4>
				<ul class="wpwh-checklist wpwh-checklist--two-col">
					<li><?php echo WPWHPRO()->helpers->translate( 'All Flows', 'wpwhpro-page-tools' ); ?></li>
					<li><?php echo WPWHPRO()->helpers->translate( 'All "Send Data" URLs and settings', 'wpwhpro-page-tools' ); ?></li>
					<li><?php echo WPWHPRO()->helpers->translate( 'All "Receive Data" URLs and settings', 'wpwhpro-page-tools' ); ?></li>
					<li><?php echo WPWHPRO()->helpers->translate( 'All Authentication and Data Mapping templates', 'wpwhpro-page-tools' ); ?></li>
					<li><?php echo WPWHPRO()->helpers->translate( 'The IP Whitelist configuration', 'wpwhpro-page-tools' ); ?></li>
					<li><?php echo WPWHPRO()->helpers->translate( 'All of the plugin settings', 'wpwhpro-page-tools' ); ?></li>
				</ul>
				<h4><?php echo WPWHPRO()->helpers->translate( 'Not included in export', 'wpwhpro-page-tools' ); ?></h4>
				<ul class="wpwh-checklist wpwh-checklist--two-col">
					<li><?php echo WPWHPRO()->helpers->translate( 'The Logs data', 'wpwhpro-page-tools' ); ?></li>
					<li><?php echo WPWHPRO()->helpers->translate( 'The license', 'wpwhpro-page-tools' ); ?></li>
					<li><?php echo WPWHPRO()->helpers->translate( 'Whitelist requests', 'wpwhpro-page-tools' ); ?></li>
					<li><?php echo WPWHPRO()->helpers->translate( 'Whitelabel data', 'wpwhpro-page-tools' ); ?></li>
					<li><?php echo WPWHPRO()->helpers->translate( 'Extensions', 'wpwhpro-page-tools' ); ?></li>
					<li><?php echo WPWHPRO()->helpers->translate( 'Backup data from previous versions', 'wpwhpro-page-tools' ); ?></li>
				</ul>
				<ul class="wpwh-checklist--two-col">
					<li class="p-2">
						<p><strong><?php echo WPWHPRO()->helpers->translate( 'Export', 'wpwhpro-page-tools' ); ?></strong></p>
						
						<?php if( ! $plugin_export ) : ?>
							<p class="wpwh-text-small"><?php echo WPWHPRO()->helpers->translate( 'To create a plugin export, please click the button down below. This will generate a JSON construct that you can use to import on other WP Webhooks installations.', 'wpwhpro-page-tools' ); ?></p>
							<a title="<?php echo WPWHPRO()->helpers->translate( 'Create export', 'wpwhpro-page-tools' ); ?>" class="wpwh-btn wpwh-btn--secondary" href="<?php echo $tools_export_url; ?>"><?php echo WPWHPRO()->helpers->translate( 'Create export', 'wpwhpro-page-tools' ); ?></a>
						<?php else : ?>
							<p class="wpwh-text-small"><?php echo WPWHPRO()->helpers->translate( 'Copy the content from the text area below. You can store it or import it into a different plugin installation.', 'wpwhpro-page-tools' ); ?></p>
							<textarea class="w-100 mb-2" style="height:100px;min-height:100px;" readonly><?php echo base64_encode( json_encode( $plugin_export ) ); ?></textarea>
							<a title="<?php echo WPWHPRO()->helpers->translate( 'Recreate export', 'wpwhpro-page-tools' ); ?>" class="wpwh-btn wpwh-btn--secondary" href="<?php echo $tools_export_url; ?>"><?php echo WPWHPRO()->helpers->translate( 'Recreate export', 'wpwhpro-page-tools' ); ?></a>
						<?php endif; ?>
					</li>
					<li class="p-2">
						<p><strong><?php echo WPWHPRO()->helpers->translate( 'Import', 'wpwhpro-page-tools' ); ?></strong></p>
						<p class="wpwh-text-small"><?php echo WPWHPRO()->helpers->translate( 'Import an existing data export from any other version.', 'wpwhpro-page-tools' ); ?></p>
						<p class="wpwh-text-small wpwh-text-danger"><?php echo WPWHPRO()->helpers->translate( 'Please note: Importing a plugin configuration will reset the plugin and fill it with all the import data. The data not included in the export will be lost. This action is irreversible.', 'wpwhpro-page-tools' ); ?></p>
						<form id="wpwh-main-settings-form" method="post" action="">
							<textarea class="w-100 mb-2" style="height:100px;min-height:100px;" name="wpwh_plugin_import_data"></textarea>
							<button type="submit" class="wpwh-btn wpwh-btn--secondary" name="wpwhpro_tools_import_plugin"><?php echo WPWHPRO()->helpers->translate( 'Import', 'wpwhpro-page-tools' ); ?></button>

							<?php echo WPWHPRO()->helpers->get_nonce_field( $tools_import_nonce_data ); ?>
						</form>
					</li>
				</ul>
			
				
			</div>
		</div>

		<div class="wpwh-box wpwh-box--big mb-3">
			<div class="wpwh-box__body">
				<h2><?php echo WPWHPRO()->helpers->translate( 'Create system report', 'wpwhpro-page-tools' ); ?></h2>
				<p class="mb-4">
					<?php echo sprintf( WPWHPRO()->helpers->translate( 'Use the button below to create a system report of your current system.', 'wpwhpro-page-tools' ), $this->page_title ); ?>
				</p>

				<?php if( ! empty( $plugin_system_report ) ) : ?>
					<textarea id="wpwh-plugin-report" class="wpwh-form-input wpwh-w-100" style="min-height:250px;"><?php echo htmlspecialchars( json_encode( $plugin_system_report ) ); ?></textarea>
				<?php else : ?>
					<form id="wpwh-create-system-export-form" method="post" action="">
						<button type="submit" class="wpwh-btn wpwh-btn--secondary" name="wpwhpro_tools_create_system_report"><?php echo WPWHPRO()->helpers->translate( 'Create system export', 'wpwhpro-page-tools' ); ?></button>

						<?php echo WPWHPRO()->helpers->get_nonce_field( $tools_import_nonce_data ); ?>
					</form>
				<?php endif; ?>
			</div>
		</div>

</div>