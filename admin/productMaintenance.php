<?php
$_title = 'Product Maintenance';
$_mainCssFileName = "table";
require '../_base.php';
if (is_post()) {
    $action = $_POST['action'] ?? '';
    if ($action === 'create_voucher') {
        $promo_code = $_POST['promo_code'] ?? '';
        $discount_price = $_POST['discount_price'] ?? '';
        try {
            $stmt = $_db->prepare("INSERT INTO voucher (promo_code, discount_price) VALUES (?, ?)");
            $stmt->execute([$promo_code, $discount_price]);
            echo "Success";
        } catch (Exception $e) {
            echo "Error adding voucher: " . $e->getMessage();
        }
        exit;
    }
    if ($action === 'toggle_voucher') {
        $promo_code = trim($_POST['promo_code'] ?? '');
        $status = (int)($_POST['status'] ?? 0);
        try {
            $stmt = $_db->prepare("UPDATE voucher SET is_active = ? WHERE promo_code = ?");
            $stmt->execute([$status, $promo_code]);
            echo "Success";
        } catch (Exception $e) {
            echo "Database Error: " . $e->getMessage();
        }
        exit;
    }
    if ($action === 'delete_voucher') {
        $promo_code = trim($_POST['promo_code'] ?? '');

        if (empty($promo_code) ) {
            echo "Error: Promo code is empty.";
            exit;
        }

        try {
            $_db->beginTransaction();
            $stmt1 = $_db->prepare("DELETE FROM voucher_handle WHERE promo_code = ?");
            $stmt1->execute([$promo_code]);

            $stmt2 = $_db->prepare("DELETE FROM voucher WHERE promo_code = ?");
            $stmt2->execute([$promo_code]);

            $_db->commit();

            echo "Success";
        } catch (Exception $e) {
            $_db->rollBack();
            echo "Database Error: " . $e->getMessage();
        }
        exit;
    }
    if ($action === 'delete_product') {
        $id = $_POST['id'] ?? '';

        $stmt = $_db->prepare("UPDATE product SET is_deleted = '1' WHERE product_id = ?");
        $stmt->execute([$id]);
        echo "Success";
        exit;
    }

    if ($action === 'create_product') {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        $release_date = $_POST['release_date'] . " 00:00:00";
        $stock = $_POST['stock'];
        $category = $_POST['category'];

        $image_name = 'default.png';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image_name = time() . '_' . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], "../img/product_Img/" . $image_name);
        }

        try {
            $stmt = $_db->prepare("INSERT INTO product (name, price, description, release_date, stock, category, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $price, $description, $release_date, $stock, $category, $image_name]);
            echo "Success";
        } catch (Exception $e) {
            echo "Database Error: " . $e->getMessage();
        }
        exit;
    }

    if ($action === 'update_product') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        $release_date = $_POST['release_date'] . " 00:00:00";
        $stock = $_POST['stock'];
        $category = $_POST['category'];

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image_name = time() . '_' . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], "../img/product_Img/" . $image_name);

            $stmt = $_db->prepare("UPDATE product SET name=?, price=?, description=?, release_date=?, stock=?, category=?, image=? WHERE product_id=?");
            $stmt->execute([$name, $price, $description, $release_date, $stock, $category, $image_name, $id]);
        } else {
            $stmt = $_db->prepare("UPDATE product SET name=?, price=?, description=?, release_date=?, stock=?, category=? WHERE product_id=?");
            $stmt->execute([$name, $price, $description, $release_date, $stock, $category, $id]);
        }

        echo "Success";
        exit;
    }
}
// ==========================================

include root('_header.php');

$totalpdtcount = 0;
$totalpdtcount = getCount($_db, "SELECT COUNT(*) AS total FROM product WHERE is_deleted = 0");

$instockcount = 0;
$instockcount = getCount($_db, "SELECT COUNT(*) as total FROM product WHERE stock > 30 AND is_deleted = 0");

$lowstockcount = 0;
$lowstockcount = getCount($_db, "SELECT COUNT(*) AS total FROM product WHERE stock < 30 AND is_deleted = 0");

