<?php 
  $current_page = basename($_SERVER['PHP_SELF']); 
?>
<div class="sidebar-column" id="adminProfileCard">
    <div class="content-card profile-card" style="margin: 0;">
        <div class="profile-header">
            <div class="profile-img-container" onclick="document.getElementById('pfp-input').click()" style="cursor: pointer; position: relative; width: 100px; height: 100px; margin: 0 auto 15px;">
                <div class="img-circle" id="pfp-preview" style="width: 100%; height: 100%; overflow: hidden; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #f0f2f5; border: 2px solid var(--primary-blue-solid);">
                    <?php if(!empty($user['profile_pic'])): ?>
                        <img src="../../src/profiles/<?php echo $user['profile_pic']; ?>" style="width:100%; height:100%; object-fit:cover;">
                        <?php else: ?>
                            <i class="fas fa-user-shield" style="font-size: 40px; color: var(--primary-blue-solid);"></i>
                        <?php endif; ?>
                    </div>
                    <div class="pfp-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; opacity: 0; transition: 0.3s;">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>

                <input type="file" id="pfp-input" style="display:none;" accept="image/*" onchange="uploadPFP(this)">

                <h2 id="display-user"><?php echo htmlspecialchars($user['username']); ?></h2>
                <span class="badge <?php echo (strtoupper($user['position']) == 'ADMIN') ? 'badge-admin' : 'badge-blue'; ?>">
                    <?php echo htmlspecialchars($user['position']); ?>
                </span>
            </div>

            <div class="card-inner">
                <div class="info-row"><span>Full Name:</span><strong id="display-name"><?php echo htmlspecialchars($user['full_name']); ?></strong></div>  
            <?php if ($current_page !== 'staffProfile.php'): ?>
                    <button class="btn-primary-action" onclick="openModal('settingsModal')" style="width: 100%; margin-top: 15px;">
                        <i class="fas fa-cog"></i> Account Settings
                    </button>
            <?php endif; ?>
        </div>
    </div>
</div>