<?php
/**
 * Form fields page
 *
 * @var WPDF_Form $form
 * @var WPDF_Admin $this
 */

$available_fields = array('text', 'textarea', 'select', 'checkbox', 'radio');
$form_id = '';
$fields = array();
if($form !== false){
	$form_id = $form->getId();
	$fields = $form->getFields();
}
?>
<form action="" method="post">

	<input type="hidden" name="wpdf-action" value="edit-form-fields" />
	<input type="hidden" name="wpdf-form" value="<?php echo $form_id; ?>" />
	<div class="wpdf-form-manager">

		<?php $this->display_form_header('fields', $form); ?>
		<div class="wpdf-cols">
			<div class="wpdf-left">
				<div class="wpdf-left__inside">
					<div class="wpdf-fields">
						<ul id="sortable">
							<?php if(empty($fields)): ?>
								<li class="placeholder">Drop field here to add to the form</li>
							<?php else: ?>

								<?php
								foreach($fields as $field):
									?>
									<li class="ui-state-highlight ui-draggable ui-draggable-handle wpdf-dropped-item" data-field="text" style="width: auto; height: auto; right: auto; bottom: auto;">
										<?php $this->display_field_panel($field, $form); ?>
									</li>
									<?php
								endforeach;
								?>

							<?php endif; ?>
						</ul>
					</div>
				</div>

			</div>
			<div class="wpdf-right">
				<div class="wpdf-right__inside">
					<div class="wpdf-panel wpdf-panel--active">
						<div class="wpdf-panel__header">
							<p class="wpdf-panel__title">Available Fields</p>
						</div>
						<div class="wpdf-panel__content">
							<ul class="wpdf-field-list">
								<?php foreach($available_fields as $field): ?>
									<li class="draggable ui-state-highlight" data-field="<?php echo $field; ?>"><a href="#"><?php echo ucfirst($field); ?></a></li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
				</div>

			</div>
		</div>

		<div class="wpdf-clear"></div>
	</div>
</form>

<div id="field-placeholder" style="display:none;">
	<?php
	foreach($available_fields as $field){
		$this->display_field_panel($field);
	}
	?>
</div>