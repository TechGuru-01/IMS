function createItemRow(index, name = "", desc = "", maker = "", uom = "", qty = "", price = "") {
    const currencySelect = document.getElementById("currency_type");
    const currencySign = currencySelect ? currencySelect.value : "PHP";
    const subtotal = (qty * price).toLocaleString(undefined, { minimumFractionDigits: 2 });

    return `
        <div class="item-row pr-items-grid" style="margin-bottom:8px; border-bottom:1px solid #f1f5f9; padding-bottom:8px;">
            <div class="row-number">${index + 1}</div>
            <input type="text" name="item_names[]" value="${name}" class="pr-input-style" required>
            <input type="text" name="item_descs[]" value="${desc}" class="pr-input-style">
            <input type="text" name="item_makers[]" value="${maker}" class="pr-input-style" placeholder="shopee...">
            
            <input type="text" name="item_uoms[]" value="${uom}" class="pr-input-style" style="text-align:center; border: 1px solid blue !important;" placeholder="pcs/pack..."> 
            
            <input type="number" name="item_qtys[]" value="${qty}" class="pr-input-style item-qty" placeholder="0" style="text-align:center;" oninput="calculateGrandTotal()">
            <input type="number" step="0.01" name="item_prices[]" value="${price}" class="pr-input-style item-price" placeholder="0.00" style="text-align:right;" oninput="calculateGrandTotal()">
            
           <div class="row-total-display" style="background:#f9fafb; padding:8px; border:1px solid #e2e8f0; border-radius:6px; font-size:12px; font-weight:700; color:#1e293b; text-align:right;">
                ${currencySign} ${subtotal}
            </div>
            
            <button type="button" onclick="removePRItem(this)" style="background:#fee2e2; border:none; border-radius:4px; height:30px;">
                <span class="material-symbols-outlined" style="font-size:18px; color:red;">delete</span>
            </button>
        </div>
    `;
}

function addNewRow() {
    const listBody = document.getElementById("items_list_body");
    if (!listBody) return;

    const currentIndex = listBody.querySelectorAll(".item-row").length;
    const newRowHTML = createItemRow(currentIndex, "", "", "","");
    
    listBody.insertAdjacentHTML("beforeend", newRowHTML);

    const lastRow = listBody.lastElementChild;
    const nameInput = lastRow.querySelector('.item-name-field');
    if (nameInput) nameInput.focus();

    calculateGrandTotal();
}


function removePRItem(btn) {
    const row = btn.closest(".item-row");
    if (confirm("Alisin ang item na ito?")) {
        row.remove();
        
        const rows = document.querySelectorAll(".item-row");
        rows.forEach((r, idx) => {
            const numDiv = r.querySelector(".row-number");
            if (numDiv) numDiv.innerText = idx + 1;
        });

        calculateGrandTotal();
    }
}


function calculateGrandTotal() {
    let grandTotal = 0;
    const currencySelect = document.getElementById("currency_type");
    if (!currencySelect) return;

    const selectedOption = currencySelect.options[currencySelect.selectedIndex];
    const rate = parseFloat(selectedOption.getAttribute("data-rate")) || 1;
    const currencySign = currencySelect.value;


    const currencyLabel = document.getElementById("currency_label");
    if (currencyLabel) currencyLabel.innerText = currencySign;

    const rows = document.querySelectorAll(".item-row");
    rows.forEach((row) => {
        const qty = parseFloat(row.querySelector(".item-qty").value) || 0;
        const price = parseFloat(row.querySelector(".item-price").value) || 0;
        const subtotal = qty * price;

        const displaySubtotal = row.querySelector(".row-total-display");
        if (displaySubtotal) {
            displaySubtotal.innerText = `${currencySign} ${subtotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        }
        grandTotal += (subtotal * rate);
    });

    const totalInput = document.getElementById("pr_total");
    if (totalInput) totalInput.value = grandTotal.toFixed(2);
}


function prepareSubmission() {
    const rows = document.querySelectorAll(".item-row");
    if (rows.length === 0) {
        alert("Please add at least one item.");
        return false;
    }


    const genRef = document.getElementById("gen_ref").value;
    const suffix = document.getElementById("admin_suffix").value.trim();
    document.getElementById("final_ref").value = suffix ? `${genRef}-${suffix}` : genRef;

    return true;
}

function closePRModal() {
    document.getElementById("prModal").style.display = "none";
}

function openNewPRModal() {
    document.getElementById("exportForm").reset();
    document.getElementById("items_list_body").innerHTML = "";
    addNewRow();
    
    document.getElementById("prModal").style.display = "flex";
}