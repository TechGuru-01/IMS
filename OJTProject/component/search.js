window.filterTable = function (type) {
  let inputId, containerId;

  // Determine kung anong search bar ang ginagamit
  if (type === "modal") {
    inputId = "modalSearch";
    containerId = "editorTableContainer"; // Dapat naka-wrap ang table mo sa div na 'to
  } else {
    inputId = "inventorySearch";
    containerId = "inventoryTableContainer"; // Dito i-re-render ang inventory table
  }

  const input = document.getElementById(inputId);
  if (!input) return;

  const filter = input.value;

  // Kunin ang current Month at Year mula sa hidden inputs o URL params
  const urlParams = new URLSearchParams(window.location.search);
  const month = urlParams.get("month") || new Date().getMonth() + 1;
  const year = urlParams.get("year") || new Date().getFullYear();

  // AJAX Fetch: Tatawagin ang PHP file para sa bagong records
  // Gagamit tayo ng debounce (optional) o diretso na
  fetch(
    `fetch_inventory.php?search=${encodeURIComponent(filter)}&month=${month}&year=${year}`,
  )
    .then((response) => response.text())
    .then((html) => {
      document.getElementById(containerId).innerHTML = html;
    })
    .catch((err) => console.warn("Search Error:", err));
};
