document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. 表单格式化逻辑 ---
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

    // --- 2. 优惠券弹窗 (Modal) 逻辑 ---
    const voucherModal = document.getElementById('voucherModal');
    const openBtn = document.getElementById('openVoucherBtn');
    const closeBtn = document.getElementById('closeVoucherBtn');

    // 绑定：点击按钮打开
    if (openBtn && voucherModal) {
        openBtn.addEventListener('click', function() {
            voucherModal.style.display = 'flex';
        });
    }

    // 绑定：点击 X 关闭
    if (closeBtn && voucherModal) {
        closeBtn.addEventListener('click', function() {
            voucherModal.style.display = 'none';
        });
    }

    // 绑定：点击弹窗外部黑色背景关闭
    window.addEventListener('click', function(event) {
        if (event.target === voucherModal) {
            voucherModal.style.display = "none";
        }
    });

});