const errorRow = $("#errorrow");
const errorMessage = $("#error_dly");

function showError(message) {
    errorMessage.text(message);
    errorRow.css("display", "flex");
}

$("#okError").on("click", function () {
    errorRow.hide();
});

function addData() {
    let product_name = $("#pdtnme").val()?.trim() || "";
    let product_price = $("#pdtpc").val()?.trim() || "";
    let product_category = $("#pdtcy").val()?.trim() || "";
    let product_stock = $("#pdtsk").val()?.trim() || "";
    let product_description = $("#pdtdc").val()?.trim() || "";
    let product_release_date = $("#pdtrd").val()?.trim() || "";
    let editId = $("#pdtadd").data("edit-id");

    if (product_name === "" || product_price === "" || product_category === "" || product_stock === "" || product_release_date === "") {
        return showError("Please fill all required fields!");
    }
    if (product_description.length > 200) return showError("Description is over maximum length!");
    if (product_category.length > 50) return showError("Category is over maximum length!");

    let price = parseFloat(product_price);
    let stock = parseInt(product_stock, 10);

    if (isNaN(price) || price < 0) return showError("Price must be a valid positive number!");
    if (price === 0) return showError("Price cannot be zero!"); 
    if (isNaN(stock) || stock < 0) return showError("Stock must be a valid positive number!");
    
    let dateRegex = /^\d{4}-\d{2}-\d{2}$/;
    if (!dateRegex.test(product_release_date)) {
        return showError("Invalid date format! Expected YYYY-MM-DD.");
    }

    let parsedDate = new Date(product_release_date);
    if (isNaN(parsedDate.getTime())) {
        return showError("This is not a real calendar date!");
    }

    let today = new Date();
    today.setHours(0, 0, 0, 0);
    if (parsedDate < today) {
        return showError("Release date cannot be in the past!");
    }

    let isDuplicate = false;
    $("#pdtable tr:gt(0)").each(function() {
        let existingName = $(this).find("td:eq(2)").text().trim().toLowerCase();
        let existingId = $(this).find("td:eq(0)").text().replace("#", "").trim();
        
        if (editId && existingId == editId) return true;
        if (product_name.toLowerCase() === existingName) {
            isDuplicate = true;
            return false; 
        }
    });
    if (isDuplicate) return showError("This product name already exists!");

    let formData = new FormData();
    formData.append("action", editId ? "update_product" : "create_product"); 
    if (editId) formData.append("id", editId);

    formData.append("name", product_name);
    formData.append("price", product_price);
    formData.append("description", product_description);
    formData.append("release_date", product_release_date);
    formData.append("stock", product_stock);
    formData.append("category", product_category);

    let file = $("#uploadImg")[0].files[0];
    if (file) formData.append("image", file);

    $.ajax({
        url: window.location.href,
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.includes("Success")) {
                location.reload();
            } else {
                showError("Server Error:\n" + response);
            }
        },
        error: function(xhr, status, error) {
            showError("Network/Server Error!\nStatus: " + xhr.status);
        }
    });
}
function resetProductForm() {
    $("#pdtnme, #pdtpc, #pdtsk, #pdtcy, #pdtdc, #pdtrd").val("");
    let fileInput = $("#uploadImg")[0];
    fileInput.value = "";
    try {
        fileInput.files = new DataTransfer().files; 
    } catch(e) {} 
    
    $("#current_image_name").val("");

    $('#uploadImgLabel').text("Click or Drag to upload product image");
    $("#detailImage").empty();
    $("#pdtadd").text("Submit →").removeData("edit-id");

    $('.productSideNav a').removeClass('active');
    $('.productSideNav a:first').addClass('active');
    $('.productLbl, .productPrice, .productDetails').hide();
    $('.productLbl').show();
}
function addProduct_popup() {
    resetProductForm();
    $("#addPopup").css("display", "flex");
    
}

$("#pdtaddCancel").on("click", function() {
    resetProductForm();
    $("#addPopup").hide();
});

