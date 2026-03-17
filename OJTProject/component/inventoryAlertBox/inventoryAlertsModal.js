// 1. In-update ang parameters para tanggapin ang matDesc
function createItemRow(index, matName, matDesc = "", acknowledgeId = "") {
  return `
    <div class="item-row pr-items-grid" data-index="${index}" style="margin-bottom:8px; border-bottom:1px solid #f1f5f9; padding-bottom:8px;">
        <div class="row-number" style="font-size: 11px; font-weight: 800; color: #94a3b8; text-align: center;">${index + 1}</div>
        
        <input type="text" name="item_names[]" class="pr-input-style" value="${matName}" readonly style="background:#f8fafc; font-weight:600;">
        
        <input type="text" name="item_descs[]" class="pr-input-style" value="${matDesc}" placeholder="Input Description/Specs here..." required>
        
        <input list="maker_options" name="item_makers[]" class="pr-input-style" placeholder="Maker">
        <input type="number" name="item_qtys[]" class="pr-input-style item-qty text-center" value="0" min="1" oninput="calculateGrandTotal()">
        <input type="number" step="0.01" name="item_prices[]" class="pr-input-style item-price" value="0.00" min="0" oninput="calculateGrandTotal()">
        <div class="row-total-display text-right" style="font-size:12px; font-weight:700; color:#072d7a; background: #f8fafc; padding: 5px 8px; border-radius: 6px;">0.00</div>
        
        <div style="display:flex; justify-content:center;">
            <button type="button" onclick="removeItemRow(this)" style="background:#fee2e2; border:1px solid #fecaca; color:#ef4444; cursor:pointer; height:30px; width:30px; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                <span class="material-symbols-outlined" style="font-size:16px;">delete</span>
            </button>
        </div>
        ${acknowledgeId ? `<input type="hidden" name="acknowledge_ids[]" value="${acknowledgeId}">` : ""}
    </div>`;
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
    const qtyInput = row.querySelector(".item-qty");
    const priceInput = row.querySelector(".item-price");
    const displaySubtotal = row.querySelector(".row-total-display");

    if (qtyInput && priceInput) {
      const qty = parseFloat(qtyInput.value) || 0;
      const price = parseFloat(priceInput.value) || 0;
      const rowSubtotal = qty * price * rate;

      displaySubtotal.innerText = `${currencySign} ${rowSubtotal.toLocaleString(
        undefined,
        { minimumFractionDigits: 2, maximumFractionDigits: 2 },
      )}`;

      grandTotal += rowSubtotal;
    }
  });

  const totalInput = document.getElementById("pr_total");
  const label = document.getElementById("currency_label");

  if (totalInput) totalInput.value = grandTotal.toFixed(2);
  if (label) label.innerText = currencySign;
}

// 2. In-update para kuhanin ang .desc-cell text
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

    // KINUKUHA ANG DESCRIPTION DITO
    const itemDesc = row.querySelector(".desc-cell")
      ? row.querySelector(".desc-cell").innerText.trim()
      : "";

    if (row.innerText.includes("OUT OF STOCK")) hasUrgent = true;

    // IPINAPASA ANG itemDesc SA PANGATLONG ARGUMENT
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
      createItemRow(index, matName, ""), // Blank muna desc sa reuse mode kung walang source
    );
  });

  document.getElementById("prModal").style.display = "flex";
  calculateGrandTotal();
}

function prepareSubmission() {
  if (document.querySelectorAll(".item-row").length === 0) {
    alert("Hindi pwedeng walang item ang PR!");
    return false;
  }

  const genRef = document.getElementById("gen_ref").value;
  const suffix = document.getElementById("admin_suffix").value.trim();
  document.getElementById("final_ref").value = suffix
    ? `${genRef}-${suffix}`
    : genRef;

  // MAG-RELOAD PAGKATAPOS NG 2 SECONDS
  // Para pagbukas mo ulit ng modal, bago na ang Control Number
  setTimeout(() => {
    window.location.reload();
  }, 2000);

  return true; // Magpapatuloy ang form submission (Download Excel/PDF)
}s
function closePRModal() {
  document.getElementById("prModal").style.display = "none";
  document.getElementById("exportForm").reset();
}
