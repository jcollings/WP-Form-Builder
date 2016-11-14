<?php
/**
 * Display form field panel
 *
 * @var WPDF_FormField|string $field
 * @var WPDF_Form $form
 * @var bool $active
 */
$field_type = $field->getType();
?>
<div class="wpdf-panel wpdf-panel--white <?php echo $active == true ? 'wpdf-panel--active' : ''; ?>" data-field-type="<?php echo $field_type; ?>">
	<div class="wpdf-panel__header">
		<?php echo ucfirst($field_type); ?>: <?php echo $field->getLabel(); ?> - <a href="#delete" class="wpdf-del-field">Delete</a>
		<a href="#" class="wpdf-panel__toggle"></a>
	</div>
	<div class="wpdf-panel__content">

		<?php
		// hidden fields
		?>
		<input type="hidden" name="field[][type]" value="<?php echo $field_type; ?>" />
		<input type="hidden" name="field[][id]" value="<?php echo $field->getName(); ?>" />

		<?php
		// general fields
		?>
		<div class="wpdf-field-row">
			<div class="wpdf-col wpdf-col__half">
				<label for="" class="wpdf-label">Label</label>
				<input type="text" class="wpdf-input" name="field[][label]" value="<?php echo $field->getLabel(); ?>">
			</div>
			<div class="wpdf-col wpdf-col__half">
				<label for="" class="wpdf-label">Placeholder</label>
				<input type="text" class="wpdf-input" name="field[][placeholder]" value="<?php echo $field->getPlaceholder(); ?>">
			</div>
		</div>

		<?php
		// specific fields based on field type
		$field->displaySettings();

		// validation fields
		?>
		<div class="wpdf-clear"></div>
		<div class="wpdf-repeater wpdf-validation-repeater" data-min="0" data-template-name="validation_repeater">
			<p>Validation</p>
			<div class="wpdf-repeater-container">
				<script type="text/html" class="wpdf-repeater-template">
					<?php wpdf_displayValidationBlock(); ?>
				</script>
				<?php
				// load saved validation rules
				if($form){
					$rules = $form->getValidationRules();
					if(isset($rules[$field->getName()]) && !empty($rules[$field->getName()])){
						foreach($rules[$field->getName()] as $rule){
							$type = $rule['type'];
							wpdf_displayValidationBlock($type, $rule);
						}
					}
				}
				?>
			</div>
			<a href="#" class="wpdf-add-row button button-primary">Add Validation Rule</a>
		</div>
		<?php
		// add-on fields
		?>
		<div class="wpdf-clear"></div>
	</div>
</div>