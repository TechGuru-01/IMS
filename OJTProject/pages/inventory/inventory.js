const openBtn = document.getElementById("openBtn");
const closeBtn = document.getElementById("closeBtn");
const modal = document.getElementById("modal");
const tableContainer = document.getElementById("modal-table-container");
const actionContainer = document.getElementById("modal-actions");
const mainTable = document.getElementById("inventoryTable");

const triggerModal = () => {
  modal.classList.add("show");
  tableContainer.innerHTML = "";
  actionContainer.innerHTML = "";

  const tableClone = mainTable.cloneNode(true);
  tableClone.id = "editorTable";
  tableClone.style.width = "100%";
  tableClone.style.borderCollapse = "collapse";

  const theadRow = tableClone.querySelector("thead tr");
  const selectTh = document.createElement("th");
  selectTh.textContent = "Select";
  theadRow.insertBefore(selectTh, theadRow.firstChild);

  tableClone.querySelectorAll("tbody tr").forEach((row) => {
    const id = row.getAttribute("data-id");

    const selectTd = document.createElement("td");
    selectTd.innerHTML = `<input type="checkbox" name="selectedItems[]" value="${id}" class="row-select">`;
    row.insertBefore(selectTd, row.firstChild);

    const headers = Array.from(tableClone.querySelectorAll("thead th")).map(
      (th) => th.textContent.toLowerCase(),
    );
    row.querySelectorAll("td").forEach((td, i) => {
      const h = headers[i];
      if (!["select", "id", "total"].includes(h)) {
        td.setAttribute("contenteditable", "true");
        td.style.borderBottom = "1px dashed #ed0505";
        
        // PANG-IWAS SA 2.00 ISSUE: 
        // Tanggalin ang ₱ at Comma sa editor para hindi malito ang user at ang script
        if (h === "price") {
            td.textContent = td.textContent.replace(/[₱,]/g, "");
        }
      }
    });
  });

  const saveBtn = document.createElement("button");
  saveBtn.textContent = "Sync Changes";
  saveBtn.className = "opnbtn";
  saveBtn.style.backgroundColor = "#28a745";
  saveBtn.style.height = "32px";
  saveBtn.style.width = "auto";
  saveBtn.style.padding = "0 15px";
  saveBtn.style.fontSize = "14px";

  saveBtn.onclick = async (e) => {
    e.preventDefault();
    const rows = tableClone.querySelectorAll("tbody tr");
    const headers = Array.from(tableClone.querySelectorAll("thead th")).map(
      (th) => th.textContent.toLowerCase(),
    );

    const updateData = Array.from(rows).map((row) => {
      const obj = { id: row.getAttribute("data-id") };
      row.querySelectorAll("td").forEach((td, i) => {
        const h = headers[i];
        if (!["select", "id", "total"].includes(h)) {
          let value = td.textContent.trim();

          
          if (h === "price") {
            value = value.replace(/[₱,]/g, "");
          }

          obj[h] = value;
        }
      });
      return obj;
    });

    const res = await fetch("inventory.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ updateData }),
    });

    if (res.ok) window.location.href = "inventory.php?keepOpen=1";
  };

  actionContainer.appendChild(saveBtn);
  tableContainer.appendChild(tableClone);
};

openBtn.addEventListener("click", triggerModal);
closeBtn.onclick = () => {
  modal.classList.remove("show");
  const url = new URL(window.location);
  url.searchParams.delete("keepOpen");
  window.history.replaceState({}, "", url);
};

window.onload = () => {
  const params = new URLSearchParams(window.location.search);
  if (params.has("keepOpen")) triggerModal();
};