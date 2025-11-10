<?php
/***********************
 * process-register.php
 ***********************/
session_start();
require_once __DIR__ . '/db_connect.php';

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/vendor/autoload.php';

/**
 * CONFIG – EDIT THESE
 */
const SMTP_HOST          = 'smtp.resilientshieldcybersolutions.com'; // your SMTP host
const SMTP_USER          = 'noreply@resilientshieldcybersolutions.com';
const SMTP_PASS          = '9989145049Sai@';
const FROM_EMAIL         = 'noreply@resilientshieldcybersolutions.com';
const FROM_NAME          = 'Shubhakruth Medical Genetics';

// If your mailserver has a proper cert, set this to false
const ALLOW_SELF_SIGNED  = true;

// Where to write SMTP debug logs if send fails
const SMTP_LOG_DIR       = __DIR__ . '/logs';

// Helper: make sure log dir exists
if (!is_dir(SMTP_LOG_DIR)) {
    @mkdir(SMTP_LOG_DIR, 0775, true);
}

// Strict POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// --------- Input ---------
$full_name = trim($_POST['full_name'] ?? '');
$email     = trim($_POST['email'] ?? '');
$password  = $_POST['password'] ?? '';
$role      = $_POST['role'] ?? 'user';

// --------- Basic validation ---------
$allowed_roles = ['user', 'manager', 'admin'];

if ($full_name === '' || $email === '' || $password === '' || $role === '') {
    exit('All fields are required.');
}
if (!in_array($role, $allowed_roles, true)) {
    exit('Invalid role specified.');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    exit('Invalid email format.');
}
if (strlen($password) < 8) {
    exit('Password must be at least 8 characters.');
}

// --------- Check duplicate email ---------
try {
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    if ($exists) {
        $_SESSION['message'] = 'An account with this email already exists.';
        $_SESSION['message_type'] = 'error';
        header('Location: login.php');
        exit();
    }
} catch (Throwable $e) {
    http_response_code(500);
    exit('Database error (checking user).');
}

// --------- Create user with OTP ---------
$password_hash = password_hash($password, PASSWORD_DEFAULT);
$otp           = random_int(100000, 999999);
$otp_expiry    = date('Y-m-d H:i:s', strtotime('+10 minutes'));

try {
    $stmt = $conn->prepare('
        INSERT INTO users (full_name, email, password_hash, role, otp, otp_expiry)
        VALUES (?, ?, ?, ?, ?, ?)
    ');
    $stmt->bind_param('ssssss', $full_name, $email, $password_hash, $role, $otp, $otp_expiry);
    $ok = $stmt->execute();
    $stmt->close();

    if (!$ok) {
        throw new Exception('Insert failed.');
    }
} catch (Throwable $e) {
    http_response_code(500);
    exit('Error during registration. Please try again.');
}

// --------- Send OTP email ---------
function sendOtpMail(string $toEmail, string $toName, int $otp, &$error = null): bool
{
    $mail = new PHPMailer(true);
    $smtpLog = '';

    // Capture SMTP debug into $smtpLog
    $mail->SMTPDebug  = SMTP::DEBUG_SERVER;        // turn off after debugging
    $mail->Debugoutput = function($str, $level) use (&$smtpLog) {
        $smtpLog .= '[' . $level . '] ' . $str . PHP_EOL;
    };

    // Common mail content
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->setFrom(FROM_EMAIL, FROM_NAME);
    $mail->addAddress($toEmail, $toName);
    $mail->Subject = 'Your Verification Code';
    $safeName = htmlspecialchars($toName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $mail->Body    = 'Hello ' . $safeName . ',<br><br>Your One-Time Password (OTP) is: <b>' . $otp . '</b><br><br>This code is valid for 10 minutes.';
    $mail->AltBody = 'Your OTP is: ' . $otp . ' (valid for 10 minutes).';

    // Allow self-signed (only if needed)
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer'       => !ALLOW_SELF_SIGNED,
            'verify_peer_name'  => !ALLOW_SELF_SIGNED,
            'allow_self_signed' => ALLOW_SELF_SIGNED,
        ],
    ];

    // Some PHP/OpenSSL stacks prefer IPv4 only
    $ipv4Host = gethostbyname(SMTP_HOST); // returns IPv4; if it returns the same host string, DNS failed

    // Try a few common port/encryption combos
    $candidates = [
        ['port' => 587, 'secure' => PHPMailer::ENCRYPTION_STARTTLS], // STARTTLS
        ['port' => 465, 'secure' => PHPMailer::ENCRYPTION_SMTPS],    // SMTPS
        ['port' => 26,  'secure' => PHPMailer::ENCRYPTION_STARTTLS], // alt cPanel port (if enabled)
    ];

    foreach ($candidates as $cfg) {
        try {
            $mail->smtpClose();
            $mail->isSMTP();
            $mail->Host       = $ipv4Host;            // force IPv4 to avoid IPv6 issues
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = $cfg['secure'];
            $mail->Port       = $cfg['port'];
            $mail->Timeout    = 15;                   // seconds
            $mail->SMTPKeepAlive = false;

            if ($mail->send()) {
                return true;
            }
        } catch (Throwable $e) {
            // keep trying next candidate
            $error = $e->getMessage();
            continue;
        }
    }

    // If we get here, all attempts failed — write log file
    $date = date('Ymd-His');
    $logFile = rtrim(SMTP_LOG_DIR, '/\\') . '/smtp-' . $date . '.log';
    $details  = "Last error: " . ($error ?? 'unknown') . PHP_EOL;
    $details .= "Debug log:" . PHP_EOL . $smtpLog;
    @file_put_contents($logFile, $details);

    $error = "SMTP connection failed. See log: {$logFile}";
    return false;
}

$error = null;
$sent  = sendOtpMail($email, $full_name, $otp, $error);

if ($sent) {
    $_SESSION['email_for_verification'] = $email;
    header('Location: verify-otp.php');
    exit();
}

// If email failed, you may choose to delete the inserted user to allow re-registration.
try {
    $stmt = $conn->prepare('DELETE FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->close();
} catch (Throwable $ignore) {}

$_SESSION['message'] = 'We could not send the verification email. Please try again later or contact support.';
$_SESSION['message_type'] = 'error';

// For admins/devs, expose a minimal hint (real details are in the log file)
if (isset($error)) {
    // uncomment during debugging:
    // echo htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

header('Location: register.php');
exit();
