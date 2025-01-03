<?php
require 'path/to/PHPMailer/PHPMailerAutoload.php';

$host = 'localhost'; $db = 'your_database'; $user = 'your_username'; $pass = 'your_password';
$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch all subscribers
$stmt = $pdo->query("SELECT email, name FROM subscribers WHERE subscribed = TRUE");
$subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare the email
$mail = new PHPMailer;
$mail->isSMTP();
$mail->Host = 'smtp.example.com';
$mail->SMTPAuth = true;
$mail->Username = 'your_smtp_username';
$mail->Password = 'your_smtp_password';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
$mail->setFrom('noreply@example.com', 'Lifted Women Circle');

foreach ($subscribers as $subscriber) {
    $mail->addAddress($subscriber['email'], $subscriber['name']);
    $mail->Subject = 'Recent Activities Update';
    $mail->Body = "Hi {$subscriber['name']},\n\nHere are the recent updates...\n\nBest regards,\nLifted Women Circle";

    if (!$mail->send()) {
        error_log("Failed to send email to {$subscriber['email']}: " . $mail->ErrorInfo);
    }

    $mail->clearAddresses();
}
echo 'Updates sent successfully!';
?>
