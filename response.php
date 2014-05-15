<?php

if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {

	include('includes/class.phpmailer.php');
	include('includes/class.validate.php');

	$v = new Validate( $_POST, "Something Went Wrong." );

	$v->require_fields(true, array("age_range", "food") );
	
	$v->check_alpha("name", "Name can only contain letters.");
	$v->check_email("email", "Enter a valid email address.");
	$v->check_phone("phone", "Enter a ten digit phone number.");
	$v->check_url("url", "Enter your domain (ex: yourdomain.com).");

	$v->is_checked( array("age_range", "food", "choice") );

	if ( !empty($v->form_errors) ) {
 		
 		$v->error_response($_POST["submit"]);
	 
	 } else {
	 	/*
		*  If everything checks out we initiate a new mailer object
		*  and set up the recipitents & message content
		*/
		$mailer = new PHPmailer();

		//$mailer->IsSendmail();

		$mailer->CharSet = 'UTF-8';

		$mailer->AddReplyTo( $v->clean_vals["email"], $v->clean_vals["name"] );
		$mailer->SetFrom( $v->clean_vals["email"], $v->clean_vals["name"] );

		$address = "mail@yourdomain.com";

		$mailer->AddAddress($address, "Sender Name");

		$mailer->Subject = "A Test Message";

		$msgcontent = "Hi";

		$mailer->msgHTML($msgcontent);

		/*  End message content setup
		*/

		/*	Try to send 
		*
		*/
		$v->try_send( !$mailer->Send(), $mailer->ErrorInfo );

		$v->error_response($_POST["submit"]);

	 }

}