<?php
/**
 * Form settings page
 *
 * @var WPDF_DB_Form $form
 */

$form_id = '';
if($form !== false){
	$form_id = $form->getId();
}

$settings = $form->export();
$confirmation_redirect = $confirmation_message = $confirmation_type = $submit_label = '';
if($settings){

	if(isset($settings['confirmations'])) {
		$confirmation = $settings['confirmations'][0];
		$confirmation_type = $confirmation['type'];
		$confirmation_message = $confirmation['message'];
		$confirmation_redirect = $confirmation['redirect_url'];
	}

	if(isset($settings['settings'])){

		if(isset($settings['settings']['labels'])){
			$submit_label = $settings['settings']['labels']['submit'];
		}
	}
}

$recaptcha_private = $form->get_setting('recaptcha_private');
$recaptcha_public = $form->get_setting('recaptcha_public');
?>
<form action="" method="post">

	<input type="hidden" name="wpdf-action" value="edit-form-settings" />
	<input type="hidden" name="wpdf-form" value="<?php echo $form_id; ?>" />
	<div class="wpdf-form-manager">

		<?php $this->display_form_header('settings', $form); ?>
		<div class="wpdf-cols">

			<div class="wpdf-full">
				<div class="wpdf-left__inside">

					<table class="wpdf-form-table">
						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="submit_label">Form Label</label>
								<span class="wpdf-tooltip" title="Used as the form name and only to help identify the form.">?</span>
							</td>
							<td class="notification__input"><input id="submit_label" type="text" name="wpdf_settings[form_label]" value="<?php echo $form->getLabel(); ?>" /></td>
						</tr>
						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="submit_label">Submit Button Text</label>
								<span class="wpdf-tooltip" title="Text displayed on the forms submit button">?</span>
							</td>
							<td class="notification__input"><input id="submit_label" type="text" name="wpdf_settings[submit_label]" value="<?php echo $submit_label; ?>" /></td>
						</tr>
						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="confirmation_type">Confirmation Type</label>
								<span class="wpdf-tooltip" title="On successful form submission redirect the user to a page or display a message">?</span>
							</td>
							<td class="notification__input"><select name="wpdf_settings[confirmation_type]" id="confirmation_type">
									<option value="message" <?php selected($confirmation_type, 'message', true); ?>>Message</option>
									<option value="redirect" <?php selected($confirmation_type, 'redirect', true); ?>>Redirect</option>
								</select></td>
						</tr>
						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="confirmation_message">Confirmation Message</label>
								<span class="wpdf-tooltip" title="Message to be displayed on successful form submission">?</span>
							</td>
							<td class="notification__input"><textarea name="wpdf_settings[confirmation_message]" id="confirmation_message" cols="30" rows="10"><?php echo $confirmation_message; ?></textarea></td>
						</tr>
						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="confirmation_redirect">Confirmation Redirect</label>
								<span class="wpdf-tooltip" title="Url to redirect the user on successful form submission">?</span>
							</td>
							<td class="notification__input"><input name="wpdf_settings[confirmation_redirect]" id="confirmation_redirect" type="text" value="<?php echo $confirmation_redirect; ?>" /></td>
						</tr>
						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="recaptcha_public">ReCAPTCHA Public Key</label>
								<span class="wpdf-tooltip" title="Enter your ReCAPTCHA Site key">?</span>
							</td>
							<td class="notification__input"><input name="wpdf_settings[recaptcha_public]" id="recaptcha_public" type="text" value="<?php echo $recaptcha_public; ?>" /></td>
						</tr>
						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="recaptcha_private">ReCAPTCHA Private Key</label>
								<span class="wpdf-tooltip" title="Enter your ReCAPTCHA Secret key">?</span>
							</td>
							<td class="notification__input"><input name="wpdf_settings[recaptcha_private]" id="recaptcha_private" type="text" value="<?php echo $recaptcha_private; ?>" /></td>
						</tr>
					</table>
					&nbsp;
				</div>
			</div>

		</div>

		<div class="wpdf-clear"></div>
	</div>
</form>