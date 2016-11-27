<div class="wpdf-field-row">
	<div class="wpdf-col wpdf-col__half">
		<label for="" class="wpdf-label">
			Rows
			<span class="wpdf-tooltip wpdf-tooltip__inline" title="Changes the height of the textarea">?</span>
		</label>

		<input type="text" class="wpdf-input" name="field[][rows]" value="<?php echo $this->getRows(); ?>">
	</div>
</div>
<div class="wpdf-field-row">
	<div class="wpdf-col wpdf-col__full">
		<label for="" class="wpdf-label">Default Value <span class="wpdf-tooltip wpdf-tooltip__inline" title="Default value shown when the form is loaded">?</span></label>
		<textarea class="wpdf-input" name="field[][default]"><?php echo $this->getDefaultValue(); ?></textarea>
	</div>
</div>