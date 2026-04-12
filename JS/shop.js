
document.addEventListener('DOMContentLoaded', () => {
    const buyButtons = document.querySelectorAll('.btn-buy');
    const cancelBtn = document.getElementById('btn-cancel');
    //quantity selector
    const minusBtn = document.getElementById('minusBtn');
    const plusBtn = document.getElementById('plusBtn');
    const quantityInput = document.getElementById('quantity');

    buyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-product-id');
            const name = this.getAttribute('data-product-name');
            const price = this.getAttribute('data-product-price');

            document.getElementById('popup-name').innerText = name;
            document.getElementById('popup-price').innerText = parseFloat(price).toFixed(2);
            document.getElementById('popup-product-id').value = id;
            document.getElementById('buyPopup').classList.add('show');
        });
    });

    if(cancelBtn){
        cancelBtn.addEventListener('click',function(){
            quantityInput.value = 1;
            document.getElementById('buyPopup').classList.remove('show');
    });
    }

    minusBtn.addEventListener('click', () => {
        let currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
        }
    });

    plusBtn.addEventListener('click', () => {
        let currentValue = parseInt(quantityInput.value);
        quantityInput.value = currentValue + 1;
    });
});