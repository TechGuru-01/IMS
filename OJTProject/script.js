document.addEventListener('DOMContentLoaded', function() {
    if (window.authStatus && window.authStatus !== "") {
        let titleText = "Oops...";
        if (window.authStatus === "success") titleText = "Success!";
        else if (window.authStatus === "warning") titleText = "Wait!";

        Swal.fire({
            icon: window.authStatus,
            title: titleText,
            text: window.authMsg,
            showConfirmButton: false, 
            timer: 2500,              
            timerProgressBar: true,   
            allowOutsideClick: false
        }).then(() => {
            if (window.authStatus === 'success') {
                if (window.location.href.includes('signup')) {
                    window.location.href = '../../index.php';
                } else {
                    window.location.href = './pages/dashBoard/dashBoard.php';
                }
            }
        });
    }

    const loginBtn = document.querySelector('.login-btn');
    if (loginBtn) {
        loginBtn.addEventListener('click', function(e) {
            const passFields = document.getElementsByName('password');
            const confirmFields = document.getElementsByName('confirm_password');

            if (confirmFields.length > 0) {
                const pass = passFields[0].value;
                const confirm = confirmFields[0].value;

                if (pass !== confirm) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops!',
                        text: 'Passwords do not match.',
                        confirmButtonColor: '#d32f2f'
                    });
                }
            }
        });
    }
});