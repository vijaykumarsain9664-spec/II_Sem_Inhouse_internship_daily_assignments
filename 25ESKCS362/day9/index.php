<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require "db_connect.php";
 
$errors = [];
$success = false;
 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
 
    // 1. Collect + sanitize input
    $full_name = trim($_POST["full_name"]);
    $email     = trim($_POST["email"]);
    $phone     = trim($_POST["phone"]);
    $course    = trim($_POST["course"]);
 
    // 2. Basic validation
    if ($full_name === "") $errors[] = "Full name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid email is required.";
    if (!preg_match('/^[0-9]{10}$/', $phone)) $errors[] = "Phone must be exactly 10 digits.";
    if ($course === "") $errors[] = "Please select a course.";
 
    // 3. Check for duplicate email BEFORE insert (nice friendly message
    //    instead of a raw MySQL "duplicate entry" error)
    if (empty($errors)) {
        $check = $conn->prepare("SELECT id FROM students WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $errors[] = "That email is already registered.";
        }
        $check->close();
    }
 
    // 4. Insert using a prepared statement (prevents SQL injection)
    if (empty($errors)) {
        $stmt = $conn->prepare(
            "INSERT INTO students (full_name, email, phone, course) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssss", $full_name, $email, $phone, $course);
 
        if ($stmt->execute()) {
            $_SESSION["flash_success"] = "$full_name was registered successfully!";
            $stmt->close();
            $conn->close();
            header("Location: index.php"); // Post/Redirect/Get pattern - avoids resubmission on refresh
            exit;
        } else {
            $errors[] = "Something went wrong: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Registration Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title mb-4 text-center">Student Registration</h3>
 
                    <?php if (isset($_SESSION["flash_success"])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= htmlspecialchars($_SESSION["flash_success"]) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION["flash_success"]); ?>
                    <?php endif; ?>
 
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
 
                    <form method="POST" action="index.php">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control"
                                   value="<?= isset($full_name) ? htmlspecialchars($full_name) : "" ?>" required>
                        </div>
 
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= isset($email) ? htmlspecialchars($email) : "" ?>" required>
                        </div>
 
                        <div class="mb-3">
                            <label class="form-label">Phone (10 digits)</label>
                            <input type="text" name="phone" class="form-control" maxlength="10"
                                   value="<?= isset($phone) ? htmlspecialchars($phone) : "" ?>" required>
                        </div>
 
                        <div class="mb-3">
                            <label class="form-label">Course</label>
                            <select name="course" class="form-select" required>
                                <option value="">-- Select Course --</option>
                                <?php
                                $courses = ["B.Tech Computer Science", "BBA", "B.Sc Physics", "B.Com", "BCA"];
                                foreach ($courses as $c):
                                    $selected = (isset($course) && $course === $c) ? "selected" : "";
                                ?>
                                    <option value="<?= $c ?>" <?= $selected ?>><?= $c ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
 
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
 
                    <div class="text-center mt-3">
                        <a href="students.php">View All Registered Students &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>