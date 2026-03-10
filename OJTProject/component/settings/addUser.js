document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById("addUserModal");
    const mainFabBtn = document.getElementById("mainFabBtn");
    const fabOptions = document.getElementById('fabOptions');
    const closeBtn = document.getElementById("closeModalBtn");
    
    // Mini Buttons
    const addTechBtn = document.getElementById("addTechBtn");
    const logoutBtn = document.getElementById("logoutBtn");

    // 1. Toggle FAB Menu
    mainFabBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        fabOptions.classList.toggle('show');
        this.classList.toggle('active');
    });

    // 2. Open Modal (Galing sa Mini Button)
    addTechBtn.addEventListener('click', function() {
        modal.style.display = "flex"; 
        fabOptions.classList.remove('show');
        mainFabBtn.classList.remove('active');
    });

    // 3. Logout Button
    logoutBtn.addEventListener('click', function() {
        window.location.href = './logout.php'; // I-adjust ang path kung kailangan
    });

    // 4. Close Modal via 'X'
    closeBtn.onclick = function() {
        modal.style.display = "none";
    }

    // 5. Global Click Handler
    window.addEventListener('click', function(event) {
        // Isara modal pag click sa labas ng content box
        if (event.target == modal) {
            modal.style.display = "none";
        }
        
        // Isara FAB menu pag click sa labas ng wrapper
        if (!mainFabBtn.contains(event.target) && !fabOptions.contains(event.target)) {
            fabOptions.classList.remove('show');
            mainFabBtn.classList.remove('active');
        }
    });
});