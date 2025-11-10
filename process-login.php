<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // --- Fetch user from database ---
    $stmt = $conn->prepare("SELECT id, full_name, email, password_hash, role, is_verified FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // --- Verify password ---
        if (password_verify($password, $user['password_hash'])) {
            
            // --- Check if account is verified ---
            if ($user['is_verified'] == 1) {
                // --- Set session variables ---
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                // --- Role-Based Redirection ---
                switch ($user['role']) {
                    case 'admin':
                        header("Location: admin_dashboard.php");
                        break;
                    case 'manager':
                        header("Location: manager_dashboard.php");
                        break;
                    default:
                        header("Location: user_dashboard.php");
                        break;
                }
                exit();

            } else {
                $_SESSION['message'] = "Your account is not verified. Please check your email for the OTP.";
                $_SESSION['message_type'] = "error";
                $_SESSION['email_for_verification'] = $email;
                header("Location: verify-otp.php");
                exit();
            }

        } else {
            $_SESSION['message'] = "Invalid email or password.";
            $_SESSION['message_type'] = "error";
            header("Location: login.php");
            exit();
        }

    } else {
        $_SESSION['message'] = "Invalid email or password.";
        $_SESSION['message_type'] = "error";
        header("Location: login.php");
        exit();
    }
    
    $stmt->close();
    $conn->close();
}