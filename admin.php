<?php
    $_title = 'admin';
    $_mainCssFileName = "table";
    require 'php/_base.php';
    include 'php/_header.php';
?>
<main>
    <div class="pdt_main">
        <div id="pdtwd">Product Maintaince</div>
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
            <div class="productadd">
                <input type="name" maxlength="100" placeholder="Product Name" id="pdtnme">
                <input type="number" min="0" maxlength="7" placeholder="Price(RM)" id="pdtpc">
                <button onclick="addData()" id="pdtadd">Add</button>
            </div>
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
    <script src="script.js"></script>
</main>
<?php
    include 'php/_footer.php';
?>