<?php
// =========================================================
// db_connect.php - single shared database connection
// =========================================================
 
$host     = "localhost";
$username = "root";       // change if your MySQL user is different
$password = "";           // change if your MySQL has a password
$database = "student_portal";
 
// mysqli - object oriented style
$conn = new mysqli($host, $username, $password, $database);
 
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
 
// Force UTF-8 so names etc. store correctly
$conn->set_charset("utf8mb4");
?>