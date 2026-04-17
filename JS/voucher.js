document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('usedModal');
    const openBtn = document.getElementById('viewUsedBtn');
    const closeBtn = document.getElementById('closeModal');

    if (openBtn && modal) {
        openBtn.addEventListener('click', () => {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            document.documentElement.style.overflow = 'hidden';
        });
    }

    if (closeBtn && modal) {
        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
        });
    }
});