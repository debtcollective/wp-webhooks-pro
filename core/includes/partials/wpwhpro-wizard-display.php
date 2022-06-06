<?php
/**
 * Wizard Template
 */

$wizard_nonce_data = WPWHPRO()->settings->get_wizard_nonce();
$current_step = sanitize_title( WPWHPRO()->wizard->get_current_step() );
$next_step = sanitize_title( WPWHPRO()->wizard->get_next_step() );
$previous_step = sanitize_title( WPWHPRO()->wizard->get_previous_step() );
$current_step_url = WPWHPRO()->helpers->built_url( '', array_merge( $_GET, array( 'wpwhwizard' => $current_step ) ) );

$next_step_url = '';
if( ! empty( $next_step ) ){
    $next_step_url = WPWHPRO()->helpers->built_url( '', array_merge( $_GET, array( 'wpwhwizard' => $next_step ) ) );
}

$previous_step_url = '';
if( ! empty( $previous_step ) ){
    $previous_step_url = WPWHPRO()->helpers->built_url( '', array_merge( $_GET, array( 'wpwhwizard' => $previous_step ) ) );
}

?>

<div class="wpwh">
    <div class="wpwh-main">
        <div class="wpwh-wizard p-4">
            <div class="wpwh-wizard__progress"></div>
            <form action="<?php echo $next_step_url; ?>" method="post" class="wpwh-wizard__form">

                <?php
                    if( current_user_can( apply_filters( 'wpwhpro/admin/settings/wizard', WPWHPRO()->settings->get_admin_cap( 'wpwhpro-wizard-settings' ), $current_step ) ) ){
                        do_action( 'wpwhpro/admin/settings/wizard/place_content', $current_step );
                    }
                ?>

                <div class="wpwh-separator"></div>
                <div class="wpwh-wizard__footer">
                    <input type="hidden" name="wpwh_wizard_step" value="<?php echo $current_step; ?>">
                    <?php echo WPWHPRO()->helpers->get_nonce_field( $wizard_nonce_data ); ?>
                    <div class="d-flex justify-content-center wpwh-text-center mt-4 pt-3">
                        <?php if( ! empty( $previous_step_url ) ) : ?>
                            <a class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm mr-1" href="<?php echo $previous_step_url; ?>">
                                <span><?php echo WPWHPRO()->helpers->translate( 'Previous step', 'admin-settings' ); ?></span>
                            </a>
                        <?php endif; ?>

                        <?php if( $next_step === 'complete' ) : ?>
                            <button class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm ml-1" type="submit" name="wpwh_wizard_submit">
                                <span><?php echo WPWHPRO()->helpers->translate( 'Complete', 'admin-settings' ); ?></span>
                            </button>
                        <?php else : ?>
                            <button class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm ml-1" type="submit" name="wpwh_wizard_submit">
                                <span><?php echo WPWHPRO()->helpers->translate( 'Next step', 'admin-settings' ); ?></span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

            </form>
        </div>
    </div>

</div>