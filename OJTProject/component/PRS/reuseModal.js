let isReuseMode = false;

// 1. TOGGLE SELECTION (Dashboard Table)
function toggleReuseMode() {
  isReuseMode = !isReuseMode;
  const btn = document.getElementById("reuseBtn");
  const text = document.getElementById("reuseText");
  const rows = document.querySelectorAll("tr[data-ref]");

  if (isReuseMode) {
    if (btn) btn.style.background = "#dc3545";
    if (text) text.innerText = "Cancel Reuse";
    rows.forEach((row) => {
      row.style.cursor = "pointer";
      row.style.outline = "2px dashed #dc3545";
      row.onclick = function () {
        const refToCopy = this.getAttribute("data-ref");
        if (refToCopy) fetchAndPopulateModal(refToCopy);
      };
    });
  } else {
    if (btn) btn.style.background = "#28a745";
    if (text) text.innerText = "Reuse";
    rows.forEach((row) => {
      row.style.cursor = "default";
      row.style.outline = "none";
      row.onclick = null;
    });
  }
}

// 2. CREATE ROW HTML (Single Version - Sakto sa Layout)
function createItemRow(name, maker, qty, price) {
  const currencySign = document.getElementById("currency_type")?.value || "PHP";
  return `
        <div class="pr-item-row item-row" style="display:grid; grid-template-columns: 2.5fr 1.8fr 0.8fr 1.5fr 1.5fr 45px; gap:12px; margin-bottom:12px; align-items:center;">
            <input type="text" name="item_names[]" value="${name}" class="pr-input-style" readonly>
            <input list="maker_options" name="item_makers[]" value="${maker || ""}" class="pr-input-style" placeholder="Maker">
            <input type="number" name="item_qtys[]" value="${qty}" class="pr-input-style item-qty" style="text-align:center;" oninput="calculateGrandTotal()">
            <div style="position:relative; display:flex; align-items:center;">
                <span style="position:absolute; left:10px; font-weight:600; color:#64748b; font-size:12px;">${currencySign}</span>
                <input type="number" step="0.01" name="item_prices[]" value="${price}" class="pr-input-style item-price" style="padding-left:40px; text-align:right;" oninput="calculateGrandTotal()">
            </div>
            <div class="row-total-display" style="background:#f9fafb; padding:10px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; font-weight:700; color:#1e293b; min-height:38px; display:flex; align-items:center; justify-content:flex-end;">
                ${currencySign} 0.00
            </div>
            <button type="button" class="delete-btn" onclick="removePRItem(this)" style="background:#fee2e2; color:#ef4444; border:1px solid #fecaca; border-radius:6px; height:38px; cursor:pointer; display:flex; justify-content:center; align-items:center;">
                <span class="material-symbols-outlined" style="font-size:18px;">delete</span>
            </button>
        </div>
    `;
}

// 3. MAIN CALCULATION LOGIC
function calculateGrandTotal() {
  let grandTotal = 0;
  const currencySelect = document.getElementById("currency_type");
  if (!currencySelect) return;

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

      if (displaySubtotal) {
        displaySubtotal.innerText = `${currencySign} ${rowSubtotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
      }
      grandTotal += rowSubtotal;
    }
  });

  const totalInput = document.getElementById("pr_total");
  if (totalInput) totalInput.value = grandTotal.toFixed(2);
}

// 4. FETCH AND POPULATE (Fixed: No auto-show)
async function fetchAndPopulateModal(refToCopy) {
  try {
    const response = await fetch(
      `./getPRSDetail.php?ref=${encodeURIComponent(refToCopy)}`,
    );
    const data = await response.json();

    if (data.success) {
      const header = data.header;

      // Set Ref Number
      const genRefField = document.getElementById("gen_ref");
      if (!genRefField.value || genRefField.value.includes("ERROR")) {
        genRefField.value = "JIG-" + new Date().getTime();
      }
      document.getElementById("final_ref").value = genRefField.value;

      // Header mapping
      document.getElementById("modal_pr_date").value = new Date()
        .toISOString()
        .split("T")[0];
      document.getElementById("modal_company").value = header.company || "";
      document.getElementById("modal_remarks").value = header.remarks || "";
      if (document.getElementById("currency_type")) {
        document.getElementById("currency_type").value =
          header.currency || "PHP";
      }

      // Items population
      const listBody = document.getElementById("items_list_body");
      listBody.innerHTML = "";
      if (data.items && data.items.length > 0) {
        data.items.forEach((item) => {
          listBody.insertAdjacentHTML(
            "beforeend",
            createItemRow(
              item.material_name,
              item.maker || "",
              item.quantity,
              item.unit_price,
            ),
          );
        });
      } else {
        listBody.innerHTML =
          '<p style="text-align:center; color:#94a3b8; padding:10px;">No items found.</p>';
      }

      // ONLY SHOW HERE
      document.getElementById("modal_title").innerText =
        "♻️ Reuse PR: " + refToCopy;
      document.getElementById("prModal").style.display = "flex";

      calculateGrandTotal();
      if (isReuseMode) toggleReuseMode();
    } else {
      alert("Error: " + data.message);
    }
  } catch (err) {
    console.error("Critical Error:", err);
  }
}

function removePRItem(btn) {
  const row = btn.closest(".item-row");
  if (confirm("Alisin ang item na ito?")) {
    row.remove();
    calculateGrandTotal();
  }
}

function closePRModal() {
  document.getElementById("prModal").style.display = "none";
}

function prepareSubmission() {
  const base = document.getElementById("gen_ref").value;
  const suffix = document.getElementById("admin_suffix").value.trim();
  document.getElementById("final_ref").value = suffix
    ? `${base}-${suffix}`
    : base;
  return true;
}
