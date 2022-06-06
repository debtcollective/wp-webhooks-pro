<?php

$current_url = WPWHPRO()->helpers->get_current_url( false, true );
$data_mapping_url = WPWHPRO()->helpers->built_url( $current_url, array( 'page' => $this->page_name, 'wpwhprovrs' => 'data-mapping' ) );
$logs_url = WPWHPRO()->helpers->built_url( $current_url, array( 'page' => $this->page_name, 'wpwhprovrs' => 'logs' ) );
$authentication_url = WPWHPRO()->helpers->built_url( $current_url, array( 'page' => $this->page_name, 'wpwhprovrs' => 'authentication' ) );
$whitelist_url = WPWHPRO()->helpers->built_url( $current_url, array( 'page' => $this->page_name, 'wpwhprovrs' => 'whitelist' ) );

$logs_is_active = ( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_logs' ) !== 'yes' ) ? true : false;
$auth_is_active = ( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_authentication' ) !== 'yes' ) ? true : false;
$data_mapping_is_active = ( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_data_mapping' ) !== 'yes' ) ? true : false;
$whitelist_is_active = ( WPWHPRO()->whitelist->is_active() && ( ! WPWHPRO()->whitelabel->is_active() || WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_hide_ip_whitelist' ) !== 'yes' ) ) ? true : false;

?>
<div class="wpwh-container">
  <div class="wpwh-title-area mb-4">
    <h1><?php echo WPWHPRO()->helpers->translate( 'Features', 'wpwhpro-page-features' ); ?></h1>
    <p>
      <?php if( WPWHPRO()->whitelabel->is_active() && ! empty( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_features' ) ) ) : ?>
        <?php echo WPWHPRO()->helpers->translate( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_features' ), 'admin-settings-license' ); ?>
      <?php else : ?>
        <?php echo sprintf(WPWHPRO()->helpers->translate( 'This plugin features a wide range of additional features as seen down below. To learn more about each of them in detail, feel free to check out our <a class="text-secondary" title="Go to the documentation" href="%s" target="_blank" >documentation</a>.', 'wpwhpro-page-features' ), 'https://wp-webhooks.com/docs/article-categories/features/'); ?>
      <?php endif; ?>
    </p>
  </div>

  <?php if( $logs_is_active ) : ?>
    <div class="wpwh-box wpwh-box--big mb-3">
      <div class="wpwh-box__body">
          <h2><?php echo WPWHPRO()->helpers->translate( 'Logs', 'wpwhpro-page-features' ); ?></h2>
          <p class="mb-4"><?php echo sprintf( WPWHPRO()->helpers->translate( 'Review every request that was sent or received by %s. This is perfect for debugging and to review traffic. Flows are supported as well.', 'wpwhpro-page-features' ), $this->page_title ); ?></p>
          <p class="mb-1">
              <a href="<?php echo $logs_url; ?>" rel="noopener noreferrer" class="text-secondary mr-4"><strong><?php echo WPWHPRO()->helpers->translate( 'Go to logs', 'wpwhpro-page-features' ); ?></strong></a>
              <a href="https://wp-webhooks.com/docs/article-categories/logs/" target="_blank" rel="noopener noreferrer" class="mr-2"><strong><?php echo WPWHPRO()->helpers->translate( 'Documentation', 'wpwhpro-page-features' ); ?></strong></a>
          </p>
      </div>
    </div>
  <?php endif; ?>

  <?php if( $data_mapping_is_active ) : ?>
    <div class="wpwh-box wpwh-box--big mb-3">
      <div class="wpwh-box__body">
          <h2><?php echo WPWHPRO()->helpers->translate( 'Data Mapping', 'wpwhpro-page-features' ); ?></h2>
          <p class="mb-4"><?php echo WPWHPRO()->helpers->translate( 'This feature allows you to directly manipulate the format and structure of the (payload) data for trigger and action requests.', 'wpwhpro-page-features' ); ?></p>
          <p class="mb-1">
              <a href="<?php echo $data_mapping_url; ?>" rel="noopener noreferrer" class="text-secondary mr-4"><strong><?php echo WPWHPRO()->helpers->translate( 'Go to data mapping', 'wpwhpro-page-features' ); ?></strong></a>
              <a href="https://wp-webhooks.com/docs/article-categories/data-mapping/" target="_blank" rel="noopener noreferrer" class="mr-2"><strong><?php echo WPWHPRO()->helpers->translate( 'Documentation', 'wpwhpro-page-features' ); ?></strong></a>
          </p>
      </div>
    </div>
  <?php endif; ?>

  <?php if( $auth_is_active ) : ?>
    <div class="wpwh-box wpwh-box--big mb-3">
      <div class="wpwh-box__body">
          <h2><?php echo WPWHPRO()->helpers->translate( 'Authentication', 'wpwhpro-page-features' ); ?></h2>
          <p class="mb-4"><?php echo WPWHPRO()->helpers->translate( 'Add authentication to webhook triggers to communicate with externa, protected endpoints, or use it to protect incoming connections by applying authentication to them.', 'wpwhpro-page-features' ); ?></p>
          <p class="mb-1">
              <a href="<?php echo $authentication_url; ?>" rel="noopener noreferrer" class="text-secondary mr-4"><strong><?php echo WPWHPRO()->helpers->translate( 'Go to authentication', 'wpwhpro-page-features' ); ?></strong></a>
              <a href="https://wp-webhooks.com/docs/article-categories/authentication/" target="_blank" rel="noopener noreferrer" class="mr-2"><strong><?php echo WPWHPRO()->helpers->translate( 'Documentation', 'wpwhpro-page-features' ); ?></strong></a>
          </p>
      </div>
    </div>
  <?php endif; ?>

  <?php if( $whitelist_is_active ) : ?>
    <div class="wpwh-box wpwh-box--big mb-3">
      <div class="wpwh-box__body">
          <h2><?php echo WPWHPRO()->helpers->translate( 'IP Whitelist', 'wpwhpro-page-features' ); ?></h2>
          <p class="mb-4"><?php echo WPWHPRO()->helpers->translate( 'Protect evey incoming connection by only allowing a set (or range) of whitelisted IP addresses.', 'wpwhpro-page-features' ); ?></p>
          <p class="mb-1">
              <a href="<?php echo $whitelist_url; ?>" rel="noopener noreferrer" class="text-secondary mr-4"><strong><?php echo WPWHPRO()->helpers->translate( 'Go to IP whitelist', 'wpwhpro-page-features' ); ?></strong></a>
              <a href="https://wp-webhooks.com/docs/article-categories/whitelist/" target="_blank" rel="noopener noreferrer" class="mr-2"><strong><?php echo WPWHPRO()->helpers->translate( 'Documentation', 'wpwhpro-page-features' ); ?></strong></a>
          </p>
      </div>
    </div>
  <?php endif; ?>

</div>