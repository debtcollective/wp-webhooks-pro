<?php

$flows = WPWHPRO()->flows->get_flows();
$flows_nonce = WPWHPRO()->settings->get_flows_nonce();
$clear_form_url = WPWHPRO()->helpers->get_current_url( true, true );
$clean_url = WPWHPRO()->helpers->get_current_url( false, true );

if( isset( $_POST['wpwh-flows-name'] ) ){
  if ( check_admin_referer( $flows_nonce['action'], $flows_nonce['arg'] ) ) {

    if( WPWHPRO()->helpers->current_user_can( WPWHPRO()->settings->get_admin_cap( 'wpwhpro-page-flows-add-flow' ), 'wpwhpro-page-flows-add-flow' ) ){
      $flows_template = isset( $_POST['wpwh-flows-name'] ) ? wp_strip_all_tags( sanitize_text_field( $_POST['wpwh-flows-name'] ) ) : '';

      if( ! empty( $flows_template ) ){
        $flow_name = sanitize_title( $flows_template );
  
        $check = WPWHPRO()->flows->add_flow( array(
          'flow_title' => $flows_template,
          'flow_name' => $flow_name,
        ) );
  
          if( ! empty( $check ) && is_numeric( $check ) ){
            
            if( ! headers_sent() ){
              $new_flow_url = WPWHPRO()->helpers->built_url( $clean_url, array_merge( $_GET, array( 'flow_id' => $check, ) ) );
              wp_redirect( $new_flow_url );
              die();
            } else {
              $flows = WPWHPRO()->flows->get_flows( array(), false );
            }
            
          } else {
            echo WPWHPRO()->helpers->create_admin_notice( 'An error occured while creating the flow. Please try again.', 'warning', true );
          }
  
      }
    }

  }
}

