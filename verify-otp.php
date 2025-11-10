<?php 
session_start();
if (!isset($_SESSION['email_for_verification'])) {
    header("Location: login.php"); // Redirect if they haven't registered
    exit();
}
include 'header.php'; 
?>

<main class="content-area">
    <div class="auth-container">
        <div class="auth-form-wrapper">
            <form action="process-verify.php" method="POST">
                <h2>Verify Your Account</h2>
                <p>An OTP has been sent to <strong><?php echo htmlspecialchars($_SESSION['email_for_verification']); ?></strong>. Please enter it below.</p>
                <div class="form-group">
                    <label for="otp">6-Digit OTP</label>
                    <input type="text" id="otp" name="otp" required maxlength="6">
                </div>
                <button type="submit" class="btn-primary">Verify</button>
            </form>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>