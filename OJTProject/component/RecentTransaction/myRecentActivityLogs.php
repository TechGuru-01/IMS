<div class="content-card">
    <div class="box-title">
        <span><i class="fas fa-history"></i> My Recent Activity Logs</span>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Transaction Details</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($logs_result->num_rows > 0): ?>
                    <?php while($h = $logs_result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if ($h['quantity_in'] > 0): ?>
                                    <span class="badge badge-green">STOCK-IN</span>
                                <?php elseif ($h['quantity_out'] > 0): ?>
                                    <span class="badge" style="background: #e74c3c; color: white; padding: 4px 8px; border-radius: 4px; font-size: 10px;">
                                        STOCK-OUT
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-blue">UPDATED</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($h['item']); ?></strong><br>
                                <small style="color:#666;">
                                    <?php echo htmlspecialchars($h['description']); ?>
                                </small>
                            </td>
                            <td style="white-space: nowrap;">
                                <i class="far fa-clock" style="font-size: 0.7rem; color: #999;"></i>
                                <?php echo date('M d, Y | h:i A', strtotime($h['date'])); ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align:center; padding: 30px; color: #999;">
                            No recent inventory activities recorded.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>