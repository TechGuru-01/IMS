<div id="settingsModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Account Settings</h3>
            <span class="close-modal" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Display Name</label>
                <input type="text" id="edit-name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>">
            </div>

            <div class="form-group" style="margin-top:15px; position: relative;">
                <label>New Password</label>
                <input type="password" id="edit-pass" class="form-control" placeholder="Leave blank to keep current">
                <i class="fas fa-eye-slash" onclick="togglePasswordVisibility('edit-pass', this)" 
                   style="position: absolute; right: 15px; top: 38px; cursor: pointer; color: #666;"></i>
            </div>

            <div class="form-group" style="margin-top:15px; position: relative;">
                <label>Confirm New Password</label>
                <input type="password" id="confirm-pass" class="form-control" placeholder="Re-type new password">
                <i class="fas fa-eye-slash" onclick="togglePasswordVisibility('confirm-pass', this)" 
                   style="position: absolute; right: 15px; top: 38px; cursor: pointer; color: #666;"></i>
            </div>

            <p style="font-size: 11px; color: #888; margin-top: 10px;">
                * Password must be 8+ chars, with Upper, Lower, Number, and Symbol ($#@!?).
            </p>

            <button class="btn-save" onclick="saveChanges()" id="saveBtn" style="width: 100%; margin-top: 20px;">
                <i class="fas fa-check-circle"></i> Save Changes
            </button>
        </div>
    </div>
</div>