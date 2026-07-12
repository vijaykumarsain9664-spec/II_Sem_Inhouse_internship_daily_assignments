<?php
require_once __DIR__ . '/config.php';

function redirectWithErrors(array $errors, array $old): void {
    $qs = http_build_query([
        'errors' => base64_encode(json_encode($errors)),
        'old'    => base64_encode(json_encode($old)),
    ]);
    header("Location: add_student.php?$qs");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: add_student.php');
    exit;
}

$name   = trim($_POST['name'] ?? '');
$email  = trim($_POST['email'] ?? '');
$branch = trim($_POST['branch'] ?? '');
$cgpa   = trim($_POST['cgpa'] ?? '');
$status = ($_POST['status'] ?? 'Active') === 'Inactive' ? 'Inactive' : 'Active';

$old = compact('name', 'email', 'branch', 'cgpa', 'status');
$errors = [];

// -------------------- Server-side validation --------------------
if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
    $errors[] = 'Name must be between 2 and 100 characters.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email address is required.';
}
if (!in_array($branch, BRANCHES, true)) {
    $errors[] = 'Please select a valid branch.';
}
if ($cgpa === '' || !is_numeric($cgpa) || $cgpa < 0 || $cgpa > 10) {
    $errors[] = 'CGPA must be a number between 0 and 10.';
}

// Uniqueness check on email
if (!$errors) {
    $check = $pdo->prepare("SELECT COUNT(*) AS c FROM students WHERE email = :email");
    $check->execute([':email' => $email]);
    if ((int)$check->fetch()['c'] > 0) {
        $errors[] = 'A student with this email already exists.';
    }
}

// -------------------- Photo upload --------------------
$photoFilename = null;
if (!$errors && !empty($_FILES['photo']['name'])) {
    $file = $_FILES['photo'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'There was a problem uploading the photo. Please try again.';
    } elseif ($file['size'] > MAX_UPLOAD_BYTES) {
        $errors[] = 'Photo must be smaller than 2 MB.';
    } elseif (!in_array(mime_content_type($file['tmp_name']), ALLOWED_PHOTO_TYPES, true)) {
        $errors[] = 'Photo must be a JPG, PNG, WEBP, or GIF image.';
    } else {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $photoFilename = 'student_' . uniqid() . '.' . $ext;

        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }
        if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $photoFilename)) {
            $errors[] = 'Failed to save the uploaded photo.';
            $photoFilename = null;
        }
    }
}

if ($errors) {
    redirectWithErrors($errors, $old);
}

// -------------------- Insert into database --------------------
$stmt = $pdo->prepare(
    "INSERT INTO students (name, email, branch, cgpa, status, photo)
     VALUES (:name, :email, :branch, :cgpa, :status, :photo)"
);
$stmt->execute([
    ':name'   => $name,
    ':email'  => $email,
    ':branch' => $branch,
    ':cgpa'   => $cgpa,
    ':status' => $status,
    ':photo'  => $photoFilename,
]);

header('Location: index.php?msg=added');
exit;
