<?php
    $_title = 'admin';
    $_mainCssFileName = "table";
    require 'php/_base.php';
    include 'php/_header.php';

    $totalpdtcount = 0;
    $totalpdtcount = getCount($_db, "SELECT COUNT(*) AS total FROM product");

    $instockcount = 0;
    $instockcount = getCount($_db, "SELECT COUNT(*) as total FROM product WHERE stock > 30 ");

    $lowstockcount = 0;
    $lowstockcount = getCount($_db, "SELECT COUNT(*) AS total FROM product WHERE stock < 30");

    $categorycount = 0;
    $categorycount = getCount($_db, "SELECT COUNT(DISTINCT category) AS total FROM product");

    $voucherCount = 0;
    $voucherCount = getCount($_db, "SELECT COUNT(*) AS total FROM voucher");

    $result = $_db->query("SELECT * FROM product");
    $product = $result->fetch_all(MYSQLI_ASSOC);

    $voucherResult = $_db->query("SELECT promo_code, discount_price FROM voucher");
    $voucher = $voucherResult->fetch_all(MYSQLI_ASSOC);
?>
<main>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <div class="pdt_main">
        <div id="pdtwd">Product Maintaince</div>
        <div id="pdtwdTwo">Dashboard / Products</div>
        <div class="top">
            <div class="dashboardDisplay">
                <div class="dashboardTPcontainer">
                    <label>Total Products</label>
                    <label id="totalProductCount"><?= $totalpdtcount ?></label>
                    <label id="totalProductStatus">Active</label>
                </div>

                <div class="dashboardIScontainer">
                    <label>In Stock</label>
                    <label id="inStockCount"><?= $instockcount ?></label>
                    <label id="inStockStatus">Available</label>
                </div>
                <div class="dashboardLScontainer">
                    <label>Low Stock</label>
                    <label id="lowStockCount"><?= $lowstockcount ?></label>
                    <label id="lowStockStatus">Alert</label>
                </div>
                <div class="dashboardCcontainer">
                    <label>Categories</label>
                    <label id="categoriesCount"><?= $categorycount ?></label>
                    <label id="categoriesStatus">Types</label>
                </div>
                <div class="dashboardVcontainer">
                    <label>Voucher</label>
                    <label id="voucherCount"><?= $voucherCount ?></label>
                    <label>Click here to create voucher</label>
                </div>

            </div>
            <div class="addVoucher">
                <div class="voucher_container">
                    <div class="vouchertop">
                        <label id="voucherwd">Voucher Information</label>
                        <div id="vcaddCancel">✖</div>
                    </div>
                    <div class="voucher_row">
                        <table class="vouchertable">
                            <thead>
                                <tr>
                                    <?php
                                        $voucherHeader = ["Promo Code", "Discount Price", "Action"];

                                        foreach($voucherHeader as $header) {
                                            echo "<th>$header</th>";
                                        }
                                    ?>
                                </tr>
                            </thead>

                            <tbody>
                               <?php foreach($voucher as $voucherRow): ?>
                                    <tr>
                                        <td><?= $voucherRow['promo_code'] ?></td>
                                        <td><?= $voucherRow['discount_price'] ?></td>
                                        <td>
                                            <button class="vcdl" data-id="<?= $voucherRow['promo_code'] ?>">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>

                        <form class="voucherinput" onsubmit="addVoucher(event)">
                            <input type="hidden" name="action" value="create">
                            <?php 
                                $voucherinput = [
                                    ["id" => "promo_code", "label" => "Promo Code"],
                                    ["id" => "discount_price", "label" => "Discount Price"]
                                ];
                                ?>
                                <?php foreach ($voucherinput as $item): ?>
                                    <label>
                                        <?= $item['label'] ?>
                                    </label>
                                
                                    <input type="text" id="<?= $item['id'] ?>">
                                <?php endforeach; ?>
                            <button type="submit" onclick="addVoucher()">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
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
                                <a href="#">Price & Stock</a>
                                <a href="#">Details</a>
                            </div>
                            <div class="productDetail">
                                <form class="productLbl">
                                    <label for="productwd">Product Name</label>
                                    <input type="name" maxlength="100" placeholder="e.g. Pokemon Card Box" id="pdtnme">

                                    <label for="productImage">Add Image</label>
                                    <label class="uploadArea" for="uploadImg">
                                        Click or Drag to upload product image
                                    </label>
                                    <input type="file" accept="image/*" id="uploadImg">

                                    <label for="productDescription">Description</label>
                                    <textarea id="pdtdc" placeholder="Short description of this product..." maxlength="200"></textarea>
                                </form>
                                <form class="productPrice">
                                    <div class="priceRow">
                                        <div>
                                            <label for="productPrice" id="pdtpcname">Price (RM)</label>
                                            <input type="number" min='0' id="pdtpc" placeholder="0.00">
                                        </div>
                                        <div>
                                            <label for="productMemberDiscount" id="pdtdtname">Member Discount (%)</label>
                                            <input id="pdtdt" type="number" min="0" placeholder="0">
                                        </div>

                                    </div>
                                    <div class="dateStockRow">
                                        <div>
                                            <label for="productReleaseDate">Release Date</label>
                                            <input id="pdtrd" type="date" max="2029-12-31">
                                        </div>
                                        <div>
                                            <label for="productStock">Stock</label>
                                            <input type="number" id="pdtsk" min="0" max="9999" placeholder="Product Stock">
                                        </div>
                                        
                                    </div>
            
                                    <label for="productCategory">Category</label>
                                    <input type="text" id="pdtcy" maxlength="50" placeholder="e.g. Limited Edition, Classic, Art Toy">

                                </form>

                                <form class="productDetails">
                                    <div class="detailRow">
                                        <div id="detailImage"></div>
                                        <div class="detailInfo">
                                            <?php 
                                            $details = ["detailName", "detailPrice", "detailStock"];

                                            foreach($details as $detail) {
                                                echo '<label id="'.$detail.'"></label>';
                                            }
                                            ?>
                                            <hr>
                                            <div>
                                                <label class="detailTitle">Category</label>
                                                <label id="detailCategory">-</label>
                                            </div>
                                            <div>
                                                <label class="detailTitle">Discount</label>
                                                <label id="detailDiscount">-</label>
                                            </div>
                                            <hr>
                                            <div>
                                                <label class="detailTitle">Release Date</label>
                                                <label id="detailDate">-</label>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="detailRowDescription">
                                        <label class="detailTitle">Description</label>
                                        <label id="detailDescription">-</label>

                                    </div>

                                </form>
                                    <button type="button" onclick="addData()" id="pdtadd">Submit →</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <table id="pdtable">
                <tr>
                <?php
                $header = ["ID", "Photo", "Product Name", "Category", "Price (RM)", "Stock", "Action"];
                
                foreach ($header as $head) {
                    echo "<th>$head</th>";
                }
                ?>
                </tr>
                <?php foreach($product as $row):?>
                <tr>
                    <td>#<?=  $row['product_id'] ?></td>
                    <td><img src="image/<?= $row['image'] ?>" width="50" height="50" style="border-radius:5px; object-fit:cover;"></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['category'] ?></td>
                    <td><?= number_format($row['price'], 2) ?></td>
                    <td><?= $row['stock'] ?></td>

                    <td>
                        <button class="pdtet"
                            data-id="<?= $row['product_id'] ?>"
                            data-name="<?= $row['name'] ?>"
                            data-category="<?= $row['category'] ?>"
                            data-price="<?= $row['price'] ?>"
                            data-stock="<?= $row['stock'] ?>"
                            data-description="<?= $row['description'] ?>"
                            data-date="<?= date('Y-m-d', strtotime($row['release_date'])) ?>">Edit</button>
                        <button class="pdtdl" data-id="<?= $row['product_id'] ?>">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
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