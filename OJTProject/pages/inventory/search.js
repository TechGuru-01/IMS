/**
 * SORTING FUNCTION
 */
function sortTable(n) {
  const table = document.getElementById("inventoryTable");
  // Siguraduhin na sa tbody tayo naghahanap ng rows
  const tbody = table.querySelector("tbody");
  const rows = Array.from(tbody.querySelectorAll("tr"));

  // Kunin ang header na kinlik
  const header = table.querySelectorAll("th")[n];
  // Tignan kung kasalukuyang naka-ascending
  const isAscending = header.classList.contains("th-sort-asc");
  const direction = isAscending ? -1 : 1;

  // I-reset ang lahat ng ibang headers (alisin ang arrows)
  table.querySelectorAll("th").forEach((th) => {
    th.classList.remove("th-sort-asc", "th-sort-desc");
    const icon = th.querySelector(".material-symbols-outlined");
    if (icon) icon.textContent = "unfold_more"; // Ibalik sa default icon
  });

  // Sort logic
  const sortedRows = rows.sort((a, b) => {
    // Gamitin ang .cells[n] para mas accurate ang pagkuha ng column
    const aColText = a.cells[n].textContent.trim();
    const bColText = b.cells[n].textContent.trim();

    const aValue = parseValue(aColText);
    const bValue = parseValue(bColText);

    if (aValue > bValue) return 1 * direction;
    if (aValue < bValue) return -1 * direction;
    return 0;
  });

  // I-update ang class para sa visual indicator at susunod na click
  header.classList.toggle("th-sort-asc", !isAscending);
  header.classList.toggle("th-sort-desc", isAscending);

  // I-update ang icon (Optional: para makita kung up o down)
  const currentIcon = header.querySelector(".material-symbols-outlined");
  if (currentIcon) {
    currentIcon.textContent = isAscending ? "expand_more" : "expand_less";
  }

  // I-append ulit ang sorted rows sa tbody
  tbody.append(...sortedRows);
}

// Helper function para malinis ang data (₱ at commas)
function parseValue(value) {
  const cleanValue = value.replace(/[₱,]/g, "");
  // I-check kung valid number, kung hindi, ituring na string
  return isNaN(cleanValue) || cleanValue === ""
    ? value.toLowerCase()
    : parseFloat(cleanValue);
}
