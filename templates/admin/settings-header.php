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

	<?php if($form): ?>
	<ul class="wpdf-header__links">
		<?php if($form->getId()): ?>
		<li class="<?php echo $active == 'fields' ? 'active' : ''; ?>"><a href="<?php echo admin_url('admin.php?page=wpdf-forms&action=manage&form_id=' . $form->getId()); ?>"><?php echo WPDF()->text->get('fields', 'menu'); ?></a></li>
		<li class="<?php echo $active == 'settings' ? 'active' : ''; ?>"><a href="<?php echo admin_url('admin.php?page=wpdf-forms&action=settings&form_id=' . $form->getId()); ?>"><?php echo WPDF()->text->get('settings', 'menu'); ?></a></li>
		<?php if( $form->get_setting('enable_style') == 'enabled'): ?>
		<li class="<?php echo $active == 'style' ? 'active' : ''; ?>"><a href="<?php echo admin_url('admin.php?page=wpdf-forms&action=style&form_id=' . $form->getId()); ?>"><?php echo WPDF()->text->get('style', 'menu'); ?></a></li>
		<?php endif; ?>
		<li class="<?php echo $active == 'notifications' ? 'active' : ''; ?>"><a href="<?php echo admin_url('admin.php?page=wpdf-forms&action=notifications&form_id=' . $form->getId()); ?>"><?php echo WPDF()->text->get('notifications', 'menu'); ?></a></li>
		<li class="<?php echo $active == 'submissions' ? 'active' : ''; ?>"><a href="<?php echo admin_url('admin.php?page=wpdf-forms&action=entries&form_id=' . $form->getId()); ?>"><?php echo WPDF()->text->get('submissions', 'menu'); ?></a></li>
		<?php else: ?>
		<li class="<?php echo $active == 'submissions' ? 'active' : ''; ?>"><a href="<?php echo admin_url('admin.php?page=wpdf-forms&action=entries&form=' . $form->getName()); ?>"><?php echo WPDF()->text->get('submissions', 'menu'); ?></a></li>
		<?php endif; ?>

	</ul>
	<?php endif; ?>

	<div class="wpdf-header__actions">
		<?php if(!$form || $form->getId()): ?>
		<input type="submit" value="<?php _e('Update', 'wpdf'); ?>" class="" />
		<?php endif; ?>
	</div>
</div>
<div class="wpdf-subheader">
	<div class="wpdf-subheader__left">
		<?php if($form): ?>
		<p class="wpdf-subheader__form">Form: <?php echo $form->getLabel(); ?></p>
		<?php else: ?>
			<p class="wpdf-subheader__form"><?php _e('New Form', 'wpdf'); ?></p>
		<?php endif; ?>
	</div>
</div>