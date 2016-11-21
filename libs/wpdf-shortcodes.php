<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 07/08/2016
 * Time: 18:06
 */

/**
 * Shortcode to display form
 *
 * @param $atts
 * @param null $content
 *
 * @return string
 */
function wpdf_shortcode_form( $atts, $content = null ){

	$a = shortcode_atts( array(
		'form' => '',
		'form_id' => 0
		// ...etc
	), $atts );

	$form_id = intval($a['form_id']);
	if($form_id > 0){
		$form = new WPDF_DB_Form($form_id);
		$form_key = $form->getName();
	}else{
		$form_key = $a['form'];
		$form = wpdf_get_form($form_key);
	}

	if(!$form){
		return sprintf('<p>%s</p>', __( "Shortcode Error: Form could not be displayed!", "wpdf"));
	}

	if($form_id > 0 && $form->get_setting('enable_style') == 'enabled'):
	?>
	<style type="text/css">
		<?php if($form->hasStyle('form_bg_colour')): ?>
		.wpdf-form {
			background: <?php echo $form->getStyle('form_bg_colour'); ?>;
		}
		<?php endif; ?>
		<?php if($form->hasStyle('form_text_colour')): ?>
		.wpdf-form, .wpdf-form p, .wpdf-form label {
			color: <?php echo $form->getStyle('form_text_colour'); ?>;
		}
		<?php endif; ?>
		<?php if($form->hasStyle('field_input_bg_colour') || $form->hasStyle('field_input_text_colour')): ?>
		.wpdf-form .wpdf-field {
			<?php if($form->hasStyle('field_input_bg_colour')): ?>
			background: <?php echo $form->getStyle('field_input_bg_colour'); ?>;
			<?php endif; ?>
			<?php if($form->hasStyle('field_input_text_colour')): ?>
			color: <?php echo $form->getStyle('field_input_text_colour'); ?>;
			<?php endif; ?>
		}
		<?php endif; ?>

		<?php if($form->hasStyle('field_label_bg_colour') || $form->hasStyle('field_label_text_colour')): ?>
		.wpdf-form .wpdf-label label {
			background: <?php echo $form->getStyle('field_label_bg_colour'); ?>;
			color: <?php echo $form->getStyle('field_label_text_colour'); ?>;
		}
		<?php endif; ?>

		<?php if($form->hasStyle('checkbox_text_colour')): ?>
		.wpdf-form .wpdf-choices label {
			color: <?php echo $form->getStyle('checkbox_text_colour'); ?>;
		}
		<?php endif; ?>

		<?php if($form->hasStyle('button_text_colour') || $form->hasStyle('button_bg_colour')): ?>
		.wpdf-form .wpdf-button {
			color: <?php echo $form->getStyle('button_text_colour'); ?>;
			background: <?php echo $form->getStyle('button_bg_colour'); ?>;
		}
		<?php endif; ?>

		<?php if($form->hasStyle('button_hover_text_colour') || $form->hasStyle('button_hover_bg_colour')): ?>
		.wpdf-form .wpdf-button:hover {
			color: <?php echo $form->getStyle('button_hover_text_colour'); ?>;
			background: <?php echo $form->getStyle('button_hover_bg_colour'); ?>;
		}
		<?php endif; ?>

		<?php if($form->hasStyle('form_bg_error_colour')): ?>
		.wpdf-form-error{
			background: <?php echo $form->getStyle('form_bg_error_colour'); ?>;
		}
		<?php endif; ?>
		<?php if($form->hasStyle('form_text_error_colour')): ?>
		.wpdf-form-error ul, .wpdf-form-error p {
			color: <?php echo $form->getStyle('form_text_error_colour'); ?>;
		}
		<?php endif; ?>
		<?php if($form->hasStyle('field_error_text_colour')): ?>
		.wpdf-field-error{
			color: <?php echo $form->getStyle('field_error_text_colour'); ?>;
		}
		<?php endif; ?>
		<?php if($form->hasStyle('field_border_colour')): ?>
		.wpdf-field{
			border-color: <?php echo $form->getStyle('field_border_colour'); ?>
		}
		<?php endif; ?>
		<?php if($form->hasStyle('field_error_border_colour')): ?>
		.wpdf-has-error .wpdf-field{
			border-color: <?php echo $form->getStyle('field_error_border_colour'); ?>
		}
		<?php endif; ?>
	</style>
	<?php
	endif;

	// output form
	ob_start();
	wpdf_display_form($form_key);
	return ob_get_clean();
}
add_shortcode('wp_form', 'wpdf_shortcode_form');
