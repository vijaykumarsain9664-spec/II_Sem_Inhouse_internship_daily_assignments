<?php
include "config.php";
include "header.php";
 
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
 
    $stmt = $conn->prepare("SELECT id, name, password, pic FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
 
    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["uid"] = $user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["pic"] = $user["pic"];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<h2>Login</h2>
<?php if ($error): ?><p class="flash"><?= $error ?></p><?php endif; ?>
<form method="POST">
    <label>Email</label>
    <input type="email" name="email" required>
    <label>Password</label>
    <input type="password" name="password" required>
    <button type="submit">Login</button>
</form>
<p><a href="forgot_password.php">Forgot password?</a> | <a href="register.php">Register</a></p>
<?php include "footer.php";?>