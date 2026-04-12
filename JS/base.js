//user dropwarp menu
document.addEventListener('DOMContentLoaded', () => {
    const avatar = document.getElementById('userAvatar');
    const menu = document.getElementById('userDropdown');

    if (avatar && menu) {
        avatar.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        });

        window.addEventListener('click', () => {
            menu.style.display = 'none';
        });
    }
});