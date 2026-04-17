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

    var editId = $("#pdtadd").data("edit-id");
    var numError = [
        {name: "Price", value: parseFloat(product_price)},
        {name: "Stock", value: parseInt(product_stock)}];

    var table = $("#pdtable")[0]; // DOM table

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

        let row = table.rows[i];
        let existingname = $(row.cells[2]).text();

        let existingId = $(row.cells[0]).text().replace("#", "").trim();;


        if (editId && existingId == editId) {
            continue;
        }

        if (product_name.toLowerCase() == existingname.toLowerCase()) {
            showError("This product name already exists!");
            return;
        }
    }

    var formData = new FormData();

    formData.append("name", $("#pdtnme").val());
    formData.append("price", $("#pdtpc").val());
    formData.append("description", $("#pdtdc").val());
    formData.append("release_date", $("#pdtrd").val());
    formData.append("stock", $("#pdtsk").val());
    formData.append("category", $("#pdtcy").val());

    var file = $("#uploadImg")[0].files[0];
    if(file) {
        formData.append("image", file);
    }
    
    if (editId) {
        formData.append("id", editId)
        
        $.ajax({
            url: "updateProduct.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,

            success: function(response) {
                var lines = response.trim().split('\n');
                var lastLine = lines[lines.length - 1].trim();

                if(lastLine.trim() === "Success") {
                    location.reload();
                }
            }
        })
    } else {

        $.ajax({
            url: "addProduct.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,

            success: function(response) {
                var lines = response.trim().split('\n');
                var lastLine = lines[lines.length - 1].trim();

                if(lastLine.trim() === "Success") {
                    location.reload();
                } else {
                    alert(response);
                }
            }
        });
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



/////////////////////////////////////////


let deleteID = null;
let deleteRow = null;

$(document).on("click", ".pdtdl", function() {
    deleteID = $(this).data("id");
    deleteRow = $(this).closest("tr");

    $("#deleterow").css("display", "flex");
})

$("#canceldelete").click(function() {
    $("#deleterow").hide();
})

$("#yesdelete").click(function() {
    $.post("deleteProduct.php", {id: deleteID}, function() {

        deleteRow.remove();
        $("#deleterow").hide();
        location.reload(); //reload page
    })
})

$(document).on("click", ".pdtet", function() {
    var id = $(this).data("id");
    var name = $(this).data("name");
    var category = $(this).data("category");
    var description = $(this).data("description");
    var date = $(this).data("date");


    $("#pdtnme").val(name);
    $("#pdtpc").val($(this).data("price"));
    $("#pdtsk").val($(this).data("stock"));
    $("#pdtcy").val(category);
    $("#pdtdc").val(description);
    $("#pdtrd").val(date);

    $("#pdtadd").text("Update →").data("edit-id", id);
    $("#addPopup").css("display", "flex");

    $('.productSideNav a').removeClass('active');
    $('.productSideNav a:first').addClass('active');
    $('.productLbl, .productPrice, .productDetails').hide();
    $('.productLbl').show();
})

$(document).on("click", "#pdtaddCancel", function() {
    $("#addPopup").hide();
    $("#pdtadd").text("Submit →").removeData("edit-id");
})

$(document).on("click", "#vcaddCancel", function() {
    $(".addVoucher").hide();
})

$(document).on("click", ".dashboardVcontainer", function() {
    $(".addVoucher").css("display", "flex");
})

function addVoucher(e){
    e.preventDefault();

    var d_price = parseFloat($("#discount_price").val());

    if (isNaN(d_price)) {
        showError("Discount price must be digit!");
        return;
    }

    var formData = new FormData();
    formData.append("action", "create");
    formData.append("promo_code", $("#promo_code").val());
    formData.append("discount_price", d_price);

    fetch("addVoucher.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        alert(data);
        location.reload();
    });
}

let voucherCode = null;
let voucherRow = null;

$(document).on("click", ".vcdl", function() {
    voucherCode = $(this).data("id"); // promo_code
    voucherRow = $(this).closest("tr");

    $("#deleterow").css("display", "flex");
});

$("#yesdelete").click(function() {
    $.post("deleteProduct.php", {id: deleteID}, function() {

        deleteRow.remove();
        $("#deleterow").hide();
        location.reload();
    })
})

$("#yesdelete").click(function() {

    // 👉 删除 Product
    if (deleteID) {
        $.post("deleteProduct.php", {id: deleteID}, function() {
            deleteRow.remove();
            location.reload();
        });
    }

    // 👉 删除 Voucher
    if (voucherCode) {
        $.post("addVoucher.php", {
            action: "delete",
            promo_code: voucherCode
        }, function() {
            voucherRow.remove();
            location.reload();
        });
    }

    $("#deleterow").hide();

    // reset
    deleteID = null;
    voucherCode = null;
});

$(document).on("click", "#canceldelete", function() {
    $("#deleterow").hide();
})
