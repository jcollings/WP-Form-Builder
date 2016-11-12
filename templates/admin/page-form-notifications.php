<?php
/**
 * Form notifications page
 *
 * @var WPDF_Form $form
 */

$form_id = '';
if($form !== false){
	$form_id = $form->getId();
}

$fields = $form->getFields();
$fieldKeys = array();
if(!empty($fields)) {
	foreach ( $fields as $field_id => $field ) {
		$fieldKeys[] = sprintf( '%s <code>{{field_%s}}</code>', $field->getLabel(), $field_id );
	}
}

$settings = $form->export();
$notifications = isset($settings['notifications']) && !empty($settings['notifications']) ? $settings['notifications'] : array(array(
	'to' => '',
	'subject' => '',
	'message' => '',
	'from' => '',
	'cc' => '',
	'bcc' => '',
));
?>
<form action="" method="post">

	<input type="hidden" name="wpdf-action" value="edit-form-notifications" />
	<input type="hidden" name="wpdf-form" value="<?php echo $form_id; ?>" />
	<div class="wpdf-form-manager">

		<?php $this->display_form_header('notifications', $form); ?>

		<div class="wpdf-cols">

			<div class="wpdf-full">
				<div class="wpdf-left__inside  wpdf-repeater" data-min="0" data-template-name="notification_repeater" data-template-index="notification\[[0-9]*\]" data-template-prefix="notification">

					<ul class="wpdf-notifications wpdf-repeater-container">
						<script type="text/html" class="wpdf-repeater-template">
							<li class="wpdf-notification wpdf-repeater-row">
								<div class="wpdf-panel wpdf-panel--white wpdf-panel--active">
									<div class="wpdf-panel__header">
										Notification - <a href="#" class="wpdf-del-row">Delete</a>
									</div>
									<div class="wpdf-panel__content">

										<table class="wpdf-form-table">
											<tr>
												<td><label for="to">Send To</label></td>
												<td class="notification__input"><input id="to" type="text" name="notification[][to]" value="" /></td>
												<td></td>
											</tr>
											<tr>
												<td><label for="subject">Subject</label></td>
												<td class="notification__input"><input id="subject" type="text" name="notification[][subject]" value="" /></td>
												<td></td>
											</tr>
											<tr>
												<td><label for="message">Message</label></td>
												<td class="notification__input"><textarea name="notification[][message]" id="message" cols="30" rows="10"></textarea></td>
												<td></td>
											</tr>
											<tr>
												<td><label for="from">From</label></td>
												<td class="notification__input"><input id="from" type="text" name="notification[][from]" value="" /></td>
												<td></td>
											</tr>
											<tr>
												<td><label for="cc">Cc</label></td>
												<td class="notification__input"><input id="cc" type="text" name="notification[][cc]" value="" /></td>
												<td></td>
											</tr>
											<tr>
												<td><label for="bcc">Bcc</label></td>
												<td class="notification__input"><input id="bcc" type="text" name="notification[][bcc]" value="" /></td>
												<td></td>
											</tr>


										</table>

									</div>
								</div>
							</li>
						</script>
						<?php foreach($notifications as $i => $notification): ?>
						<li class="wpdf-notification wpdf-repeater-row">
							<div class="wpdf-panel wpdf-panel--white wpdf-panel--active">
								<div class="wpdf-panel__header">
									Notification - <a href="#" class="wpdf-del-row">Delete</a>
								</div>
								<div class="wpdf-panel__content">

									<table class="wpdf-form-table">
										<tr>
											<td><label for="to">Send To</label></td>
											<td class="notification__input"><input id="to" type="text" name="notification[<?php echo $i; ?>][to]" value="<?php echo $notification['to']; ?>" /></td>
											<td></td>
										</tr>
										<tr>
											<td><label for="subject">Subject</label></td>
											<td class="notification__input"><input id="subject" type="text" name="notification[<?php echo $i; ?>][subject]" value="<?php echo $notification['subject']; ?>" /></td>
											<td></td>
										</tr>
										<tr>
											<td><label for="message">Message</label></td>
											<td class="notification__input"><textarea name="notification[<?php echo $i; ?>][message]" id="message" cols="30" rows="10"><?php echo $notification['message']; ?></textarea></td>
											<td>
												Form data can be displayed in the message using merge tags, to display all fields <code>{{fields}}</code>, to display individual fields you can use the following merge tags: <?php
												echo '<br />' . implode(',<br /> ', $fieldKeys);
												?>
											</td>
										</tr>
										<tr>
											<td><label for="from">From</label></td>
											<td class="notification__input"><input id="from" type="text" name="notification[<?php echo $i; ?>][from]" value="<?php echo $notification['from']; ?>" /></td>
											<td></td>
										</tr>
										<tr>
											<td><label for="cc">Cc</label></td>
											<td class="notification__input"><input id="cc" type="text" name="notification[<?php echo $i; ?>][cc]" value="<?php echo $notification['cc']; ?>" /></td>
											<td></td>
										</tr>
										<tr>
											<td><label for="bcc">Bcc</label></td>
											<td class="notification__input"><input id="bcc" type="text" name="notification[<?php echo $i; ?>][bcc]" value="<?php echo $notification['bcc']; ?>" /></td>
											<td></td>
										</tr>


									</table>

								</div>
							</div>
						</li>
						<?php endforeach; ?>
					</ul>

					<a href="#" class="wpdf-add-row button button-primary">Add Notification</a>
					&nbsp;
				</div>
			</div>

		</div>

		<div class="wpdf-clear"></div>
	</div>
</form>