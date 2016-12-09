<?php
/**
 * Select field edit panel
 *
 * @var WPDF_SelectField $this
 */
?>
<div class="wpdf-field-row">
	<div class="wpdf-col wpdf-col__half">
		<label for="" class="wpdf-label">Empty Text <span class="wpdf-tooltip wpdf-tooltip__inline" title="Default value shown when the form is loaded, leave empty to not show">?</span></label>
		<input type="text" class="wpdf-input" name="field[][empty_text]" value="<?php echo esc_attr( $this->get_empty() ); ?>">
	</div>
	<div class="wpdf-col wpdf-col__half">
		<label for="" class="wpdf-label">Select Type <span class="wpdf-tooltip wpdf-tooltip__inline" title="Multiple selects allow the user to select multiple values instead of only a single value.">?</span></label>
		<select name="field[][select_type]" class="wpdf-input">
			<option value="single" <?php selected( 'single', $this->get_select_type(), true ); ?>>Single</option>
			<option value="multiple" <?php selected( 'multiple', $this->get_select_type(), true ); ?>>Multiple</option>
		</select>
	</div>
</div>
<div class="wpdf-field-row">
	<div class="wpdf-col wpdf-col__full">

		<div class="field-values__header">
			<strong>Values <span class="wpdf-tooltip wpdf-tooltip__inline" title="List of available options">?</span></strong>
		</div>

		<table width="100%" class="wpdf-repeater wpdf-field__values" data-min="1" data-template-name="field_value_repeater_select">
			<thead>
			<tr>
				<th>Label <span class="wpdf-tooltip wpdf-tooltip__inline" title="Text displayed in dropdown">?</span></th>
				<th>Value <span class="wpdf-tooltip wpdf-tooltip__inline" title="Value stored in when selected, leave blank to auto generate from label">?</span></th>
				<th>Default <span class="wpdf-tooltip wpdf-tooltip__inline" title="Check value to make default when form is loaded">?</span></th>
				<th>_</th>
			</tr>
			</thead>
			<tbody class="wpdf-repeater-container">
			<script type="text/html" class="wpdf-repeater-template">
				<tr class="wpdf-repeater-row wpdf-repeater__template">
					<td><input title="Label" type="text" class="wpdf-input wpdf-data__label" name="field[][value_labels][]"></td>
					<td><input title="Key" type="text" class="wpdf-input wpdf-data__key" name="field[][value_keys][]"></td>
					<td><input title="Default?" type="checkbox" name="field[][value_default][]"></td>
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
						<td><input title="Label" type="text" class="wpdf-input wpdf-data__label"
						           name="field[][value_labels][]" value="<?php echo esc_attr( $option ); ?>" /></td>
						<td><input title="Key" type="text" class="wpdf-input wpdf-data__key"
						           name="field[][value_keys][]" value="<?php echo esc_attr( $key ); ?>" /></td>
						<td><input title="Default?" type="checkbox"
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
				foreach ( $defaults as $value => $label ) :
				?>
				<tr class="wpdf-repeater-row wpdf-repeater__template">
					<td><input title="Label" type="text" class="wpdf-input wpdf-data__label" name="field[][value_labels][]" value="<?php echo esc_attr( $label ); ?>"></td>
					<td><input title="Key" type="text" class="wpdf-input wpdf-data__key" name="field[][value_keys][]" value="<?php echo esc_attr( $value ); ?>"></td>
					<td><input title="Default?" type="checkbox" name="field[][value_default][]"></td>
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