<?php
include "../../include/config.php"; 

// --- 1. HANDLE ADD ACTION ---
if (isset($_POST['add_employee'])) {
    $name = $_POST['empName'];
    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO technicians (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        header("Location: " . $_SERVER['PHP_SELF']); 
        exit;
    }
}

// --- 2. HANDLE DELETE ACTION ---
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM technicians WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']); 
    exit;
}

// --- 3. FETCH EMPLOYEES ---
$result = $conn->query("SELECT * FROM technicians ORDER BY Name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Employee Management | Admin</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="addUser.css">
    <link rel="stylesheet" href="../../style.css">
    <link rel="stylesheet" href="../../component/navBar/nav-bar.css" />
</head>
<body>
    <?php include "../../component/navbar/nav-bar.php";?>

    <div class="container">
        <div class="form-section">
            <h3 class="section-title">Add New Employee</h3>
            <form method="POST">
                <div class="input-group">
                    <label for="empName">Full Name</label>
                    <input type="text" name="empName" id="empName" placeholder="Last Name, First Name, M.I." required>
                </div>
                <button type="submit" name="add_employee" class="submit-btn-outline">
                    Add Employee <span class="material-symbols-outlined">person_add</span>
                </button>
            </form>
        </div>

        <div class="table-section">
            
            <div class="table-responsive">
                <table id="employeeTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['Name']) ?></td>
                                    <td class="action-cell" style="text-align: center;">
                                        <a href="?delete_id=<?= $row['id'] ?>" 
                                           class="delete-btn-outline" 
                                           onclick="return confirm('Are you sure?')"
                                           style="text-decoration: none; display: inline-block;">
                                            <span class="material-symbols-outlined" style="color: #ed0505;">delete</span>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="2" style="text-align:center;">No employees found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>