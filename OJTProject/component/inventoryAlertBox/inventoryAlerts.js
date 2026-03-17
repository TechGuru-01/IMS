const masterCheck = document.getElementById("selectAllAlerts");
if (masterCheck) {
  masterCheck.addEventListener("change", function () {
    const checks = document.querySelectorAll(".alert-checkbox");
    checks.forEach((c) => (c.checked = this.checked));
  });
}
