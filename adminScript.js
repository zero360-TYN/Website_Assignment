//-----------------------ERROR POPUP--------------------------------

function addData() {
    
    let currentRow = null;
    var product_name = document.getElementById("pdtnme").value;
    var product_price = document.getElementById("pdtpc").value;
    var table = document.getElementById("pdtable");
    const errorRow = document.getElementById("errorrow");
    const errorMessage = document.getElementById("error_dly");
    const okBtn = document.getElementById("okError");

    function showError(message) {
        errorMessage.textContent = message;
        errorRow.style.display = "flex";
    }

    okBtn.addEventListener("click", () => {
        errorRow.style.display = "none";
    })

    if (product_name == "" || product_price == "") {
        showError("Please fill all field!");
        return;
    } else if (product_price == 0){
        showError("Price cannot be zero!");
        return;
    } else if (product_price < 0) {
        showError("Price cannot be negative!");
        return;
    }

    for(var i = 1; i < table.rows.length; i++) {
        existingname = table.rows[i].cells[2].innerHTML;

        if(product_name.toLowerCase() == existingname.toLowerCase()) {
            showError("This product name already exists!");
            return;
        }
    }

    var row = table.insertRow();

    var cell0 = row.insertCell(0);
    var cell1 = row.insertCell(1);
    var cell2 = row.insertCell(2);
    var cell3 = row.insertCell(3);
    var cell4 = row.insertCell(4);
    var cell5 = row.insertCell(5);
    var cell6 = row.insertCell(6);

    cell0.innerHTML = "PD"+ Math.floor(Math.random() * 1000);
    cell1.innerHTML = "-";
    cell2.innerHTML = product_name;
    cell3.innerHTML = "-";
    cell4.innerHTML = parseFloat(product_price).toFixed(2);
    cell5.innerHTML = "-";
    cell6.innerHTML = '<button class="pdtet">Edit</button><button class="pdtdl">Delete</button>';

    document.getElementById("pdtnme").value = "";
    document.getElementById("pdtpc").value = "";

    var pdtdlbtn = cell6.querySelector(".pdtdl");
    
    pdtdlbtn.addEventListener("click", function() {
        currentRow = row;
        document.getElementById("deleterow").style.display = "flex";
    });
    document.getElementById("yesdelete").onclick = function() {
        if (currentRow) {
            currentRow.remove();
        }
        document.getElementById("deleterow").style.display = "none";
    }
    document.getElementById("canceldelete").onclick = function() {
        document.getElementById("deleterow").style.display = "none";
    }

}

function addProduct_popup() {
    document.getElementById("addBtn").addEventListener("click", () => {
        document.getElementById("addPopup").style.display = "flex";
    })

    document.getElementById("pdtaddCancel").addEventListener("click", () => {
        document.getElementById("addPopup").style.display = "none";
    })
}


$(document).ready(function(){
    $(document).on('click', '.productSideNav a', function(e) {
        e.preventDefault();
    
        $('.productSideNav a').removeClass('active');
        $(this).addClass('active');
    
        const text = $(this).text().trim();
    
        // 全部先隐藏
        $('.productLbl, .productPrice').hide();
    
        // 根据点击内容显示
        if (text === "Product") {
            $('.productLbl').show();
            $("#pdtadd").css({'margin-top': '2rem', 'display': 'block'});
        } 
        else if (text === "Price & Category") {
             $('.productPrice').css('display', 'flex'); 
             $("#pdtadd").css({'margin-top': '4.75rem', 'display': 'block'});
        } else if(text == "Details") {
            $("#pdtadd").css('display', 'none');
        }
    });
});