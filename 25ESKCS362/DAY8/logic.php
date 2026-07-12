<?php
/**
 * logic.php
 * All backend logic for the Student Registration Confirmation System.
 * No HTML lives in this file — only PHP.
 */
 
$errors    = [];
$submitted = false;
$data      = [
    'name'    => '',
    'email'   => '',
    'cgpa'    => '',
    'branch'  => '',
    'college' => ''
];
$grade     = '';
$alertType = '';
$icon      = '';
$today     = date("l, F j, Y");
 
// ---------------------------------------------------
// Grade logic
// ---------------------------------------------------
function calculateGrade($cgpa) {
    if ($cgpa >= 9.0)  return ['grade' => 'A+', 'alert' => 'success', 'icon' => 'fa-star'];
    if ($cgpa >= 8.0)  return ['grade' => 'A',  'alert' => 'success', 'icon' => 'fa-thumbs-up'];
    if ($cgpa >= 7.0)  return ['grade' => 'B',  'alert' => 'info',    'icon' => 'fa-face-smile'];
    if ($cgpa >= 6.0)  return ['grade' => 'C',  'alert' => 'warning', 'icon' => 'fa-face-meh'];
    if ($cgpa >= 5.0)  return ['grade' => 'D',  'alert' => 'warning', 'icon' => 'fa-triangle-exclamation'];
    return ['grade' => 'F', 'alert' => 'danger', 'icon' => 'fa-circle-xmark'];
}
 
// ---------------------------------------------------
// Handle POST submission
// ---------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 
    $data['name']    = trim($_POST['name']    ?? '');
    $data['email']   = trim($_POST['email']   ?? '');
    $data['cgpa']    = trim($_POST['cgpa']    ?? '');
    $data['branch']  = trim($_POST['branch']  ?? '');
    $data['college'] = trim($_POST['college'] ?? '');
 
    // Check every field is filled in
    foreach ($data as $key => $value) {
        if ($value === '') {
            $errors[] = ucfirst($key) . ' is required.';
        }
    }
 
    // Validate CGPA range/type
    if ($data['cgpa'] !== '' && (!is_numeric($data['cgpa']) || $data['cgpa'] < 0 || $data['cgpa'] > 10)) {
        $errors[] = 'CGPA must be a number between 0 and 10.';
    }
 
    // Basic email format check
    if ($data['email'] !== '' && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
 
    if (empty($errors)) {
        $submitted = true;
        $data['cgpa'] = (float) $data['cgpa'];
 
        $result    = calculateGrade($data['cgpa']);
        $grade     = $result['grade'];
        $alertType = $result['alert'];
        $icon      = $result['icon'];
    }
}
 
// ---------------------------------------------------
// Helper for safe output
// ---------------------------------------------------
function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES);
}