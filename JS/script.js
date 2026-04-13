function ensureAdminPage() {
    var currentUser = localStorage.getItem('currentUser');
    if (!currentUser) {
        window.location.href = 'login.html';
        return;
    }
    setupProfileEventListeners();
}

function logout() {
    localStorage.removeItem('currentUser');
    window.location.href = 'login.html';
}

// User dropdown menu
if (document.getElementById('userbtn')) {
    document.getElementById('userbtn').onclick = function(e) {
        e.stopPropagation();
        var menu = document.getElementById('usermenu');
        if (menu) {
            menu.classList.toggle('show');
        }
    };
    
    document.onclick = function() {
        var menu = document.getElementById('usermenu');
        if (menu) {
            menu.classList.remove('show');
        }
    };
}

// PROFILE MANAGEMENT 
function getCurrentUser() {
    var currentUser = localStorage.getItem('currentUser');
    if (currentUser) {
        return JSON.parse(currentUser);
    }
    return null;
}

function saveCurrentUser(user) {
    localStorage.setItem('currentUser', JSON.stringify(user));
}

function getAllUsers() {
    var users = localStorage.getItem('users');
    if (users) {
        return JSON.parse(users);
    }
    return [];
}

function saveAllUsers(users) {
    localStorage.setItem('users', JSON.stringify(users));
}

function loadProfileData() {
    var currentUser = getCurrentUser();
    if (!currentUser) return;
    
    document.getElementById('profileUsername').value = currentUser.username || '';
    document.getElementById('profileEmail').value = currentUser.email || '';
    document.getElementById('profileFullname').value = currentUser.fullname || '';
    document.getElementById('profilePhone').value = currentUser.phone || '';
    document.getElementById('profileAddress').value = currentUser.address || '';
    
    if (currentUser.profilePic) {
        document.getElementById('profileImage').src = currentUser.profilePic;
    } else {
        document.getElementById('profileImage').src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"%3E%3Ccircle cx="50" cy="50" r="50" fill="%23cccccc"/%3E%3Ctext x="50" y="65" text-anchor="middle" fill="%23666666" font-size="40" dy=".3em"%3E🥸%3C/text%3E%3C/svg%3E';
    }
}

function openProfileModal() {
    loadProfileData();
    var modal = document.getElementById('profileModal');
    if (modal) {
        modal.style.display = 'flex';
    }
    showTab('info');
}

function closeProfileModal() {
    var modal = document.getElementById('profileModal');
    if (modal) {
        modal.style.display = 'none';
    }
    clearAlerts();
}

function showTab(tabName) {
    var infoTab = document.getElementById('infoTab');
    var passwordTab = document.getElementById('passwordTab');
    var infoBtn = document.querySelector('.tab-btn[data-tab="info"]');
    var passwordBtn = document.querySelector('.tab-btn[data-tab="password"]');
    
    if (!infoTab || !passwordTab) return;
    
    if (tabName === 'info') {
        infoTab.classList.add('active');
        passwordTab.classList.remove('active');
        if (infoBtn) infoBtn.classList.add('active');
        if (passwordBtn) passwordBtn.classList.remove('active');
    } else {
        infoTab.classList.remove('active');
        passwordTab.classList.add('active');
        if (infoBtn) infoBtn.classList.remove('active');
        if (passwordBtn) passwordBtn.classList.add('active');
    }
}

function clearAlerts() {
    var alerts = document.querySelectorAll('.alert-message');
    for (var i = 0; i < alerts.length; i++) {
        alerts[i].remove();
    }
}

function showAlert(message, type) {
    clearAlerts();
    var activeTab = document.querySelector('.tab-pane.active');
    if (!activeTab) return;
    var alertDiv = document.createElement('div');
    alertDiv.className = 'alert-message alert-' + type;
    alertDiv.innerHTML = message;
    activeTab.insertBefore(alertDiv, activeTab.firstChild);
    
    setTimeout(function() {
        if (alertDiv) alertDiv.remove();
    }, 3000);
}

function hashProfilePassword(password) {
    var hash = 0;
    for (var i = 0; i < password.length; i++) {
        var char = password.charCodeAt(i);
        hash = ((hash << 5) - hash) + char;
        hash = hash & hash;
    }
    return hash.toString();
}

