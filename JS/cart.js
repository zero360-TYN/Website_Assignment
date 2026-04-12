document.addEventListener('DOMContentLoaded', () => {

    const minusBtn = document.getElementById('minusBtn');
    const plusBtn = document.getElementById('plusBtn');
    const popupQty = document.getElementById('popup-qty'); 
    const popupId = document.getElementById('popup-id');
    const popup = document.getElementById('qtyPopup');
    const closeBtn = document.getElementById('closePopup');

    if (!popup) return; 

    document.querySelectorAll('.btn-update-popup').forEach(btn => {
        btn.addEventListener('click', function() {

            popupId.value = this.getAttribute('data-id');
            popupQty.value = this.getAttribute('data-qty');
            
            popup.style.display = 'block';
        });
    });

    closeBtn.addEventListener('click', function() {
        popup.style.display = 'none';
    });

    minusBtn.addEventListener('click', () => {
        let currentValue = parseInt(popupQty.value);
        if (currentValue > 0) {
            popupQty.value = currentValue - 1;
        }
    });

    plusBtn.addEventListener('click', () => {
        let currentValue = parseInt(popupQty.value);
        popupQty.value = currentValue + 1;
    });
});