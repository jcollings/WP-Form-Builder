<?php
function wpdf_getValidationRules(){
	$rules = array(
		'required' => 'Required',
		'email' => 'Email',
		'unique' => 'Unique',
	);
	return $rules;
}

/**
 * Display validation settings on field panel
 *
 * @param string $type
 * @param array $data
 */
function wpdf_displayValidationBlock($type = '', $data = array()){
	$rules = wpdf_getValidationRules();
	?>
	<div class="wpdf-validation-row wpdf-repeater-row" data-rule="<?php echo $type; ?>">
		<select name="field[][validation][][type]" class="validation_type">
			<option value="">Choose Validation Type</option>
			<?php foreach($rules as $id => $label): ?>
				<option value="<?php echo $id; ?>" <?php selected($id, $type, true); ?>><?php echo $label; ?></option>
			<?php endforeach; ?>
		</select>
		<a href="#" class="wpdf-del-row">Remove</a>

		<div class="wpdf-validation__rule">
		<?php
		// display validation options
		if( !empty($type) ){
			wpdf_displayValidationBlockSection($type, $data);
		}
		?>
		</div>
	</div>
	<?php

}

function wpdf_displayValidationBlockSection($type = '', $data = array()){
	?>
	<div class="wpdf-col">
		<label class="wpdf-label" for="">Validation Message (<?php echo $type; ?>):</label>
		<input type="text" name="field[][validation][][msg]" value="<?php echo isset($data['msg']) ? $data['msg'] : ''; ?>" class="wpdf-input" />
	</div>
	<?php
}