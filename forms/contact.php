<?php
// Database connection
$host = 'localhost'; // Change to your DB host
$db = 'your_database'; // Change to your DB name
$user = 'your_username'; // Change to your DB user
$pass = 'your_password'; // Change to your DB password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Form inputs
$name = htmlspecialchars($_POST['name']);
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$subject = htmlspecialchars($_POST['subject']);
$message = htmlspecialchars($_POST['message']);

if (!$email) {
    die('Invalid email address.');
}

// Save to database
try {
    $stmt = $pdo->prepare("INSERT INTO subscribers (name, email) VALUES (:name, :email)
                           ON DUPLICATE KEY UPDATE subscribed = TRUE");
    $stmt->execute(['name' => $name, 'email' => $email]);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Send thank-you email
require 'path/to/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer;
$mail->isSMTP();
$mail->Host = 'smtp.example.com'; // Replace with your SMTP host
$mail->SMTPAuth = true;
$mail->Username = 'your_smtp_username'; // Replace with your SMTP username
$mail->Password = 'your_smtp_password'; // Replace with your SMTP password
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

$mail->setFrom('noreply@example.com', 'Lifted Women Circle'); // Change as needed
$mail->addAddress($email, $name);
$mail->Subject = 'Thank you for reaching out!';
$mail->Body = "Hi $name,\n\nThank you for reaching out to Lifted Women Circle. We'll keep you updated on recent activities.\n\nBest regards,\nLifted Women Circle";

if (!$mail->send()) {
    error_log('Mailer Error: ' . $mail->ErrorInfo);
    die('Unable to send thank-you email.');
}

echo 'Form submitted successfully!';
?>
