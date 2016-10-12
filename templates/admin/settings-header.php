<?php
/**
 * Form global header
 *
 * @var WPDF_Form $form
 * @var string $active Currently active page
 */
?>
<div class="wpdf-header">
	<?php if($form): ?>
		<ul class="wpdf-header__links">
			<li><a href="<?php echo admin_url('admin.php?page=wpdf-forms&action=manage&form_id=' . $form->getId()); ?>">Fields</a></li>
			<li><a href="<?php echo admin_url('admin.php?page=wpdf-forms&action=settings&form_id=' . $form->getId()); ?>">Settings</a></li>
			<li><a href="<?php echo admin_url('admin.php?page=wpdf-forms&action=notifications&form_id=' . $form->getId()); ?>">Notifications</a></li>
		</ul>
	<?php endif; ?>
	<div class="wpdf-header__actions">
		<input type="submit" value="Update" />
	</div>
</div>