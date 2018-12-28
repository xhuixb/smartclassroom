<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/phpmailer/src/Exception.php';
require '../phpmailer/phpmailer/src/PHPMailer.php';
require '../phpmailer/phpmailer/src/SMTP.php';
//Load composer's autoloader


$mail = new PHPMailer();                              // Passing `true` enables exceptions
try {
    //Server settings
    $mail->SMTPSecure = 'tls';
    $mail->Username = "xhuixb@hotmail.es";
    $mail->Password = "xhb110693";
    $mail->AddAddress("no_resposta@iesrocagrossa.cat");
    $mail->FromName = "My Name";
    $mail->Subject = "My Subject";
    $mail->isHTML(true);
    $mail->Body = "<h1>My Body</h1>";
    $mail->AltBody = 'per altres clients';
    $mail->Host = "smtp.live.com";
    $mail->Port = 587;
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->From = $mail->Username;
    $mail->Send();
    echo 'Message has been sent';                          // Enable verbose debug output
} catch (Exception $e) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
}



 /*$mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->SMTPDebug = 2;     
    $mail->Host = 'smtp.live.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'xhuixb@hotmail.es';                 // SMTP username
    $mail->Password = 'xhb110693';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                 // TCP port to connect to
    //Recipients
    $mail->from= $mail->Username;
    $mail->addAddress('no_resposta@iesrocagrossa.cat', 'ximplet');     // Add a recipient
    //$mail->addAddress('ellen@example.com');               // Name is optional
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');
    //Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
    //Content
    $mail->isHTML(true);
    // Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body = 'This is the HTML message body <b>in bold!</b>';
    //$mail->AltBody = 'This is a plain-text message body';
    // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';*/
