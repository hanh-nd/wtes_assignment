<?php
	require_once ROOT . DS . 'services' . DS . 'ProductService.php';
	require_once ROOT . DS . 'services' . DS . 'BrandService.php';

    $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "/";
    $url_components = parse_url($url);

    $page = 1;
    $searchQuery = '';
    $sortBy = 'created_at';
    $orderBy = 'desc';
    $brandFilters = array();

    $brandService = new BrandService();
    $brands = $brandService->getAllBrands();
    
    if (isset($url_components['query'])) {
        parse_str($url_components['query'], $params);
        if (isset($params['page'])) {
            $page = $params['page'];
        }

        if (isset($params['q'])) {
            $searchQuery = $params['q'];
        }

        if (isset($params['sort'])) {
            $sort = explode(':', $params['sort']);
            $sortBy = $sort[0];
            $orderBy = $sort[1];
        }

        if (isset($params['filter'])) {
            $brandFilters = $params['filter'];
        }

        if (isset($params['brand_id'])) {
            array_push($brandFilters, $params['brand_id']);
        }


    }


?>

<!DOCTYPE html>
<html lang="vi">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="public/css/products.css" type="text/css">
		<link rel="stylesheet" href="public/css/footer.css" type="text/css">
		<link rel="stylesheet" href="public/css/nav_bar.css" type="text/css">
		<title>Products</title>
	</head>
<body>
	<?php require_once ROOT . DS . 'mvc' . DS . 'views' . DS . 'nav_bar.php'; ?>
    <div class="product-list-container">
        <div class="product-list-content">
            <div class="content-header">
                Danh sách sản phẩm: Tổng <?php 
                    $service = new ProductService();
                    $filters = new stdClass();
                    $filters->searchQuery = $searchQuery;
                    $filters->brandFilters = $brandFilters;
                    $totalRows = $service->getTotalProducts($filters);
                    $totalPages = ceil($totalRows / 8);
                    echo "<span>$totalRows</span> sản phẩm";
                ?>
            </div>
            <div class="filters">
                <form class="filters-form" action="" method="GET" >
                    <?php if (isset($searchQuery)) { ?>
                        <input type="hidden" name="q" value="<?= $searchQuery ?>" />
                    <?php } ?>

                    <div class="multi-select">
                        <div class="select-box" onclick="showCheckboxes()">
                            <select>
                                <option>Lọc theo nhãn hàng</option>
                            </select>
                            <div class="over-select"></div>
                        </div>
                        <div class="checkbox-item-wrapper" id="checkboxes">
                                <input 
                                    type="checkbox" 
                                    <?php if (empty($brandFilters)) echo 'checked' ?>
                                    onclick="toggleCheckbox(this)"
                                />
                                Chọn/Bỏ chọn tất cả
                            <?php
                                foreach ($brands as $brand) {
                            ?>
                            <label for="<?php echo $brand->getId() ?>">
                                <input 
                                    type="checkbox"
                                    class="brand-checkbox-item" 
                                    name="filter[]" 
                                    value="<?php echo $brand->getId() ?>" 
                                    <?php if (empty($brandFilters) || in_array($brand->getId(), $brandFilters)) echo 'checked' ?>
                                />
                                <?php echo " " . $brand->getBrandName() ?>
                            </label>
                            <?php
                                }
                            ?>
                        </div>
                    </div>
                    
                    <select class="" name="sort">
                        <option disabled <?php if ($sortBy . ":" . $orderBy == "created_at:desc") echo 'selected'?>>
                            Sắp xếp
                        </option>
                        <option value="price:desc" <?php if ($sortBy . ":" . $orderBy == "price:desc") echo 'selected'?>>
                            Giá giảm dần
                        </option>
                        <option value="price:asc" <?php if ($sortBy . ":" . $orderBy == "price:asc") echo 'selected'?>>
                            Giá tăng dần
                        </option>
                        <option value="product_name:asc" <?php if ($sortBy . ":" . $orderBy == "product_name:asc") echo 'selected'?>>
                            Tên A-Z
                        </option>
                        <option value="product_name:desc" <?php if ($sortBy . ":" . $orderBy == "product_name:desc") echo 'selected'?>>
                            Tên Z-A
                        </option>
                    </select>
                    <input class="submit-btn" type="submit" value="Lọc" />
                </form>
            </div>
            <div class="product-list">
                <?php
                    $products = $service->getPaginatedProducts($page, 8, $filters, $sortBy, $orderBy);
                    foreach($products as $product) {
                ?>
                    <div class="product-item-row">
                        <a href="<?php echo  "/" . $path_project . "/" . "detail?id=" .  $product->getId() ?>">
                            <div class="image-wrapper">
                                <img src=<?php echo $product->getImageUrl() ?> width="100%">
                                <div class="see-detail">Xem chi tiết</div>
                            </div>
                            <p><?php echo $product->getProductName() ?></p>
                            <p class="price-pr"><?php echo $product->getFormattedPrice() ?></p>
                        </a>
                    </div>
                <?php
                    }
                ?>	
            </div>
            <div class="pagination">
                <a 
                    style="<?php if ($page <= 1) echo 'display: none' ?>" 
                    href="<?php 
                        $href = 'products?page=' . $page - 1;
                        if ($searchQuery) {
                            $href = $href . '&q=' . $searchQuery;
                        }
                        if ($sortBy) {
                            $href = $href . '&sort=' . $sortBy . '%3A' . $orderBy;
                        }
                        echo $href;
                    ?>"
                >
                    &laquo;
                </a>
                <?php
                    for ($i = 1; $i <= $totalPages; $i++) {
                ?>
                    <a 
                        class="<?php if ($page == $i) echo 'active' ?>" 
                        href="<?php 
                            $href = 'products?page=' . $i;
                            if ($searchQuery != '') {
                                $href = $href . '&q=' . $searchQuery;
                            }
                            if ($sortBy != 'created_at') {
                                $href = $href . '&sort=' . $sortBy . '%3A' . $orderBy;
                            }
                            echo $href;
                        ?>"
                    >
                        <?php echo $i ?>
                    </a>
                <?php
                    }
                ?>
                <a 
                    style="<?php if ($page >= $totalPages) echo 'display: none' ?>" 
                    href="<?php
                        $href = 'products?page=' . $page + 1;
                        if ($searchQuery) {
                            $href = $href . '&q=' . $searchQuery;
                        }
                        if ($sortBy) {
                            $href = $href . '&sort=' . $sortBy . '%3A' . $orderBy;
                        }
                        echo $href;
                    ?>"
                >
                    &raquo;
                </a>
            </div>
        </div>
	</div>
    <script type="text/javascript" src = <?php echo "/" . $path_project . "/" . "public/js/products.js" ?>></script>