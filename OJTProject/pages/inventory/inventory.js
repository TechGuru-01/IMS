document.addEventListener("DOMContentLoaded", () => {
  const openBtn = document.getElementById("openBtn");
  const openBtnEmpty = document.getElementById("openBtnEmpty");
  const closeBtn = document.getElementById("closeBtn");
  const modal = document.getElementById("modal");
  const tableContainer = document.getElementById("modal-table-container");
  const actionContainer = document.getElementById("modal-actions");
  const mainTable = document.getElementById("inventoryTable");

  const triggerModal = () => {
    if (!modal) return;
    modal.classList.add("show");

    if (!mainTable) {
      tableContainer.innerHTML =
        "<p style='padding:20px; text-align:center;'>No data to edit.</p>";
      return;
    }

    tableContainer.innerHTML = "";
    actionContainer.innerHTML = "";

    const tableClone = mainTable.cloneNode(true);
    tableClone.id = "editorTable";

    const rawHeaders = Array.from(mainTable.querySelectorAll("thead th")).map(
      (th) => th.getAttribute("data-column") || "",
    );

    // --- SELECT ALL LOGIC ---
    const theadRow = tableClone.querySelector("thead tr");
    const selectTh = document.createElement("th");
    selectTh.innerHTML = `<input type="checkbox" id="selectAllRows" style="cursor: pointer;">`;
    theadRow.insertBefore(selectTh, theadRow.firstChild);

    const selectAllCheckbox = selectTh.querySelector("#selectAllRows");
    selectAllCheckbox.onclick = (e) => {
      const isChecked = e.target.checked;
      tableClone
        .querySelectorAll(".row-select")
        .forEach((cb) => (cb.checked = isChecked));
    };

    // --- ROW EDITING LOGIC ---
    tableClone.querySelectorAll("tbody tr").forEach((row) => {
      const id = row.getAttribute("data-id");
      const selectTd = document.createElement("td");
      selectTd.innerHTML = `<input type="checkbox" name="selectedItems[]" value="${id}" class="row-select">`;
      row.insertBefore(selectTd, row.firstChild);

      row.querySelectorAll("td").forEach((td, i) => {
        const h = rawHeaders[i - 1]; 

        const readOnlyCols = [
          "select", "id", "total_value", "beginning_inventory",
          "received_qty", "is_acknowledged", "item_uuid",
        ];

        if (h && !readOnlyCols.includes(h)) {
          td.setAttribute("contenteditable", "true");
          td.style.backgroundColor = "#fffdf0";
          if (h === "price")
            td.textContent = td.textContent.replace(/[₱,]/g, "");
        } else {
          td.setAttribute("contenteditable", "false");
          td.style.backgroundColor = "#f0f0f0";
        }
      });
    });

    // --- SYNC CHANGES BUTTON (SWEETALERT) ---
    const saveBtn = document.createElement("button");
    saveBtn.textContent = "Sync Changes";
    saveBtn.className = "opnbtn";

    saveBtn.onclick = async (e) => {
      e.preventDefault();

      const confirmSync = await Swal.fire({
        title: 'Sync Records?',
        text: "Apply changes to the database?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1beb10',
        cancelButtonColor: '#eb0e0e',
        confirmButtonText: 'Yes, Sync'
      });

      if (!confirmSync.isConfirmed) return;

      const updateData = Array.from(
        tableClone.querySelectorAll("tbody tr"),
      ).map((row) => {
        const obj = { id: row.getAttribute("data-id") };
        row.querySelectorAll("td").forEach((td, i) => {
          const h = rawHeaders[i - 1];
          if (h && !["select", "id", "total_value", "beginning_inventory", "received_qty", "is_acknowledged", "item_uuid"].includes(h)) {
            let val = td.textContent.trim();
            if (h === "price") val = val.replace(/[₱,]/g, "");
            obj[h] = val;
          }
        });
        return obj;
      });

      Swal.fire({ title: 'Syncing...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() } });

      try {
        const response = await fetch("inventoryFunction.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ updateData }),
        });
        if (response.ok) {
          await Swal.fire({ icon: 'success', title: 'Synced!', timer: 1000, showConfirmButton: false });
          const currentUrl = new URL(window.location.href);
          currentUrl.searchParams.set("keepOpen", "true");
          window.location.href = currentUrl.toString();
        } else {
          Swal.fire('Error', 'Sync failed.', 'error');
        }
      } catch (err) {
        Swal.fire('Error', 'System error.', 'error');
      }
    };

    actionContainer.appendChild(saveBtn);
    tableContainer.appendChild(tableClone);

    // --- QR GENERATION VALIDATION (SWEETALERT) ---
    const qrBtnContainer = document.querySelector(".qr-btn-container");
    if (qrBtnContainer) {
      const qrLink = qrBtnContainer.closest("a"); 
      if (qrLink) {
        qrLink.onclick = (e) => {
          e.preventDefault();
          const selectedCheckboxes = tableClone.querySelectorAll(".row-select:checked");
          const selectedIds = Array.from(selectedCheckboxes).map((cb) => cb.value);

          if (selectedIds.length === 0) {
            Swal.fire({
              title: 'No Selection',
              text: "Please select at least one item before generating a QR code.",
              icon: 'warning',
              confirmButtonColor: '#1beb10'
            });
          } else {
            const baseUrl = qrLink.getAttribute("href").split("?")[0];
            window.location.href = `${baseUrl}?ids=${selectedIds.join(",")}`;
          }
        };
      }
    }
  };

  // --- DELETE LOGIC (KEEPS MODAL OPEN) ---
  const bulkDeleteBtn = document.querySelector('button[name="bulkDelete"]');
  if (bulkDeleteBtn) {
    bulkDeleteBtn.onclick = async (e) => {
      e.preventDefault();
      
      
      const modalCheckboxes = document.querySelectorAll('#editorTable .row-select:checked');
      
      if (modalCheckboxes.length === 0) {
          Swal.fire('Empty', 'Select items inside the editor to delete.', 'warning');
          return;
      }

      const confirmDel = await Swal.fire({
        title: 'Delete Selected?',
        text: `You are deleting ${modalCheckboxes.length} item(s).`,
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#f70d0d',
        confirmButtonText: 'Yes, Delete'
      });

      if (confirmDel.isConfirmed) {
        Swal.fire({ title: 'Deleting...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() } });
        
        const form = document.getElementById('bulkDeleteForm');

        
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set("keepOpen", "true");
        form.action = currentUrl.toString();

        
        form.querySelectorAll('.temp-del-input').forEach(el => el.remove());

        
        modalCheckboxes.forEach(cb => {
            const hiddenId = document.createElement('input');
            hiddenId.type = 'hidden';
            hiddenId.name = 'selectedItems[]';
            hiddenId.value = cb.value;
            hiddenId.className = 'temp-del-input';
            form.appendChild(hiddenId);
        });

        
        const hKeepOpen = document.createElement('input');
        hKeepOpen.type = 'hidden';
        hKeepOpen.name = 'keepOpen';
        hKeepOpen.value = 'true';
        hKeepOpen.className = 'temp-del-input';
        form.appendChild(hKeepOpen);
        
        const sInput = document.createElement('input');
        sInput.type = 'hidden';
        sInput.name = 'bulkDelete';
        sInput.value = 'true';
        sInput.className = 'temp-del-input';
        form.appendChild(sInput);

        form.submit();
      }
    };
  }

  if (openBtn) openBtn.onclick = triggerModal;
  if (openBtnEmpty) openBtnEmpty.onclick = triggerModal;
  if (closeBtn) closeBtn.onclick = () => modal.classList.remove("show");

  const params = new URLSearchParams(window.location.search);
  if (params.has("keepOpen")) triggerModal();
});