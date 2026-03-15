function openPRModal() {
  const selected = document.querySelectorAll(
    'input[name="acknowledge_ids[]"]:checked',
  );
  const idContainer = document.getElementById("hidden_id_container");
  const dynamicContainer = document.getElementById("dynamic_items_container");

  if (selected.length === 0) {
    alert("Pumili muna ng items!");
    return;
  }

  idContainer.innerHTML = "";
  dynamicContainer.innerHTML = "";
  let hasUrgent = false;

  selected.forEach((cb, index) => {
    const row = cb.closest("tr");
    const itemName = row.cells[1].innerText.replace("OUT OF STOCK", "").trim();
    const itemIdx = index + 1;

    if (row.innerText.includes("OUT OF STOCK")) hasUrgent = true;

    idContainer.innerHTML += `<input type="hidden" name="acknowledge_ids[]" value="${cb.value}">`;
    dynamicContainer.insertAdjacentHTML(
      "beforeend",
      `
            <div style="display:flex; gap:10px; margin-bottom:8px;">
                <input type="hidden" name="item_nums[]" value="${itemIdx}">
                <span style="font-weight:bold;">${itemIdx}.</span>
                <input type="text" name="material_names[]" value="${itemName}" class="pr-input-style">
            </div>
        `,
    );
  });

  document.getElementById("admin_suffix").value = hasUrgent ? "URGENT" : "";
  document.getElementById("prModal").style.display = "flex";
}

function calculateTotal() {
  const price = parseFloat(document.getElementById("pr_price").value) || 0;
  const qty = parseFloat(document.getElementById("pr_qty").value) || 0;
  const selectedOption =
    document.getElementById("currency_type").options[
      document.getElementById("currency_type").selectedIndex
    ];
  const rate = parseFloat(selectedOption.getAttribute("data-rate")) || 1;

  // Compute converted total
  const totalInPHP = price * qty * rate;
  document.getElementById("pr_total").value = totalInPHP.toFixed(2);
  document.getElementById("currency_label").innerText = "PHP (Converted)";
}

function prepareSubmission() {
  const base = document.getElementById("gen_ref").value;
  const suff = document.getElementById("admin_suffix").value.trim();
  document.getElementById("final_ref").value = suff ? `${base}-${suff}` : base;

  const total = parseFloat(document.getElementById("pr_total").value) || 0;
  if (total <= 0) {
    alert("Pakilagay ang price at quantity!");
    return false;
  }
  return confirm("Save this report?");
}

function closePRModal() {
  document.getElementById("prModal").style.display = "none";
}