// Update profile information
function updateProfile() {
    var currentUser = getCurrentUser();
    if (!currentUser) return;
    
    var newEmail = document.getElementById('profileEmail').value.trim();
    var newFullname = document.getElementById('profileFullname').value.trim();
    
    currentUser.email = newEmail;
    currentUser.fullname = newFullname;
    
    var users = getAllUsers();
    for (var i = 0; i < users.length; i++) {
        if (users[i].username === currentUser.username) {
            users[i].email = newEmail;
            users[i].fullname = newFullname;
            users[i].profilePic = currentUser.profilePic;
            break;
        }
    }
    
    saveAllUsers(users);
    saveCurrentUser(currentUser);
    showAlert('Profile updated successfully!', 'success');
}

// Update password
function updatePassword() {
    var currentUser = getCurrentUser();
    if (!currentUser) return;
    
    var currentPassword = document.getElementById('currentPassword').value;
    var newPassword = document.getElementById('newPassword').value;
    var confirmPassword = document.getElementById('confirmNewPassword').value;
    
    var hashedCurrent = hashProfilePassword(currentPassword);
    if (hashedCurrent !== currentUser.password) {
        showAlert('Current password is incorrect!', 'error');
        return;
    }
    
    if (newPassword.length < 4) {
        showAlert('New password must be at least 4 characters long!', 'error');
        return;
    }
    
    if (newPassword !== confirmPassword) {
        showAlert('New password and confirmation do not match!', 'error');
        return;
    }
    
    var hashedNew = hashProfilePassword(newPassword);
    currentUser.password = hashedNew;
    
    var users = getAllUsers();
    for (var i = 0; i < users.length; i++) {
        if (users[i].username === currentUser.username) {
            users[i].password = hashedNew;
            break;
        }
    }
    
    saveAllUsers(users);
    saveCurrentUser(currentUser);
    
    document.getElementById('currentPassword').value = '';
    document.getElementById('newPassword').value = '';
    document.getElementById('confirmNewPassword').value = '';
    
    showAlert('Password updated successfully!', 'success');
}

// Upload profile photo
function setupPhotoUpload() {
    var uploadBtn = document.getElementById('uploadBtn');
    var photoUpload = document.getElementById('photoUpload');
    var profileImage = document.getElementById('profileImage');
    
    if (uploadBtn) {
        uploadBtn.onclick = function() {
            if (photoUpload) photoUpload.click();
        };
    }
    
    if (photoUpload) {
        photoUpload.onchange = function(e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    var imageData = event.target.result;
                    if (profileImage) profileImage.src = imageData;
                    
                    var currentUser = getCurrentUser();
                    if (currentUser) {
                        currentUser.profilePic = imageData;
                        saveCurrentUser(currentUser);
                        
                        var users = getAllUsers();
                        for (var i = 0; i < users.length; i++) {
                            if (users[i].username === currentUser.username) {
                                users[i].profilePic = imageData;
                                break;
                            }
                        }
                        saveAllUsers(users);
                        showAlert('Profile photo updated!', 'success');
                    }
                };
                reader.readAsDataURL(file);
            }
        };
    }
}

// Setup profile event listeners
function setupProfileEventListeners() {
    var closeModal = document.getElementById('closeProfileModal');
    var updateProfileBtn = document.getElementById('updateProfileBtn');
    var updatePasswordBtn = document.getElementById('updatePasswordBtn');
    var modal = document.getElementById('profileModal');
    var tabBtns = document.querySelectorAll('.tab-btn');
    
    if (closeModal) {
        closeModal.onclick = closeProfileModal;
    }
    
    if (updateProfileBtn) {
        updateProfileBtn.onclick = updateProfile;
    }
    
    if (updatePasswordBtn) {
        updatePasswordBtn.onclick = updatePassword;
    }
    
    if (modal) {
        modal.onclick = function(e) {
            if (e.target === modal) {
                closeProfileModal();
            }
        };
    }
    
    for (var i = 0; i < tabBtns.length; i++) {
        tabBtns[i].onclick = function() {
            var tab = this.getAttribute('data-tab');
            showTab(tab);
        };
    }
    
    setupPhotoUpload();
}