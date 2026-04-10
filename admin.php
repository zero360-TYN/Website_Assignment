<?php
    $_title = 'admin';
    $_mainCssFileName = "table";
    require 'php/_base.php';
    include 'php/_header.php';
?>
<main>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <div class="pdt_main">
        <div class="top">
            <div id="pdtwd">Product Maintaince</div>
            <div id="addrow">
                <button id="addBtn" onclick="addProduct_popup()">Add Product</button>
                <div class="add_container" id="addPopup">
                    <div class="productadd">
                        <div class="addTop">
                            <div id="addwd">Product Information</div>
                            <div id="pdtaddCancel">✖</div>
                        </div>
                        <div class="productContainer">
                            <div class="productSideNav">
                                <a href="#" class="active">Product</a>
                                <a href="#">Price & Category</a>
                                <a href="#">Stock</a>
                                <a href="#">Details</a>
                            </div>
                            <div class="productDetail">
                                <form class="productLbl">
                                    <label for="productwd">Product</label>
                                    <input type="name" maxlength="100" placeholder="Product Name" id="pdtnme">

                                    <label for="productImage">Add Image</label>
                                    <input type="file" accept="image/*" id="uploadImg">

                                    <label for="productDescription">Description</label>
                                    <textarea id="pdtdc"></textarea>
                                </form>
                                <form class="productPrice">
                                    <label for="productPrice">Price</label>
                                    <input type="number" min='0' id="pdtpc" placeholder="Product Price">
                                    
                                    <label for="productMemberDiscount">Discount</label>
                                    <input type="number" min="0" placeholder="Member Discount">
                                    
                                    <label for="productCategory">Category</label>
                                    <input type="text" maxlength="100" placeholder="Product Category">

                                </form>
                                <form class="productStock">
                                    <label for="productReleaseDate">Release Date</label>
                                    <input type="date" max="2029-12-31">

                                    <label for="productStock">Stock</label>
                                    <input type="number" min="0" max="9999" placeholder="Product Stock">
                                </form>
                                <form class="productDetails">
                                    <label for="productNameDetails">Product Name: </label>
                                    <label for="productImageDetails">Product Image: </label>
                                    <label for="productPriceDetails">Product Price: </label>
                                    <label for="productDiscountDetails">Member Discount: </label>
                                    <label for="productCategoryDetails">Category: </label>
                                    <label for="productReleaseDetails">Release Date: </label>
                                    <label for="productStockDetails">Stock: </label>
                                </form>
                                    <button type="button" onclick="addData()" id="pdtadd">Continue →</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <table id="pdtable">
                <tr>
                    <th>ID</th>
                    <th>Photo</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price (RM)</th>
                    <th>Stock</th>
                    <th>Action</th>
                </tr>
            </table>
        </div>
    </div>
    <div id="deleterow" class="row">
            <div class="row_container">
                <div id="dltwd">Are you sure you want to delete this product?</div>
                <div class="dtlbtn">
                    <button id="yesdelete">Delete</button>
                    <button id="canceldelete">Cancel</button>
                </div>
            </div>
        </div>
        
    <div id="errorrow" class="error">
        <div class="error_container">
            <div id="error_dly"></div>
            <div class="errorbtn">
                <button id="okError">OK</button>
            </div>
        </div>

    </div>
    <script src="adminScript.js"></script>
    
</main>
<?php
    include 'php/_footer.php';
?>