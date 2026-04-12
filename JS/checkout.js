document.addEventListener('DOMContentLoaded', () => {
    const cardNmae = document.getElementById('cardName');
    const cardInput = document.getElementById('cardNumber');
    const expDate = document.getElementById('expriy_date');
    const cvv = document.getElementById('cvv');
    if(cardNmae){
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
        })
    };
});