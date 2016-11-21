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

		<div class="wpdf-validation__selector">
			<select name="field[][validation][][type]" class="validation_type">
				<option value="">Choose Validation Type</option>
				<?php foreach($rules as $id => $label): ?>
					<option value="<?php echo $id; ?>" <?php selected($id, $type, true); ?>><?php echo $label; ?></option>
				<?php endforeach; ?>
			</select>
			<a href="#" class="wpdf-del-row">Remove</a>
		</div>

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
		<label class="wpdf-label" for="">
			Validation Message:
			<span class="wpdf-tooltip wpdf-tooltip__inline" title="Enter text to be displayed, or leave blank to display default text.">?</span>
		</label>
		<input type="text" name="field[][validation][][msg]" value="<?php echo isset($data['msg']) ? $data['msg'] : ''; ?>" class="wpdf-input" />
	</div>
	<?php
}

function wpdf_displayNotificationSettings($notification, $i = '', $fieldKeys = array()){
	?>
	<li class="wpdf-notification wpdf-repeater-row">
		<div class="wpdf-panel wpdf-panel--white wpdf-panel--active">
			<div class="wpdf-panel__header">
				Notification
				<a class="wpdf-tooltip wpdf-panel__delete wpdf-del-field" title="Delete notification">Delete</a>
				<a href="#" class="wpdf-panel__toggle wpdf-tooltip-blank" title="Toggle display of notification settings"></a>
			</div>
			<div class="wpdf-panel__content">

				<table class="wpdf-form-table">
					<tr>
						<td class="notification__label wpdf-tooltip__wrapper"><label for="to">Send To <span class="wpdf-tooltip" title="Email addresses to notify, seperated by ','">?</span></label></td>
						<td class="notification__input"><input id="to" type="text" name="notification[<?php echo $i; ?>][to]" value="<?php echo $notification['to']; ?>" /></td>
						<td></td>
					</tr>
					<tr>
						<td class="notification__label wpdf-tooltip__wrapper"><label for="subject">Subject <span class="wpdf-tooltip" title="Email subject line">?</span></label></td>
						<td class="notification__input"><input id="subject" type="text" name="notification[<?php echo $i; ?>][subject]" value="<?php echo $notification['subject']; ?>" /></td>
						<td></td>
					</tr>
					<tr>
						<td class="notification__label wpdf-tooltip__wrapper"><label for="message">Message <span class="wpdf-tooltip" title="Email message text">?</span></label></td>
						<td class="notification__input"><textarea name="notification[<?php echo $i; ?>][message]" id="message" cols="30" rows="10"><?php echo $notification['message']; ?></textarea></td>
						<td>
							Form data can be displayed in the message using merge tags, to display all fields <code>{{fields}}</code>, to display individual fields you can use the following merge tags: <?php
							echo '<br />' . implode(',<br /> ', $fieldKeys);
							?>
						</td>
					</tr>
					<tr>
						<td class="notification__label wpdf-tooltip__wrapper"><label for="from">From <span class="wpdf-tooltip" title="Sent from email address">?</span></label></td>
						<td class="notification__input"><input id="from" type="text" name="notification[<?php echo $i; ?>][from]" value="<?php echo $notification['from']; ?>" /></td>
						<td></td>
					</tr>
					<tr>
						<td class="notification__label wpdf-tooltip__wrapper"><label for="cc">Cc <span class="wpdf-tooltip" title="Email addresses to cc to, seperated by ','">?</span></label></td>
						<td class="notification__input"><input id="cc" type="text" name="notification[<?php echo $i; ?>][cc]" value="<?php echo $notification['cc']; ?>" /></td>
						<td></td>
					</tr>
					<tr>
						<td class="notification__label wpdf-tooltip__wrapper"><label for="bcc">Bcc <span class="wpdf-tooltip" title="Email addresses to bcc to, seperated by ','">?</span></label></td>
						<td class="notification__input"><input id="bcc" type="text" name="notification[<?php echo $i; ?>][bcc]" value="<?php echo $notification['bcc']; ?>" /></td>
						<td></td>
					</tr>


				</table>

			</div>
		</div>
	</li>
	<?php
}

/**
 * Display form input using iris colour picker
 *
 * @param $name input name
 * @param string $colour default colour
 */
function wpdf_irisPicker($name, $colour = '#FFFFFF'){
	?>
	<div class="wpdf-iris-wrap">
		<span class="wpdf-color-pick-preview" style="background: <?php echo $colour; ?>"></span>
		<input type="text" class="wpdf-color-picker-input" name="<?php echo $name; ?>" value="<?php echo $colour; ?>" />
	</div>
	<?php
}