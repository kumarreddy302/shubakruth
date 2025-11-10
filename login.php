<?php 
session_start();
include 'header.php'; 
?>

<main class="content-area">
    <div class="auth-container">
        <div class="auth-form-wrapper" id="login-form">
            <form action="process-login.php" method="POST">
                <h2>Login</h2>
                <p>Welcome back! Please enter your details.</p>
                <?php 
                    if (isset($_SESSION['message'])) {
                        echo '<p class="message ' . $_SESSION['message_type'] . '">' . htmlspecialchars($_SESSION['message']) . '</p>';
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                    }
                ?>
                <div class="form-group">
                    <label for="login-email">Email</label>
                    <input type="email" id="login-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" name="password" required>
                </div>
                <button type="submit" class="btn-primary">Login</button>
                <p class="form-toggle-text">Don't have an account? <a href="#" id="show-register">Register here</a></p>
            </form>
        </div>

        <div class="auth-form-wrapper hidden" id="register-form">
            <form action="process-register.php" method="POST">
                <h2>Create Account</h2>
                <p>Get started with your free account.</p>
                <div class="form-group">
                    <label for="reg-name">Full Name</label>
                    <input type="text" id="reg-name" name="full_name" required>
                </div>
                <div class="form-group">
                    <label for="reg-email">Email</label>
                    <input type="email" id="reg-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="reg-password">Password</label>
                    <input type="password" id="reg-password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="reg-role">Register as</label>
                    <select id="reg-role" name="role">
                        <option value="user" selected>User</option>
                        <option value="manager">Manager</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">Register</button>
                <p class="form-toggle-text">Already have an account? <a href="#" id="show-login">Login here</a></p>
            </form>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>