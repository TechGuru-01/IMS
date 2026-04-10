<?php
function createAuditLog($conn, $userId, $actionType, $description) {
    $sql = "INSERT INTO audit_logs (user_id, action_type, description, created_at) 
            VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $userId, $actionType, $description);
    return $stmt->execute();
}
?>