<?php
require_once '../config/config.php';
require_once '../includes/admin_check.php';

$action = $_GET['action'] ?? 'list';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_product'])) {
        $name = $_POST['name'];
        $category_id = $_POST['category_id'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];
        $description = $_POST['description'];
        $image = 'NovaBook Pro 16.png'; // Mock image

        // Handle image upload for add product
        $imageFile = $_FILES['image'];
        $imageName = '';
        if ($imageFile['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($imageFile['name'], PATHINFO_EXTENSION);
            $imageName = uniqid('prod_') . '.' . $ext;
            $dest = __DIR__ . '/../images/' . $imageName;
            move_uploaded_file($imageFile['tmp_name'], $dest);
        }
        $stmt = $pdo->prepare("INSERT INTO products (name, category_id, price, stock_quantity, description, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $category_id, $price, $stock, $description, $imageName]);
        header("Location: admin_products.php");
        exit();
    } elseif (isset($_POST['edit_product'])) {
        // Handle image upload for edit product (optional, keep existing if none)
        $imageName = $edit_product['image_url'] ?? '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageName = uniqid('prod_') . '.' . $ext;
            $dest = __DIR__ . '/../images/' . $imageName;
            move_uploaded_file($_FILES['image']['tmp_name'], $dest);
        }
        $stmt = $pdo->prepare("UPDATE products SET name=?, category_id=?, price=?, stock_quantity=?, description=?, image_url=? WHERE product_id=?");
        $stmt->execute([$name, $category_id, $price, $stock, $description, $imageName, $id]);
        header("Location: admin_products.php");
        exit();

        // Duplicate edit handling removed; image upload handled above.
    }
}

if ($action === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE product_id=?");
    $stmt->execute([$id]);
    header("Location: admin_products.php");
    exit();
}

$stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id ORDER BY p.product_id DESC");
$products = $stmt->fetchAll();

$cat_stmt = $pdo->query("SELECT * FROM categories");
$categories = $cat_stmt->fetchAll();

$edit_product = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id=?");
    $stmt->execute([$_GET['id']]);
    $edit_product = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products -Fatima Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin-orders.css">
    <link rel="stylesheet" href="../css/admin-prouducts.css">
    <style>
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .admin-form-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            max-width: 600px;
            margin: 0 auto;
        }

        .btn-submit {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="app-layout" style="display: flex; flex-direction: row; min-height: 100vh">

        <?php include '../includes/sidebar.php'; ?>

        <div class="main-content-wrapper">
            <header class="page-top-header">
                <div class="orders-title">
                    <button type="button" class="sidebar-toggle-btn" aria-label="Open sidebar">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div>
                        <h1>Manage Products</h1>
                        <p style="color: var(--secondary-text); margin: 5px 0 0 0;">Overview of your TechNova inventory and stock levels.</p>
                    </div>
                </div>

                <div class="search-area" style="display: flex; align-items: center; gap: 15px;">
                    <a href="admin_products.php?action=add" class="btn-primary" style="white-space: nowrap; text-decoration:none;">
                        <i class="fa-solid fa-plus"></i> Add New Product
                    </a>
                </div>
            </header>

            <main class="main-content">
                <div class="content-wrapper">

                    <?php if ($action === 'add' || $action === 'edit'): ?>
                        <div class="admin-form-container">
                            <h2><?= $action === 'add' ? 'Add New Product' : 'Edit Product' ?></h2>
                            <form method="POST" action="admin_products.php" enctype="multipart/form-data">
                                <?php if ($action === 'edit'): ?>
                                    <input type="hidden" name="edit_product" value="1">
                                    <input type="hidden" name="id" value="<?= $edit_product['product_id'] ?>">
                                <?php else: ?>
                                    <input type="hidden" name="add_product" value="1">
                                <?php endif; ?>

                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" name="name" required value="<?= htmlspecialchars($edit_product['name'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Category</label>
                                    <select name="category_id" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= $cat['category_id'] ?>" <?= ($edit_product['category_id'] ?? '') == $cat['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Price</label>
                                    <input type="number" step="0.01" name="price" required value="<?= htmlspecialchars($edit_product['price'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Stock</label>
                                    <input type="number" name="stock" required value="<?= htmlspecialchars($edit_product['stock_quantity'] ?? '') ?>">
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" rows="4" required><?= htmlspecialchars($edit_product['description'] ?? '') ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Image</label>
                                    <input type="file" name="image" accept="image/*" <?= $action === 'add' ? 'required' : '' ?>>
                                    <?php if ($action === 'edit' && !empty($edit_product['image_url'])): ?>
                                        <div style="margin-top:5px;">
                                            <img src="../images/<?= htmlspecialchars($edit_product['image_url']) ?>" alt="Current Image" style="max-width:150px;">
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <button type="submit" class="btn-submit"><?= $action === 'add' ? 'Add Product' : 'Update Product' ?></button>
                                <a href="admin_products.php" style="margin-left: 10px; color: #666; text-decoration: none;">Cancel</a>
                            </form>
                        </div>
                    <?php else: ?>

                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>IMAGE</th>
                                        <th>NAME</th>
                                        <th>PRICE</th>
                                        <th>STOCK</th>
                                        <th>CATEGORY</th>
                                        <th>ACTIONS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $p): ?>
                                        <tr>
                                            <td class="text-muted">#TN-<?= $p['product_id'] ?></td>
                                            <td>
                                                <div class="img-container">
                                                    <img src="../images/<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="product-title"><?= htmlspecialchars($p['name']) ?></div>
                                                <div class="product-desc"><?= htmlspecialchars(substr($p['description'], 0, 30)) ?>...</div>
                                            </td>
                                            <td class="font-medium">$<?= number_format($p['price'], 2) ?></td>
                                            <td>
                                                <div class="stock-count <?= $p['stock_quantity'] < 5 ? 'text-red' : '' ?>"><?= $p['stock_quantity'] ?></div>
                                                <?php if ($p['stock_quantity'] < 5): ?>
                                                    <div class="badge-red">LOW STOCK</div>
                                                <?php else: ?>
                                                    <div class="stock-status">AVAILABLE</div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="category-text"><?= htmlspecialchars($p['category_name']) ?></td>
                                            <td>
                                                <div class="actions-col">
                                                    <a href="admin_products.php?action=edit&id=<?= $p['product_id'] ?>" class="btn-icon edit" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                                    <a href="admin_products.php?action=delete&id=<?= $p['product_id'] ?>" class="btn-icon delete" title="Delete" onclick="return confirm('Are you sure?')"><i class="fa-regular fa-trash-can"></i></a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                    <?php endif; ?>

                </div>
            </main>
        </div>
    </div>

    <div class="sidebar-overlay"></div>

    <script src="../js/script.js"></script>
</body>

</html>