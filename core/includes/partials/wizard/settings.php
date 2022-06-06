<?php

$wpwh_plugin_name   = WPWHPRO()->settings->get_page_title();
$settings = WPWHPRO()->settings->get_settings();
$step_count = intval( WPWHPRO()->wizard->get_current_step_number() );

$log_setting = isset( $settings['wpwhpro_autoclean_logs'] ) ? $settings['wpwhpro_autoclean_logs'] : array();
$log_is_checked = '';
$log_value = '1';

if( ! empty( $log_setting ) ){
	$log_is_checked = ( $log_setting['type'] == 'checkbox' && $log_setting['value'] == 'yes' ) ? 'checked' : '';
	$log_value = ( $log_setting['type'] != 'checkbox' ) ? $log_setting['value'] : '1';
}

?>
<header class="wpwh-wizard__header">
	<h2><?php echo sprintf( WPWHPRO()->helpers->translate( 'Step %d', 'wpwhpro-page-wizard' ), $step_count ); ?></h2>
	<p><?php echo WPWHPRO()->helpers->translate( 'Optimize your performance', 'wpwhpro-page-wizard' ); ?></p>
</header>
<div class="wpwh-separator"></div>
<div class="wpwh-wizard__main">
	
	<div class="wpwh-form-field">
		<label for="form_1" class="wpwh-form-label"><?php echo WPWHPRO()->helpers->translate( 'Auto-clean logs after 30 days?', 'wpwhpro-page-wizard' ); ?></label>
		<p class="wpwh-form-description"><?php echo WPWHPRO()->helpers->translate( 'By default, we collect all logs without ever deleting them. Activating this setting results in the logs being automatically deleted in case they are older than 30 days.', 'wpwhpro-page-wizard' ); ?></p>
		<div class="wpwh-toggle wpwh-toggle--on-off">
			<input type="checkbox" id="wpwh-wizard-settings-log-autoclean" name="wpwh_wizard_log_autoclean" class="wpwh-toggle__input" placeholder="" value="<?php echo $log_value; ?>" <?php echo $log_is_checked; ?>>
			<label class="wpwh-toggle__btn" for="wpwh-wizard-settings-log-autoclean"></label>
		</div>
	</div>
	
</div>