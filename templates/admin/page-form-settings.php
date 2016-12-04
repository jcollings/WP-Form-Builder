<?php
/**
 * Form settings page
 *
 * @var WPDF_DB_Form $form
 *
 * @package WPDF/Admin
 * @author James Collings
 * @created 06/08/2016
 */

$form_id = '';
if ( false !== $form ) {
	$form_id = $form->get_id();
}

$settings              = $form->export();

$confirmation_location = $confirmation_redirect = $confirmation_message = $confirmation_type = $submit_label = '';
if ( $settings ) {

	if ( isset( $settings['confirmations'] ) ) {
		$confirmation          = $settings['confirmations'][0];
		$confirmation_type     = $confirmation['type'];
		$confirmation_message  = $confirmation['message'];
		$confirmation_redirect = isset( $confirmation['redirect_url'] ) ? $confirmation['redirect_url'] : '';
	}

	$confirmation_location = isset( $settings['confirmation_location'] ) ? $settings['confirmation_location'] : 'after';

	if ( isset( $settings['settings'] ) ) {

		if ( isset( $settings['settings']['labels'] ) ) {
			$submit_label = $settings['settings']['labels']['submit'];
		}
	}
}

// reCAPTCHA settings.
$recaptcha_private = $form->get_setting( 'recaptcha_private' );
$recaptcha_public  = $form->get_setting( 'recaptcha_public' );
?>
<form action="" method="post">

	<input type="hidden" name="wpdf-action" value="edit-form-settings"/>
	<input type="hidden" name="wpdf-form" value="<?php echo esc_attr( $form_id ); ?>"/>
	<div class="wpdf-form-manager wpdf-form-manager--inputs">

		<?php $this->display_form_header( 'settings', $form ); ?>
		<div class="wpdf-cols">

			<div class="wpdf-full">
				<div class="wpdf-left__inside">

					<h2 class="wpdf-settings__header">
						General Settings
					</h2>

					<table class="wpdf-form-table">
						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="form_label">Form Name</label>
								<span class="wpdf-tooltip"
								      title="Name of form, displayed when outputting form.">?</span>
							</td>
							<td class="notification__input"><input id="form_label" type="text"
							                                       name="wpdf_settings[form_label]"
							                                       value="<?php echo esc_attr( $form->get_label() ); ?>"/></td>
						</tr>
						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="form_content">Form Content</label>
								<span class="wpdf-tooltip"
								      title="Content displayed on the form before the fields">?</span>
							</td>
							<td class="notification__input"><textarea name="wpdf_settings[form_content]"
							                                          id="form_content" cols="30"
							                                          rows="10"><?php echo esc_textarea( $form->get_content() ); ?></textarea>
							</td>
						</tr>
						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="submit_label">Submit Button Text</label>
								<span class="wpdf-tooltip" title="Text displayed on the forms submit button">?</span>
							</td>
							<td class="notification__input"><input id="submit_label" type="text"
							                                       name="wpdf_settings[submit_label]"
							                                       value="<?php echo esc_attr( $submit_label ); ?>"/></td>
						</tr>
					</table>

					<h2 class="wpdf-settings__header">
						Form Confirmation <span class="wpdf-tooltip wpdf-tooltip__inline"
						                        title="Set what happens when the form is successfully submitted.">?</span>
					</h2>

					<table class="wpdf-form-table">
						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="confirmation_type">Confirmation Type</label>
								<span class="wpdf-tooltip"
								      title="On successful form submission redirect the user to a page or display a message">?</span>
							</td>
							<td class="notification__input"><select name="wpdf_settings[confirmation_type]"
							                                        id="confirmation_type">
									<option value="message" <?php selected( $confirmation_type, 'message', true ); ?>>
										Message
									</option>
									<option value="redirect" <?php selected( $confirmation_type, 'redirect', true ); ?>>
										Redirect
									</option>
								</select></td>
						</tr>
						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="confirmation_location">Confirmation Location</label>
								<span class="wpdf-tooltip"
								      title="Choose where the confirmation message is displayed.">?</span>
							</td>
							<td class="notification__input"><select name="wpdf_settings[confirmation_location]"
							                                        id="confirmation_location">
									<option value="after" <?php selected( $confirmation_location, 'after', true ); ?>>
										After Form Content
									</option>
									<option value="replace" <?php selected( $confirmation_location, 'replace', true ); ?>>
										Replace Form Content
									</option>
								</select></td>
						</tr>
						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="confirmation_message">Confirmation Message</label>
								<span class="wpdf-tooltip"
								      title="Message to be displayed on successful form submission">?</span>
							</td>
							<td class="notification__input"><textarea name="wpdf_settings[confirmation_message]"
							                                          id="confirmation_message" cols="30"
							                                          rows="10"><?php echo esc_textarea( $confirmation_message ); ?></textarea>
							</td>
						</tr>
						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="confirmation_redirect">Confirmation Redirect</label>
								<span class="wpdf-tooltip"
								      title="Url to redirect the user on successful form submission">?</span>
							</td>
							<td class="notification__input"><input name="wpdf_settings[confirmation_redirect]"
							                                       id="confirmation_redirect" type="text"
							                                       value="<?php echo esc_attr( $confirmation_redirect ); ?>"/></td>
						</tr>
					</table>

					<h2 class="wpdf-settings__header">
						Form Errors <span class="wpdf-tooltip wpdf-tooltip__inline"
						                       title="Modify text displayed on form output.">?</span>
					</h2>

					<table class="wpdf-form-table">
						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="submit_label">Field Error Message</label>
								<span class="wpdf-tooltip" title="Change text displayed when form has error.">?</span>
							</td>
							<td class="notification__input"><textarea name="wpdf_settings[error][general_message]"
							                                          id="general_error_message" cols="30"
							                                          rows="10"><?php echo esc_textarea( $form->get_setting( 'general_message', 'error' ) ); ?></textarea>
							</td>
						</tr>
						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="submit_label">Display list of field errors</label>
								<span class="wpdf-tooltip" title="Enable the list of field errors below the general error">?</span>
							</td>
							<td class="notification__input">
								<select name="wpdf_settings[error][show_fields]" id="wpdf_settings-enable_style">
									<option value="yes" <?php selected( 'yes', $form->get_setting( 'show_fields', 'error' ), true ); ?>>
										Yes
									</option>
									<option value="no" <?php selected( 'no', $form->get_setting( 'show_fields', 'error' ), true ); ?>>
										No
									</option>
								</select>
							</td>
						</tr>
					</table>

					<h2 class="wpdf-settings__header">
						Display Settings <span class="wpdf-tooltip wpdf-tooltip__inline"
						                       title="Settings related to the form output">?</span>
					</h2>

					<table class="wpdf-form-table">
						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="submit_label">Style Editor</label>
								<span class="wpdf-tooltip" title="Enable the use of the visual style editor">?</span>
							</td>
							<td class="notification__input">
								<select name="wpdf_settings[enable_style]" id="wpdf_settings-enable_style">
									<option value="enabled" <?php selected( 'enabled', $form->get_setting( 'enable_style' ), true ); ?>>
										Enable
									</option>
									<option value="disabled" <?php selected( 'disabled', $form->get_setting( 'enable_style' ), true ); ?>>
										Disable
									</option>
								</select>
							</td>
						</tr>

						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="submit_label">Layout CSS</label>
								<span class="wpdf-tooltip"
								      title="Enable this to help override the themes default display of the form">?</span>
							</td>
							<td class="notification__input">
								<select name="wpdf_settings[enable_layout_css]" id="wpdf_settings-enable_style">
									<option value="enabled" <?php selected( 'enabled', $form->get_setting( 'enable_layout_css' ), true ); ?>>
										Enable
									</option>
									<option value="disabled" <?php selected( 'disabled', $form->get_setting( 'enable_layout_css' ), true ); ?>>
										Disable
									</option>
								</select>
							</td>
						</tr>
					</table>

					<h2 class="wpdf-settings__header">
						ReCAPTCHA Settings <span class="wpdf-tooltip wpdf-tooltip__inline"
						                         title="reCAPTCHA is a free service that protects your website from spam and abuse">?</span>
					</h2>

					<div class="wpdf-settings__desc">
						<p>To generate or get your ReCAPTCHA details goto: <a target="_blank"
						                                                      href="https://www.google.com/recaptcha/intro/index.html">https://www.google.com/recaptcha/intro/index.html</a>
							and follow their instructions to generate an api key (you will need both site key and secret
							key).</p>
					</div>

					<table class="wpdf-form-table">
						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="recaptcha_public">ReCAPTCHA Site Key</label>
								<span class="wpdf-tooltip" title="Enter your ReCAPTCHA Site key">?</span>
							</td>
							<td class="notification__input"><input name="wpdf_settings[recaptcha_public]"
							                                       id="recaptcha_public" type="text"
							                                       value="<?php echo esc_attr( $recaptcha_public ); ?>"/></td>
						</tr>
						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="recaptcha_private">ReCAPTCHA Public Key</label>
								<span class="wpdf-tooltip" title="Enter your ReCAPTCHA Secret key">?</span>
							</td>
							<td class="notification__input"><input name="wpdf_settings[recaptcha_private]"
							                                       id="recaptcha_private" type="text"
							                                       value="<?php echo esc_attr( $recaptcha_private ); ?>"/></td>
						</tr>
					</table>
					&nbsp;
				</div>
			</div>

		</div>

		<div class="wpdf-clear"></div>
	</div>
</form>