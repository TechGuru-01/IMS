const launcherBtn = document.getElementById("launcherBtn");
const ubuntuMenu = document.getElementById("ubuntuMenu");

// Toggle menu
launcherBtn.addEventListener("click", (e) => {
  e.stopPropagation();
  ubuntuMenu.classList.toggle("active");
});

// Close menu pag nag-click sa labas
document.addEventListener("click", () => {
  ubuntuMenu.classList.remove("active");
});

// Function para sa Logout
function confirmLogout() {
  if (confirm("Are you sure you want to logout?")) {
    // Ginamit natin ang absolute path simula sa root folder ng project mo
    window.location.href = "/OJTProject/component/settings/logout.php";
  }
}

// Function para sa Add Technician
function openAddTechModal() {
  console.log("Add Technician clicked");
  window.location.href = "/OJTProject/component/settings/addUser.php";
}