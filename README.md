# WordPress Developer Forms
Author: James Collings  
Version: 0.2.2  
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

## WPDF Documentation

### Form Email notifications

Email notifications are setup to send when the form is submitted successfully.

The following function allows you to register an email notification for when the form is submitted.
 
```
$form->add_notification( String $recipient, String $subject, String $message, Array $args);
```

**$recipient** : Recipients email address  
**$subject** : Email Subject Line  
**$message** : Email message  
**$args** : Extra arguments

##### Basic Example

```
$form->add_notification( "to@email.com", "Email Subject", "Email Message" );
```

#### Add from, cc, or bcc

Email options such as adding a sent from address, cc, or bcc can be done by passing the following fields into the functions $args array

```
$args = array(
    'from' => '',  
    'cc' => '',
    'bcc' => '',
);
```

**$from** : Email address notification is sent from  
**$cc** : List of email addresses to cc  
**$bcc** : List of email addresses to bcc  

##### Example

Send a notification with custom from, cc, and bcc email addresses, this example would send the email address "from@email.com", send a carbon copy to "cc@email.com" and a blind carbon copy to "bcc@email.com"

```
$args = array(
    'from' => 'from@email.com',  
    'cc' => 'cc@email.com',
    'bcc' => 'bcc@email.com',
);

$form->add_notification( "to@email.com", "Email Subject", "Email Message", $args );
```

#### Conditional Notifications

If you want to send email notifications to specific email addresses depending on what data has been submitted you can 
use the **conditions** argument to add a list of what fields have to equal for the conditions to be met.

```
$form->add_notification( String $recipient, String $subject, String $message, Array $args );
```

```
$args = array(
    'conditions' => array(
        'field_id' => ''
    )
);
```

**$conditions**: An array of keys which are a field name and values which are the specified field value

##### Example

The following example uses a field called "Subject", depending if the value of that field is either "booking" or 
"enquiry" different notifications will be delivered.

```
// register notifiaction to be sent if the field "subject" equals "booking"
$args = array(
    'conditions' => array(
        'subject' => 'booking'
    )
);
$form->add_notification( "booking@email.com", "Booking Email Subject", "Email Message", $args );

// register notifiaction to be sent if the field "subject" equals "enquiry"
$args = array(
    'conditions' => array(
        'subject' => 'enquiry'
    )
);
$form->add_notification( "enquiry@email.com", "Enquiry Email Subject", "Email Message", $args );
```

#### Displaying Form Data in Email

To display form data in your notification there are merge tags, these are based on the fields in your form. You 
can display the data from any field in the form with "{{field_**FIELD_NAME**}}" for example if you have a field called fname
to display this field you would use the merge tag "{{field_fname}}".

##### Display Single Field

In the following example it expects the form to have a field called "Name".

```
$form->add_notification( "to@email.com", "Submission for {{name}}", "A new submission has been submitted {{name}}" );
```

##### Display All Fields

There is also a merge tag that allows you to display a list of all fields submitted, this can only be used in the message field "{{fields}}"

The following example will replace the merge tag `{{fields}}` with a list of all fields and their values

```
$form->add_notification( "to@email.com", "Email Subject", "Form Submission: {{fields}}" );
```

##### Send to Email Field

The following example expects the form to have a field called "Email", and the notification will be sent to the email address submitted from that field.

```
$form->add_notification( "{{field_email}}", "Thank you!", "Thank you for taking the time to fill out the form with the following data: {{fields}}" );
```

### Entry Table Headings Columns

Entry admin headings can be customised by to display specific entry data.

```
$form->settings(array(
    'admin_columns' => array(
        'column_name' 	=> __('Column Label', 'wpdf')
    )
));
```