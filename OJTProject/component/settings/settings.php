<?php 

include '../../include/config.php'; 

if (isset($_POST['addTechnician'])) {
    $tech_name = mysqli_real_escape_string($conn, $_POST['tech_name']);
    if (!empty($tech_name)) {
        $insertQuery = "INSERT INTO technicians (Name) VALUES ('$tech_name')";
        if (mysqli_query($conn, $insertQuery)) {
            $_SESSION['keep_modal_open'] = true; 
            echo "<script>window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
            exit();
        }
    }
}


if (isset($_GET['delete_id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $deleteQuery = "DELETE FROM technicians WHERE id = '$id'";
    if (mysqli_query($conn, $deleteQuery)) {
        $_SESSION['keep_modal_open'] = true;
        echo "<script>window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
        exit();
    }
}


$fetchQuery = "SELECT id, Name FROM technicians ORDER BY Name ASC";
$techResult = mysqli_query($conn, $fetchQuery);
?>


    
    <div class="fab-wrapper">
        <div class="fab-options" id="fabOptions">
            <button class="fab-mini" id="addTechBtn" title="Add / Remove Technicians">
                <span class="material-symbols-outlined">group_add</span>
            </button>
            <button class="fab-mini" id="logoutBtn" title="Logout">
                <span class="material-symbols-outlined">logout</span>
            </button>
        </div>
        <button class="fab" id="mainFabBtn">
            <span class="material-symbols-outlined">settings</span>
        </button>
    </div>

    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" id="closeModalBtn">&times;</span>
            <h2>Manage Technicians</h2>
            <hr>
            
            <form action="" method="POST">
                <div class="form-group">
                    <label>Technician Full Name</label>
                    <input type="text" name="tech_name" placeholder="Last Name, First Name, M.I." required>
                </div>
                <button type="submit" name="addTechnician" class="save-btn">Add Technician</button>
            </form>

            <div class="tech-list-container">
                <table class="tech-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($techResult) > 0) {
                            while($row = mysqli_fetch_assoc($techResult)) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['Name']) . "</td>";
                                echo "<td style='text-align: center;'>
                                        <a href='?delete_id=" . $row['id'] . "' class='delete-link' onclick='return confirm(\"Are you sure you want to delete this technician?\")'>
                                            <span class='material-symbols-outlined' style='color: #e74c3c; font-size: 20px;'>delete</span>
                                        </a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='2' style='text-align:center; color:#999;'>No technicians registered.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['keep_modal_open'])): ?>
    <script>
        window.addEventListener('load', function() {
            const modal = document.getElementById("addUserModal");
            if (modal) {
                modal.style.display = "flex";
            }
        });
    </script>
    <?php unset($_SESSION['keep_modal_open']); ?>
    <?php endif; ?>

    <script src="addUser.js"></script>
