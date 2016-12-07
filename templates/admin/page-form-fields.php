<?php
/**
 * Form fields page
 *
 * @var WPDF_Form $form
 * @var WPDF_Admin $this
 */

$available_fields = array('text', 'textarea', 'select', 'checkbox', 'radio', 'file');
$form_id = '';
$fields = array();
if($form !== false){
	$form_id = $form->get_id();
	$fields = $form->get_fields();
}
?>
<form id="wpdf-form-fields" action="" method="post">

	<input type="hidden" name="wpdf-action" value="edit-form-fields" />
	<input type="hidden" name="wpdf-form" value="<?php echo esc_attr( $form_id ); ?>" />
	<div class="wpdf-form-manager wpdf-form-manager--inputs">

		<?php $this->display_form_header('fields', $form); ?>
		<div class="wpdf-cols">
			<div class="wpdf-left">
				<div class="wpdf-left__inside">

					<div id="error-wrapper">
						<?php
						if ( $this->get_success() > 0 ) {
							?>
							<p class="notice notice-success wpdf-notice wpdf-notice--success"><?php echo WPDF()->text->get('form_saved', 'general'); ?></p>
							<?php
						}
						?>
					</div>

					<div class="wpdf-fields">
						<ul id="sortable">

								<li class="placeholder" <?php if(!empty($fields)): ?>style="display: none;"<?php endif; ?>>Drop field here to add to the form</li>


								<?php
								if(!empty($fields)):
								foreach($fields as $field):
									?>
									<li class="ui-state-highlight ui-draggable ui-draggable-handle wpdf-dropped-item" data-field="text" style="width: auto; height: auto; right: auto; bottom: auto;">
										<?php $this->display_field_panel($field, $form); ?>
									</li>
									<?php
								endforeach;
								endif;
								?>
						</ul>
					</div>
				</div>

			</div>
			<div class="wpdf-right">
				<div class="wpdf-right__inside">
					<div class="wpdf-panel wpdf-panel--active">
						<div class="wpdf-panel__header">
							<p class="wpdf-panel__title">Available Fields <span class="wpdf-tooltip wpdf-tooltip__inline" title="Hover over the type of field you want to add and drag into the left dropzone.">?</span></p>
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
<?php
$rules = wpdf_get_validation_rules();
foreach($rules as $rule_id => $rule_label): ?>
<script type="text/html" class="wpdf-validation__rule" data-rule="<?php echo $rule_id; ?>">
	<?php wpdf_display_validation_block_section($rule_id); ?>
</script>
<?php endforeach; ?>