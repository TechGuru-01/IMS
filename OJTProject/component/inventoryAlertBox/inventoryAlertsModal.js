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

function removeItemRow(btn) {
  const row = btn.closest(".item-row");
  if (row && confirm("Alisin ang item na ito?")) {
    row.remove();
    if (document.querySelectorAll(".item-row").length === 0) {
      document.getElementById("items_list_body").innerHTML =
        `<p style="color:#94a3b8; font-size:12px; text-align:center; padding:20px;">No items selected.</p>`;
    }
    calculateGrandTotal();
  }
}

function calculateGrandTotal() {
    let grandTotal = 0;
    const currencySelect = document.getElementById("currency_type");
    const selectedOption = currencySelect.options[currencySelect.selectedIndex];
    const rate = parseFloat(selectedOption.getAttribute("data-rate")) || 1;
    const currencySign = currencySelect.value;

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

function closePRModal() {
    document.getElementById("prModal").style.display = "none";
}
function openPRModal() {
  const selected = document.querySelectorAll(
    'input[name="acknowledge_ids[]"]:checked',
  );
  const itemsListBody = document.getElementById("items_list_body");

  if (selected.length === 0) {
    alert("Pumili muna ng items sa inventory!");
    return;
  }

  itemsListBody.innerHTML = "";
  let hasUrgent = false;

  selected.forEach((cb, index) => {
    const row = cb.closest("tr");
    const itemName = row.cells[1].innerText.replace("OUT OF STOCK", "").trim();
    const itemDesc = row.querySelector(".desc-cell")
      ? row.querySelector(".desc-cell").innerText.trim()
      : "";

    if (row.innerText.includes("OUT OF STOCK")) hasUrgent = true;

    itemsListBody.insertAdjacentHTML(
      "beforeend",
      createItemRow(index, itemName, itemDesc, cb.value),
    );
  });

  document.getElementById("admin_suffix").value = hasUrgent ? "URGENT" : "";
  document.getElementById("form_mode").value = "create";
  document.getElementById("modal_title").innerText =
    "📝 Prepare Purchase Request";
  document.getElementById("prModal").style.display = "flex";

  calculateGrandTotal();
}

function openReuseModal(data) {
  const itemsListBody = document.getElementById("items_list_body");

  document.getElementById("form_mode").value = "reuse";
  document.getElementById("modal_title").innerText =
    "♻️ Reuse Purchase Request";

  document.getElementById("modal_company").value = data.company || "";
  if (document.getElementById("modal_maker"))
    document.getElementById("modal_maker").value = data.maker || "";
  document.getElementById("modal_uom").value = data.uom || "PC/s";
  document.getElementById("currency_type").value = data.currency || "PHP";

  itemsListBody.innerHTML = "";
  const materials = data.all_materials ? data.all_materials.split(", ") : [];

  materials.forEach((matName, index) => {
    itemsListBody.insertAdjacentHTML(
      "beforeend",
      createItemRow(index, matName, ""), 
    );
  });

  document.getElementById("prModal").style.display = "flex";
  calculateGrandTotal();
}

function prepareSubmission() {
    const modal = document.getElementById("prModal");
    // Selects ALL input, select, and textarea elements inside the form
    const allInputs = modal.querySelectorAll("input, select, textarea");
    const itemsList = document.querySelectorAll(".item-row");
    
    let isFormValid = true;
    let firstErrorElement = null;

    // 1. Check if there are any items at all
    if (itemsList.length === 0) {
        alert("Error: You must select at least one item from the inventory.");
        return false;
    }

    // 2. Loop through EVERY single input box in the entire modal
    allInputs.forEach(input => {
        // Skip hidden inputs (like form_mode or final_ref) as they are auto-filled
        if (input.type === "hidden") return;

        const value = input.value.trim();
        
        // Validation Rule: Field must not be empty, and numbers must be > 0
        if (value === "" || (input.type === "number" && parseFloat(value) <= 0)) {
            isFormValid = false;
            input.style.border = "2px solid #ef4444"; // Bright Red Border
            input.style.backgroundColor = "#fff1f2"; // Light Red Background
            
            if (!firstErrorElement) firstErrorElement = input;
        } else {
            // Reset style if it's filled
            input.style.border = "1px solid #cbd5e1";
            input.style.backgroundColor = "white";
        }
    });

    // 3. Final Check
    if (!isFormValid) {
        alert("Incomplete Form: Please fill out ALL fields, including Remarks and Item Details.");
        
        // Automatically scroll/focus on the first empty box found
        if (firstErrorElement) firstErrorElement.focus();
        
        return false; 
    }
    const genRef = document.getElementById("gen_ref").value;
    const suffix = document.getElementById("admin_suffix").value.trim();
    document.getElementById("final_ref").value = suffix ? `${genRef}-${suffix}` : genRef;
    const submitBtn = document.querySelector('button[name="bulk_resolve"]');
    if(submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerText = "Processing...";
    }


    setTimeout(() => {
        window.location.reload();
    }, 2500);

    return true; 
}
function closePRModal() {
  document.getElementById("prModal").style.display = "none";
  document.getElementById("exportForm").reset();
}
