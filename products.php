<?php
require_once 'config/config.php';

$query = "SELECT products.*, categories.name as category_name FROM products LEFT JOIN categories ON products.category_id = categories.category_id WHERE 1=1";
$params = [];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $query .= " AND products.name LIKE ?";
    $params[] = '%' . $_GET['search'] . '%';
}

if (isset($_GET['category']) && is_array($_GET['category'])) {
    $placeholders = implode(',', array_fill(0, count($_GET['category']), '?'));
    $query .= " AND products.category_id IN ($placeholders)";
    foreach ($_GET['category'] as $cat) {
        $params[] = $cat;
    }
}

$sort = $_GET['sort'] ?? 'newest';
if ($sort === 'price_low') {
    $query .= " ORDER BY price ASC";
} elseif ($sort === 'price_high') {
    $query .= " ORDER BY price DESC";
} else {
    $query .= " ORDER BY created_at DESC";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

$cat_stmt = $pdo->query("SELECT * FROM categories");
$categories = $cat_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatima Store -products</title>
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/products.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="shop-page-container">

        <aside class="shop-sidebar">
            <form action="products.php" method="GET">
                <div class="filter-section">
                    <h3>Search</h3>
                    <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" style="width:100%; padding:8px;">
                </div>

                <div class="filter-section">
                    <h3>Category</h3>
                    <?php foreach ($categories as $cat): ?>
                        <label class="filter-checkbox">
                            <input type="checkbox" name="category[]" value="<?= $cat['category_id'] ?>" <?php echo (isset($_GET['category']) && in_array($cat['category_id'], $_GET['category'])) ? 'checked' : ''; ?>>
                            <span><?= htmlspecialchars($cat['name']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="filter-section">
                    <h3>Sort By</h3>
                    <div class="custom-select-wrapper">
                        <select class="filter-select" name="sort" onchange="this.form.submit()">
                            <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest Arrivals</option>
                            <option value="price_low" <?php echo $sort === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_high" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                        </select>
                    </div>
                </div>

                <button type="submit" style="width:100%; padding:10px; background:#007bff; color:white; border:none; cursor:pointer;">Apply Filters</button>
            </form>
        </aside>

        <main class="shop-products-area">
            <div class="shop-catalog-grid">

                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $row): ?>
                        <div class="catalog-card">
                            <div class="catalog-img-box">
                                <img src="images/<?= htmlspecialchars($row['image_url']); ?>" alt="<?= htmlspecialchars($row['name']); ?>">
                            </div>
                            <div class="catalog-info">
                                <h3><?= htmlspecialchars($row['name']); ?></h3>

                                <p class="catalog-specs"><?= htmlspecialchars(substr($row['description'], 0, 50)); ?>...</p>

                                <div class="catalog-price-row">
                                    <span class="current-price">$<?= htmlspecialchars(number_format($row['price'], 2)); ?></span>
                                </div>

                                <div class="catalog-actions">
                                    <form action="add_to_cart.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn-cart-submit"><i class="fa-solid fa-cart-shopping"></i> Add to Cart</button>
                                    </form>
                                    <a href="product_detail.php?id=<?= $row['product_id'] ?>" class="btn-details-link" style="text-decoration:none; display:inline-block; text-align:center;">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style='grid-column: 1 / -1; text-align: center; color: #64748b;'>No products available.</p>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>

</html>