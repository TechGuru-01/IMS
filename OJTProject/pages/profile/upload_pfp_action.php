<?php
session_start();
require_once '../../include/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update_pfp') {
    $user_id = $_SESSION['id'];
    $file = $_FILES['profile_pic'];

    $check_old = $conn->prepare("SELECT profile_pic FROM users WHERE id = ?");
    $check_old->bind_param("i", $user_id);
    $check_old->execute();
    $old_res = $check_old->get_result()->fetch_assoc();
    $old_filename = $old_res['profile_pic'] ?? '';

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = "user_" . $user_id . "_" . time() . "." . $ext;
    $target = "../../src/profiles/" . $filename;

    if (move_uploaded_file($file['tmp_name'], $target)) {
        $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
        $stmt->bind_param("si", $filename, $user_id);
        
        if ($stmt->execute()) {

            if (!empty($old_filename) && file_exists("../../src/profiles/" . $old_filename)) {
                unlink("../../src/profiles/" . $old_filename);
            }
            $action_type = "UPDATE";
            $description = "Updated profile picture";
            
            $log_sql = "INSERT INTO audit_logs (user_id, action_type, description) VALUES (?, ?, ?)";
            $log_stmt = $conn->prepare($log_sql);
            $log_stmt->bind_param("iss", $user_id, $action_type, $description);
            $log_stmt->execute();

            echo json_encode(['status' => 'success', 'filename' => $filename]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database update failed']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Upload failed']);
    }
}