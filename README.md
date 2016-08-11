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

### Step 1 - Registering the Form

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

### Step 2 - Displaying the Form

Now we have registered the form we can output it, WPDF gives you many ways to display the form going from really simple
 with displaying the form with the plugins default output, or you can have complete control how and what is output.
 
#### Display Form via theme function

```
<?php
if(function_exists('wpdf_display_form')){
    wpdf_display_form("Contact");
}
?>
```

#### Display form via Shortcode Output

```
[wp_form form="Contact"/]
```

#### Displaying the form Manually  

Custom output is best used when you want to have control over how overything looks and want it to match how your
 existing form looks.
 
```
<?php
$form = wpdf_get_form("Contact");

if($form->is_complete()):

    // display successful message
    wpdf_display_confirmation($form);

else: ?>

    <?php
    // output the form tag <form>
    $form->start();
    ?>
    
    <?php
    // display form errors if there are any
    if($form->has_errors()): ?>
        <div class="form-errors">
        <?php $form->errors(); ?>
    </div>
    <?php endif; ?>
    
    <!-- Display Name Field -->
    <div class="form-row <?php $form->classes("name", 'validation'); ?> <?php $form->classes("name", 'type'); ?>">
        <div class="label"><?php $form->label( "name" ); ?></div>
        <div class="input"><?php $form->input( "name" ); ?></div>
        <?php $form->error( "name" ); ?>
    </div>
    
    <!-- Display Email Field -->
    <div class="<?php $form->classes("email", 'validation'); ?> <?php $form->classes("email", 'type'); ?>">
        <label><?php echo $form->getFieldLabel("email"); ?></label>
        <input type="text" name="<?php echo $form->getFieldName("email"); ?>" value="<?php echo $form->getFieldValue("email"); ?>" />
        <?php $form->error( "email" ); ?>
    </div>
    
    <!-- Display Message Field -->
    <div class="<?php $form->classes("message", 'validation'); ?> <?php $form->classes("message", 'type'); ?>">
        <label><?php echo $form->getFieldLabel("message"); ?></label>
        <textarea name="<?php echo $form->getFieldName("message"); ?>"><?php echo $form->getFieldValue("message"); ?></textarea>
        <?php $form->error( "message" ); ?>
    </div>
    
    <?php $form->submit("SEND); ?>

<?php endif; ?>
```

If you want to go a step further and customise how the inputs are displayed you can the same example as above with more
flexibility.

```
<?php
$form = wpdf_get_form("Contact");

if($form->is_complete()):

    // display successful message
    ?>
    <div class="form-confirmation">
        <p><?php echo $form->getConfirmationMessage(); ?></p>
    </div>
    <?php

else: ?>

    <?php
    // output the form tag <form>
    $form->start();
    ?>

    <?php
    // display form errors if there are any
    if($form->has_errors()): ?>
        <div class="form-errors">
        <?php $form->errors(); ?>
        </div>
    <?php endif; ?>
    
    <!-- Display Name Field -->
    <div class="<?php $form->classes("name", 'validation'); ?> <?php $form->classes("name", 'type'); ?>">
        <label><?php echo $form->getFieldLabel("name"); ?></label>
        <input type="text" name="<?php echo $form->getFieldName("name"); ?>" value="<?php echo $form->getFieldValue("name"); ?>" />
        <?php $form->error( "name" ); ?>
    </div>
    
    <!-- Display Email Field -->
    <div class="<?php $form->classes("email", 'validation'); ?> <?php $form->classes("email", 'type'); ?>">
        <label><?php echo $form->getFieldLabel("email"); ?></label>
        <input type="text" name="<?php echo $form->getFieldName("email"); ?>" value="<?php echo $form->getFieldValue("email"); ?>" />
        <?php $form->error( "email" ); ?>
    </div>
    
    <!-- Display Message Field -->
    <div class="<?php $form->classes("message", 'validation'); ?> <?php $form->classes("message", 'type'); ?>">
        <label><?php echo $form->getFieldLabel("message"); ?></label>
        <textarea name="<?php echo $form->getFieldName("message"); ?>"><?php echo $form->getFieldValue("message"); ?></textarea>
        <?php $form->error( "message" ); ?>
    </div>
    
    <?php $form->submit("SEND); ?>

<?php endif; ?>
```