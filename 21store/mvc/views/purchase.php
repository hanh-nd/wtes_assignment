<?php require_once ROOT . DS . 'services' . DS . "UserService.php"; ?>
<?php require_once ROOT . DS . 'services' . DS . "BillService.php"; ?>
<?php require_once ROOT . DS . 'services' . DS . "OrderItemService.php"; ?>
<?php require_once ROOT . DS . 'services' . DS . "ProductService.php"; ?>

<!DOCTYPE html>
<html lang="vi">

<?php
    $status = "Tất cả";
    if (isset($_POST['status'])) {
        $status = $_POST['status'];
    }
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="public/css/footer.css" type="text/css">
    <link rel="stylesheet" href="public/css/nav_bar.css" type="text/css">
    <link rel="stylesheet" href="public/css/purchase.css" type="text/css">
    <title>Purchase</title>
</head>

<body>
    <?php require_once ROOT . DS . 'mvc' . DS . 'views' . DS . 'nav_bar.php'; ?>
    <div class="purchase-container">
        <div class="side-bar">
            <h2>Đơn hàng: <?php echo $status ?></h2>
            <form method="post" name="filter-bill" class="filter-bill">
                <input type="radio" id="all" name="status" value="Tất cả" onChange="autoSubmit();" 
                <?php if($status == "Tất cả") echo 'checked'?>>
                <label for="all"> Tất cả</label><br>
                <input type="radio" id="confirm" name="status" value="Chờ xác nhận" onChange="autoSubmit();"
                <?php if($status == "Chờ xác nhận") echo 'checked'?> >
                <label for="confirm">Chờ xác nhận</label><br>
                <input type="radio" id="ship" name="status" value="Đang giao" onChange="autoSubmit();"
                <?php if($status == "Đang giao") echo 'checked'?> >
                <label for="ship">Đang giao</label><br>
                <input type="radio" id="completed" name="status" value="Đã mua" onChange="autoSubmit();"
                <?php if($status == "Đã mua") echo 'checked'?> >
                <label for="completed">Đã mua</label><br>
            </form>
        </div>
        <div class="bill-container">
            <?php
            $billService = new BillService();
            $orderService = new OrderItemService();
            $allBill = $billService->getFilterBillFormUser($_COOKIE['userId'], $status);
            if(count($allBill) == 0){
                echo "<div class='not-buy'>Không có sản phẩm để hiển thị</div>";
            }
            else
            foreach (array_reverse($allBill) as $bill) {
            ?>
                <div class="infor-bill" id="#username">
                    <header>
                        <h3>Trạng thái đơn hàng</h3>
                        <p><?php echo $bill->getStatus() ?></p>
                        <div class="divider"></div>
                    </header>
                    <?php
                    $items = $orderService->getAllOrderFormBill($bill->getId());
                    foreach ($items as $item) {
                    ?>
                        <?php $product = $item->getProduct(); ?>
                        <a href="<?php echo  "/" . $path_project . "/" . "detail?id=" .  $item->getProductId() ?>" class="purchase-infor">
                            <img src="<?php echo $product->getImageUrl() ?>">
                            <div class="name-price">
                                <p><?php echo $product->getProductName() ?></p>
                                <p class="quantity">X<?php echo $item->getQuantity() ?>
                                    <span><?php echo $product->getFormattedPrice() ?></span>
                                </p>
                            </div>
                        </a>
                        <div class="divider"></div>
                    <?php } ?>

                    <div class="sum-price">
                        <p>Tổng số tiền: &emsp;<span><?php echo $bill->getFormattedTotalAmount() ?></span></p>
                        <div class="list-button">
                            <?php if($bill->getStatus() == "Đã mua"){ ?>
                                <a href="<?php echo  "/" . $path_project . "/" . "detail?id=" .  $items[0]->getProductId() . "#rateProduct" ?>" style="color: white;">
                                    <button id="evaluate" type="button">
                                        Đánh giá
                                    </button>
                                </a>
                            <?php } ?>
                            <a href="<?php echo  "/" . $path_project . "/" . "detail?id=" . $items[0]->getProductId() . "#buy" ?>">
                                <button type="button">Mua lại</button>
                            </a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</body>

<script type="text/javascript" src = <?php echo "/" . $path_project . "/" . "public/js/purchase.js" ?>></script>
