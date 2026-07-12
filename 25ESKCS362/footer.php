<?php
require_once __DIR__ . '/config.php';

function redirectEditWithErrors(int $id, array $errors, array $old): void {
    $qs = http_build_query([
        'errors' => base64_encode(json_encode($errors)),
        'old'    => base64_encode(json_encode($old)),
    ]);
    header("Location: edit_student.php?id=$id&$qs");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id     = (int)($_POST['id'] ?? 0);
$name   = trim($_POST['name'] ?? '');
$email  = trim($_POST['email'] ?? '');
$branch = trim($_POST['branch'] ?? '');
$cgpa   = trim($_POST['cgpa'] ?? '');
$status = ($_POST['status'] ?? 'Active') === 'Inactive' ? 'Inactive' : 'Active';

$old = compact('name', 'email', 'branch', 'cgpa', 'status');
$errors = [];

// Fetch existing record
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
$stmt->execute([':id' => $id]);
$existing = $stmt->fetch();
if (!$existing) {
    header('Location: index.php');
    exit;
}

// -------------------- Validation --------------------
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

// Uniqueness check (excluding this record)
if (!$errors) {
    $check = $pdo->prepare("SELECT COUNT(*) AS c FROM students WHERE email = :email AND id != :id");
    $check->execute([':email' => $email, ':id' => $id]);
    if ((int)$check->fetch()['c'] > 0) {
        $errors[] = 'Another student already uses this email address.';
    }
}

// -------------------- Optional photo replacement --------------------
$photoFilename = $existing['photo']; // keep existing by default
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
        $newFilename = 'student_' . uniqid() . '.' . $ext;

        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0755, true);
        }
        if (move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $newFilename)) {
            // Remove old photo file if it existed
            if (!empty($existing['photo']) && file_exists(UPLOAD_DIR . $existing['photo'])) {
                @unlink(UPLOAD_DIR . $existing['photo']);
            }
            $photoFilename = $newFilename;
        } else {
            $errors[] = 'Failed to save the uploaded photo.';
        }
    }
}

if ($errors) {
    redirectEditWithErrors($id, $errors, $old);
}

// -------------------- Update --------------------
$stmt = $pdo->prepare(
    "UPDATE students
     SET name = :name, email = :email, branch = :branch, cgpa = :cgpa, status = :status, photo = :photo
     WHERE id = :id"
);
$stmt->execute([
    ':name'   => $name,
    ':email'  => $email,
    ':branch' => $branch,
    ':cgpa'   => $cgpa,
    ':status' => $status,
    ':photo'  => $photoFilename,
    ':id'     => $id,
]);

header('Location: index.php?msg=updated');
exit;
