
<div class="content-card" id=recentTransaction>
    <div class="box-title">
        <span><i class="fas fa-history"></i> My Recent Transactions</span>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Item & Description</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
                <tbody>
                        <?php if ($history_logs && $history_logs->num_rows > 0): ?>
                            <?php while($h = $history_logs->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php 
                                        if ($h['quantity_in'] > 0) {
                                            echo '<span class="badge badge-green">STOCK IN</span>';
                                        } elseif ($h['quantity_out'] > 0) {
                                            echo '<span class="badge badge-admin">STOCK OUT</span>';
                                        } else {
                                            echo '<span class="badge badge-blue">LOGGED</span>';
                                        }
                                    ?>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--text-dark);">
                                        <?php echo htmlspecialchars($h['item']); ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: #666;">
                                        <?php echo htmlspecialchars($h['description']); ?>
                                    </div>
                                </td>

                                <td style="white-space: nowrap; font-family: monospace; font-size: 0.85rem; color: #555;">
                                    <i class="far fa-calendar-alt" style="margin-right: 5px; color: var(--primary-blue-solid);"></i>
                                    <?php 
                                    
                                        echo date('M d, Y | h:i A', strtotime($h['date'])); 
                                    ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align:center; padding: 30px; color: #999;">
                            <i class="fas fa-inbox" style="display:block; font-size: 20px; margin-bottom: 5px;"></i>
                            No inventory history found under your name.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>