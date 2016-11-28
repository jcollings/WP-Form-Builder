<?php
/**
 * Radio field edit panel
 *
 * @var WPDF_RadioField $this
 */
?>
<div class="wpdf-field-row">
	<div class="wpdf-col wpdf-col__full">

		<div class="field-values__header">
			<strong>Values <span class="wpdf-tooltip wpdf-tooltip__inline" title="List of available options">?</span></strong>
		</div>

		<table width="100%" class="wpdf-repeater wpdf-field__values" data-min="1" data-template-name="field_value_repeater_radio">
			<thead>
			<tr>
				<th>Label <span class="wpdf-tooltip wpdf-tooltip__inline" title="Text displayed in dropdown">?</span></th>
				<th>Value <span class="wpdf-tooltip wpdf-tooltip__inline" title="Value stored in database when selected, leave blank to auto generate from label">?</span></th>
				<th>Default <span class="wpdf-tooltip wpdf-tooltip__inline" title="Check value to make default when form is loaded">?</span></th>
				<th>_</th>
			</tr>
			</thead>
			<tbody class="wpdf-repeater-container">
			<script type="text/html" class="wpdf-repeater-template">
				<tr class="wpdf-repeater-row wpdf-repeater__template">
					<td><input title="Label" type="text" class="wpdf-input wpdf-data__label" name="field[][value_labels][]"></td>
					<td><input title="Key" type="text" class="wpdf-input wpdf-data__key" name="field[][value_keys][]"></td>
					<td><input title="Default?" type="radio" name="field[][value_default]" class="wpdf-data__default"></td>
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
						<td><input title="Label" type="text" class="wpdf-input wpdf-data__label"
						           name="field[][value_labels][]" value="<?php echo $option; ?>" /></td>
						<td><input title="Key" type="text" class="wpdf-input wpdf-data__key wpdf-data__key--set"
						           name="field[][value_keys][]" value="<?php echo $key; ?>" /></td>
						<td><input title="Default?" type="radio"
						           name="field[][value_default]" class="wpdf-data__default" value="<?php echo $key; ?>" <?php
							if ( is_array( $default ) ) {
								checked( in_array( $key, $default ), true, true );
							}else{
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
			}else{
				?>
				<tr class="wpdf-repeater-row wpdf-repeater__template">
					<td><input title="Label" type="text" class="wpdf-input wpdf-data__label" name="field[][value_labels][]"></td>
					<td><input title="Key" type="text" class="wpdf-input wpdf-data__key" name="field[][value_keys][]"></td>
					<td><input title="Default?" type="radio" name="field[][value_default]" class="wpdf-data__default"></td>
					<td>
						<a href="#" class="wpdf-add-row button">+</a>
						<a href="#" class="wpdf-del-row button">-</a>
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>

	</div>
</div>