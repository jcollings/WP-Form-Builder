<?php
/**
 * Number edit panel
 *
 * @var WPDF_NumberField $this
 *
 * @package WPDF/Admin/Fields
 * @author James Collings
 * @created 11/12/2016
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="wpdf-field-row">
	<div class="wpdf-col wpdf-col__half">
		<label for="" class="wpdf-label">
			Type
			<span class="wpdf-tooltip wpdf-tooltip__inline" title="Field type to display">?</span>
		</label>

		<select name="" id="">
			<option value="input">Number Input</option>
			<option value="input-range">Number Range Input</option>
			<option value="slider">Number Slider</option>
			<option value="slider-range">Number Range Slider</option>
		</select>
	</div>
</div>
<div class="wpdf-field-row">
	<div class="wpdf-col wpdf-col__half">
		<label for="" class="wpdf-label">Minimum <span class="wpdf-tooltip wpdf-tooltip__inline" title="Minimum allowed value">?</span></label>
		<input class="wpdf-input" name="field[][min]" type="text" />
	</div>
	<div class="wpdf-col wpdf-col__half">
		<label for="" class="wpdf-label">Maximum <span class="wpdf-tooltip wpdf-tooltip__inline" title="Maximum allowed value">?</span></label>
		<input class="wpdf-input" name="field[][max]" type="text" />
	</div>
</div>
<div class="wpdf-field-row">
	<div class="wpdf-col wpdf-col__half">
		<label for="" class="wpdf-label">Number Increment<span class="wpdf-tooltip wpdf-tooltip__inline" title="Number increment">?</span></label>
		<input class="wpdf-input" name="field[][increment]" type="text" />
	</div>
</div>