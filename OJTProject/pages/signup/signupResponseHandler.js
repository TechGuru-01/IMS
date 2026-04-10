document.addEventListener('DOMContentLoaded', function() {
    if (window.phpStatus && window.phpStatus !== "") {
        if (typeof Swal === 'undefined') {
            alert("Warning: SweetAlert2 library not found! Status: " + window.phpStatus);
        } else {
            let titleText = "Oops...";
            if (window.phpStatus === "success") titleText = "Success!";
            else if (window.phpStatus === "warning") titleText = "Wait!";

            Swal.fire({
                icon: window.phpStatus,
                title: titleText,
                text: window.phpMsg,
                showConfirmButton: false,
                timer: 2500,
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed && window.phpStatus === 'success') {
                    window.location.href = '../../index.php'; 
                }
            });
        }
    }
   
    const signupForm = document.getElementById('signupForm');
    if (signupForm) {
        signupForm.addEventListener('submit', function(e) {
            const pass = document.getElementsByName('password')[0].value;
            const confirm = document.getElementsByName('confirm_password')[0].value;

            if (pass !== confirm) {
                e.preventDefault();
                Swal.fire({ icon: 'error', title: 'Oops...', text: 'Passwords do not match!' });
                return;
            }
            if (pass.length < 8) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Wait!', text: 'Minimum 8 characters required.' });
            } else if (!/[A-Z]/.test(pass)) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Wait!', text: 'Need at least one uppercase letter (A-Z).' });
            } else if (!/[a-z]/.test(pass)) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Wait!', text: 'Need at least one lowercase letter (a-z).' });
            } else if (!/\d/.test(pass)) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Wait!', text: 'Need at least one number (0-9).' });
            } else if (!/[$#@!?-_]/.test(pass)) {
                e.preventDefault();
                Swal.fire({ icon: 'warning', title: 'Wait!', text: 'Need at least one special character ($#@!?).' });
            }
        });
    }
});