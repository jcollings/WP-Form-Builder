<?php
/**
 * Textarea edit panel
 *
 * @var WPDF_TextareaField $this
 */
?>
<div class="wpdf-field-row">
	<div class="wpdf-col wpdf-col__half">
		<label for="" class="wpdf-label">
			Rows
			<span class="wpdf-tooltip wpdf-tooltip__inline" title="Changes the height of the textarea">?</span>
		</label>

		<input type="text" class="wpdf-input" name="field[][rows]" value="<?php echo $this->get_rows(); ?>">
	</div>
</div>
<div class="wpdf-field-row">
	<div class="wpdf-col wpdf-col__full">
		<label for="" class="wpdf-label">Default Value <span class="wpdf-tooltip wpdf-tooltip__inline" title="Default value shown when the form is loaded">?</span></label>
		<textarea class="wpdf-input" name="field[][default]"><?php echo $this->get_default_value(); ?></textarea>
	</div>
</div>