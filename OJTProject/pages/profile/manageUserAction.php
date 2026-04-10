<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($conn)) {
    require_once '../../include/config.php'; 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    

    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');

    $action = $_POST['action'];
    $target_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;

    if (!$target_id) {
        echo json_encode(['status' => 'error', 'message' => 'User ID is required.']);
        exit;
    }

    if ($action === 'reset_password') {
        $user_id  = $_POST['user_id'] ?? ''; 
        $new_pass = $_POST['new_password'] ?? '';
        
        if (empty($new_pass)) {
            echo json_encode(['status' => 'error', 'message' => 'Password is required']);
            exit;
        }
        if (strlen($new_pass) < 8) {
            $status = "error";
            $msg_text = "Password should be at least 8 characters long";
        } elseif (!preg_match('/[A-Z]/', $new_pass)) {
            $status = 'error';
            $msg_text = 'Password must include at least one uppercase letter';
        } elseif (!preg_match('/[a-z]/', $new_pass)) {
            $status = 'error';
            $msg_text = 'Password must include at least one lowercase letter';
        } elseif (!preg_match('/\d/', $new_pass)) {
            $status = 'error';
            $msg_text = 'Password must include at least one number';
        } elseif (!preg_match('/[$#@!?]/', $new_pass)) {
            $status = 'error';
            $msg_text = 'Password must include at least one special character ($#@!?)';
        } else {
            // Success Logic - Process the Update
            $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
            
            try {
                // Use UPDATE because we are resetting an existing password
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashed_password, $user_id);

                if ($stmt->execute()) {
                    $status = "success";
                    $msg_text = "Password updated successfully!";
                } else {
                    $status = "error";
                    $msg_text = "Failed to update password.";
                }
                $stmt->close();
            } catch (mysqli_sql_exception $e) {
                $status = "error";
                $msg_text = "Database error: " . $e->getMessage();
            }
        }

    // Return the response as JSON
    echo json_encode(['status' => $status, 'message' => $msg_text]);
    exit;
}

    if ($action === 'delete') {
        $sql = "DELETE FROM users WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $target_id);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'User deleted!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => $stmt->error]);
            }
            $stmt->close();
        }
        exit;
    }
}