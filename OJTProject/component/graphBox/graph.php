<?php
$m = $_SESSION['selected_month'] ?? null;
$y = $_SESSION['selected_year'] ?? (int)date('Y');
$view = $_GET['view'] ?? 'weekly';

if ($m !== null) {
    $whereClause = "WHERE MONTH(date) = $m AND YEAR(date) = $y";
} else {
  
    $whereClause = "WHERE 1=0"; 
}

$chartSql = "SELECT 
                item, 
                DATE(date) as log_date, 
                DAYNAME(date) as day_name, 
                COUNT(*) as usage_count, 
                WEEKDAY(date) as day_index
             FROM history 
             $whereClause
             GROUP BY item, log_date, day_name, day_index 
             ORDER BY log_date ASC";

$chartResult = $conn->query($chartSql);
$chartData   = [];

if ($chartResult) {
    while ($row = $chartResult->fetch_assoc()) {
        $itemName = $row['item'];
        
        if ($view === 'weekly') {
            if (!isset($chartData[$itemName])) { 
                $chartData[$itemName] = array_fill(0, 7, 0); 
            }
            $chartData[$itemName][$row['day_index']] = (int)$row['usage_count'];
        } else {
            $chartData[$itemName][$row['log_date']] = (int)$row['usage_count'];
        }
    }
}
?>

<div class="box-content box-4" id="box-graph">
    <div class="content-container" id="graph-content" style="height: 100%;">
        
        <div class="graph-header">
            <h2 style="margin: 0;">Usage per Item</h2>
            <div class="chart-toggle">
                <a href="?view=weekly" class="<?= ($view === 'weekly') ? 'active' : '' ?>">Weekly</a>
                <a href="?view=monthly" class="<?= ($view === 'monthly') ? 'active' : '' ?>">Monthly</a>
            </div>
        </div>

        <div id="chartsWrapper">
            <?php if (empty($chartData)): ?>
                <div class="no-data-msg" style="text-align: center; padding: 20px;">
                    <p style="color: gray;">No history data found.</p>
                </div>
            <?php else: ?>
                <?php endif; ?>
        </div>

    </div>
</div>