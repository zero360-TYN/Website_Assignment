function closeSystemModal() {
    const modal = document.getElementById('systemModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

window.onclick = function(e) {
    const modal = document.getElementById('systemModal');
    if (e.target === modal && modal) {
        modal.style.display = 'none';
    }
};