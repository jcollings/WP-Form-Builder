<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 20/11/2016
 * Time: 18:49
 */
$forms = WPDF()->get_forms();

$options = array();
if(!empty($forms)) {
	foreach ( $forms as $form_id => $form ) {

		if(is_array($form)) {
			$form = WPDF()->get_form( $form_id );
		}

		$id = $form->get_id();
		if ( $id ) {
			$options[ $id ] = array(
				'label' => $form->get_label(),
				'type'  => 'form_id'
			);
		} else {
			$options[ $form->get_name() ] = array(
				'label' => $form->get_label(),
				'type'  => 'form'
			);
		}
	}
}
?>

<div class="wpdf-dialog">
	<?php if( empty($options) ): ?>
		<div id="wpdf_form_error" style="display:block;">
			<p><?php echo WPDF()->text->get('empty_forms', 'shortcode_error'); ?></p>
		</div>
	<?php else: ?>
		<div id="wpdf_form_error" style="display:none;">
			<p><?php echo WPDF()->text->get('selection', 'shortcode_error'); ?></p>
		</div>

		<table class="wpdf-dialog-table">
			<tr>
				<th class="notification__label">
					<label for="wpdf_form_select"><?php _e('Form','wpdf'); ?>:</label>
				</th>
				<td class="notification__input">
					<select name="wpdf_form_select" id="wpdf_form_select" data-options='<?php echo json_encode($options); ?>'>
						<?php
						foreach($options as $id => $option):
							?>
							<option value="<?php echo $id; ?>"><?php echo $option['label']; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
		</table>
	<?php endif; ?>
</div>



