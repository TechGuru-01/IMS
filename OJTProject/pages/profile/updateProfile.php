<?php
session_start();
require_once '../../include/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? 1;
    $new_name = $_POST['full_name'];
    $new_pass = $_POST['password'];

    if (!empty($new_pass)) {
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET full_name = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$new_name, $hashed_pass, $user_id]);
    } else {
        $sql = "UPDATE users SET full_name = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$new_name, $user_id]);
    }

    echo json_encode(['status' => 'success']);
}
?>