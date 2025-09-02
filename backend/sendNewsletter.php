<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$jsonData = file_get_contents("php://input");
$data = json_decode($jsonData, true);

if ($data === null || empty($data['email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit();
}

$email = htmlspecialchars($data['email']);

$gmailUser = "hamzakhaliddev@gmail.com";  
$gmailPass = "scll eeth uvxi epqi"; // Gmail App Password

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
    $mail->addAddress($gmailUser); // Admin email

    $mail->isHTML(true);
    $mail->Subject = 'New Newsletter Subscription';
    $mail->Body = "
        <p>New subscriber has joined your newsletter:</p>
        <p><strong>Email:</strong> {$email}</p>
    ";

    $mail->send();

    echo json_encode(['status' => 'success', 'message' => 'Subscription email sent.']);
} catch (Exception $e) {
    error_log('Newsletter Email Error: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Failed to send email.']);
}
