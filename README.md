# WordPress Developer Forms

WordPress forms plugin built for developers.

## Register Form

```
// register form
if(function_exists('wpdf_register_form')){

	wpdf_register_form("Contact", [
		'notifications' => [

			// test admin notification
			[
				'to' => 'to@test.com',
				'cc' => 'cc@test.com',
				'bcc' => 'bcc@test.com',
				'subject' => 'Test {{field_email}} admin notification',
				'message' => 'Test message Notification {{fields}}'
			],

			// test user notifiaction, send to field:
			[
				'to' => '{{field_email}}',
				'cc' => 'cc@test.com',
				'bcc' => 'bcc@test.com',
				'subject' => 'Test {{field_email}} user notification, sending to field address',
				'message' => 'Test user notification, sending to field address {{fields}}'
			]
		],
		'fields' => [
			'fname' => [
				'type' => 'text',
				'label' => 'First Name',
				'validation' => [
					[
						'msg' => 'This field is required!',
						'type' => 'required'
					]
				]
			],
			'sname' => [
				'type' => 'text',
				'label' => 'Surname',
				'validation' => [
					[
						'type' => ['min_length', 5]
					]
				]
			],
			'email' => [
				'type' => 'text',
				'label' => 'Email Address',
				'validation' => [
					[
						'msg' => 'This field is required!',
						'type' => 'required'
					],
					[
						'msg' => 'Please enter a valid email address!',
						'type' => 'email'
					]
				]
			],
			'subject' => [
				'label' => 'Subject',
				'type' => 'select',
				'options' => ['One', 'Two', 'Three'],
				'empty' => 'Custom select message',
				'validation' => [
					[
						'type' => 'required'
					]
				]
			],
			'phone' => [
				'label' => 'Phone Number',
				'type' => 'text'
			],
			'message' => [
				'label' => 'Message',
				'type' => 'textarea'
			],
			'poster' => [
				'label' => 'Poster Attachment',
				'type' => 'file',
				'validation' => [
					[
						'type' => 'required'
					]
				]
			],
			'radio' => [
				'label' => 'Radio Choices:',
				'type' => 'radio',
				'options' => ['One', 'Two', 'Three']
			],
			'checkbox' => [
				'label' => 'Checkbox Choices:',
				'type' => 'checkbox',
				'options' => ['One', 'two' => 'Two', 'Three']
			]
		]
	]);

}
```

## Display Form

```
// form
	
if(function_exists('wpdf_get_form')){
	
	$form = wpdf_get_form("Contact");

	if(!$form->is_complete()){

		// output the form tag <form>
		$form->start();

		// display form errors if any
		if($form->has_errors()){
			$form->errors();
		}

		// output fields
		?>

		<div class="form-row <?php $form->classes('fname', 'validation'); ?> <?php $form->classes('fname', 'type'); ?>">
			<div class="label"><?php $form->label( "fname" ); ?></div>
			<div class="input"><?php $form->input( "fname" ); ?></div>
			<?php $form->error( "fname" ); ?>
		</div>

		<div class="form-row <?php $form->classes('sname', 'validation'); ?> <?php $form->classes('sname', 'type'); ?>">
			<div class="label"><?php $form->label( "sname" ); ?></div>
			<div class="input"><?php $form->input( "sname" ); ?></div>
			<?php $form->error( "sname" ); ?>
		</div>

		<div class="form-row <?php $form->classes('phone', 'validation'); ?> <?php $form->classes('phone', 'type'); ?>">
			<div class="label"><?php $form->label( "phone" ); ?></div>
			<div class="input"><?php $form->input( "phone" ); ?></div>
			<?php $form->error( "phone" ); ?>
		</div>

		<div class="form-row <?php $form->classes('email', 'validation'); ?> <?php $form->classes('email', 'type'); ?>">
			<div class="label"><?php $form->label( "email" ); ?></div>
			<div class="input"><?php $form->input( "email" ); ?></div>
			<?php $form->error( "email" ); ?>
		</div>

		<div class="form-row <?php $form->classes('subject', 'validation'); ?> <?php $form->classes('subject', 'type'); ?>">
			<div class="label"><?php $form->label( "subject" ); ?></div>
			<div class="input"><?php $form->input( "subject" ); ?></div>
			<?php $form->error( "subject" ); ?>
		</div>

		<div class="form-row <?php $form->classes('message', 'validation'); ?> <?php $form->classes('message', 'type'); ?>">
			<div class="label"><?php $form->label( "message" ); ?></div>
			<div class="input"><?php $form->input( "message" ); ?></div>
			<?php $form->error( "message" ); ?>
		</div>

		<div class="form-row <?php $form->classes('poster', 'validation'); ?> <?php $form->classes('poster', 'type'); ?>">
			<div class="label"><?php $form->label( "poster" ); ?></div>
			<div class="input"><?php $form->input( "poster" ); ?></div>
			<?php $form->error( "poster" ); ?>
		</div>

		<div class="form-row <?php $form->classes('radio', 'validation'); ?> <?php $form->classes('radio', 'type'); ?>">
			<div class="label"><?php $form->label( "radio" ); ?></div>
			<div class="input"><?php $form->input( "radio" ); ?></div>
			<?php $form->error( "radio" ); ?>
		</div>

		
		<div class="form-row <?php $form->classes('checkbox', 'validation'); ?> <?php $form->classes('checkbox', 'type'); ?>">
			<div class="label"><?php $form->label( "checkbox" ); ?></div>
			<div class="input"><?php $form->input( "checkbox" ); ?></div>
			<?php $form->error( "checkbox" ); ?>
		</div>

		<?php
		// close </form>
		$form->end();
	}
}

// end form
```