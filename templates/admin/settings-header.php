<?php
/**
 * Form global header
 *
 * @var WPDF_Form $form
 * @var string $active Currently active page
 */
?>
<div class="wpdf-header">
	<div class="wpdf-header__logo">
		<h1>WP Form Builder</h1>
	</div>

	<ul class="wpdf-header__links">
		<?php if($form->getId()): ?>
		<li class="<?php echo $active == 'fields' ? 'active' : ''; ?>"><a href="<?php echo admin_url('admin.php?page=wpdf-forms&action=manage&form_id=' . $form->getId()); ?>">Fields</a></li>
		<li class="<?php echo $active == 'settings' ? 'active' : ''; ?>"><a href="<?php echo admin_url('admin.php?page=wpdf-forms&action=settings&form_id=' . $form->getId()); ?>">Settings</a></li>
		<li class="<?php echo $active == 'notifications' ? 'active' : ''; ?>"><a href="<?php echo admin_url('admin.php?page=wpdf-forms&action=notifications&form_id=' . $form->getId()); ?>">Notifications</a></li>
		<li class="<?php echo $active == 'submissions' ? 'active' : ''; ?>"><a href="<?php echo admin_url('admin.php?page=wpdf-forms&action=entries&form_id=' . $form->getId()); ?>">Submissions</a></li>
		<?php else: ?>
		<li class="<?php echo $active == 'submissions' ? 'active' : ''; ?>"><a href="<?php echo admin_url('admin.php?page=wpdf-forms&action=entries&form=' . $form->getName()); ?>">Submissions</a></li>
		<?php endif; ?>

	</ul>

	<div class="wpdf-header__actions">
		<?php if($form->getId()): ?>
		<input type="submit" value="Update" class="" />
		<?php endif; ?>
	</div>
</div>
<div class="wpdf-subheader">
	<div class="wpdf-subheader__left">
		<p class="wpdf-subheader__form"><?php echo $form->getLabel(); ?></p>
	</div>
</div>