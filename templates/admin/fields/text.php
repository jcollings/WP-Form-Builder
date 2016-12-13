<?php
/**
 * Text edit panel
 *
 * @var WPDF_TextField $this
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
		<label for="" class="wpdf-label"><?php echo esc_html( WPDF()->text->get( 'fields.text.default.label' ) ); ?> <span class="wpdf-tooltip wpdf-tooltip__inline" title="<?php echo esc_attr( WPDF()->text->get( 'fields.text.default.help' ) ); ?>">?</span></label>
		<input type="text" class="wpdf-input" name="field[][default]" value="<?php echo esc_attr( $this->get_default_value() ); ?>">
	</div>
</div>
