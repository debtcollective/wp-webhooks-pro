<?php

$license_key = WPWHPRO()->settings->get_license('key');
$step_count = intval( WPWHPRO()->wizard->get_current_step_number() );
$wpwh_plugin_name   = WPWHPRO()->settings->get_page_title();

$license_key_output = '';
if( ! empty( $license_key ) ){
	$license_key_output = $license_key;
}

?>
<header class="wpwh-wizard__header">
	<h2><?php echo sprintf( WPWHPRO()->helpers->translate( 'Step %d', 'wpwhpro-page-wizard' ), $step_count ); ?></h2>
	<p><?php echo WPWHPRO()->helpers->translate( 'Useful information', 'wpwhpro-page-wizard' ); ?></p>
</header>
<div class="wpwh-separator"></div>
<div class="wpwh-wizard__main">

	<div class="wpwh-form-field">
		<p><?php echo sprintf( WPWHPRO()->helpers->translate( 'Thank you for being a part of %s. Down below, you will find more useful links that help you getting started.', 'wpwhpro-page-wizard' ), $wpwh_plugin_name ); ?></p>
	</div>

	<div class="wpwh-form-field">
		<label for="form_1" class="wpwh-form-label"><?php echo WPWHPRO()->helpers->translate( 'Join the community', 'wpwhpro-page-wizard' ); ?></label>
		<div class="wpwh-form-description">
			<p>
				<?php echo WPWHPRO()->helpers->translate( 'A big part of WP Webhooks is our community, and we would love to see you there as well.', 'wpwhpro-page-wizard' ); ?>
			</p>

			<p class="mb-4">
				<a href="https://www.facebook.com/groups/wordpress.automation/" target="_blank" rel="noopener noreferrer" class="text-facebook mr-2"><strong><?php echo WPWHPRO()->helpers->translate( 'Join our Facebook group', 'wpwhpro-page-wizard' ); ?></strong></a>
				<a href="https://wp-webhooks.com/#newsletter" target="_blank" rel="noopener noreferrer" class="text-success mr-2"><strong><?php echo WPWHPRO()->helpers->translate( 'Newsletter', 'wpwhpro-page-wizard' ); ?></strong></a>
			</p>
		</div>
	</div>

	<div class="wpwh-form-field">
		<label for="form_1" class="wpwh-form-label"><?php echo WPWHPRO()->helpers->translate( 'Support & help', 'wpwhpro-page-wizard' ); ?></label>
		<div class="wpwh-form-description">
			<p>
				<?php echo WPWHPRO()->helpers->translate( 'Sometimes, things can be a bit challenging, but we are here to help. Simpy follow the links below for more information.', 'wpwhpro-page-wizard' ); ?>
			</p>

			<p class="mb-4">
				<a href="https://wp-webhooks.com/get-help/" target="_blank" rel="noopener noreferrer" class="text-facebook mr-2"><strong><?php echo WPWHPRO()->helpers->translate( 'Get help', 'wpwhpro-page-wizard' ); ?></strong></a>
				<a href="https://wp-webhooks.com/docs/" target="_blank" rel="noopener noreferrer" class="text-success mr-2"><strong><?php echo WPWHPRO()->helpers->translate( 'Documentation', 'wpwhpro-page-wizard' ); ?></strong></a>
				<a href="https://wp-webhooks.com/visit/youtube" target="_blank" rel="noopener noreferrer" class=""><strong><?php echo WPWHPRO()->helpers->translate( 'YouTube', 'wpwhpro-page-wizard' ); ?></strong></a>
			</p>
		</div>
	</div>

	<div class="wpwh-form-field pd-4">
		<label for="form_1" class="wpwh-form-label"><?php echo WPWHPRO()->helpers->translate( 'Suggestions and bugs', 'wpwhpro-page-wizard' ); ?></label>
		<div class="wpwh-form-description">
			<p><?php echo WPWHPRO()->helpers->translate( 'Our plugin is made for you. That\'s why we value your feedback more than everything else. In case you ever find a bug or crave for a feature, we are more than happy to help.', 'wpwhpro-page-wizard' ); ?></p>
			<p class="mb-4">
				<a href="https://wp-webhooks.com/contact/?custom-subject=I%20would%20like%20to%20suggest%20a%20feature" target="_blank" rel="noopener noreferrer" class="text-secondary mr-2"><strong><?php echo WPWHPRO()->helpers->translate( 'Suggest feature', 'wpwhpro-page-wizard' ); ?></strong></a>
				<a href="https://wp-webhooks.com/contact/?custom-subject=I%20would%20like%20to%20report%20a%20bug" target="_blank" rel="noopener noreferrer" class="text-success mr-2"><strong><?php echo WPWHPRO()->helpers->translate( 'Report bug', 'wpwhpro-page-wizard' ); ?></strong></a>
				<a href="https://wp-webhooks.com/contact/?custom-subject=Contact%20us" target="_blank" rel="noopener noreferrer" class="text-instagram"><strong><?php echo WPWHPRO()->helpers->translate( 'Contact us', 'wpwhpro-page-wizard' ); ?></strong></a>
			</p>
		</div>
	</div>
</div>