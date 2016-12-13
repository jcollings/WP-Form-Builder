<?php
/**
 * Radio field edit panel
 *
 * @var WPDF_RadioField $this
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
			<strong><?php echo esc_html( WPDF()->text->get( 'fields.radio.values.heading.label' ) ); ?> <span class="wpdf-tooltip wpdf-tooltip__inline" title="<?php echo esc_attr( WPDF()->text->get( 'fields.radio.values.heading.help' ) ); ?>">?</span></strong>
		</div>

		<table width="100%" class="wpdf-repeater wpdf-field__values" data-min="1" data-template-name="field_value_repeater_radio">
			<thead>
			<tr>
				<th><?php echo esc_html( WPDF()->text->get( 'fields.radio.values.label.label' ) ); ?> <span class="wpdf-tooltip wpdf-tooltip__inline" title="<?php echo esc_attr( WPDF()->text->get( 'fields.radio.values.label.help' ) ); ?>">?</span></th>
				<th><?php echo esc_html( WPDF()->text->get( 'fields.radio.values.value.label' ) ); ?> <span class="wpdf-tooltip wpdf-tooltip__inline" title="<?php echo esc_attr( WPDF()->text->get( 'fields.radio.values.value.help' ) ); ?>">?</span></th>
				<th><?php echo esc_html( WPDF()->text->get( 'fields.radio.values.default.label' ) ); ?> <span class="wpdf-tooltip wpdf-tooltip__inline" title="<?php echo esc_attr( WPDF()->text->get( 'fields.radio.values.default.help' ) ); ?>">?</span></th>
				<th>_</th>
			</tr>
			</thead>
			<tbody class="wpdf-repeater-container">
			<script type="text/html" class="wpdf-repeater-template">
				<tr class="wpdf-repeater-row wpdf-repeater__template">
					<td><input title="<?php echo esc_attr( WPDF()->text->get( 'fields.radio.values.label.label' ) ); ?>" type="text" class="wpdf-input wpdf-data__label" name="field[][value_labels][]"></td>
					<td><input title="<?php echo esc_attr( WPDF()->text->get( 'fields.radio.values.value.label' ) ); ?>" type="text" class="wpdf-input wpdf-data__key" name="field[][value_keys][]"></td>
					<td><input title="<?php echo esc_attr( WPDF()->text->get( 'fields.radio.values.default.label' ) ); ?>" type="radio" name="field[][value_default]" class="wpdf-data__default"></td>
					<td>
						<a href="#" class="wpdf-add-row button">+</a>
						<a href="#" class="wpdf-del-row button">-</a>
					</td>
				</tr>
			</script>
			<?php
			$options = $this->get_options();
			$default = $this->get_default_value();
			if ( ! empty( $options ) ) {
				foreach ( $options as $key => $option ) {
					?>
					<tr class="wpdf-repeater-row wpdf-repeater__template">
						<td><input title="<?php echo esc_attr( WPDF()->text->get( 'fields.radio.values.label.label' ) ); ?>" type="text" class="wpdf-input wpdf-data__label"
						           name="field[][value_labels][]" value="<?php echo esc_attr( $option ); ?>" /></td>
						<td><input title="<?php echo esc_attr( WPDF()->text->get( 'fields.radio.values.value.label' ) ); ?>" type="text" class="wpdf-input wpdf-data__key wpdf-data__key--set"
						           name="field[][value_keys][]" value="<?php echo esc_attr( $key ); ?>" /></td>
						<td><input title="<?php echo esc_attr( WPDF()->text->get( 'fields.radio.values.default.label' ) ); ?>" type="radio"
						           name="field[][value_default]" class="wpdf-data__default" value="<?php echo esc_attr( $key ); ?>" <?php
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
					<td><input title="<?php echo esc_attr( WPDF()->text->get( 'fields.radio.values.label.label' ) ); ?>" type="text" class="wpdf-input wpdf-data__label" name="field[][value_labels][]" value="<?php echo esc_attr( $label ); ?>"></td>
					<td><input title="<?php echo esc_attr( WPDF()->text->get( 'fields.radio.values.value.label' ) ); ?>" type="text" class="wpdf-input wpdf-data__key" name="field[][value_keys][]" value="<?php echo esc_attr( $value ); ?>"></td>
					<td><input title="<?php echo esc_attr( WPDF()->text->get( 'fields.radio.values.default.label' ) ); ?>" type="radio" name="field[][value_default]" class="wpdf-data__default"></td>
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
