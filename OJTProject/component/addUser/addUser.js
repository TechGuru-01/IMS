// Function para mag-switch ng tab
function switchTab(tab) {
    // Kinukuha natin ang current path/filename (test.php) para doon lang mag-redirect
    const currentPath = window.location.pathname;
    window.location.href = currentPath + "?tab=" + tab;
}

// Function para sa delete confirmation
function confirmDelete(id, tab) {
    if (confirm("Are you sure you want to delete this?")) {
        const currentPath = window.location.pathname;
        window.location.href = currentPath + "?delete_id=" + id + "&tab=" + tab;
    }
}