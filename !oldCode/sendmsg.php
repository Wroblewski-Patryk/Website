<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require './php/PHPMailer/src/Exception.php';
require './php/PHPMailer/src/PHPMailer.php';
require './php/PHPMailer/src/SMTP.php';

//VALIDATE INPUT DATA
if ( !empty($_POST['name']) && $_POST['name'] != '' && preg_match("/^[a-zA-Z-' ]*$/",$_POST['name']) ){
    $name = removeHackers($_POST['name']);
} else
    echo('ErrorName');

if ( !empty($_POST['email']) && $_POST['email'] != '' && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ){
    $email = removeHackers($_POST['email']);
} else
    echo('ErrorEmail');

if ( !empty($_POST['message']) && $_POST['message'] != '' ){
    $message = removeHackers($_POST['message']);
} else
    echo('ErrorMessage');


if ( isset($name) && isset($email) && isset($message) ){
    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = SMTP::DEBUG_OFF;               //Enable verbose debug output
        $mail->isSMTP();                                     //Send using SMTP
        $mail->Host = 'ssl0.ovh.net';                    //Set the SMTP server to send through
        $mail->SMTPAuth = true;                              //Enable SMTP authentication
        $mail->Username = 'no-reply@wroblewskipatryk.pl';    //SMTP username
        $mail->Password = 'Wroblewski12Patryk#$';            //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;     //Enable implicit TLS encryption
        $mail->Port = 465;                                   //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        $mail->setFrom('no-reply@wroblewskipatryk.pl', 'Wroblewski Patryk');
        $mail->addAddress($email, $name);
        $mail->addBCC('wroblewskipatryk@gmail.com', 'Patryk Wroblewski');

        $messageContent = "Dear ".$name.",<br/>Your message was sent.<br/><br/>Copy of message: ".$message."<br/><br/>This e-mail was generated automaticly. Please don't reply.<br/><br/>Best regards<br/>Patryk Wroblewski";
        $messageContentNoHTML = str_replace('<br/>', '', $messageContent);

        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'E-mail from website: wroblewskipatryk.pl';
        $mail->Body = $messageContent;
        $mail->AltBody = $messageContentNoHTML;

        $mail->send();
        echo('Success');
    } catch (Exception $e) {
        echo "ErrorOther";
        echo $_POST;
    }
}

function removeHackers($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
