<?php
/**
 * Checkbox field edit panel
 *
 * @var WPDF_CheckboxField $this
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
	<div class="wpdf-col wpdf-col__full">

		<div class="field-values__header">
			<strong><?php echo esc_html( WPDF()->text->get( 'fields.checkbox.values.heading.label' ) ); ?> <span class="wpdf-tooltip wpdf-tooltip__inline" title="<?php echo esc_attr( WPDF()->text->get( 'fields.checkbox.values.heading.help' ) ); ?>">?</span></strong>
		</div>

		<table width="100%" class="wpdf-repeater wpdf-field__values" data-min="1" data-template-name="field_value_repeater_checkbox">
			<thead>
			<tr>
				<th><?php echo esc_html( WPDF()->text->get( 'fields.checkbox.values.label.label' ) ); ?> <span class="wpdf-tooltip wpdf-tooltip__inline" title="<?php echo esc_attr( WPDF()->text->get( 'fields.checkbox.values.label.help' ) ); ?>">?</span></th>
				<th><?php echo esc_html( WPDF()->text->get( 'fields.checkbox.values.value.label' ) ); ?> <span class="wpdf-tooltip wpdf-tooltip__inline" title="<?php echo esc_attr( WPDF()->text->get( 'fields.checkbox.values.value.help' ) ); ?>">?</span></th>
				<th><?php echo esc_html( WPDF()->text->get( 'fields.checkbox.values.default.label' ) ); ?> <span class="wpdf-tooltip wpdf-tooltip__inline" title="<?php echo esc_attr( WPDF()->text->get( 'fields.checkbox.values.default.help' ) ); ?>">?</span></th>
				<th>_</th>
			</tr>
			</thead>
			<tbody class="wpdf-repeater-container">
			<script type="text/html" class="wpdf-repeater-template">
				<tr class="wpdf-repeater-row wpdf-repeater__template">
					<td><input title="<?php echo esc_attr( WPDF()->text->get( 'fields.checkbox.values.label.label' ) ); ?>" type="text" class="wpdf-input wpdf-data__label" name="field[][value_labels][]"></td>
					<td><input title="<?php echo esc_attr( WPDF()->text->get( 'fields.checkbox.values.value.label' ) ); ?>" type="text" class="wpdf-input wpdf-data__key" name="field[][value_keys][]"></td>
					<td><input title="<?php echo esc_attr( WPDF()->text->get( 'fields.checkbox.values.default.label' ) ); ?>" type="checkbox" name="field[][value_default][]"></td>
					<td>
						<a href="#" class="wpdf-add-row button">+</a>
						<a href="#" class="wpdf-del-row button">-</a>
					</td>
				</tr>
			</script>
			<?php
			$options = $this->get_options();
			if ( ! empty( $options ) ) {
				foreach ( $options as $key => $option ) {
					?>
					<tr class="wpdf-repeater-row wpdf-repeater__template">
						<td><input title="<?php echo esc_attr( WPDF()->text->get( 'fields.checkbox.values.label.label' ) ); ?>" type="text" class="wpdf-input wpdf-data__label"
						           name="field[][value_labels][]" value="<?php echo esc_attr( $option ); ?>" /></td>
						<td><input title="<?php echo esc_attr( WPDF()->text->get( 'fields.checkbox.values.value.label' ) ); ?>" type="text" class="wpdf-input wpdf-data__key"
						           name="field[][value_keys][]" value="<?php echo esc_attr( $key ); ?>" /></td>
						<td><input title="<?php echo esc_attr( WPDF()->text->get( 'fields.checkbox.values.default.label' ) ); ?>" type="checkbox"
						           name="field[][value_default][]" <?php
									$default = $this->get_default_value();
									if ( is_array( $default ) ) {
										checked( in_array( $key, $default, true ), true, true );
									} else {
										checked( $key, $default, true );
									}
								?> /></td>
						<td>
							<a href="#" class="wpdf-add-row button">+</a>
							<a href="#" class="wpdf-del-row button">-</a>
						</td>
					</tr>
					<?php
				}
			} else {
				$defaults = array(
					'one' => 'Option One',
					'two' => 'Option Two',
					'three' => 'Option Three',
				);
				foreach ( $defaults as $value => $label ) : ?>
				<tr class="wpdf-repeater-row wpdf-repeater__template">
					<td><input title="<?php echo esc_attr( WPDF()->text->get( 'fields.checkbox.values.label.label' ) ); ?>" type="text" class="wpdf-input wpdf-data__label" name="field[][value_labels][]" value="<?php echo esc_attr( $label ); ?>"></td>
					<td><input title="<?php echo esc_attr( WPDF()->text->get( 'fields.checkbox.values.value.label' ) ); ?>" type="text" class="wpdf-input wpdf-data__key" name="field[][value_keys][]" value="<?php echo esc_attr( $value ); ?>"></td>
					<td><input title="<?php echo esc_attr( WPDF()->text->get( 'fields.checkbox.values.default.label' ) ); ?>" type="checkbox" name="field[][value_default][]"></td>
					<td>
						<a href="#" class="wpdf-add-row button">+</a>
						<a href="#" class="wpdf-del-row button">-</a>
					</td>
				</tr>
				<?php
				endforeach;
			}
			?>
			</tbody>
		</table>

	</div>
</div>
