<div class="box-content" id="box-dollar">
    <div class="inner-box" id="inner-dollar">
        <div class="box-header">
            <span>Dollar Rate ($1 = ₱<?= number_format($d_rate, 2) ?>)</span>
            <form class="set-rate" method="POST">
                <input type="number" step="0.01" name="new_dollar_rate" placeholder="rate" required>
                <button type="submit" name="update_dollar">Set</button>
            </form> 
        </div>
       <h1 class="count-up" data-target="<?= $dollarTotal ?>">$0.00</h1>

    </div>
</div>

<div class="box-content" id="box-yen">
    <div class="inner-box" id="inner-yen">
        <div class="box-header">
            <span>Yen Rate (¥1 = ₱<?= number_format($y_rate, 2) ?>)</span>
            <form class="set-rate" method="POST">
                <input type="number" step="0.01" name="new_yen_rate" placeholder="rate" required>
                <button type="submit" name="update_yen">Set</button>
            </form> 
        </div>
       <h1 class="count-up" data-target="<?= $yenTotal ?>">¥0.00</h1>

    </div>
</div>