document.addEventListener('DOMContentLoaded', function() {
const userBtn = document.getElementById('userbtn');
const userMenu = document.getElementById('usermenu');

    if (userBtn && userMenu) {
        userBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userMenu.classList.toggle('show');
        });

        document.addEventListener('click', function() {
            userMenu.classList.remove('show');
        });
    }

const uploadBtn = document.getElementById('uploadBtn');
const profilePhotoInput = document.getElementById('profilePhotoInput');
const profilePicForm = document.getElementById('profilePicForm');

if (uploadBtn && profilePhotoInput) {
    uploadBtn.onclick = function() {
        profilePhotoInput.click();
    };
}

if (profilePhotoInput) {
    profilePhotoInput.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const profileImage = document.getElementById('profileImage');
                if (profileImage) {
                    profileImage.src = event.target.result;
                }
            };
            reader.readAsDataURL(file);

            if (profilePicForm) {
                profilePicForm.submit();
            }
        }
    };
}

document.querySelectorAll('.tab-btn').forEach(function(btn) {
    btn.onclick = function() {
        showTab(this.getAttribute('data-tab'));
    };
});

const closeModalBtn = document.getElementById('closeProfileModal');
    if (closeModalBtn) {
        closeModalBtn.onclick = closeProfileModal;
    }
});

function openProfileModal() {
    const modal = document.getElementById('profileModal');
    if (modal) modal.style.display = 'flex';
}

function closeProfileModal() {
    const modal = document.getElementById('profileModal');
    if (modal) modal.style.display = 'none';
}

function showTab(tabName) {
    const infoTab = document.getElementById('infoTab');
    const passwordTab = document.getElementById('passwordTab');
    const infoBtn = document.querySelector('.tab-btn[data-tab="info"]');
    const passwordBtn = document.querySelector('.tab-btn[data-tab="password"]');

    if (tabName === 'info') {
        if (infoTab) infoTab.classList.add('active');
        if (passwordTab) passwordTab.classList.remove('active');
        if (infoBtn) infoBtn.classList.add('active');
        if (passwordBtn) passwordBtn.classList.remove('active');
    } else {
        if (infoTab) infoTab.classList.remove('active');
        if (passwordTab) passwordTab.classList.add('active');
        if (infoBtn) infoBtn.classList.remove('active');
        if (passwordBtn) passwordBtn.classList.add('active');
    }
}

window.onclick = function(e) {
    const modal = document.getElementById('profileModal');
    if (e.target === modal) {
        closeProfileModal();
    }
};