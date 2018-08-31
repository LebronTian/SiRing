<?php
/**
 * Created by PhpStorm.
 * User: 李火生
 * Date: 2018/8/6
 * Time: 9:57
 */


namespace phpmailer;

use phpmailer\PHPMailer;


class  Email
{
    //发送邮件的方法
    /**
     * @param $to
     * @param $title
     * @param $content
     */
    public static function send($to, $title, $content)
    {

        date_default_timezone_set('PRC');//set time

//Create a new PHPMailer instance
        $mail = new PHPMailer();
//Tell PHPMailer to use SMTP
        $mail->isSMTP();
//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
        //TODO关闭信息
        $mail->SMTPDebug = 0;
//Ask for HTML-friendly debug output
        $mail->Debugoutput = 'html';
//Set the hostname of the mail server
        //TODO:ddd
        $mail->Host = config('email.host');
//Set the SMTP port number - likely to be 25, 465 or 587
        $mail->Port = 25;
//Whether to use SMTP authentication
        $mail->SMTPAuth = true;
//Username to use for SMTP authentication
        //TODO:
        $mail->Username = config('email.username');
//Password to use for SMTP authentication
        //TODO:
        $mail->Password = config('email.password');
//Set who the message is to be sent from
        //TODO:
        $mail->setFrom(config('email.username'), 'lee');
//Set an alternative reply-to address
//$mail->addReplyTo('replyto@example.com', 'First Last');
//Set who the message is to be sent to
        //TODO
        $mail->addAddress($to);
//Set the subject line
        //TODO
        $mail->Subject = $title;
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
        //TODO
        $mail->msgHTML($content);
//Replace the plain text body with one created manually
//$mail->AltBody = 'This is a plain-text message body';
//Attach an image file
//$mail->addAttachment('images/phpmailer_mini.png');

//send the message, check for errors
        //TODO
        if (!$mail->send()) {

           return false;
        } else {
            return true;
        }


    }

}