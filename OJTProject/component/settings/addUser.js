document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById("addUserModal");
    const mainFabBtn = document.getElementById("mainFabBtn");
    const fabOptions = document.getElementById('fabOptions');
    const closeBtn = document.getElementById("closeModalBtn");
    const addTechBtn = document.getElementById("addTechBtn");
    const logoutBtn = document.getElementById("logoutBtn");

    mainFabBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        fabOptions.classList.toggle('show');
        this.classList.toggle('active');
    });

    addTechBtn.addEventListener('click', function() {
        modal.style.display = "flex"; 
        fabOptions.classList.remove('show');
        mainFabBtn.classList.remove('active');
    });

    logoutBtn.addEventListener('click', function() {
        window.location.href = './logout.php';
    });

    closeBtn.onclick = function() {
        modal.style.display = "none";
    }

    window.addEventListener('click', function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
        
        if (!mainFabBtn.contains(event.target) && !fabOptions.contains(event.target)) {
            fabOptions.classList.remove('show');
            mainFabBtn.classList.remove('active');
        }
    });
});