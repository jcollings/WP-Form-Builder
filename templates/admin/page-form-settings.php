<?php
/**
 * Form settings page
 *
 * @var WPDF_Form $form
 */

$form_id = '';
if($form !== false){
	$form_id = $form->getId();
}
?>
<form action="" method="post">

	<input type="hidden" name="wpdf-action" value="edit-form-settings" />
	<input type="hidden" name="wpdf-form" value="<?php echo $form_id; ?>" />
	<div class="wpdf-form-manager">

		<?php $this->display_form_header('settings', $form); ?>
		<div class="wpdf-cols">

			<div class="wpdf-left">
				<div class="wpdf-left__inside">
					&nbsp;
				</div>
			</div>

			<div class="wpdf-right"></div>

		</div>

		<div class="wpdf-clear"></div>
	</div>
</form>