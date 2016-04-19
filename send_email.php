<?php

  require 'vendor/autoload.php';
  
  // Read post request
  $from     = $_POST['from'];
  $to       = $_POST['to'];
  $subject  = $_POST['subj'];
  $ehtml    = $_POST['body'];

  // printf("From: $from<br>");
  // printf("To  : $to<br>");
  // printf("Subj: $subject<br>");
  // printf("html: $ehtml<br>");
  
  $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
   
  if(!preg_match($email_exp,$to)) {
      $error_message = 'The Email Address you entered does not appear to be valid.<br />';
      printf("<h1>ERROR! Malformed email</h1>");
  }
  
  // Sendgrid info 
  $sendgrid_username = 'jennad7';
  $sendgrid_password = 'Database2';
  
  $sendgrid = new SendGrid($sendgrid_username, $sendgrid_password, array("turn_off_ssl_verification" => true));
  $emailMessage = new SendGrid\Email();
  $emailMessage->addTo($to)
      ->setFrom($from)
      ->setSubject($subject)
      ->setHtml($ehtml)
  ;
  
  $response = $sendgrid->send($emailMessage);
  
  //Redirect to referencing page
  header("location:javascript://history.go(-1)");
?>
 <html>
  <body>
    <h1>Email Sent!</h1>
  </body>
</html>