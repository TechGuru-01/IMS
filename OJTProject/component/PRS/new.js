let itemCount = 1;

// Function para buksan ang modal
function openPrsModal() {
  const modal = document.getElementById("prsModal");
  if(modal) modal.style.display = "flex";
}

// Function para isara ang modal
function closePrsModal() {
  const modal = document.getElementById("prsModal");
  if(modal) modal.style.display = "none";
}

function addItem() {
  // Siguraduhin na "items-container" ang ID sa HTML mo
  const container = document.getElementById("items-container"); 
  if (!container) return;

  const newItem = document.createElement("div");
  
  // Ginamit natin ang class names para kumagat ang CSS mo
  newItem.className = "item-row-entry"; 
  newItem.style.marginTop = "10px"; // Konting space sa pagitan ng rows
  newItem.style.position = "relative";
  newItem.style.display = "block"; // Dahil vertical list ang gusto natin sa details

  newItem.innerHTML = `
        <button type="button" onclick="this.parentElement.remove()" 
            style="background: #ef4444; color: white; border: none; padding: 2px 8px; border-radius: 4px; 
            cursor: pointer; position: absolute; top: 10px; right: 10px; font-size: 11px; font-weight: bold;">
            REMOVE
        </button>

        <div class="item-num" style="margin-bottom: 10px;">${itemCount + 1}</div>

        <div class="pr-form-group" style="margin-bottom: 10px;">
            <label style="font-size: 11px; font-weight: 700; color: #64748b;">Material Name:</label>
            <input type="text" name="items[${itemCount}][material_name]" class="pr-input" required>
        </div>

        <div class="pr-form-group" style="margin-bottom: 10px;">
            <label style="font-size: 11px; font-weight: 700; color: #64748b;">Description:</label>
            <textarea name="items[${itemCount}][description]" class="pr-input" rows="1"></textarea>
        </div>

        <div class="pr-grid" style="grid-template-columns: 1fr 1fr; margin-bottom: 10px;">
            <div class="pr-form-group">
                <label style="font-size: 11px; font-weight: 700; color: #64748b;">Type:</label>
                <input type="text" name="items[${itemCount}][types_of_req]" class="pr-input">
            </div>
            <div class="pr-form-group">
                <label style="font-size: 11px; font-weight: 700; color: #64748b;">Maker:</label>
                <input type="text" name="items[${itemCount}][maker]" class="pr-input">
            </div>
        </div>

        <div class="pr-grid" style="grid-template-columns: 1fr 1fr 1.2fr;">
            <div class="pr-form-group">
                <label style="font-size: 11px; font-weight: 700; color: #64748b;">QTY:</label>
                <input type="number" name="items[${itemCount}][qty]" class="pr-input">
            </div>
            <div class="pr-form-group">
                <label style="font-size: 11px; font-weight: 700; color: #64748b;">UOM:</label>
                <input type="text" name="items[${itemCount}][uom]" class="pr-input">
            </div>
            <div class="pr-form-group">
                <label style="font-size: 11px; font-weight: 700; color: #64748b;">Price:</label>
                <input type="number" step="0.01" name="items[${itemCount}][unit_price]" class="pr-input">
            </div>
        </div>
    `;

  container.appendChild(newItem);
  itemCount++;
}