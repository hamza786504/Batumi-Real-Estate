<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$jsonData = file_get_contents("php://input");
$data = json_decode($jsonData, true);

if ($data === null) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data.']);
    exit();
}

$name  = htmlspecialchars($data['name']);
$email = htmlspecialchars($data['email']);
$phone = htmlspecialchars($data['phone']);

// Gmail credentials
$gmailUser = "hamzakhaliddev@gmail.com";  
$gmailPass = "scll eeth uvxi epqi"; // Gmail App Password

$adminEmailSent  = sendAdminEmail($gmailUser, $gmailPass, $email, $phone, $name);
$clientEmailSent = sendClientConfirmationEmail($gmailUser, $gmailPass, $email, $phone, $name);

$response = [];
if ($adminEmailSent && $clientEmailSent) {
    $response['status'] = 'success';
    $response['message'] = 'Emails sent successfully.';
} else {
    $response['status'] = 'error';
    $response['message'] = 'There was an issue sending the emails.';
}

echo json_encode($response);

function sendAdminEmail($gmailUser, $gmailPass, $email, $phone, $name) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $gmailUser;
        $mail->Password   = $gmailPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom($gmailUser, 'Batumi');
        $mail->addAddress($gmailUser);

        $mail->isHTML(true);
        $mail->Subject = 'New Client Query';
        $mail->Body = "
            <p>You have received a new contact form submission:</p>
            <table border='1' cellpadding='5' cellspacing='0'>
                <tr><td><strong>Name:</strong></td><td>{$name}</td></tr>
                <tr><td><strong>Email:</strong></td><td>{$email}</td></tr>
                <tr><td><strong>Phone:</strong></td><td>{$phone}</td></tr>
            </table>
        ";
        return $mail->send();
    } catch (Exception $e) {
        error_log('Admin Email Error: ' . $e->getMessage());
        return false;
    }
}

function sendClientConfirmationEmail($gmailUser, $gmailPass, $email, $phone, $name) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $gmailUser;
        $mail->Password   = $gmailPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom($gmailUser, 'Batumi');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Confirmation of Your Contact Form Submission';
        $mail->Body = "
            <p>Dear {$name},</p>
            <p>Thank you for contacting us! We have received your details and will get back to you soon.</p>
            <p><strong>Your Submitted Details:</strong></p>
            <table border='1' cellpadding='5' cellspacing='0'>
                <tr><td><strong>Name:</strong></td><td>{$name}</td></tr>
                <tr><td><strong>Email:</strong></td><td>{$email}</td></tr>
                <tr><td><strong>Phone:</strong></td><td>{$phone}</td></tr>
            </table>
            <p>Best regards,<br><strong>Bitumi</strong></p>
        ";
        return $mail->send();
    } catch (Exception $e) {
        error_log('Client Email Error: ' . $e->getMessage());
        return false;
    }
}
