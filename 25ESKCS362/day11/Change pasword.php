<?php
include "config.php";
include "header.php";
if (!isset($_SESSION["uid"])) {
    header("Location: login.php");
    exit;
}
 
$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION["uid"]);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
 
    if (!password_verify($_POST["current"], $user["password"])) {
        $message = "Current password is incorrect.";
    } elseif ($_POST["new"] !== $_POST["confirm"]) {
        $message = "New passwords do not match.";
    } else {
        $newHash = password_hash($_POST["new"], PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->bind_param("si", $newHash, $_SESSION["uid"]);
        $update->execute();
        $message = "Password updated successfully.";
    }
}
?>
<h2>Change Password</h2>
<?php if ($message): ?><p class="flash"><?= $message ?></p><?php endif; ?>
<form method="POST">
    <label>Current Password</label>
    <input type="password" name="current" required>
    <label>New Password</label>
    <input type="password" name="new" required>
    <label>Confirm New Password</label>
    <input type="password" name="confirm" required>
    <button type="submit">Update Password</button>
</form>
<?php include "footer.php"; ?>