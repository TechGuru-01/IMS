<?php
include "../../include/config.php";
$status = ""; 
$msg_text = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['full_name']); 
    $position = trim($_POST['position']);
    $user = trim($_POST['username']);
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    if (empty($fullname) || empty($position) || empty($user) || empty($pass)) {
        $status = "warning";
        $msg_text = "Please fill in all fields.";
    } 

    elseif ($pass !== $confirm_pass) {
        $status = "error";
        $msg_text = "Passwords do not match!";
    } else {
        if (strlen($pass) < 8) {
            $status = "error";
            $msg_text = "Password should atleast be 8 characters long";
        } elseif (!preg_match('/[A-Z]/', $pass)) {
            $status = 'error';
            $msg_text = 'Password must include atleast one upper case letter';
        } elseif (!preg_match('/[a-z]/', $pass)) {
            $status = 'error';
            $msg_text = 'Password must include atleast one upper case letter';
        } elseif (!preg_match('/\d/', $pass)) {
            $status = 'error';
            $msg_text = 'Password must include atleast one number';
        } elseif (!preg_match('/[$#@!?-_]/', $pass)) {
            $status = 'error';
            $msg_text = 'Password must include atleast one special character';
        } else {
            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
            try {
                $stmt = $conn->prepare("INSERT INTO users (full_name, position, username, password) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $fullname, $position, $user, $hashed_password);

                if ($stmt->execute()) {
                    $status = "success";
                    $msg_text = "Registration successful!";
                }
                $stmt->close();
            } catch (mysqli_sql_exception $e) {
                if ($e->getCode() == 1062) {
                    $status = "warning";
                    $msg_text = "Username '$user' is already taken.";
                } else {
                    $status = "error";
                    $msg_text = "Database error: " . $e->getMessage();
                }
            }
        }
    }
}
?>