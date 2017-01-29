# Pro Modules

Modules only included within the pro version of WP Form Builder.

## User Registration

Basic usage:
```
add_filter( 'wpdf/form_settings', 'wpdf_user_regi_example', 10, 2 );
function wpdf_user_regi_example($default_settings, $form_id){
	$default_settings['user_registration'] = [
		'fields' => [
			'user_login' => 'username'
		]
	];
	return $default_settings;
}
```
