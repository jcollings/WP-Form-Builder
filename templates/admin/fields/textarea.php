<?php
/**
 * Textarea edit panel
 *
 * @var WPDF_TextareaField $this
 *
 * @package WPDF/Admin/Field
 * @author James Collings
 * @created 03/11/2016
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="wpdf-field-row">
	<div class="wpdf-col wpdf-col__half">
		<label for="" class="wpdf-label">
			<?php echo esc_html( WPDF()->text->get( 'fields.textarea.default.label' ) ); ?>
			<span class="wpdf-tooltip wpdf-tooltip__inline" title="<?php echo esc_attr( WPDF()->text->get( 'fields.textarea.default.help' ) ); ?>">?</span>
		</label>

		<input type="text" class="wpdf-input" name="field[][rows]" value="<?php echo esc_attr( $this->get_rows() ); ?>">
	</div>
</div>
<div class="wpdf-field-row">
	<div class="wpdf-col wpdf-col__full">
		<label for="" class="wpdf-label"><?php echo esc_html( WPDF()->text->get( 'fields.textarea.rows.label' ) ); ?> <span class="wpdf-tooltip wpdf-tooltip__inline" title="<?php echo esc_attr( WPDF()->text->get( 'fields.textarea.rows.help' ) ); ?>">?</span></label>
		<textarea class="wpdf-input" name="field[][default]"><?php echo esc_textarea( $this->get_default_value() ); ?></textarea>
	</div>
</div>
