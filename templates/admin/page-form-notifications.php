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

			<div class="wpdf-left">
				<div class="wpdf-left__inside">

					<ul class="wpdf-notifications wpdf-repeater" data-min="0" data-template-name="notification_repeater" data-template-index="notification\[[0-9]*\]" data-template-prefix="notification">
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
												<td><input id="to" type="text" name="notification[][to]" value="" /></td>
											</tr>
											<tr>
												<td><label for="subject">Subject</label></td>
												<td><input id="subject" type="text" name="notification[][subject]" value="" /></td>
											</tr>
											<tr>
												<td><label for="message">Message</label></td>
												<td><textarea name="notification[][message]" id="message" cols="30" rows="10"></textarea></td>
											</tr>
											<tr>
												<td><label for="from">From</label></td>
												<td><input id="from" type="text" name="notification[][from]" value="" /></td>
											</tr>
											<tr>
												<td><label for="cc">Cc</label></td>
												<td><input id="cc" type="text" name="notification[][cc]" value="" /></td>
											</tr>
											<tr>
												<td><label for="bcc">Bcc</label></td>
												<td><input id="bcc" type="text" name="notification[][bcc]" value="" /></td>
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
											<td><input id="to" type="text" name="notification[<?php echo $i; ?>][to]" value="<?php echo $notification['to']; ?>" /></td>
										</tr>
										<tr>
											<td><label for="subject">Subject</label></td>
											<td><input id="subject" type="text" name="notification[<?php echo $i; ?>][subject]" value="<?php echo $notification['subject']; ?>" /></td>
										</tr>
										<tr>
											<td><label for="message">Message</label></td>
											<td><textarea name="notification[<?php echo $i; ?>][message]" id="message" cols="30" rows="10"><?php echo $notification['message']; ?></textarea></td>
										</tr>
										<tr>
											<td><label for="from">From</label></td>
											<td><input id="from" type="text" name="notification[<?php echo $i; ?>][from]" value="<?php echo $notification['from']; ?>" /></td>
										</tr>
										<tr>
											<td><label for="cc">Cc</label></td>
											<td><input id="cc" type="text" name="notification[<?php echo $i; ?>][cc]" value="<?php echo $notification['cc']; ?>" /></td>
										</tr>
										<tr>
											<td><label for="bcc">Bcc</label></td>
											<td><input id="bcc" type="text" name="notification[<?php echo $i; ?>][bcc]" value="<?php echo $notification['bcc']; ?>" /></td>
										</tr>


									</table>

								</div>
							</div>
						</li>
						<?php endforeach; ?>
						<li>
							<a href="#" class="wpdf-add-row button button-primary">Add Notification</a>
						</li>
					</ul>
					&nbsp;
				</div>
			</div>

			<div class="wpdf-right"></div>

		</div>

		<div class="wpdf-clear"></div>
	</div>
</form>