<?php
/**
 * Form Shortcodes
 *
 * @package WPDF
 * @author James Collings
 * @created 07/08/2016
 */

/**
 * Shortcode to display form
 *
 * @param array  $atts Shortcode attributes.
 * @param string $content Text in shortcode.
 *
 * @return string
 */
function wpdf_shortcode_form( $atts, $content = null ) {

	$a = shortcode_atts( array(
		'form'    => '',
		'form_id' => 0,
	), $atts );

	$form_id = intval( $a['form_id'] );
	if ( $form_id > 0 ) {
		$form     = new WPDF_DB_Form( $form_id );
		$form_key = $form->get_name();
	} else {
		$form_key = $a['form'];
		$form     = wpdf_get_form( $form_key );
	}

	if ( ! $form ) {
		return sprintf( '<p>%s</p>', __( 'Shortcode Error: Form could not be displayed!', 'wpdf' ) );
	}

	if ( $form_id > 0 && 'enabled' === $form->get_setting( 'enable_style' ) ) :
		?>
		<style type="text/css">
			<?php if ( $form->has_style( 'form_bg_colour' ) ) : ?>
			.wpdf-form {
				background: <?php echo esc_attr( $form->get_style( 'form_bg_colour' ) ); ?>;
			}

			<?php endif; ?>
			<?php if ( $form->has_style( 'form_text_colour' ) ) : ?>
			.wpdf-form, .wpdf-form p, .wpdf-form label {
				color: <?php echo esc_attr( $form->get_style( 'form_text_colour' ) ); ?>;
			}

			<?php endif; ?>
			<?php if ( $form->has_style( 'field_input_bg_colour' ) || $form->has_style( 'field_input_text_colour' ) ) : ?>
			.wpdf-form .wpdf-field {
				<?php if ( $form->has_style( 'field_input_bg_colour' ) ) : ?> background: <?php echo esc_attr( $form->get_style( 'field_input_bg_colour' ) ); ?>;<?php endif; ?>
				<?php if ( $form->has_style( 'field_input_text_colour' ) ) : ?> color: <?php echo esc_attr( $form->get_style( 'field_input_text_colour' ) ); ?>;<?php endif; ?>
			}

			<?php endif; ?>

			<?php if ( $form->has_style( 'field_label_bg_colour' ) || $form->has_style( 'field_label_text_colour' ) ) : ?>
			.wpdf-form .wpdf-label label {
				background: <?php echo esc_attr( $form->get_style( 'field_label_bg_colour' ) ); ?>;
				color: <?php echo esc_attr( $form->get_style( 'field_label_text_colour' ) ); ?>;
			}

			<?php endif; ?>

			<?php if ( $form->has_style( 'checkbox_text_colour' ) ) : ?>
			.wpdf-form .wpdf-choices label {
				color: <?php echo esc_attr( $form->get_style( 'checkbox_text_colour' ) ); ?>;
			}

			<?php endif; ?>

			<?php if ( $form->has_style( 'button_text_colour' ) || $form->has_style( 'button_bg_colour' ) ) : ?>
			.wpdf-form .wpdf-button {
				color: <?php echo esc_attr( $form->get_style( 'button_text_colour' ) ); ?>;
				background: <?php echo esc_attr( $form->get_style( 'button_bg_colour' ) ); ?>;
				border: 1px solid <?php echo esc_attr( $form->get_style( 'button_bg_colour' ) ); ?>;
			}

			<?php endif; ?>

			<?php if ( $form->has_style( 'button_hover_text_colour' ) || $form->has_style( 'button_hover_bg_colour' ) ) : ?>
			.wpdf-form .wpdf-button:hover {
				color: <?php echo esc_attr( $form->get_style( 'button_hover_text_colour' ) ); ?>;
				background: <?php echo esc_attr( $form->get_style( 'button_hover_bg_colour' ) ); ?>;
				border: 1px solid <?php echo esc_attr( $form->get_style( 'button_hover_bg_colour' ) ); ?>;
			}

			<?php endif; ?>

			<?php if ( $form->has_style( 'form_bg_error_colour' ) ) : ?>
			.wpdf-form-error {
				background: <?php echo esc_attr( $form->get_style( 'form_bg_error_colour' ) ); ?>;
			}

			<?php endif; ?>
			<?php if ( $form->has_style( 'form_text_error_colour' ) ) : ?>
			.wpdf-form-error ul, .wpdf-form-error p {
				color: <?php echo esc_attr( $form->get_style( 'form_text_error_colour' ) ); ?>;
			}

			<?php endif; ?>
			<?php if ( $form->has_style( 'field_error_text_colour' ) ) : ?>
			.wpdf-field-error {
				color: <?php echo esc_attr( $form->get_style( 'field_error_text_colour' ) ); ?>;
			}

			<?php endif; ?>
			<?php if ( $form->has_style( 'field_border_colour' ) ) : ?>
			.wpdf-field {
				border: 1px solid <?php echo esc_attr( $form->get_style( 'field_border_colour' ) ); ?>
			}

			<?php endif; ?>
			<?php if ( $form->has_style( 'field_error_border_colour' ) ) : ?>
			.wpdf-has-error .wpdf-field {
				border: 1px solid <?php echo esc_attr( $form->get_style( 'field_error_border_colour' ) ); ?>
			}

			<?php endif; ?>

			<?php if ( $form->has_style( 'form_bg_success_colour' ) || $form->has_style( 'form_text_success_colour' ) ) : ?>
			.wpdf-form-confirmation {
			<?php if ( $form->has_style( 'form_bg_success_colour' ) ) : ?> background: <?php echo esc_attr( $form->get_style( 'form_bg_success_colour' ) ); ?>;
			<?php endif; ?> <?php if ( $form->has_style( 'form_text_success_colour' ) ) : ?> color: <?php echo esc_attr( $form->get_style( 'form_text_success_colour' ) ); ?>;
			<?php endif; ?>
			}

			<?php endif; ?>

		</style>
		<?php
	endif;

	ob_start();
	wpdf_display_form( $form_key );

	return ob_get_clean();
}

add_shortcode( 'wp_form', 'wpdf_shortcode_form' );