$categorycount = 0;
$categorycount = getCount($_db, "SELECT COUNT(DISTINCT category) AS total FROM product WHERE is_deleted = 0");

$voucherCount = 0;
$voucherCount = getCount($_db, "SELECT COUNT(*) AS total FROM voucher");

$result = $_db->query("SELECT * FROM product WHERE is_deleted = 0");
$product = $result->fetchAll();

$voucherResult = $_db->query("SELECT * FROM voucher");
$voucher = $voucherResult->fetchAll();
?>
<main>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <div class="pdt_main">
        <div id="pdtwd">Product Maintaince</div>
        <div id="pdtwdTwo"><a href="/admin/adminDashboard.php">Dashboard</a>/ Products</div>
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
                        <h2 id="voucherwd">Voucher Management</h2>
                        <button id="vcaddCancel" class="close-btn">✖</button>
                    </div>
                    <div class="voucher_row">
                        <div class="vouchertable_wrapper">
                            <table class="vouchertable">
                                <thead>
                                    <tr>
                                        <th>Promo Code</th>
                                        <th>Discount Price</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($voucher as $voucherRow): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($voucherRow->promo_code) ?></td>
                                            <td>RM <?= htmlspecialchars($voucherRow->discount_price) ?></td>
                                            <td>
                                                <label class="vc-switch">
                                                    <input type="checkbox" class="toggle-status"
                                                        data-id="<?= htmlspecialchars($voucherRow->promo_code) ?>"
                                                        <?= isset($voucherRow->is_active) && $voucherRow->is_active ? 'checked' : '' ?>>
                                                    <span class="slider round"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <button class="vcdl" data-id="<?= htmlspecialchars($voucherRow->promo_code) ?>">Delete</button>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                        <form class="voucherinput" onsubmit="addVoucher(event)">
                            <h3 class="form-title">Add New Voucher</h3>
                            <input type="hidden" name="action" value="create">

                            <div class="input-group">
                                <label for="promo_code">Promo Code</label>
                                <input type="text" id="promo_code" name="promo_code" placeholder="e.g. SUMMER26" required>
                            </div>

                            <div class="input-group">
                                <label for="discount_price">Discount Price (RM)</label>
                                <input type="number" step="0.01" id="discount_price" name="discount_price" placeholder="e.g. 15.00" required>
                            </div>
                            <button type="submit" class="btn-submit">Submit</button>
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
                                    </div>
                                    <div class="dateStockRow">
                                        <div>
                                            <label for="productReleaseDate">Release Date</label>
                                            <input id="pdtrd" type="date" min="<?= date('Y-m-d') ?>" max="2029-12-31">
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

                                            foreach ($details as $detail) {
                                                echo '<label id="' . $detail . '"></label>';
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
                <?php foreach ($product as $row): ?>
                    <tr>
                        <td>#<?= $row->product_id ?></td>
                        <td><img src="/img/product_Img/<?= $row->image ?>" width="50" height="50" style="border-radius:5px; object-fit:cover;"></td>
                        <td><?= $row->name ?></td>
                        <td><?= $row->category ?></td>
                        <td><?= number_format($row->price, 2) ?></td>
                        <td><?= $row->stock ?></td>

                        <td>
                            <button class="pdtet"
                                data-id="<?= $row->product_id ?>"
                                data-name="<?= $row->name ?>"
                                data-category="<?= $row->category ?>"
                                data-price="<?= $row->price ?>"
                                data-stock="<?= $row->stock ?>"
                                data-description="<?= $row->description ?>"
                                data-date="<?= date('Y-m-d', strtotime($row->release_date)) ?>">Edit</button>
                            <button class="pdtdl" data-id="<?= $row->product_id ?>">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
    <div id="deleterow" class="row">
        <div class="row_container">
            <div id="dltwd">Are you sure you want to delete this?</div>
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
</main>
<?php
$_jsFileName = 'productMaintenance';
include root('_footer.php');
?>