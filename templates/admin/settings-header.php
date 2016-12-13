<?php
/**
 * Form global header
 *
 * @var WPDF_Form $form
 * @var string $active Currently active page
 *
 * @package WPDF/Admin
 * @author James Collings
 * @created 30/11/2016
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="wpdf-header">
	<div class="wpdf-header__logo">
		<h1>WP Form Builder</h1>
	</div>

	<?php if ( $form ) : ?>
		<ul class="wpdf-header__links">
			<?php if ( $form->get_id() ) : ?>
				<li class="<?php echo 'fields' === $active ? 'active' : ''; ?>"><a
							href="<?php echo esc_url( admin_url( 'admin.php?page=wpdf-forms&action=manage&form_id=' . $form->get_id() ) ); ?>"><?php echo esc_html( WPDF()->text->get( 'fields', 'menu' ) ); ?></a>
				</li>
				<li class="<?php echo 'settings' === $active ? 'active' : ''; ?>"><a
							href="<?php echo esc_url( admin_url( 'admin.php?page=wpdf-forms&action=settings&form_id=' . $form->get_id() ) ); ?>"><?php echo esc_html( WPDF()->text->get( 'settings', 'menu' ) ); ?></a>
				</li>
				<?php if ( 'enabled' === $form->get_setting( 'enable_style' ) ) : ?>
					<li class="<?php echo 'style' === $active ? 'active' : ''; ?>"><a
								href="<?php echo esc_url( admin_url( 'admin.php?page=wpdf-forms&action=style&form_id=' . $form->get_id() ) ); ?>"><?php echo esc_html( WPDF()->text->get( 'style', 'menu' ) ); ?></a>
					</li>
				<?php endif; ?>
				<li class="<?php echo 'notifications' === $active ? 'active' : ''; ?>"><a
							href="<?php echo esc_url( admin_url( 'admin.php?page=wpdf-forms&action=notifications&form_id=' . $form->get_id() ) ); ?>"><?php echo esc_html( WPDF()->text->get( 'notifications', 'menu' ) ); ?></a>
				</li>
				<li class="<?php echo 'submissions' === $active ? 'active' : ''; ?>"><a
							href="<?php echo esc_url( admin_url( 'admin.php?page=wpdf-forms&action=entries&form_id=' . $form->get_id() ) ); ?>"><?php echo esc_html( WPDF()->text->get( 'submissions', 'menu' ) ); ?></a>
				</li>
			<?php else : ?>
				<li class="<?php echo 'submissions' === $active ? 'active' : ''; ?>"><a
							href="<?php echo esc_url( admin_url( 'admin.php?page=wpdf-forms&action=entries&form=' . $form->get_name() ) ); ?>"><?php echo esc_html( WPDF()->text->get( 'submissions', 'menu' ) ); ?></a>
				</li>
			<?php endif; ?>

		</ul>
	<?php endif; ?>

	<div class="wpdf-header__actions">
		<?php if ( ! $form || $form->get_id() ) : ?>
			<a class="wpdf-header__btn wpdf-header__preview"
			   href="<?php echo esc_url( admin_url( 'admin.php?page=wpdf-forms&action=preview-form&form_id=' . $form->get_id() ) ); ?>"
			   target="_blank">Preview</a>
			<input type="submit" class="wpdf-header__submit" value="<?php esc_attr_e( 'Update', 'wpdf' ); ?>"/>
		<?php endif; ?>
	</div>
</div>
<div class="wpdf-subheader">
	<div class="wpdf-subheader__left">
		<?php if ( $form ) : ?>
			<p class="wpdf-subheader__form">Form: <?php echo esc_html( $form->get_label() ); ?></p>
		<?php else : ?>
			<p class="wpdf-subheader__form"><?php echo esc_html( 'New Form', 'wpdf' ); ?></p>
		<?php endif; ?>
	</div>
</div>
