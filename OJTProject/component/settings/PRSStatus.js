document.addEventListener("DOMContentLoaded", function () {
  const listBtn = document.getElementById("listView");
  const gridBtn = document.getElementById("gridView");
  const container = document.getElementById("mainContainer");

  // --- 1. CHECK SAVED VIEW ON LOAD ---
  // Tinitingnan kung ano ang huling view na ginamit (default is list-mode)
  const savedView = localStorage.getItem("prs_view_preference") || "list-mode";

  if (savedView === "grid-mode") {
    applyGridView();
  } else {
    applyListView();
  }

  // --- 2. GRID BUTTON CLICK ---
  gridBtn.addEventListener("click", function (e) {
    e.preventDefault();
    applyGridView();
  });

  // --- 3. LIST BUTTON CLICK ---
  listBtn.addEventListener("click", function (e) {
    e.preventDefault();
    applyListView();
  });

  // Helper function para sa Grid
  function applyGridView() {
    container.classList.add("grid-mode");
    container.classList.remove("list-mode");
    gridBtn.classList.add("active");
    listBtn.classList.remove("active");
    localStorage.setItem("prs_view_preference", "grid-mode"); // Save preference
  }

  // Helper function para sa List
  function applyListView() {
    container.classList.add("list-mode");
    container.classList.remove("grid-mode");
    listBtn.classList.add("active");
    gridBtn.classList.remove("active");
    localStorage.setItem("prs_view_preference", "list-mode"); // Save preference
  }
});
