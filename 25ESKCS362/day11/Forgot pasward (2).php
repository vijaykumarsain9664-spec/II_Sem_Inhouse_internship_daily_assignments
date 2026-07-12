
<?php
include "header.php";
$sent = $_SERVER["REQUEST_METHOD"] === "POST";
?>
<h2>Forgot Password</h2>
<?php if ($sent): ?>
    <p class="success">If an account exists for that email, a reset link has been sent.</p>
<?php else: ?>
    <form method="POST">
        <label>Enter your email</label>
        <input type="email" name="email" required>
        <button type="submit">Send Reset Link</button>
    </form>
<?php endif; ?>
<p><a href="login.php">Back to login</a></p>
<?php include "footer.php"; ?>
 
