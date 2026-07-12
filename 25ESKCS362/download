<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = (int)($_POST['id'] ?? 0);

$stmt = $pdo->prepare("SELECT photo FROM students WHERE id = :id");
$stmt->execute([':id' => $id]);
$student = $stmt->fetch();

if ($student) {
    $del = $pdo->prepare("DELETE FROM students WHERE id = :id");
    $del->execute([':id' => $id]);

    // Clean up the uploaded photo file, if any
    if (!empty($student['photo']) && file_exists(UPLOAD_DIR . $student['photo'])) {
        @unlink(UPLOAD_DIR . $student['photo']);
    }
}

header('Location: index.php?msg=deleted');
exit;
