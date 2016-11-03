<div class="wpdf-field-row">
	<div class="wpdf-col wpdf-col__full">

		<strong>Values</strong>

		<table width="100%" class="wpdf-repeater" data-min="1" data-template-name="field_value_repeater">
			<thead>
			<tr>
				<th>Label</th>
				<th>Key</th>
				<th>Default?</th>
				<th>_</th>
			</tr>
			</thead>
			<tbody class="wpdf-repeater-container">
			<script type="text/html" class="wpdf-repeater-template">
				<tr class="wpdf-repeater-row wpdf-repeater__template">
					<td><input title="Label" type="text" class="wpdf-input" name="field[][value_labels][]"></td>
					<td><input title="Key" type="text" class="wpdf-input" name="field[][value_keys][]"></td>
					<td><input title="Default?" type="checkbox" name="field[][value_default][]"></td>
					<td>
						<a href="#" class="wpdf-add-row button">+</a>
						<a href="#" class="wpdf-del-row button">-</a>
					</td>
				</tr>
			</script>
			<?php
			$options = $this->getOptions();
			if(!empty($options)) {
				foreach ( $options as $key => $option ) {
					?>
					<tr class="wpdf-repeater-row wpdf-repeater__template">
						<td><input title="Label" type="text" class="wpdf-input"
						           name="field[][value_labels][]" value="<?php echo $option; ?>" /></td>
						<td><input title="Key" type="text" class="wpdf-input"
						           name="field[][value_keys][]" value="<?php echo $key; ?>" /></td>
						<td><input title="Default?" type="checkbox"
						           name="field[][value_default][]" <?php
							$default = $this->getDefaultValue();
							if(is_array($default)){
								checked(in_array($key, $default), true, true);
							}else{
								checked($key, $default, true);
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
					<td><input title="Label" type="text" class="wpdf-input" name="field[][value_labels][]"></td>
					<td><input title="Key" type="text" class="wpdf-input" name="field[][value_keys][]"></td>
					<td><input title="Default?" type="checkbox" name="field[][value_default][]"></td>
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