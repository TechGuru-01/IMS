function filterTable() {
    const input = document.getElementById("inventorySearch");
    const filter = input.value.toLowerCase();
    const table = document.getElementById("inventoryTable");
    const tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        const row = tr[i];
        const rowText = row.textContent || row.innerText;

        if (rowText.toLowerCase().indexOf(filter) > -1) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    }
}