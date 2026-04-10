<script>
    const allChartData = <?php echo json_encode($allChartData); ?>;
    const currentView = "<?php echo $view; ?>";
</script>

<div class="box-content box-4" id="box-graph">
    <div class="content-container" id="graph-content" style="height: 100%;">
        
        <div class="graph-header" style="display: flex; justify-content: space-between; align-items: center; padding: 10px;">
            <h2 style="margin: 0; font-size: 1.2rem;">Usage per Item (<?= date('F', mktime(0, 0, 0, $m, 10)) ?> <?= $y ?>)</h2>
            <div class="chart-toggle">
                <a href="?view=weekly&month=<?= $m ?>&year=<?= $y ?>" class="<?= ($view === 'weekly') ? 'active' : '' ?>">Weekly</a>
                <a href="?view=monthly&month=<?= $m ?>&year=<?= $y ?>" class="<?= ($view === 'monthly') ? 'active' : '' ?>">Monthly</a>
            </div>
        </div>

        <div id="chartsWrapper" style="padding: 15px;">
            <?php if (empty($allChartData)): ?>
                <div class="no-data-msg" style="text-align: center; padding: 40px; width: 100%;">
                    <p style="color: gray;">No usage history found for <?= date('F Y', mktime(0, 0, 0, $m, 10)) ?>.</p>
                </div>
          
            <?php endif; ?>
        </div>
    </div>
</div>