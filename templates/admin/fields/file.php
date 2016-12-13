<?php
/**
 * File field edit panel
 *
 * @var WPDF_FileField $this
 *
 * @package WPDF/Admin/Field
 * @author James Collings
 * @created 23/11/2016
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="wpdf-field-row">
	<div class="wpdf-col wpdf-col__half">
		<label for="" class="wpdf-label"><?php echo esc_html( sprintf( WPDF()->text->get( 'fields.file.max_file_size.label' ) , $this->get_server_limit() ) ); ?> <span class="wpdf-tooltip wpdf-tooltip__inline" title="<?php echo esc_attr( WPDF()->text->get( 'fields.file.max_file_size.help' ) ); ?>">?</span></label>
		<input type="number" class="wpdf-input" name="field[][max_file_size]" value="<?php echo esc_attr( intval( $this->get_max_filesize() ) ); ?>">
	</div>
	<div class="wpdf-col wpdf-col__half">
		<label for="" class="wpdf-label"><?php echo esc_html( WPDF()->text->get( 'fields.file.allowed_ext.label' ) ); ?> <span class="wpdf-tooltip wpdf-tooltip__inline" title="<?php echo esc_attr( WPDF()->text->get( 'fields.file.allowed_ext.help' ) ); ?>">?</span></label>
		<input type="text" class="wpdf-input" name="field[][allowed_ext]" value="<?php echo esc_attr( $this->get_allowed_ext() ); ?>">
	</div>
</div>
