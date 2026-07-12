<?php
include "config.php";
include "header.php";
 
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
 
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);
 
    if ($stmt->execute()) {
        header("Location: login.php");
        exit;
    } else {
        $error = "Email already registered.";
    }
}
?>
<h2>Register</h2>
<?php if ($error): ?><p class="flash"><?= $error ?></p><?php endif; ?>
<form method="POST">
    <label>Name</label>
    <input type="text" name="name" required>
    <label>Email</label>
    <input type="email" name="email" required>
    <label>Password</label>
    <input type="password" name="password" required>
    <button type="submit">Register</button>
</form>
<p>Already have an account? <a href="login.php">Login</a></p>
<?php include "footer.php"; ?>
