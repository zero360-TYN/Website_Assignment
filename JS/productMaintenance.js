const errorRow = $("#errorrow");
const errorMessage = $("#error_dly");

function showError(message) {
    errorMessage.text(message);
    errorRow.css("display", "flex");
}

$("#okError").on("click", function () {
    errorRow.hide();
});

// ==========================================
// 2. 核心提交逻辑 (Add & Update Product)
// ==========================================
function addData() {
    let product_name = $("#pdtnme").val().trim();
    let product_price = $("#pdtpc").val();
    let product_category = $("#pdtcy").val().trim();
    let product_stock = $("#pdtsk").val();
    let product_description = $("#pdtdc").val().trim();
    let product_discount = $("#pdtdt").val();
    let product_release_date = $("#pdtrd").val();

    let editId = $("#pdtadd").data("edit-id");
    let numError = [
        { name: "Price", value: parseFloat(product_price) },
        { name: "Stock", value: parseInt(product_stock) }
    ];

    // --- 基础验证 ---
    if (!product_name || !product_price || !product_category || !product_stock || !product_release_date) {
        return showError("Please fill all fields!");
    }
    if (product_description.length > 200) return showError("Description is over maximum length!");
    if (product_category.length > 50) return showError("Category is over maximum length!");

    // --- 数值验证 ---
    for (let item of numError) {
        if (item.value === 0) return showError(`${item.name} cannot be zero!`);
        if (isNaN(item.value)) return showError(`${item.name} must be a number!`);
        if (item.value < 0) return showError(`${item.name} cannot be negative!`);
    }

    // --- 查重验证 (前端扫表查重) ---
    let isDuplicate = false;
    $("#pdtable tr:gt(0)").each(function() {
        let existingname = $(this).find("td:eq(2)").text().trim();
        let existingId = $(this).find("td:eq(0)").text().replace("#", "").trim();
        
        if (editId && existingId == editId) return true; // 跳过自己
        if (product_name.toLowerCase() === existingname.toLowerCase()) {
            isDuplicate = true;
            return false; // 终止循环
        }
    });
    if (isDuplicate) return showError("This product name already exists!");

    // --- 组装数据 ---
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
                alert("Server Message:\n" + response); 
            }
        },
        error: function(xhr, status, error) {
            alert("Network/Server Error!\nStatus: " + xhr.status + "\nDetails: " + error);
        }
    });
}

// ==========================================
// 3. 产品弹窗控制与侧边栏 (UI)
// ==========================================
function addProduct_popup() {
    $("#addPopup").css("display", "flex");
    // 自动清空之前的残留数据
    $("form.productLbl, form.productPrice")[0].reset();
    $("form.productLbl, form.productPrice")[1].reset();
    $("#pdtadd").text("Submit →").removeData("edit-id");
}

$("#pdtaddCancel").on("click", function() {
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
        } else if(text === "Details") {
            $('.productDetails').css('display', 'flex');

            // 预览图片
            let file = $("#uploadImg")[0].files[0];
            if(file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $("#detailImage").html(`<img src="${e.target.result}" style="width:170px; height:230px; object-fit:cover; border-radius:5px;">`);
                }
                reader.readAsDataURL(file);
            }
    
            // 填充预览文字
            $("#detailName").text($('#pdtnme').val());
            $("#detailPrice").text("RM " + (parseFloat($('#pdtpc').val()).toFixed(2) || "0.00"));
            $("#detailStock").text("Stock: " + $('#pdtsk').val());
            $("#detailCategory").text($('#pdtcy').val());
            $("#detailDiscount").text($('#pdtdt').val());
            $("#detailDescription").text($('#pdtdc').val());
            $("#detailDate").text($("#pdtrd").val());
        } 
    });
});

// 图片拖拽交互
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

// ==========================================
// 4. 编辑 Product (点击 Edit 预填数据)
// ==========================================
$(document).on("click", ".pdtet", function() {
    $("#pdtnme").val($(this).data("name"));
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

// ==========================================
// 5. Voucher 管理
// ==========================================
$(".dashboardVcontainer").on("click", function() {
    $(".addVoucher").css("display", "flex");
});

$("#vcaddCancel").on("click", function() {
    $(".addVoucher").hide();
});

function addVoucher(e){
    e.preventDefault();
    let d_price = parseFloat($("#discount_price").val());

    if (isNaN(d_price)) return showError("Discount price must be a number!");

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

// ==========================================
// 6. 统一的 Delete 逻辑 (核心修复防重复提交)
// ==========================================
let deleteMode = null; // 'product' 或 'voucher'
let targetId = null;

// 点击产品删除按钮
$(document).on("click", ".pdtdl", function() {
    deleteMode = "product";
    targetId = $(this).data("id");
    $("#deleterow").css("display", "flex");
});

// 点击 Voucher 删除按钮
$(document).on("click", ".vcdl", function() {
    deleteMode = "voucher";
    targetId = $(this).data("id");
    $("#deleterow").css("display", "flex");
});

// 取消删除
$("#canceldelete").click(function() {
    $("#deleterow").hide();
    deleteMode = null;
    targetId = null;
});

// 唯一的确认删除按钮
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