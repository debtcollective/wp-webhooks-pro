<div class="wpwh-flows-wrapper">
	<div class="wpwh-container">
		<div class="wpwh-title-area mb-4">
			<h2>Add new Webhook</h2>
		</div>

		<form action="" method="post">
			<div class="wpwh-flows">

				<div class="wpwh-flows__content-sidebar">
					<div class="wpwh-flows__content">

						<div class="wpwh-form-field">
							<label for="wpwhAddNew-name" class="wpwh-form-label">Enter Title Here</label>
							<input type="text" name="title" id="wpwhAddNew-name" class="wpwh-form-input wpwh-w-100">
						</div>

						<?php
						/**
						 * START - Flows steps
						 *
						 * This part is basically steps for both triggers as well as actions.
						 * For now, I'm naming it 'steps' since I've already used 'flows' as the wrapper
						 * element for this entire page. Might change 'steps' into more easily understanable
						 * name later.
						 */
						?>
						<div class="wpwh-flows-steps">
							<div class="wpwh-flows-step">
								<div class="wpwh-flows-step__inner">

									<div class="wpwh-flows-step__header">

										<div class="wpwh-flows-step__header-icon">
											<img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/wp-icon.svg'; ?>" alt="">
										</div>

										<div class="wpwh-flows-step__header-text">

											<p class="wpwh-flows-step__header-text">
												<span class="wpwh-text-small">Trigger</span> <a href="#" data-toggle="modal" data-target="#chooseIntegrationModal">Choose integration</a>
											</p>

											<h3 class="wpwh-flows-step__title"><span>Automator</span></h3>

										</div>

									</div>
									<!-- ./ Step Header -->

									<div class="wpwh-flows-step__body">

										<h3 class="wpwh-flows-step__heading">Select a trigger</h3>

										<div class="wpwh-form-field">
											<label for="wpwhFlowTrigger1-1" class="wpwh-form-label">Lorem Ipsum Text</label>
											<select name="title" id="wpwhFlowTrigger1-1" class="wpwh-form-input wpwh-w-100">
												<option value="01">Send Data on User Deletion</option>
												<option value="02">Send Data on User Deletion 2</option>
												<option value="03">Send Data on User Deletion 3</option>
											</select>
										</div>

										<div class="wpwh-flows-step__fields-group">

											<div class="wpwh-form-field">
												<label for="wpwhFlowTrigger1-slug" class="wpwh-form-label">Slug</label>
												<input type="text" name="slug" id="wpwhFlowTrigger1-slug" class="wpwh-form-input wpwh-w-100">
											</div>

											<div class="wpwh-form-field">
												<label for="wpwhFlowTrigger1-type" class="wpwh-form-label">Type <span class="wpwh-text-danger">*</span></label>
												<select name="title" id="wpwhFlowTrigger1-type" class="wpwh-form-input wpwh-w-100" required>
													<option value="01"></option>
													<option value="02">Type 1</option>
													<option value="03">Type 2</option>
													<option value="04">Type 3</option>
												</select>
											</div>

											<div class="wpwh-form-field">
												<label for="wpwhFlowTrigger1-status" class="wpwh-form-label">Status <span class="wpwh-text-danger">*</span></label>
												<select name="title" id="wpwhFlowTrigger1-status" class="wpwh-form-input wpwh-w-100" required>
													<option value="01"></option>
													<option value="02">Status 1</option>
													<option value="03">Status 2</option>
													<option value="04">Status 3</option>
												</select>
											</div>

											<div class="wpwh-form-field wpwh-cm-field">
												<label for="wpwhFlowTrigger1-status" class="wpwh-form-label">Status <span class="wpwh-text-danger">*</span></label>

												<div class="wpwh-cm-field__input">
													<textarea name="" id=""></textarea>
												</div>
											</div>

										</div>

									</div>
									<!-- ./ Step Body -->

								</div>
							</div>
						</div>

						<?php /* START - SELECTING A TRIGGER AND ACTIONS */ ?>
						<button type="button" class="wpwh-btn wpwh-btn--secondary wpwh-btn--lg wpwh-btn--block wpwh-flows-select-trigger mt-5" data-toggle="modal" data-target="#chooseIntegrationModal">
							<span>Select a trigger</span>
						</button>
						<div class="d-flex align-items-center justify-content-center my-3">
							<svg width="5" height="24" viewBox="0 0 5 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<circle opacity="0.3" cx="2.49981" cy="2.32256" r="2.32256" fill="#393939"/>
								<circle opacity="0.3" cx="2.49981" cy="11.9998" r="2.32256" fill="#393939"/>
								<circle opacity="0.3" cx="2.49981" cy="21.6775" r="2.32256" fill="#393939"/>
							</svg>
						</div>
						<button class="wpwh-btn wpwh-btn--secondary wpwh-btn--lg wpwh-btn--block wpwh-flows-select-trigger" disabled><span>Add an action</span></button>
						<?php /* END - SELECTING A TRIGGER AND ACTIONS */ ?>

					</div>
					<!-- /.wpwh-flows__content -->

					<aside class="wpwh-flows__sidebar">

						<div class="wpwh-flows-widget">
							<div class="wpwh-publish-box">

								<p class="d-flex align-items-center">
									<span class="wpwh-publish-box__icon">
										<svg width="7" height="13" viewBox="0 0 7 13" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path opacity="0.6" d="M2.625 7.92969C2.88281 7.97656 3.14062 8 3.375 8C3.60938 8 3.84375 7.97656 4.125 7.92969V11.6094L3.60938 12.3828C3.53906 12.4766 3.46875 12.5 3.375 12.5C3.28125 12.5 3.1875 12.4766 3.14062 12.3828L2.625 11.6094V7.92969ZM3.375 0.5C3.98438 0.5 4.54688 0.664062 5.0625 0.96875C5.57812 1.27344 5.97656 1.67188 6.28125 2.1875C6.58594 2.70312 6.75 3.26562 6.75 3.875C6.75 4.48438 6.58594 5.04688 6.28125 5.5625C5.97656 6.07812 5.57812 6.5 5.0625 6.80469C4.54688 7.10938 3.98438 7.25 3.375 7.25C2.76562 7.25 2.20312 7.10938 1.6875 6.80469C1.17188 6.5 0.75 6.07812 0.445312 5.5625C0.140625 5.04688 0 4.48438 0 3.875C0 3.26562 0.140625 2.70312 0.445312 2.1875C0.75 1.67188 1.17188 1.27344 1.6875 0.96875C2.20312 0.664062 2.76562 0.5 3.375 0.5ZM3.375 2.28125C3.44531 2.28125 3.51562 2.25781 3.5625 2.21094C3.60938 2.16406 3.65625 2.09375 3.65625 2C3.65625 1.92969 3.60938 1.85938 3.5625 1.8125C3.51562 1.76562 3.44531 1.71875 3.375 1.71875C2.76562 1.71875 2.27344 1.92969 1.85156 2.35156C1.42969 2.77344 1.21875 3.28906 1.21875 3.875C1.21875 3.96875 1.24219 4.03906 1.28906 4.08594C1.33594 4.13281 1.40625 4.15625 1.5 4.15625C1.57031 4.15625 1.64062 4.13281 1.6875 4.08594C1.73438 4.03906 1.78125 3.96875 1.78125 3.875C1.78125 3.45312 1.92188 3.07812 2.25 2.75C2.55469 2.44531 2.92969 2.28125 3.375 2.28125Z" fill="#393939"/>
										</svg>
									</span>
									<span class="wpwh-publish-box__title">Status:</span>
									<select name="status" id="wpwhFlowsAddNew-status" class="wpwh-form-input wpwh-form-input--sm w-auto">
										<option value="draft">Draft</option>
										<option value="publish">Publish</option>
									</select>
								</p>

								<p class="d-flex align-items-center">
									<span class="wpwh-publish-box__icon">
										<svg width="11" height="13" viewBox="0 0 11 13" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path opacity="0.6" d="M0.28125 5C0.1875 5 0.117188 5.04688 0.0703125 5.09375C0.0234375 5.14062 0 5.21094 0 5.28125V11.375C0 11.7031 0.09375 11.9609 0.328125 12.1719C0.539062 12.4062 0.796875 12.5 1.125 12.5H9.375C9.67969 12.5 9.9375 12.4062 10.1719 12.1719C10.3828 11.9609 10.5 11.7031 10.5 11.375V5.28125C10.5 5.21094 10.4531 5.14062 10.4062 5.09375C10.3594 5.04688 10.2891 5 10.2188 5H0.28125ZM10.5 3.96875C10.5 4.0625 10.4531 4.13281 10.4062 4.17969C10.3594 4.22656 10.2891 4.25 10.2188 4.25H0.28125C0.1875 4.25 0.117188 4.22656 0.0703125 4.17969C0.0234375 4.13281 0 4.0625 0 3.96875V3.125C0 2.82031 0.09375 2.5625 0.328125 2.32812C0.539062 2.11719 0.796875 2 1.125 2H2.25V0.78125C2.25 0.710938 2.27344 0.640625 2.32031 0.59375C2.36719 0.546875 2.4375 0.5 2.53125 0.5H3.46875C3.53906 0.5 3.60938 0.546875 3.65625 0.59375C3.70312 0.640625 3.75 0.710938 3.75 0.78125V2H6.75V0.78125C6.75 0.710938 6.77344 0.640625 6.82031 0.59375C6.86719 0.546875 6.9375 0.5 7.03125 0.5H7.96875C8.03906 0.5 8.10938 0.546875 8.15625 0.59375C8.20312 0.640625 8.25 0.710938 8.25 0.78125V2H9.375C9.67969 2 9.9375 2.11719 10.1719 2.32812C10.3828 2.5625 10.5 2.82031 10.5 3.125V3.96875Z" fill="#393939"/>
										</svg>
									</span>
									<span class="wpwh-publish-box__title">Created on:</span>
									<strong>July 14, 2021</strong>
								</p>

								<button type="submit" class="wpwh-btn wpwh-btn--secondary wpwh-btn--sm"><span>Publish</span></button>

							</div>
						</div>

						<div class="wpwh-flows-widget">
							<h4 class="wpwh-flows-widget__title">Selected Steps:</h4>
							<div class="wpwh-selected-steps">

								<p class="m-0">No selected steps</p>

							</div>
						</div>

					</aside>
					<!-- /.wpwh-flows__sidebar -->
				</div>

			</div>
		</form>
	</div>
