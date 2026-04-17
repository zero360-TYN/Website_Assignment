document.addEventListener('DOMContentLoaded', () => {
    
    const cardName = document.getElementById('cardName');
    const cardInput = document.getElementById('cardNumber');
    const expDate = document.getElementById('expriy_date');
    const cvv = document.getElementById('cvv');

    if(cardName){
        cardName.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\d/g, ''); 
            e.target.value = value;
        });
    }

    if (cardInput) {
        cardInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, ''); 
            let formattedValue = value.match(/\d{1,4}/g);
            if (formattedValue) {
                e.target.value = formattedValue.join(' ');
            } else {
                e.target.value = '';
            }
        });
    }

    if(expDate){
        expDate.addEventListener('input',function(e){
            let value = e.target.value.replace(/\D/g,'');
            let formattedValue = value.match(/\d{1,2}/g);
            if (formattedValue) {
                e.target.value = formattedValue.join('/');
            } else {
                e.target.value = '';
            }
        });
    }

    if(cvv){
        cvv.addEventListener('input',function(e){
            let value = e.target.value.replace(/\D/g,'');
            e.target.value = value;
        });
    }

    const voucherModal = document.getElementById('voucherModal');
    const openBtn = document.getElementById('openVoucherBtn');
    const closeBtn = document.getElementById('closeVoucherBtn');

    if (openBtn && voucherModal) {
        openBtn.addEventListener('click', function() {
            voucherModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            document.documentElement.style.overflow = 'hidden';
        });
    }

    if (closeBtn && voucherModal) {
        closeBtn.addEventListener('click', function() {
            voucherModal.style.display = 'none';
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
        });
    }
    window.addEventListener('click', function(event) {
        if (event.target === voucherModal) {
            voucherModal.style.display = "none";
        }
    });

});