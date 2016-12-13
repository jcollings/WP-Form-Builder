<?php
/**
 * Form Style page
 *
 * @var WPDF_DB_Form $form
 *
 * @package WPDF/Admin
 * @author James Collings
 * @created 21/11/2016
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$form_id = '';
if ( false !== $form ) {
	$form_id = $form->get_id();
}
?>
<form action="" method="post">

	<input type="hidden" name="wpdf-action" value="edit-form-style"/>
	<input type="hidden" name="wpdf-form" value="<?php echo esc_attr( $form_id ); ?>"/>
	<div class="wpdf-form-manager wpdf-form-manager--inputs">

		<?php $this->display_form_header( 'style', $form ); ?>
		<div class="wpdf-cols">

			<div class="wpdf-full">
				<div class="wpdf-left__inside">

					<div id="error-wrapper">
						<?php
						if ( $this->get_success() > 0 ) {
							?>
							<p class="notice notice-success wpdf-notice wpdf-notice--success"><?php echo esc_html( WPDF()->text->get( 'form_saved', 'general' ) ); ?></p>
							<?php
						}
						?>
					</div>

					<h2 class="wpdf-settings__header">
						Form Styles
					</h2>
					<table class="wpdf-form-table wpdf-form-table--style">

						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="form_label">Background Colour</label>
								<span class="wpdf-tooltip" title="">?</span>
							</td>
							<td class="">
								<?php wpdf_iris_picker( 'wpdf_style[form_bg_colour]', $form->get_style( 'form_bg_colour', true ) ); ?>
							</td>
							<td>
								<label><input type="checkbox" name="wpdf_style_disable[]" value="form_bg_colour"
								              class="wpdf-checkbox" <?php checked( true, $form->is_style_disabled( 'form_bg_colour' ), true ); ?>/>
									Disable</label>
							</td>
						</tr>

						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="form_label">Text Colour</label>
								<span class="wpdf-tooltip" title="">?</span>
							</td>
							<td class="">
								<?php wpdf_iris_picker( 'wpdf_style[form_text_colour]', $form->get_style( 'form_text_colour', true ) ); ?>
							</td>
							<td>
								<label><input type="checkbox" name="wpdf_style_disable[]" value="form_text_colour"
								              class="wpdf-checkbox" <?php checked( true, $form->is_style_disabled( 'form_text_colour' ), true ); ?>/>
									Disable</label>
							</td>
						</tr>

					</table>

					<h3 class="wpdf-settings__header">
						Form Error Styles
					</h3>
					<table class="wpdf-form-table wpdf-form-table--style">

						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="form_label">Background Colour</label>
								<span class="wpdf-tooltip" title="">?</span>
							</td>
							<td class="">
								<?php wpdf_iris_picker( 'wpdf_style[form_bg_error_colour]', $form->get_style( 'form_bg_error_colour', true ) ); ?>
							</td>
							<td>
								<label><input type="checkbox" name="wpdf_style_disable[]" value="form_bg_error_colour"
								              class="wpdf-checkbox" <?php checked( true, $form->is_style_disabled( 'form_bg_error_colour' ), true ); ?>/>
									Disable</label>
							</td>
						</tr>

						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="form_label">Text Colour</label>
								<span class="wpdf-tooltip" title="">?</span>
							</td>
							<td class="">
								<?php wpdf_iris_picker( 'wpdf_style[form_text_error_colour]', $form->get_style( 'form_text_error_colour', true ) ); ?>
							</td>
							<td>
								<label><input type="checkbox" name="wpdf_style_disable[]" value="form_text_error_colour"
								              class="wpdf-checkbox" <?php checked( true, $form->is_style_disabled( 'form_text_error_colour' ), true ); ?>/>
									Disable</label>
							</td>
						</tr>

					</table>

					<h3 class="wpdf-settings__header">
						Form Success Styles
					</h3>
					<table class="wpdf-form-table wpdf-form-table--style">

						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="form_label">Background Colour</label>
								<span class="wpdf-tooltip" title="">?</span>
							</td>
							<td class="">
								<?php wpdf_iris_picker( 'wpdf_style[form_bg_success_colour]', $form->get_style( 'form_bg_success_colour', true ) ); ?>
							</td>
							<td>
								<label><input type="checkbox" name="wpdf_style_disable[]" value="form_bg_success_colour"
								              class="wpdf-checkbox" <?php checked( true, $form->is_style_disabled( 'form_bg_success_colour' ), true ); ?>/>
									Disable</label>
							</td>
						</tr>

						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="form_label">Text Colour</label>
								<span class="wpdf-tooltip" title="">?</span>
							</td>
							<td class="">
								<?php wpdf_iris_picker( 'wpdf_style[form_text_success_colour]', $form->get_style( 'form_text_success_colour', true ) ); ?>
							</td>
							<td>
								<label><input type="checkbox" name="wpdf_style_disable[]"
								              value="form_text_success_colour"
								              class="wpdf-checkbox" <?php checked( true, $form->is_style_disabled( 'form_text_success_colour' ), true ); ?>/>
									Disable</label>
							</td>
						</tr>

					</table>

					<h3 class="wpdf-settings__header">
						Field Styles
					</h3>
					<table class="wpdf-form-table wpdf-form-table--style">

						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="form_label">Label Background Colour</label>
								<span class="wpdf-tooltip" title="">?</span>
							</td>
							<td class="">
								<?php wpdf_iris_picker( 'wpdf_style[field_label_bg_colour]', $form->get_style( 'field_label_bg_colour', true ) ); ?>
							</td>
							<td>
								<label><input type="checkbox" name="wpdf_style_disable[]" value="field_label_bg_colour"
								              class="wpdf-checkbox" <?php checked( true, $form->is_style_disabled( 'field_label_bg_colour' ), true ); ?>/>
									Disable</label>
							</td>
						</tr>

						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="form_label">Label Text Colour</label>
								<span class="wpdf-tooltip" title="">?</span>
							</td>
							<td class="">
								<?php wpdf_iris_picker( 'wpdf_style[field_label_text_colour]', $form->get_style( 'field_label_text_colour', true ) ); ?>
							</td>
							<td>
								<label><input type="checkbox" name="wpdf_style_disable[]"
								              value="field_label_text_colour"
								              class="wpdf-checkbox" <?php checked( true, $form->is_style_disabled( 'field_label_text_colour' ), true ); ?>/>
									Disable</label>
							</td>
						</tr>

						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="form_label">Field Background Colour</label>
								<span class="wpdf-tooltip" title="">?</span>
							</td>
							<td class="">
								<?php wpdf_iris_picker( 'wpdf_style[field_input_bg_colour]', $form->get_style( 'field_input_bg_colour', true ) ); ?>
							</td>
							<td>
								<label><input type="checkbox" name="wpdf_style_disable[]" value="field_input_bg_colour"
								              class="wpdf-checkbox" <?php checked( true, $form->is_style_disabled( 'field_input_bg_colour' ), true ); ?>/>
									Disable</label>
							</td>
						</tr>

						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="form_label">Field Text Colour</label>
								<span class="wpdf-tooltip" title="">?</span>
							</td>
							<td class="">
								<?php wpdf_iris_picker( 'wpdf_style[field_input_text_colour]', $form->get_style( 'field_input_text_colour', true ) ); ?>
							</td>
							<td>
								<label><input type="checkbox" name="wpdf_style_disable[]"
								              value="field_input_text_colour"
								              class="wpdf-checkbox" <?php checked( true, $form->is_style_disabled( 'field_input_text_colour' ), true ); ?>/>
									Disable</label>
							</td>
						</tr>

						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="form_label">Field Border Colour</label>
								<span class="wpdf-tooltip" title="">?</span>
							</td>
							<td class="">
								<?php wpdf_iris_picker( 'wpdf_style[field_border_colour]', $form->get_style( 'field_border_colour', true ) ); ?>
							</td>
							<td>
								<label><input type="checkbox" name="wpdf_style_disable[]" value="field_border_colour"
								              class="wpdf-checkbox" <?php checked( true, $form->is_style_disabled( 'field_border_colour' ), true ); ?>/>
									Disable</label>
							</td>
						</tr>

						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="form_label">Field Border Error Colour</label>
								<span class="wpdf-tooltip" title="">?</span>
							</td>
							<td class="">
								<?php wpdf_iris_picker( 'wpdf_style[field_error_border_colour]', $form->get_style( 'field_error_border_colour', true ) ); ?>
							</td>
							<td>
								<label><input type="checkbox" name="wpdf_style_disable[]"
								              value="field_error_border_colour"
								              class="wpdf-checkbox" <?php checked( true, $form->is_style_disabled( 'field_error_border_colour' ), true ); ?>/>
									Disable</label>
							</td>
						</tr>

						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="form_label">Error Text Colour</label>
								<span class="wpdf-tooltip" title="">?</span>
							</td>
							<td class="">
								<?php wpdf_iris_picker( 'wpdf_style[field_error_text_colour]', $form->get_style( 'field_error_text_colour', true ) ); ?>
							</td>
							<td>
								<label><input type="checkbox" name="wpdf_style_disable[]"
								              value="field_error_text_colour"
								              class="wpdf-checkbox" <?php checked( true, $form->is_style_disabled( 'field_error_text_colour' ), true ); ?>/>
									Disable</label>
							</td>
						</tr>


					</table>

					<h3 class="wpdf-settings__header">
						Checkbox &amp; Radio Styles
					</h3>
					<table class="wpdf-form-table wpdf-form-table--style">
						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="form_label">Option Text Colour</label>
								<span class="wpdf-tooltip" title="">?</span>
							</td>
							<td class="">
								<?php wpdf_iris_picker( 'wpdf_style[checkbox_text_colour]', $form->get_style( 'checkbox_text_colour', true ) ); ?>
							</td>
							<td>
								<label><input type="checkbox" name="wpdf_style_disable[]" value="checkbox_text_colour"
								              class="wpdf-checkbox" <?php checked( true, $form->is_style_disabled( 'checkbox_text_colour' ), true ); ?>/>
									Disable</label>
							</td>
						</tr>
					</table>

					<h3 class="wpdf-settings__header">
						Button Styles
					</h3>
					<table class="wpdf-form-table wpdf-form-table--style">

						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="form_label">Background Colour</label>
								<span class="wpdf-tooltip" title="">?</span>
							</td>
							<td class="">
								<?php wpdf_iris_picker( 'wpdf_style[button_bg_colour]', $form->get_style( 'button_bg_colour', true ) ); ?>
							</td>
							<td>
								<label><input type="checkbox" name="wpdf_style_disable[]" value="button_bg_colour"
								              class="wpdf-checkbox" <?php checked( true, $form->is_style_disabled( 'button_bg_colour' ), true ); ?>/>
									Disable</label>
							</td>
						</tr>

						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="form_label">Text Colour</label>
								<span class="wpdf-tooltip" title="">?</span>
							</td>
							<td class="">
								<?php wpdf_iris_picker( 'wpdf_style[button_text_colour]', $form->get_style( 'button_text_colour', true ) ); ?>
							</td>
							<td>
								<label><input type="checkbox" name="wpdf_style_disable[]" value="button_text_colour"
								              class="wpdf-checkbox" <?php checked( true, $form->is_style_disabled( 'button_text_colour' ), true ); ?>/>
									Disable</label>
							</td>
						</tr>

						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="form_label">Hover Background Colour</label>
								<span class="wpdf-tooltip" title="">?</span>
							</td>
							<td class="">
								<?php wpdf_iris_picker( 'wpdf_style[button_hover_bg_colour]', $form->get_style( 'button_hover_bg_colour', true ) ); ?>
							</td>
							<td>
								<label><input type="checkbox" name="wpdf_style_disable[]" value="button_hover_bg_colour"
								              class="wpdf-checkbox" <?php checked( true, $form->is_style_disabled( 'button_hover_bg_colour' ), true ); ?>/>
									Disable</label>
							</td>
						</tr>

						<tr>
							<td class="wpdf-tooltip__wrapper">
								<label for="form_label">Hover Text Colour</label>
								<span class="wpdf-tooltip" title="">?</span>
							</td>
							<td class="">
								<?php wpdf_iris_picker( 'wpdf_style[button_hover_text_colour]', $form->get_style( 'button_hover_text_colour', true ) ); ?>
							</td>
							<td>
								<label><input type="checkbox" name="wpdf_style_disable[]"
								              value="button_hover_text_colour"
								              class="wpdf-checkbox" <?php checked( true, $form->is_style_disabled( 'button_hover_text_colour' ), true ); ?>/>
									Disable</label>
							</td>
						</tr>

					</table>

				</div>
			</div>

		</div>

		<div class="wpdf-clear"></div>
	</div>
</form>
