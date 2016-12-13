<?php
/**
 * Plugin Settings Page
 *
 * @package WPDF/Admin
 * @author James Collings
 * @created 30/11/2016
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="wrap">

	<h2>Settings</h2>

	<h3 class="nav-tab-wrapper">
		<a href="#" class="nav-tab nav-tab-active">Debug</a>
	</h3>

	<div id="poststuff">

		<?php
		$debug_info = array(
			'General' => array(
				'WordPress version' => get_bloginfo( 'version' ),
				'Plugin version' => WPDF()->get_version(),
				'Php version' => phpversion(),
				'Max execution time' => ini_get( 'max_execution_time' ),
				'Max input time' => ini_get( 'max_input_time' ),
			),
			'File Upload' => array(
				'Post max size' => ini_get( 'post_max_size' ),
				'Upload max filesize' => ini_get( 'upload_max_filesize' ),
				'Upload directory' => wpdf_get_uploads_dir(),
				'Upload directory writable' => true === is_writable( wpdf_get_uploads_dir() ) ? 'yes' : 'no',
			),
		);
		?>

		<?php foreach ( $debug_info as $section => $section_data ) : ?>
			<div class="postbox ">
				<button type="button" class="handlediv button-link" aria-expanded="true">
					<span class="screen-reader-text">Toggle panel: <?php echo esc_html( $section ); ?></span><span class="toggle-indicator" aria-hidden="true"></span>
				</button>
				<h2 class="hndle ui-sortable-handle">
					<span><?php echo esc_html( $section ); ?></span>
				</h2>
				<table class="wpdf-debug-table" cellpadding="0" cellspacing="0">
					<?php
					$i = 0;
					foreach ( $section_data as $heading => $content  ) : $i++; ?>
						<tr class="<?php echo ( 0 === ( $i % 2 )) ? esc_attr( 'alt' ) : ''; ?>">
							<th><?php echo esc_html( $heading ); ?>:</th>
							<td><?php echo esc_html( $content ); ?></td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>
		<?php endforeach; ?>
	</div>

</div>
