<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$m = $_SESSION['selected_month'] ?? (int)date('n');
$y = $_SESSION['selected_year'] ?? (int)date('Y');
$view = $_GET['view'] ?? 'weekly';


if ($view === 'weekly') {
    $whereClause = "WHERE YEARWEEK(date, 1) = YEARWEEK(CURDATE(), 1)";
} else {
    $whereClause = "WHERE MONTH(date) = $m AND YEAR(date) = $y";
};

$chartSql = "SELECT 
                item, 
                description, 
                DATE(date) as log_date, 
                DAYNAME(date) as day_name, 
                COUNT(*) as usage_count, 
                WEEKDAY(date) as day_index
             FROM history 
             $whereClause
             GROUP BY item, description, log_date, day_name, day_index 
             ORDER BY log_date ASC";

$chartResult = $conn->query($chartSql);
$allChartData = [];

if ($chartResult) {
    while ($row = $chartResult->fetch_assoc()) {
        $itemName = $row['item'];
        
        if (!isset($allChartData[$itemName])) {
            $allChartData[$itemName] = [
                'desc' => $row['description'] ?? 'No description available.',
                'stats' => ($view === 'weekly') ? array_fill(0, 7, 0) : []
            ];
        }

        if ($view === 'weekly') {
            $allChartData[$itemName]['stats'][$row['day_index']] = (int)$row['usage_count'];
        } else {
            $allChartData[$itemName]['stats'][$row['log_date']] = (int)$row['usage_count'];
        }
    }
}
?>