<div class="content-card" id="userManagement">
    <div class="box-title">
        <span><i class="fas fa-users-cog"></i> User Management</span>
    </div>
    
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Account</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $users_result->fetch_assoc()): ?>
                    <tr id="user-row-<?php echo $row['id']; ?>">
                        <td>
                            <strong><?php echo htmlspecialchars($row['username']); ?></strong><br>
                            <small style="color: var(--text-muted);">
                                <?php echo htmlspecialchars($row['full_name']); ?>
                            </small>
                        </td>
                        <td>
                            <span class="badge <?php echo (strtoupper($row['position']) == 'SUPER ADMIN' || strtoupper($row['position']) == 'ADMIN') ? 'badge-admin' : 'badge-blue'; ?>">
                                <?php echo htmlspecialchars($row['position']); ?>
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <button class="btn-table-action" onclick="openAdminReset(<?php echo $row['id']; ?>, '<?php echo $row['username']; ?>')" style="display: flex; align-items: center; gap: 5px;">
                                    <i class="fas fa-user-lock"></i> 
                                    <span style="font-size: 12px;">Change Password</span>
                                </button>

                                <button class="btn-table-action text-danger" title="Delete User" onclick="deleteUser(<?php echo $row['id']; ?>, '<?php echo $row['username']; ?>')" style="border-color: #ff4d4d; color: #ff4d4d;">
                                    <i class="fas fa-user-minus"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?> 
            </tbody>
        </table>
    </div>
</div>