<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Login System</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
<div class="container">
<?php if (isset($_SESSION['name'])): ?>
    <div class="navbar">
        <span>Hi, <?= htmlspecialchars($_SESSION['name']) ?></span>
        <img class="avatar" src="uploads/<?= htmlspecialchars($_SESSION['pic'] ?? 'default.png') ?>">
        <a href="dashboard.php">Dashboard</a>
        <a href="profile.php">Profile</a>
        <a href="change_password.php">Change Password</a>
        <a href="logout.php">Logout</a>
    </div>
<?php endif; ?>