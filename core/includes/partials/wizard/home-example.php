<?php

//Get the current step
//WPWHPRO()->wizard->get_current_step();


//Get next step
//WPWHPRO()->wizard->get_next_step();

//Get the previous step
//WPWHPRO()->wizard->get_previous_step();

?>
<header class="wpwh-wizard__header">
	<h2>Welcome to WP Webhooks!</h2>
	<p>Let's get you set up.</p>
</header>
<div class="wpwh-separator"></div>
<div class="wpwh-wizard__main">
	<div class="wpwh-form-field">
		<label for="form_1" class="wpwh-form-label">What category best describes yours website?</label>
		<p class="wpwh-form-description">We will recommend the optimal settings for WP Webhooks based on your choice.</p>
		<ul class="wpwh-form-options">
			<li class="wpwh-form-option">
				<input type="radio" name="website" id="website-1">
				<label for="website-1">Business website</label>
			</li>
			<li class="wpwh-form-option">
				<input type="radio" name="website" id="website-2">
				<label for="website-2">Personal (blog)</label>
			</li>
			<li class="wpwh-form-option">
				<input type="radio" name="website" id="website-3">
				<label for="website-3">Ecommerce</label>
			</li>
		</ul>
	</div>
	<div class="wpwh-form-field">
		<label for="form_1" class="wpwh-form-label">License Key</label>
		<div class="wpwh-form-description">
			<p>You're using WP Webhooks</p>
			<p>To unlock more features consider upgrading to PRO.</p>
		</div>
	</div>
	<div class="wpwh-form-field">
		<label for="form_1" class="wpwh-form-label">Input field label</label>
		<p class="wpwh-form-description">We will recommend the optimal settings for WP Webhooks based on your choice.</p>
		<input type="text" name="input" id="input-1" class="wpwh-form-input" placeholder="Placeholder goes here">
	</div>
	<div class="wpwh-form-field">
		<label for="form_1" class="wpwh-form-label">What category best describes yours website?</label>
		<p class="wpwh-form-description">We will recommend the optimal settings for WP Webhooks based on your choice.</p>
		<ul class="wpwh-form-options">
			<li class="wpwh-form-option">
				<input type="checkbox" name="checkbox-id[]" id="checkbox-id-1">
				<label for="checkbox-id-1">Checkbox option 1</label>
			</li>
			<li class="wpwh-form-option">
				<input type="checkbox" name="checkbox-id[]" id="checkbox-id-2">
				<label for="checkbox-id-2">Checkbox option 2</label>
			</li>
			<li class="wpwh-form-option">
				<input type="checkbox" name="checkbox-id[]" id="checkbox-id-3">
				<label for="checkbox-id-3">Checkbox option 3</label>
			</li>
		</ul>
	</div>
	<div class="wpwh-form-field">
		<label for="form_1" class="wpwh-form-label">Select field label</label>
		<p class="wpwh-form-description">We will recommend the optimal settings for WP Webhooks based on your choice.</p>
		<select name="input" id="input-1" class="wpwh-form-input" placeholder="Placeholder goes here">
			<option value="option-1">Option 1</option>
			<option value="option-2">Option 2</option>
			<option value="option-3">Option 3</option>
			<option value="option-4">Option 4</option>
		</select>
	</div>
</div>