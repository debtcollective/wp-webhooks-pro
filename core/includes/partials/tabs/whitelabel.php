<?php

/*
 * Whitelabel settings template
 */

$settings = WPWHPRO()->settings->get_whitelabel_settings();
$whitelabel_nonce_data = WPWHPRO()->settings->get_whitelabel_nonce();
$show_whitelabel_settings = WPWHPRO()->license->is_active();
$current_url = WPWHPRO()->helpers->get_current_url( false, true );
$license_url = WPWHPRO()->helpers->built_url( $current_url, array( 'page' => $this->page_name, 'wpwhprovrs' => 'license' ) );

if( $show_whitelabel_settings ) : ?>
    <div class="wpwh-container">
        <form id="wpwh-main-settings-form" method="post" action="">

            <div class="wpwh-title-area mb-4">
                <h2><?php echo WPWHPRO()->helpers->translate( 'Whitelabel settings', 'wpwhpro-page-settings' ); ?></h2>
                <p class="wpwh-text-small">
                    <?php echo sprintf(WPWHPRO()->helpers->translate( 'This is the hidden page for your whitelabel settings. Here, you will be able to configure all settings related to create a whitelabeled version of %1$s. To learn more about it, you can also check out our <a href="%2$s" target="_blank" >documentation</a>.', 'admin-settings-license' ), 'WP Webhooks Pro', 'https://wp-webhooks.com/docs/knowledge-base/whitelabel-wp-webhooks-pro/'); ?>
                </p>
            </div>

            <div class="wpwh-settings">
                <?php foreach( $settings as $setting_name => $setting ) :

                $is_checked = ( $setting['type'] == 'checkbox' && $setting['value'] == 'yes' ) ? 'checked' : '';
                $value = ( $setting['type'] != 'checkbox' ) ? $setting['value'] : '1';
                $is_checkbox = ( $setting['type'] == 'checkbox' ) ? true : false;
                $is_textarea = ( $setting['type'] == 'textarea' ) ? true : false;

                ?>
                <div class="wpwh-setting">
                    <div class="wpwh-setting__title">
                    <label for="<?php echo $setting['id']; ?>"><?php echo $setting['label']; ?></label>
                    </div>
                    <div class="wpwh-setting__desc">
                    <?php echo wpautop( $setting['description'] ); ?>
                    </div>
                    <?php if( $is_checkbox ) : ?>
                        <div class="wpwh-setting__action">
                            <div class="wpwh-toggle wpwh-toggle--on-off">
                                <input type="<?php echo $setting['type']; ?>" id="<?php echo $setting['id']; ?>" name="<?php echo $setting_name; ?>" class="wpwh-toggle__input" <?php echo $is_checked; ?>>
                                <label class="wpwh-toggle__btn" for="<?php echo $setting['id']; ?>"></label>
                            </div>
                        </div>
                    <?php elseif( $is_textarea ) : ?>
                        <div class="wpwh-setting__action w-25">
                            <textarea class="wpwh-form-input" id="<?php echo $setting['id']; ?>" name="<?php echo $setting_name; ?>"><?php echo $value; ?></textarea>
                        </div>
                    <?php else : ?>
                        <div class="wpwh-setting__action w-25">
                            <input class="wpwh-form-input" id="<?php echo $setting['id']; ?>" name="<?php echo $setting_name; ?>" type="<?php echo $setting['type']; ?>" value="<?php echo $value; ?>" />
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="wpwh-text-center mt-4 pt-3">
                <button class="wpwh-btn wpwh-btn--secondary active" type="submit" name="wpwh_whitelabel_submit">
                <span><?php echo WPWHPRO()->helpers->translate( 'Save All Settings', 'admin-settings' ); ?></span>
                </button>
            </div>

            <?php echo WPWHPRO()->helpers->get_nonce_field( $whitelabel_nonce_data ); ?>
        </form>
    </div>
<?php else : ?>
    <div class="wpwh-container">
        <div class="wpwh-settings p-4">
            <div class="wpwh-title-area">
                <h2><?php echo WPWHPRO()->helpers->translate( 'Activate License', 'admin-settings-license' ); ?></h2>
                <p class="wpwh-text-small mb-3">
                    <?php echo sprintf(WPWHPRO()->helpers->translate( 'To use the whitelabel feature, you must have an active WP Webhooks Pro Unlimited subscription with an active license. Please activate your license first or check out our comparison table to <a class="text-secondary" href="%2$s" target="_blank" >learn more</a>.', 'admin-settings-license' ), 'WP Webhooks Pro', 'https://wp-webhooks.com/?utm_source=wp-webhooks-pro&utm_medium=whitelabel-feature&utm_campaign=WP%20Webhooks%20Pro'); ?>
                </p>
                <a href="<?php echo $license_url; ?>" class="wpwh-btn wpwh-btn--secondary" target="_blank" rel="noopener noreferrer"><?php echo WPWHPRO()->helpers->translate( 'Activate License', 'admin-settings-license' ); ?></a>
            </div>
        </div>
    </div>
<?php endif; ?>