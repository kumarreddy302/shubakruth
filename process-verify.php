<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];
    $email = $_SESSION['email_for_verification'];

    $stmt = $conn->prepare("SELECT otp, otp_expiry FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($db_otp, $db_otp_expiry);
    $stmt->fetch();
    $stmt->close();

    if ($db_otp == $otp && strtotime($db_otp_expiry) > time()) {
        // OTP is correct and not expired
        $stmt = $conn->prepare("UPDATE users SET is_verified = 1, otp = NULL, otp_expiry = NULL WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->close();
        
        // Clean up session and redirect to login with a success message
        unset($_SESSION['email_for_verification']);
        echo "Verification successful! You can now <a href='login.php'>login</a>.";
        // In a real app, you might auto-login the user here.

    } else {
        // OTP is incorrect or expired
        die("Invalid or expired OTP. Please try registering again.");
    }

    $conn->close();
}
?>