?>
<?php add_ThickBox(); ?>
<div class="wpwh-container">
  <div class="wpwh-title-area mb-4">
    <h1><?php echo WPWHPRO()->helpers->translate( 'Flows', 'wpwhpro-page-flow' ); ?></h1>
    <p>
      <?php if( WPWHPRO()->whitelabel->is_active() && ! empty( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_flows' ) ) ) : ?>
        <?php echo WPWHPRO()->helpers->translate( WPWHPRO()->whitelabel->get_setting( 'wpwhpro_whitelabel_custom_text_flows' ), 'admin-settings-license' ); ?>
      <?php else : ?>
        <?php echo sprintf(WPWHPRO()->helpers->translate( 'Flows are automation workflows that allows you to do various actions after a specific event happened. To learn more about Flows, please take a look at <a class="text-secondary" title="Visit the Flows documentation" href="%s" target="_blank">our documentation</a>.', 'wpwhpro-page-flow' ), 'https://wp-webhooks.com/docs/knowledge-base/how-to-use-flow/'); ?>
      <?php endif; ?>
    </p>
  </div>

  <div class="wpwh-table-container">
    <div class="wpwh-table-header d-flex align-items-center justify-content-between">
      <h2><?php echo WPWHPRO()->helpers->translate( 'All Flows', 'wpwhpro-page-flow' ); ?></h2>
      <a href="#" class="wpwh-btn wpwh-btn--secondary" data-toggle="modal" data-target="#addAuthTemplateModal"><?php echo WPWHPRO()->helpers->translate( 'Create Flow', 'wpwhpro-page-flow' ); ?></a>
    </div>
    <table class="wpwh-table">
      <thead>
        <tr>
          <th class="w-10"><?php echo WPWHPRO()->helpers->translate( 'Id', 'wpwhpro-page-flow' ); ?></th>
          <th class="w-60"><?php echo WPWHPRO()->helpers->translate( 'Name', 'wpwhpro-page-flow' ); ?></th>
          <th class="w-60"><?php echo WPWHPRO()->helpers->translate( 'Trigger', 'wpwhpro-page-flow' ); ?></th>
          <th class="w-20"><?php echo WPWHPRO()->helpers->translate( 'Created', 'wpwhpro-page-flow' ); ?></th>
          <th class="text-center w-10"><?php echo WPWHPRO()->helpers->translate( 'Action', 'wpwhpro-page-flow' ); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if( ! empty( $flows ) ) : ?>
          <?php foreach( $flows as $template ) :

            $template_id = intval( $template->id );

            $template_name = 'unknown';
            if( isset( $template->flow_title ) ){
              $template_name = $template->flow_title;
            }

            $template_trigger = '';
            if( isset( $template->flow_trigger ) ){
              $template_trigger = $template->flow_trigger;
            }

          ?>
            <tr>
              <td><?php echo intval( $template_id ); ?></td>
              <td><?php echo sanitize_text_field( $template_name ); ?></td>
              <td><?php echo sanitize_text_field( $template_trigger ); ?></td>
              <td class="wpwh-w-50"><?php echo date( 'F j, Y, g:i a', strtotime( $template->flow_date ) ); ?></td>
              <td class="p-0 align-middle text-center">
                <div class="d-flex align-items-center justify-content-center">
                  <a
                    class="wpwh-btn wpwh-btn--link px-2 wpwh-btn--icon"
                    href="<?php echo WPWHPRO()->helpers->built_url( $clean_url, array_merge( $_GET, array( 'flow_id' => $template_id, ) ) ); ?>"
                    data-tippy=""
                    data-tippy-content="Edit flow"
                  >
                    <img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/cog.svg'; ?>" alt="<?php echo WPWHPRO()->helpers->translate( 'Edit', 'wpwhpro-page-logs' ); ?>">
                    <!-- <span><?php echo WPWHPRO()->helpers->translate( 'Edit', 'wpwhpro-page-logs' ); ?></span> -->
                  </a>
                  <button
                    type="button"
                    class="wpwh-btn wpwh-btn--link px-2 wpwh-btn--icon wpwh-delete-flow-template"
                    title="<?php echo WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-logs' ); ?>"
                    data-tippy=""
                    data-tippy-content="Delete"
                    data-wpwh-auth-id="<?php echo $template_id; ?>"
                  >
                    <img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/delete.svg'; ?>" alt="Delete">
                    <!-- <span><?php echo WPWHPRO()->helpers->translate( 'Delete', 'wpwhpro-page-logs' ); ?></span> -->
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else : ?>
          <tr>
            <td><?php echo WPWHPRO()->helpers->translate( 'No flows available. Create one first.', 'wpwhpro-page-flow' ); ?></td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>

<div class="modal fade" id="addAuthTemplateModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title"><?php echo WPWHPRO()->helpers->translate( 'Create Flow', 'wpwhpro-page-flow' ); ?></h3>
		    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
        </button>
      </div>
      <form action="<?php echo $clear_form_url; ?>" method="post">
        <div class="modal-body">
          <label class="wpwh-form-label" for="wpwh-flows-name"><?php echo WPWHPRO()->helpers->translate( 'Flow Name', 'wpwhpro-page-flow' ); ?></label>
					<input class="wpwh-form-input w-100" type="text" id="wpwh-flows-name" name="wpwh-flows-name" placeholder="<?php echo WPWHPRO()->helpers->translate( 'flow-name', 'wpwhpro-page-flow' ); ?>" />
        </div>
        <div class="modal-footer">
					<?php wp_nonce_field( $flows_nonce['action'], $flows_nonce['arg'] ); ?>
					<input type="submit" name="submit" id="submit" class="wpwh-btn wpwh-btn--secondary w-100" value="<?php echo WPWHPRO()->helpers->translate( 'Create', 'wpwhpro-page-flow' ); ?>">
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade modal--lg" id="editAuthTemplateModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title"><?php echo WPWHPRO()->helpers->translate( 'Edit Template', 'wpwhpro-page-flow' ); ?>: <span></span></h3>
		    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M13 1L1 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						<path d="M1 1L13 13" stroke="#0E0A1D" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
        </button>
      </div>
      <div class="modal-body" id="wpwh-flow-content-wrapper">
      </div>
      <div class="modal-footer text-center">
        <?php wp_nonce_field( $flows_nonce['action'], $flows_nonce['arg'] ); ?>
        <button type="button" id="wpwh-save-auth-template-button" class="wpwh-btn wpwh-btn--secondary" data-wpwh-auth-id="1">
          <span><?php echo WPWHPRO()->helpers->translate( 'Save Template', 'wpwhpro-page-flow' ); ?></span>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  jQuery(document).ready(function($) {
    /**
     * Flow:
     *
     * Delete flow template
     */
    $(document).on( "click", ".wpwh-delete-flow-template", function() {

      var $this = $(this);
      var dataAuthId = $this.data( 'wpwh-auth-id' );
      var wrapperHtml = '';

      if ( dataAuthId && confirm( "Are you sure you want to delete this template?" ) ) {

        // Prevent from clicking again
        if ( $this.hasClass( 'is-loading' ) ) {
          return;
        }

        $this.addClass( 'is-loading' );
        $this.find('img').animate( { 'opacity': 0 }, 150 );

        $.ajax({
          url: ironikusflows.ajax_url,
          type: 'post',
          data: {
            action: 'ironikus_flows_handler',
            ironikusflows_nonce: ironikusflows.ajax_nonce,
            handler: 'delete_flow',
            language: ironikusflows.language,
            flow_id: dataAuthId,
          },
          success: function( res ) {

            console.log(res);

            $this.removeClass( 'is-loading' );
            $this.find('img').animate( { 'opacity': 1 }, 150 );

            if ( res[ 'success' ] === 'true' || res[ 'success' ] === true ) {
              $this.closest('tr').remove();

              $('#wpwh-authentication-content-wrapper').html('');
            }
          },
          error: function( errorThrown ) {
            $this.removeClass( 'is-loading' );
            console.log( errorThrown );
          }
        });
      }

    });
  });
</script>