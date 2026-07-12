<?php
session_start();
include "config.php";
header("Content-Type: application/json");
 
if (!isset($_SESSION["uid"])) {
    echo json_encode(["success" => false, "error" => "Not logged in."]);
    exit;
}
 
if (isset($_FILES["picture"]) && $_FILES["picture"]["error"] === 0) {
    $ext = pathinfo($_FILES["picture"]["name"], PATHINFO_EXTENSION);
    $filename = "user_" . $_SESSION["uid"] . "." . $ext;
    move_uploaded_file($_FILES["picture"]["tmp_name"], "uploads/" . $filename);
 
    $stmt = $conn->prepare("UPDATE users SET pic = ? WHERE id = ?");
    $stmt->bind_param("si", $filename, $_SESSION["uid"]);
    $stmt->execute();
 
    $_SESSION["pic"] = $filename;
    echo json_encode(["success" => true, "filename" => $filename]);
} else {
    echo json_encode(["success" => false, "error" => "Upload failed."]);
}
 
