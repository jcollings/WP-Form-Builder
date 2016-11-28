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
		$fieldKeys[] = sprintf( '%s <code>{{field_%s}}</code>', $field->get_label(), $field_id );
	}
}

$settings = $form->export();
$blankNotification = array(
	'to' => '',
	'subject' => '',
	'message' => '',
	'from' => '',
	'cc' => '',
	'bcc' => '',
);
$notifications = isset($settings['notifications']) && !empty($settings['notifications']) ? $settings['notifications'] : array($blankNotification);
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
							<?php wpdf_display_notification_settings($blankNotification, '', $fieldKeys); ?>
						</script>
						<?php foreach($notifications as $i => $notification): ?>
							<?php wpdf_display_notification_settings($notification, $i, $fieldKeys); ?>
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