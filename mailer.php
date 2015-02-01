<?php

require_once 'globals.php';
require_once 'config.php';

require_once('/usr/share/php/libphp-phpmailer/class.phpmailer.php');

function send_mail_from_mailer($subject, $body, $recipient_address, $recipient_name) {
    $mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

    $mail->IsSMTP(); // telling the class to use SMTP

    try {
        $mail->SMTPDebug  = $GLOBALS['mail']->SMTPDebug;
        $mail->SMTPAuth   = $GLOBALS['mail']->SMTPAuth;
        $mail->SMTPSecure = $GLOBALS['mail']->SMTPSecure;
        $mail->Host       = $GLOBALS['mail']->Host;
        $mail->Port       = $GLOBALS['mail']->Port;
        $mail->Username   = $GLOBALS['mail']->Username; 
        $mail->Password   = $GLOBALS['mail']->Password;
        $mail->AddReplyTo($GLOBALS['mail']->AddReplyTo_address,
                          $GLOBALS['mail']->AddReplyTo_name);
        $mail->SetFrom($GLOBALS['mail']->SetFrom_mailer_address,
                       $GLOBALS['mail']->SetFrom_mailer_name);
        $mail->AddAddress($recipient_address,
                          $recipient_name);
        $mail->Subject = $subject;
        /* $mail->AltBody = ''; */
        $mail->MsgHTML($body);
        /* $mail->AddAttachment('images/phpmailer.gif');      // attachment */
        /* $mail->AddAttachment('images/phpmailer_mini.gif'); // attachment */
        $mail->Send();
        if ($GLOBALS['mail']->SMTPDebug > 0) {
            echo "Message Sent OK</p>\n";
        }
    } catch (phpmailerException $e) {
        if ($GLOBALS['mail']->SMTPDebug > 0) {
            echo $e->errorMessage(); //Pretty error messages from PHPMailer
        }
    } catch (Exception $e) {
        if ($GLOBALS['mail']->SMTPDebug > 0) {
            echo $e->getMessage(); //Boring error messages from anything else!
        }
    }
}

?>
