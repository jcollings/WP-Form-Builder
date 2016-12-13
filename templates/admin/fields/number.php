<?php
/**
 * Number edit panel
 *
 * @var WPDF_NumberField $this
 *
 * @package WPDF/Admin/Fields
 * @author James Collings
 * @created 11/12/2016
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="wpdf-field-row">
	<div class="wpdf-col wpdf-col__half">
		<label for="" class="wpdf-label">
			<?php echo esc_html( WPDF()->text->get( 'fields.number.type.label' ) ); ?>
			<span class="wpdf-tooltip wpdf-tooltip__inline" title="<?php echo esc_attr( WPDF()->text->get( 'fields.number.type.help' ) ); ?>">?</span>
		</label>

		<select name="field[][display_type]" id="">
			<option value="input" <?php selected( 'input', $this->get_display_type(), true ); ?>><?php echo esc_html( WPDF()->text->get( 'fields.number.type.option.input' ) ); ?></option>
			<option value="input-range" <?php selected( 'input-range', $this->get_display_type(), true ); ?>><?php echo esc_html( WPDF()->text->get( 'fields.number.type.option.input-range' ) ); ?></option>
			<option value="slider" <?php selected( 'slider', $this->get_display_type(), true ); ?>><?php echo esc_html( WPDF()->text->get( 'fields.number.type.option.slider' ) ); ?></option>
			<option value="slider-range" <?php selected( 'slider-range', $this->get_display_type(), true ); ?>><?php echo esc_html( WPDF()->text->get( 'fields.number.type.option.slider-range' ) ); ?></option>
		</select>
	</div>
</div>
<div class="wpdf-field-row">
	<div class="wpdf-col wpdf-col__half">
		<label for="" class="wpdf-label"><?php echo esc_html( WPDF()->text->get( 'fields.number.min.label' ) ); ?> <span class="wpdf-tooltip wpdf-tooltip__inline" title="<?php echo esc_attr( WPDF()->text->get( 'fields.number.min.help' ) ); ?>">?</span></label>
		<input class="wpdf-input" name="field[][min]" type="text" value="<?php echo esc_attr( $this->get_min_value() ); ?>" />
	</div>
	<div class="wpdf-col wpdf-col__half">
		<label for="" class="wpdf-label"><?php echo esc_html( WPDF()->text->get( 'fields.number.max.label' ) ); ?> <span class="wpdf-tooltip wpdf-tooltip__inline" title="<?php echo esc_attr( WPDF()->text->get( 'fields.number.max.help' ) ); ?>">?</span></label>
		<input class="wpdf-input" name="field[][max]" type="text" value="<?php echo esc_attr( $this->get_max_value() ); ?>" />
	</div>
</div>
<div class="wpdf-field-row">
	<div class="wpdf-col wpdf-col__half">
		<label for="" class="wpdf-label"><?php echo esc_html( WPDF()->text->get( 'fields.number.step.label' ) ); ?> <span class="wpdf-tooltip wpdf-tooltip__inline" title="<?php echo esc_attr( WPDF()->text->get( 'fields.number.step.help' ) ); ?>">?</span></label>
		<input class="wpdf-input" name="field[][step]" type="text" value="<?php echo esc_attr( $this->get_step_value() ); ?>" />
	</div>
	<div class="wpdf-col wpdf-col__half">
		<label for="" class="wpdf-label"><?php echo esc_html( WPDF()->text->get( 'fields.number.default.label' ) ); ?> <span class="wpdf-tooltip wpdf-tooltip__inline" title="<?php echo esc_attr( WPDF()->text->get( 'fields.number.default.help' ) ); ?>">?</span></label>
		<input class="wpdf-input" name="field[][default]" type="text" value="<?php echo esc_attr( $this->get_default_value() ); ?>" />
	</div>
</div>