$(document).ready(function(){
    $(document).on('click', '.productSideNav a', function(e) {
        e.preventDefault();
        $('.productSideNav a').removeClass('active');
        $(this).addClass('active');
    
        const text = $(this).text().trim();
        $('.productLbl, .productPrice, .productDetails').hide();
    
        if (text === "Product") {
            $('.productLbl').show();
        } else if (text === "Price & Stock") {
             $('.productPrice').css('display', 'flex'); 
        }
        if(text === "Details") {
            $('.productDetails').css('display', 'flex');

            let file = $("#uploadImg")[0].files[0];
            let oldImage = $("#current_image_name").val();

            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $("#detailImage").html(`<img src="${e.target.result}" style="width:170px; height:230px; object-fit:cover; border-radius:8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">`);
                }
                reader.readAsDataURL(file);
            } else if (oldImage && oldImage !== "undefined" && oldImage !== "") {
                $("#detailImage").html(`<img src="/img/product_Img/${oldImage}" style="width:170px; height:230px; object-fit:cover; border-radius:8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">`);
            } else {
                $("#detailImage").html(`
                    <div style="width:170px; height:230px; border-radius:8px; background-color:#f1f5f9; border: 2px dashed #cbd5e1; display:flex; justify-content:center; align-items:center; color:#94a3b8; font-size:14px;">
                        No Image
                    </div>
                `);
            }
            $("#detailName").text($('#pdtnme').val());
            $("#detailPrice").text("RM " + (parseFloat($('#pdtpc').val()).toFixed(2) || "0.00"));
            $("#detailStock").text("Stock: " + $('#pdtsk').val());
            $("#detailCategory").text($('#pdtcy').val());
            $("#detailDescription").text($('#pdtdc').val());
            $("#detailDate").text($("#pdtrd").val());
        } 
    });
});

$('.uploadArea').on('dragover', function(e) {
    e.preventDefault();
}).on('drop', function(e) {
    e.preventDefault();
    let file = e.originalEvent.dataTransfer.files[0];
    $('#uploadImg')[0].files = e.originalEvent.dataTransfer.files;
    $(this).text(file.name);
});

$('#uploadImg').on('change', function() {
    $('.uploadArea').text($(this)[0].files[0].name);
});

$(document).on("click", ".pdtet", function() {
    resetProductForm();
    $("#pdtnme").val($(this).data("name"));
    $('#uploadImgLabel').text($(this).data("photo") ? "Current Image: " + $(this).data("photo") : "Click or Drag to upload product image");
    $("#current_image_name").val($(this).data("photo"));
    $("#pdtpc").val($(this).data("price"));
    $("#pdtsk").val($(this).data("stock"));
    $("#pdtcy").val($(this).data("category"));
    $("#pdtdc").val($(this).data("description"));
    $("#pdtrd").val($(this).data("date"));

    $("#pdtadd").text("Update →").data("edit-id", $(this).data("id"));
    $("#addPopup").css("display", "flex");

    $('.productSideNav a').removeClass('active');
    $('.productSideNav a:first').addClass('active');
    $('.productLbl, .productPrice, .productDetails').hide();
    $('.productLbl').show();
});

//vouchers
$(".dashboardVcontainer").on("click", function() {
    $(".addVoucher").css("display", "flex");
    $("body").css("overflow", "hidden");
});

$("#vcaddCancel").on("click", function() {
    $(".addVoucher").hide();
    $("body").css("overflow", "auto");
});

function addVoucher(e){
    e.preventDefault();
    let d_price = parseFloat($("#discount_price").val());

    if (isNaN(d_price)) return showError("Discount price must be a number!");
    if(d_price <= 0) return showError("Discount price cannot be negative or Zero !");

    let formData = new FormData();
    formData.append("action", "create_voucher"); 
    formData.append("promo_code", $("#promo_code").val());
    formData.append("discount_price", d_price);

    fetch(window.location.href, { method: "POST", body: formData })
    .then(res => res.text())
    .then(data => {
        if(data.includes("Success")) {
            location.reload();
        } else {
            alert("Server Message:\n" + data);
        }
    })
    .catch(err => alert("Network Error: " + err));
}

let deleteMode = null; // 'product' or 'voucher'
let targetId = null;

// 点击产品删除按钮
$(document).on("click", ".pdtdl", function() {
    deleteMode = "product";
    targetId = $(this).data("id");
    $("#deleterow").css("display", "flex");
});

$(document).on("click", ".vcdl", function() {
    deleteMode = "voucher";
    targetId = $(this).data("id");
    $("#deleterow").css("display", "flex");
});

$("#canceldelete").click(function() {
    $("#deleterow").hide();
    deleteMode = null;
    targetId = null;
});

$("#yesdelete").off("click").on("click", function() {
    if (deleteMode === "product") {
        $.post(window.location.href, { action: "delete_product", id: targetId }, function(res) {
            location.reload();
        });
    } else if (deleteMode === "voucher") {
        $.post(window.location.href, { action: "delete_voucher", promo_code: targetId }, function(res) {
            location.reload();
        });
    }
    $("#deleterow").hide();
});

$(document).on("change", ".toggle-status", function() {
    let promoCode = $(this).attr("data-id");
    let isActive = $(this).is(":checked") ? 1 : 0;
    let toggleBtn = $(this);

    $.ajax({
        url: window.location.href,
        type: "POST",
        data: {
            action: "toggle_voucher",
            promo_code: promoCode,
            status: isActive
        },
        success: function(res) {
            if (!res.includes("Success")) {
                alert("Status Update Failed:\n" + res);
                toggleBtn.prop('checked', !isActive); 
            }
        },
        error: function() {
            alert("Network error.");
            toggleBtn.prop('checked', !isActive);
        }
    });
});