</div>

<!-- INTEGRATION MODAL - START -->
<div class="modal fade wpwh-flows-modal" id="chooseIntegrationModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Choose Integration</h3>
		    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M8.125 6.5L12.3047 10.7188C12.4219 10.8359 12.5 10.9922 12.5 11.1484C12.5 11.3438 12.4219 11.5 12.3047 11.5781L11.3281 12.5547C11.2109 12.7109 11.0547 12.75 10.8984 12.75C10.7031 12.75 10.5469 12.7109 10.4688 12.5547L6.25 8.375L2.03125 12.5547C1.91406 12.7109 1.75781 12.75 1.60156 12.75C1.40625 12.75 1.25 12.7109 1.17188 12.5547L0.195312 11.5781C0.0390625 11.5 0 11.3438 0 11.1484C0 10.9922 0.0390625 10.8359 0.195312 10.7188L4.375 6.5L0.195312 2.28125C0.0390625 2.20312 0 2.04688 0 1.85156C0 1.69531 0.0390625 1.53906 0.195312 1.42188L1.17188 0.445312C1.25 0.328125 1.40625 0.25 1.60156 0.25C1.75781 0.25 1.91406 0.328125 2.03125 0.445312L6.25 4.625L10.4688 0.445312C10.5469 0.328125 10.7031 0.25 10.8984 0.25C11.0547 0.25 11.2109 0.328125 11.3281 0.445312L12.3047 1.42188C12.4219 1.53906 12.5 1.69531 12.5 1.85156C12.5 2.04688 12.4219 2.20312 12.3047 2.28125L8.125 6.5Z" fill="#393939"/>
					</svg>
        </button>
      </div>
      <div class="modal-body" id="wpwh-authentication-content-wrapper">
				<div class="wpwh-flows-integrations">
					<a href="#" class="wpwh-flows-integration">
						<div class="wpwh-flows-integration__inner">
							<div class="wpwh-flows-integration__icon">
								<img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/wp-icon.svg'; ?>" alt="">
							</div>
							<p class="wpwh-flows-integration__text">Automator Name</p>
						</div>
					</a>
					<a href="#" class="wpwh-flows-integration">
						<div class="wpwh-flows-integration__inner">
							<div class="wpwh-flows-integration__icon">
								<img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/wp-icon.svg'; ?>" alt="">
							</div>
							<p class="wpwh-flows-integration__text">Automator Name</p>
						</div>
					</a>
					<a href="#" class="wpwh-flows-integration">
						<div class="wpwh-flows-integration__inner">
							<div class="wpwh-flows-integration__icon">
								<img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/wp-icon.svg'; ?>" alt="">
							</div>
							<p class="wpwh-flows-integration__text">Automator Name</p>
						</div>
					</a>
					<a href="#" class="wpwh-flows-integration">
						<div class="wpwh-flows-integration__inner">
							<div class="wpwh-flows-integration__icon">
								<img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/wp-icon.svg'; ?>" alt="">
							</div>
							<p class="wpwh-flows-integration__text">Automator Name</p>
						</div>
					</a>
					<a href="#" class="wpwh-flows-integration">
						<div class="wpwh-flows-integration__inner">
							<div class="wpwh-flows-integration__icon">
								<img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/wp-icon.svg'; ?>" alt="">
							</div>
							<p class="wpwh-flows-integration__text">Automator Name</p>
						</div>
					</a>
					<a href="#" class="wpwh-flows-integration">
						<div class="wpwh-flows-integration__inner">
							<div class="wpwh-flows-integration__icon">
								<img src="<?php echo WPWHPRO_PLUGIN_URL . 'core/includes/assets/img/wp-icon.svg'; ?>" alt="">
							</div>
							<p class="wpwh-flows-integration__text">Automator Name</p>
						</div>
					</a>
				</div>
      </div>
    </div>
  </div>
</div>
<!-- INTEGRATION MODAL - END -->

<script>
jQuery(document).ready(function($) {
  // $('[data-target="#chooseIntegrationModal"]').trigger('click');
});
</script>