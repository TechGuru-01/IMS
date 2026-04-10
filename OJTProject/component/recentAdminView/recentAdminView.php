<div class="content-card" id="recentTransaction">
    <div class="box-title">
        <span><i class="fas fa-users-cog"></i> Recent System Actions (Admin View)</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>User</th> 
                    <th>Action</th>
                    <th>Item & Description</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($admin_history_logs && $admin_history_logs->num_rows > 0): ?>
                    <?php while($h = $admin_history_logs->fetch_assoc()): ?>
                    <tr>
                        <td class="user-cell">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div class="pfp-wrapper">
                                    <?php if (!empty($h['profile_pic'])): ?>
                                        <img src="../../src/profiles/<?php echo htmlspecialchars($h['profile_pic']); ?>" 
                                             alt="Profile" 
                                             style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd; display: block;">
                                    <?php else: ?>
                                        <i class="fas fa-user-circle" style="font-size: 35px; color: #ccc;"></i>
                                    <?php endif; ?>
                                </div>
                                
                                <span style="font-weight: 600; color: var(--primary-blue-solid);">
                                    <?php echo htmlspecialchars($h['name']); ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <?php 
                                if ($h['quantity_in'] > 0) echo '<span class="badge badge-green">STOCK IN</span>';
                                elseif ($h['quantity_out'] > 0) echo '<span class="badge badge-admin">STOCK OUT</span>';
                                else echo '<span class="badge badge-blue">LOGGED</span>';
                            ?>
                        </td>
                        <td>
                            <div class="item-name" style="font-weight: 600; color: var(--text-dark);">
                                <?php echo htmlspecialchars($h['item']); ?>
                            </div>
                            <div class="item-desc" style="font-size: 0.75rem; color: #666;">
                                <?php echo htmlspecialchars($h['description']); ?>
                            </div>
                        </td>
                        <td class="date-cell" style="white-space: nowrap; font-family: monospace;">
                            <i class="far fa-calendar-alt"></i>
                            <?php echo date('M d, Y | h:i A', strtotime($h['date'])); ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center; padding: 20px;">No global actions found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>