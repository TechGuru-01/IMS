window.filterTable = function (type) {
  let inputId, tableId;
  if (type === "modal") {
    inputId = "modalSearch";
    tableId = "editorTable";
  } else {
    inputId = "inventorySearch";
    tableId = "inventoryTable";
  }

  const input = document.getElementById(inputId);
  const table = document.getElementById(tableId);

  if (!input || !table) return;

  const filter = input.value.toLowerCase();
  const tr = table.getElementsByTagName("tr");

  for (let i = 1; i < tr.length; i++) {
    const row = tr[i];
    const text = row.textContent || row.innerText;

    if (text.toLowerCase().indexOf(filter) > -1) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  }
};
