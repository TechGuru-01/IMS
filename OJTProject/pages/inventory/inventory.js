const openBtn = document.getElementById("openBtn");
const closeBtn = document.getElementById("closeBtn");
const modal = document.getElementById("modal");
const tableContainer = document.getElementById("modal-table-container");
const actionContainer = document.getElementById("modal-actions");
const mainTable = document.getElementById("inventoryTable");

const triggerModal = () => {
  if (!mainTable) return;

  modal.classList.add("show");
  tableContainer.innerHTML = "";
  actionContainer.innerHTML = "";

  const tableClone = mainTable.cloneNode(true);
  tableClone.id = "editorTable";

  const rawHeaders = Array.from(mainTable.querySelectorAll("thead th")).map(
    (th) => {
      return th.getAttribute("data-column") || "total_value";
    },
  );

  const theadRow = tableClone.querySelector("thead tr");
  const selectTh = document.createElement("th");
  selectTh.textContent = "Select";
  theadRow.insertBefore(selectTh, theadRow.firstChild);

  tableClone.querySelectorAll("tbody tr").forEach((row) => {
    const id = row.getAttribute("data-id");
    const selectTd = document.createElement("td");
    selectTd.innerHTML = `<input type="checkbox" name="selectedItems[]" value="${id}" class="row-select">`;
    row.insertBefore(selectTd, row.firstChild);

    row.querySelectorAll("td").forEach((td, i) => {
      const h = rawHeaders[i - 1];
      const readOnlyCols = [
        "select",
        "id",
        "total_value",
        "beginning_inventory",
        "received_qty",
      ];

      if (h && !readOnlyCols.includes(h)) {
        td.setAttribute("contenteditable", "true");
        td.style.backgroundColor = "#fffdf0";
        if (h === "price") td.textContent = td.textContent.replace(/[₱,]/g, "");
      } else {
        td.setAttribute("contenteditable", "false");
        td.style.backgroundColor = "#f0f0f0";
      }
    });
  });

  const saveBtn = document.createElement("button");
  saveBtn.textContent = "Sync Changes";
  saveBtn.className = "opnbtn";
  saveBtn.style.backgroundColor = "#28a745";
  saveBtn.style.height = "40px";

  saveBtn.onclick = async (e) => {
    e.preventDefault();
    const updateData = Array.from(tableClone.querySelectorAll("tbody tr")).map(
      (row) => {
        const obj = { id: row.getAttribute("data-id") };
        row.querySelectorAll("td").forEach((td, i) => {
          const h = rawHeaders[i - 1];
          const restricted = [
            "select",
            "id",
            "total_value",
            "beginning_inventory",
            "received_qty",
          ];

          if (h && !restricted.includes(h)) {
            let val = td.textContent.trim();
            if (h === "price") val = val.replace(/[₱,]/g, "");
            obj[h] = val;
          }
        });
        return obj;
      },
    );
    try {
      const response = await fetch("inventoryFunction.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ updateData }),
      });

      if (response.ok) {
        window.location.reload();
      } else {
        alert("Error syncing changes.");
      }
    } catch (err) {
      console.error("Sync failed:", err);
    }
  };

  actionContainer.appendChild(saveBtn);
  tableContainer.appendChild(tableClone);
};

// Modal Triggers
if (openBtn) openBtn.onclick = triggerModal;
if (closeBtn) closeBtn.onclick = () => modal.classList.remove("show");

// Auto-open modal if redirected back with keepOpen param
window.onload = () => {
  const params = new URLSearchParams(window.location.search);
  if (params.has("keepOpen")) triggerModal();
};
