# WordPress Developer Forms
Author: James Collings  
Version: 0.1  
Created: 05/08/2016  
Updated: 07/08/2016  

## About
WordPress Developer Forms plugin make it easy to create and display forms within your WordPress theme, With a WordPress admin section where you can easily view past form submissions. 

## Features

* __Saved Submissions__ - Form submissions are automatically stored and can be viewed from within the WordPress admin area.
* __Email Notifications__ - Easily setup email notifications when forms are submitted, and customise it with template tags to display for entry data.
* __Form Validation__ - Fields can make use of multiple validation functions, each allowing for custom validation messages.
* __Field Types__ - Currently supports (text, text area, file, select, checkbox, radio)

## Quick Start Guide

### Step 1 - Registering a Form

In this example we will create a form with (name, email and message field) all fields are required and the email field has to be a valid email address for the form to submit. Once the form has been submitted we will display a custom thank you message, and send website admins a copy of the data submitted via email.

```
if(function_exists('wpdf_register_form')){

    // 1. register form
	$form = wpdf_register_form("Contact", array(
		'name' => array(
			'type' => 'text',
			'label' => 'Name',
			'validation' => 'required'
		),
		'email' => array(
			'type' => 'text',
			'label' => 'Email Address',
			'validation' => array(
				array(
					'type' => 'required'
				),
				array(
					'type' => 'email'
				)
			)
		),
		'message' => array(
			'type' => 'textarea',
			'label' => 'Message',
			'validation' => 'required'
		)
	));
	
	// 2. register form success message
	$form->add_confirmation('message', 'Your form has been successfully submitted.');
	
	// 3. register form email notification
	$form->add_notification('admin@website.com', 'Email Notification Subject', 'Email Notification Message: {{fields}}');	
}
```

### Step 2 - Display Form

Now we have registered the form we can output it by adding the following code in an appropriate themes template file.

```
<?php
if(function_exists('wpdf_display_form')){
    wpdf_display_form("Contact");
}
?>
```

Or display it on a page by using a Shortcode:

```
[wp_form form="Contact"/]
```