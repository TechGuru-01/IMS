<div class="modal" id="modal">
    <div class="modal-content">
        
        <button id="closeBtn" style="float:right; background:red; color:white; border:none; padding:5px 10px; cursor:pointer;">X</button>
        <h2>Manage Inventory</h2>
        
       
        <form id="addItemForm" method="POST">
            <div class="form-group">
                <input type="text" name="category" placeholder="Category..." class="pop-input" required />
                <input type="text" name="item" placeholder="Item Name..." class="pop-input" required />
                <input type="text" name="description" placeholder="Description..." class="pop-input" required />
                <input type="number" name="cabinet" placeholder="Cabinet..." class="pop-input" required />
                <input type="number" name="quantity" placeholder="QTY..." class="pop-input" required />
                <input type="number" name="min_quantity" placeholder="Min. QTY..." class="pop-input" required />
                <input type="number" step="0.01" name="price" placeholder="Price..." class="pop-input" required />
                <button type="submit" name="addItem" class="add-btn">Add</button>
            </div>
        </form>
        

        <hr style="margin: 20px 0;">

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 10px;">
            <button type="submit" form="bulkDeleteForm" name="bulkDelete" onclick="return confirm('Delete selected?')">Delete</button>
            <div id="modal-actions"></div>
        </div>
        
        <form id="bulkDeleteForm" method="POST">
            <div id="modal-table-container"></div>
        </form>
    </div>
</div>