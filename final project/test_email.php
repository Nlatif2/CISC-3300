<?php
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Create an instance of PHPMailer
$mail = new PHPMailer;

try {
    // SMTP configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Your SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = ''; // Your email address
    $mail->Password = ''; // Your email password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Email settings
    $mail->setFrom('', 'Test Email');
    $mail->addAddress(''); // Change to a valid recipient address
    $mail->Subject = 'Test Email from Event Manager';
    $mail->Body = 'This is a test email from the Event Manager application. If you received this, the email function is working correctly.';

    // Send the email
    if ($mail->send()) {
        echo 'Test email sent successfully!';
    } else {
        echo 'Failed to send test email. Mailer Error: ' . $mail->ErrorInfo;
    }
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>