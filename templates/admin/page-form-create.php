<?php
/**
 * Created by PhpStorm.
 * User: james
 * Date: 21/11/2016
 * Time: 10:34
 */

$formName = isset($_POST['form-name']) ? sanitize_text_field($_POST['form-name']) : '';
?>
<form action="" method="post">

	<input type="hidden" name="wpdf-action" value="create-form" />

	<label for="form-name">Name</label>
	<input type="text" name="form-name" id="form-name" value="<?php echo $formName; ?>" />

	<input type="submit" class="button button-primary" value="Create Form" />

</form>

