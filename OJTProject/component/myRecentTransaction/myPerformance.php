<div class="content-card ">
                <div class="box-title">
                    <span><i class="fas fa-chart-bar"></i> My Performance Summary</span>
                </div>
                <div class="card-inner" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="stat-box-staff">
                        <h1 style="color: var(--primary-blue-solid); font-size: 2.5rem;"><?php echo number_format($issued_count); ?></h1>
                        <p>Total Items Issued</p>
                    </div>
                    <div class="stat-box-staff">
                        <h1 style="color: var(--excel-green); font-size: 2.5rem;"><?php echo number_format($trans_count); ?></h1>
                        <p>Total Transactions</p>
                    </div>
                </div>
                <div style="padding: 0 20px 20px 20px; font-size: 0.8rem; color: var(--text-muted);">
                    <i class="fas fa-info-circle"></i> Statistics reflect activity for the current month.
                </div>
            </div>