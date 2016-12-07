<?php
/**
 * Display form field panel
 *
 * @var WPDF_FormField|string $field
 * @var WPDF_Form $form
 * @var bool $active
 */
$field_type = $field->get_type();
?>
<div class="wpdf-panel wpdf-panel--white <?php echo true === $active ? 'wpdf-panel--active' : ''; ?>" data-field-type="<?php echo esc_attr( $field_type ); ?>">
	<div class="wpdf-panel__header">
		<?php echo esc_html( ucfirst( $field_type ) ); ?>: <span class="name"><?php echo esc_html( $field->get_label() ); ?></span>
		<a class="wpdf-tooltip wpdf-panel__delete wpdf-del-field" title="Delete field from form"><?php esc_html_e( 'Delete', 'wpdf' ); ?></a>
		<a href="#" class="wpdf-panel__toggle wpdf-tooltip-blank" title="Toggle display of field settings"></a>
	</div>
	<div class="wpdf-panel__content">

		<?php
		// hidden fields.
		?>
		<input type="hidden" name="field[][type]" value="<?php echo esc_attr( $field_type ); ?>" />
		<input type="hidden" name="field[][id]" value="<?php echo esc_attr( $field->get_name() ); ?>" />

		<?php
		// general fields.
		?>
		<div class="wpdf-field-row">
			<div class="wpdf-col wpdf-col__half">
				<label for="" class="wpdf-label">
					<?php esc_html_e( 'Label', 'wpdf' ); ?>
					<span class="wpdf-tooltip wpdf-tooltip__inline" title="Text displayed before the field on the form">?</span>
				</label>

				<input type="text" class="wpdf-input wpdf-input--label wpdf-input__required" name="field[][label]" value="<?php echo esc_attr( $field->get_label() ); ?>">
			</div>
			<div class="wpdf-col wpdf-col__half">
				<label for="" class="wpdf-label">
					<?php esc_html_e( 'Placeholder', 'wpdf' ); ?>
					<span class="wpdf-tooltip wpdf-tooltip__inline" title="Text displayed in the field when no value is entered">?</span>
				</label>
				<input type="text" class="wpdf-input" name="field[][placeholder]" value="<?php echo esc_attr( $field->get_placeholder() ); ?>">
			</div>
		</div>

		<div class="wpdf-field-row">
			<div class="wpdf-col wpdf-col__half">
				<label for="" class="wpdf-label">
					<?php esc_html_e( 'CSS Classes', 'wpdf' ); ?>
					<span class="wpdf-tooltip wpdf-tooltip__inline" title="Add custom css classes to field output">?</span>
				</label>

				<input type="text" class="wpdf-input" name="field[][css_class]" value="<?php echo esc_attr( $field->get_extra_classes() ); ?>">
			</div>
		</div>

		<?php
		// specific fields based on field type.
		$field->display_settings();

		// validation fields.
		?>
		<div class="wpdf-clear"></div>
		<div class="wpdf-repeater wpdf-validation-repeater" data-min="0" data-template-name="validation_repeater">
			<div class="field-values__header">
			<strong><?php esc_html_e( 'Validation Rules', 'wpdf' ); ?> <span class="wpdf-tooltip wpdf-tooltip__inline" title="Add rules to validate data entered">?</span></strong>
			</div>
			<div class="wpdf-repeater-container">
				<script type="text/html" class="wpdf-repeater-template">
					<?php wpdf_display_validation_block(); ?>
				</script>
				<?php
				// load saved validation rules.
				if ( $form ) {
					$rules = $form->get_validation_rules();
					if ( isset( $rules[ $field->get_name() ] ) && ! empty( $rules[ $field->get_name() ] ) ) {
						foreach ( $rules[ $field->get_name() ] as $rule ) {
							$type = $rule['type'];
							wpdf_display_validation_block( $type, $rule );
						}
					}
				}
				?>
			</div>
			<a href="#" class="wpdf-add-row button button-primary"><?php esc_html_e( 'Add Validation Rule', 'wpdf' ); ?></a>
		</div>
		<?php
		// add-on fields.
		?>
		<div class="wpdf-clear"></div>
	</div>
</div>