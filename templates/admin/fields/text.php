<?php
/**
 * Text edit panel
 *
 * @var WPDF_TextField $this
 */
?>
<div class="wpdf-field-row">
	<div class="wpdf-col wpdf-col__half">
		<label for="" class="wpdf-label">Default Value <span class="wpdf-tooltip wpdf-tooltip__inline" title="Default value shown when the form is loaded">?</span></label>
		<input type="text" class="wpdf-input" name="field[][default]" value="<?php echo $this->get_default_value(); ?>">
	</div>
</div>