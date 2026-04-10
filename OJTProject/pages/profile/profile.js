
document.addEventListener('submit', function (e) {
    const passInput = document.querySelector('input[name="password"], #new-pass-input, #edit-pass');
    const confirmInput = document.querySelector('input[name="confirm_password"], #confirm-pass');

    if (passInput && passInput.value.length > 0) {
        const pass = passInput.value;
        const confirm = confirmInput ? confirmInput.value : null;
        if (confirm !== null && pass !== confirm) {
            e.preventDefault();
            Swal.fire({ icon: 'error', title: 'Oops...', text: 'Passwords do not match!' });
            return;
        }
        if (pass.length < 8) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Wait!', text: 'Password should atleast be 8 characters long' });
        } else if (!/[A-Z]/.test(pass)) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Wait!', text: 'Password must include atleast one upper case letter' });
        } else if (!/[a-z]/.test(pass)) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Wait!', text: 'Password must include at least one lower case letter' });
        } else if (!/\d/.test(pass)) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Wait!', text: 'Password must include at least one number' });
        } else if (!/[$#@!?_-]/.test(pass)) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Wait!', text: 'Password must include at least one special character (e.g., $ # @ ! ?)' });
        }
    }
});

// --- UTILITY: Generic Fetch Handler ---
async function sendAction(url, formData, callback) {
    try {
        const response = await fetch(url, { method: 'POST', body: formData });
        const text = await response.text();
        try {
            const data = JSON.parse(text);
            if (data.status === 'success') {
                if (callback) { callback(data); } 
                else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message || 'Action completed successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            } else {
                Swal.fire('Error!', data.message || 'An error occurred.', 'error');
            }
        } catch (jsonErr) {
            console.error('Server Raw Response:', text);
            Swal.fire('System Error', 'Invalid server response. Check console.', 'warning');
        }
    } catch (err) {
        console.error('Fetch Error:', err);
        Swal.fire('Connection Error', 'Could not connect to the server.', 'error');
    }
}

// --- MODAL CONTROLS ---
const toggleModal = (modalId, action) => {
    const modal = document.getElementById(modalId);
    if (modal) {
        if (action === 'open') {
            modal.style.display = 'flex';
            modal.classList.add('active');
        } else {
            modal.style.display = 'none';
            modal.classList.remove('active');
        }
    }
};

function openModal(modalId) { toggleModal(modalId, 'open'); }

function closeModal() {
    const overlays = document.querySelectorAll('.modal-overlay');
    overlays.forEach(modal => {
        modal.style.display = 'none';
        modal.classList.remove('active');
    });
}

function openAdminReset(id, username) {
    const display = document.getElementById('target-user-display');
    const input = document.getElementById('target-id-input');
    if (display) display.innerText = username;
    if (input) input.value = id;
    toggleModal('adminViewModal', 'open');
}

function closeAdminViewModal() {
    toggleModal('adminViewModal', 'close');
    const passInput = document.getElementById('new-pass-input');
    if (passInput) passInput.value = "";
}

window.onclick = function(event) {
    if (event.target.classList.contains('modal-overlay')) { closeModal(); }
};

// ---  REUSABLE PASSWORD VISIBILITY ---
function togglePasswordVisibility(inputId, iconElement) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    if (iconElement) {
        iconElement.classList.toggle('fa-eye');
        iconElement.classList.toggle('fa-eye-slash');
    }
}

document.addEventListener('click', function(e) {
    if (e.target && e.target.id === 'eye-toggle') {
        togglePasswordVisibility('new-pass-input', e.target);
    }
});


function processAdminUpdate() {
    const id = document.getElementById('target-id-input').value;
    const passInput = document.getElementById('new-pass-input');
    const pass = passInput.value;

    if (!pass) return Swal.fire('Oops!', 'Please enter a new password!', 'warning');
    if (pass.length < 8) {
        Swal.fire({ icon: 'warning', title: 'Wait!', text: 'Password should at least be 8 characters long' });
    } else if (!/[A-Z]/.test(pass)) {
        Swal.fire({ icon: 'warning', title: 'Wait!', text: 'Password must include at least one upper case letter' });
    } else if (!/[a-z]/.test(pass)) {
        Swal.fire({ icon: 'warning', title: 'Wait!', text: 'Password must include at least one lower case letter' });
    } else if (!/\d/.test(pass)) {
        Swal.fire({ icon: 'warning', title: 'Wait!', text: 'Password must include at least one number' });
    } else if (!/[$#@!?]/.test(pass)) {
        Swal.fire({ icon: 'warning', title: 'Wait!', text: 'Password must include at least one special character (e.g., $ # @ ! ?)' });
    } else {
        const fd = new FormData();
        fd.append('action', 'reset_password');
        fd.append('user_id', id);
        fd.append('new_password', pass);

        sendAction('manageUserAction.php', fd, (data) => {
            Swal.fire('Updated!', data.message, 'success').then(() => closeAdminViewModal());
        });
    }
}

// --- USER ACTIONS ---
function deleteUser(id, username) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You are about to remove ${username}.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove user'
    }).then((result) => {
        if (result.isConfirmed) {
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('user_id', id);
            sendAction('manageUserAction.php', fd, () => {
                const row = document.getElementById(`user-row-${id}`);
                if (row) {
                    row.style.opacity = "0";
                    setTimeout(() => row.remove(), 300);
                }
            });
        }
    });
}

function saveChanges() {
    const newName = document.getElementById('edit-name').value;
    const newPass = document.getElementById('edit-pass').value;
    const confirmPass = document.getElementById('confirm-pass')?.value; 

    if (!newName.trim()) return Swal.fire('Oops!', 'Full Name is required!', 'warning');

    if (newPass !== "") {
        if (newPass !== confirmPass) {
            return Swal.fire('Mismatch!', 'Passwords do not match.', 'error');
        }
        if (newPass.length < 8) {
            return Swal.fire({ icon: 'warning', title: 'Wait!', text: 'Password should atleast be 8 characters long' });
        } else if (!/[A-Z]/.test(newPass)) {
            return Swal.fire({ icon: 'warning', title: 'Wait!', text: 'Password must include at least one upper case letter' });
        } else if (!/[a-z]/.test(newPass)) {
            return Swal.fire({ icon: 'warning', title: 'Wait!', text: 'Password must include at least one lower case letter' });
        } else if (!/\d/.test(newPass)) {
            return Swal.fire({ icon: 'warning', title: 'Wait!', text: 'Password must include at least one number' });
        } else if (!/[$#@!?]/.test(newPass)) {
            return Swal.fire({ icon: 'warning', title: 'Wait!', text: 'Password must include at least one special character (e.g., $ # @ ! ?)' });
        }
    }

    const fd = new FormData();
    fd.append('full_name', newName);
    fd.append('password', newPass);

    sendAction('updateProfile.php', fd, () => {
        Swal.fire('Profile Updated!', 'Refreshing...', 'success').then(() => location.reload());
    });
}

function uploadPFP(input) {
    if (input.files && input.files[0]) {
        const fd = new FormData();
        fd.append('profile_pic', input.files[0]);
        fd.append('action', 'update_pfp');

        const reader = new FileReader();
        reader.onload = (e) => {
            const preview = document.getElementById('pfp-preview');
            if (preview) preview.innerHTML = `<img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">`;
        };
        reader.readAsDataURL(input.files[0]);

        Swal.fire({ title: 'Uploading...', didOpen: () => Swal.showLoading() });
        sendAction('upload_pfp_action.php', fd, () => {
            Swal.fire('Uploaded!', 'Success.', 'success').then(() => location.reload());
        });
    }
}