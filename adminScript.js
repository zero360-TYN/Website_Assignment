//-----------------------ERROR POPUP--------------------------------


function addData() {
    let currentRow = null;

    var product_name = $("#pdtnme").val();
    var product_price = $("#pdtpc").val();
    var product_category = $("#pdtcy").val();
    var product_stock = $("#pdtsk").val();
    var product_description = $("#pdtdc").val();
    var product_discount = $("#pdtdt").val();
    var product_release_date = $("#pdtrd").val();

    var numError = [
        {name: "Price", value: parseFloat(product_price)},
        {name: "Stock", value: parseInt(product_stock)}];

    var table = $("#pdtable")[0]; // DOM table
    const errorRow = $("#errorrow");
    const errorMessage = $("#error_dly");
    const okBtn = $("#okError");

    function showError(message) {
        errorMessage.text(message);
        errorRow.css("display", "flex");
    }

    okBtn.off("click").on("click", function () {
        errorRow.hide();
    });

    if (product_name == "" || product_price == "" 
        || product_category == "" || product_stock == "" 
        || product_release_date == "") {
        showError("Please fill all field!");
        return;
    } else if(product_description.length > 200) {
        showError("Description is over maximum length!");
        return;
    } else if(product_category.length > 50) {
        showError("Category is over maxinum length!")
        return;
    }

    for(var i = 0; i < numError.length; i++) {
        if(numError[i].value == 0) {
            showError(numError[i].name + " cannot be zero!");
            return
        } else if(isNaN(numError[i].value)) {
            showError(numError[i].name + " must be a number!");
            return
        } else if (numError[i].value < 0) {
            showError(numError[i].name + " cannot be negative!");
            return
        }
    }

    for (var i = 1; i < table.rows.length; i++) {
        let existingname = $(table.rows[i].cells[2]).text();

        if (product_name.toLowerCase() == existingname.toLowerCase()) {
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

    

    cell0.innerHTML = "-";

    var file = $("#uploadImg")[0].files[0];

    if(file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            cell1.innerHTML = '<img src= "' + e.target.result + '" style="width: 50px; height: 50px; border-radius: 5px; object-fit: cover;">';
        }
        reader.readAsDataURL(file);
    } else {
        cell1.innerHTML = '-';
    }
    
    cell2.innerHTML = product_name;
    cell3.innerHTML = product_category;
    cell4.innerHTML = parseFloat(product_price).toFixed(2);
    cell5.innerHTML = product_stock;
    $(cell5).text(product_stock).attr('data-max', product_stock);
    cell6.innerHTML = '<button class="pdtet">Edit</button><button class="pdtdl">Delete</button>';

    updateDashboard();

    $('#pdtnme, #pdtpc, #pdtcy, #pdtsk, #pdtrd, #uploadImg, #pdtdt, #pdtdc').val('');
    $('.uploadArea').text('Click or Drag to upload product image');

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
        $('.productLbl, .productPrice, .productStock, .productDetails').hide();
    
        // 根据点击内容显示
        if (text === "Product") {
            $('.productLbl').show();
        } 
        else if (text === "Price & Stock") {
             $('.productPrice').css('display', 'flex'); 
        } else if(text == "Details") {
            $('.productDetails').css('display', 'flex');

            var file = $("#uploadImg")[0].files[0];

            if(file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $("#detailImage").html('<img src="' + e.target.result + '" style="width:170px; height:230px; object-fit:cover; border-radius:5px;">');
                }
                reader.readAsDataURL(file);
            }
    
            $("#detailName").text($('#pdtnme').val());
            $("#detailPrice").text("RM " + parseFloat($('#pdtpc').val()).toFixed(2) || "");
            $("#detailStock").text("Stock: " + $('#pdtsk').val());
            $("#detailCategory").text($('#pdtcy').val());
            $("#detailDiscount").text($('#pdtdt').val());
            $("#detailDescription").text($('#pdtdc').val());
            $("#detailDate").text($("#pdtrd").val());
        } 
    });
});


function updateDashboard() {
    var total = $("#pdtable tr").length - 1;
    var inStockCount = 0;
    var lowStockCount = 0;
    var categories = [];

    $("#pdtable tr").slice(1).each(function() {
        var currentStock = parseInt($(this).find("td").eq(5).text());
        var initialStock = parseInt($(this).find("td").eq(5).attr('data-max'));
        var percentage = (currentStock / initialStock) * 100;
        var category = $(this).find("td").eq(3).text();

        if (percentage > 30) {
            inStockCount++;
        } else if (percentage <= 30 && currentStock > 0) {
            lowStockCount++;
        }

        if (!categories.includes(category)) {
            categories.push(category);
        }
    });

    $("#totalProductCount").text(total);
    $("#inStockCount").text(inStockCount);
    $("#lowStockCount").text(lowStockCount);
    $("#categoriesCount").text(categories.length);
}

$('.uploadArea').on('dragover', function(e) {
    e.preventDefault();
}).on('drop', function(e) {
    e.preventDefault();
    var file = e.originalEvent.dataTransfer.files[0];
    $('#uploadImg')[0].files = e.originalEvent.dataTransfer.files;
    $(this).text(file.name);
});

$('#uploadImg').on('change', function() {
    var fileName = $(this)[0].files[0].name;
    $('.uploadArea').text(fileName);
});