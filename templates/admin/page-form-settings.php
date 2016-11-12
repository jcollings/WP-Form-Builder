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
							<td><label for="submit_label">Form Label</label></td>
							<td><input id="submit_label" type="text" name="wpdf_settings[form_label]" value="<?php echo $form->getLabel(); ?>" /></td>
						</tr>
						<tr>
							<td><label for="submit_label">Submit Button Text</label></td>
							<td><input id="submit_label" type="text" name="wpdf_settings[submit_label]" value="<?php echo $submit_label; ?>" /></td>
						</tr>
						<tr>
							<td><label for="confirmation_type">Confirmation Type</label></td>
							<td><select name="wpdf_settings[confirmation_type]" id="confirmation_type">
									<option value="message" <?php selected($confirmation_type, 'message', true); ?>>Message</option>
									<option value="redirect" <?php selected($confirmation_type, 'redirect', true); ?>>Redirect</option>
								</select></td>
						</tr>
						<tr>
							<td><label for="confirmation_message">Confirmation Message</label></td>
							<td><textarea name="wpdf_settings[confirmation_message]" id="confirmation_message" cols="30" rows="10"><?php echo $confirmation_message; ?></textarea></td>
						</tr>
						<tr>
							<td><label for="confirmation_redirect">Confirmation Redirect</label></td>
							<td><input name="wpdf_settings[confirmation_redirect]" id="confirmation_redirect" type="text" value="<?php echo $confirmation_redirect; ?>" /></td>
						</tr>
					</table>
					&nbsp;
				</div>
			</div>

		</div>

		<div class="wpdf-clear"></div>
	</div>
</form>