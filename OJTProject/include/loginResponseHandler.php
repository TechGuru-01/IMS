<?php
session_start();
include "./include/config.php"; 

if (isset($_SESSION['user_id'])) {
    header("Location: ./pages/dashBoard/dashBoard.php");
    exit();
}

$status = ""; 
$msg_text = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = isset($_POST['username']) ? trim($_POST['username']) : '';
    $pass = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (!empty($user) && !empty($pass)) {
        $stmt = $conn->prepare("SELECT id, username, password, position FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($pass, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['id'] = $row['id']; 
                $_SESSION['username'] = $row['username'];
                $_SESSION['position'] = $row['position']; 
                
                $status = "success";
                $msg_text = "Welcome back, " . $row['username'] . "!";
            } else {
                $status = "error";
                $msg_text = "Incorrect Password, Please Try.";
            }
        } else {
            $status = "error";
            $msg_text = "Username Not Found.";
        }
        $stmt->close();
    } else {
        $status = "warning";
        $msg_text = "Please fill in all fields.";
    }
    
    $error_msg = $msg_text;
}
?>