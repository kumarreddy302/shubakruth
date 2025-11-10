<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'db_connect.php'; // Include the database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize Input Data
    $name = strip_tags(trim($_POST["name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = strip_tags(trim($_POST["subject"]));
    $message = strip_tags(trim($_POST["message"]));

    // --- SAVE TO DATABASE (ADDED) ---
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);
    $stmt->execute();
    $stmt->close();

    // --- SEND EMAIL (Existing Logic) ---
    $mail = new PHPMailer(true);
    try {
        // ... (your existing mail server settings)
        // ... (your existing mail content and send logic)
        
        $_SESSION['message'] = "Thank you for your message! We will get back to you shortly.";
        $_SESSION['message_type'] = "success";
    } catch (Exception $e) {
        $_SESSION['message'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        $_SESSION['message_type'] = "error";
    }

    header("Location: index.php#contact-form");
    exit();
}
?>