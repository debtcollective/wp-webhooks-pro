<?php

$wpwh_plugin_name   = WPWHPRO()->settings->get_page_title();

?>
<header class="wpwh-wizard__header">
	<h2><?php echo sprintf( WPWHPRO()->helpers->translate( 'Welcome to <strong>%s</strong>!', 'wpwhpro-page-wizard' ), $wpwh_plugin_name ); ?></h2>
	<p><?php echo WPWHPRO()->helpers->translate( 'Let\'s get you set up.', 'wpwhpro-page-wizard' ); ?> 🚀</p>
</header>
<div class="wpwh-separator"></div>
<div class="wpwh-wizard__main">
	<p><?php echo WPWHPRO()->helpers->translate( 'To make sure you get started in the best possible way, please follow the steps of this wizard carefully. This will help you to configure the plugin without spending time on digging through the settings yourself.', 'wpwhpro-page-wizard' ); ?></p>
</div>