<?php
include 'db.php';

// If editing
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $result = $conn->query("SELECT * FROM Products WHERE product_id=$id");
    $product = $result->fetch_assoc();
} else {
    $product = ['name' => '', 'description' => '', 'price' => '', 'stock_quantity' => '', 'reorder_level' => '', 'image_path' => ''];
}

// Save product
if (isset($_POST['save'])) {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock_quantity'];
    $reorder = $_POST['reorder_level'];

    // Handle image upload
    $image_path = $product['image_path'] ?? ''; // Keep existing image if no new upload

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'assets/images/products/';

        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $file_extension;
        $target_file = $upload_dir . $filename;

        // Validate image file
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        if (
            in_array(strtolower($file_extension), $allowed_types) &&
            $_FILES['product_image']['size'] <= $max_size
        ) {

            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
                $image_path = $target_file;

                // Delete old image if exists and we're updating
                if (isset($_GET['id']) && !empty($product['image_path']) && file_exists($product['image_path'])) {
                    unlink($product['image_path']);
                }
            }
        }
    }

    if (isset($_GET['id'])) {
        $stmt = $conn->prepare("UPDATE Products SET name=?, description=?, price=?, stock_quantity=?, reorder_level=?, image_path=? WHERE product_id=?");
        $stmt->bind_param("ssdiisi", $name, $desc, $price, $stock, $reorder, $image_path, $id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO Products (name, description, price, stock_quantity, reorder_level, image_path) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiis", $name, $desc, $price, $stock, $reorder, $image_path);
        $stmt->execute();
    }

    header("Location: manage_products.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title><?= isset($_GET['id']) ? "Edit Product" : "Add Product" ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .container {
            width: 90%;
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #4a148c;
            margin-bottom: 20px;
        }

        form label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        form input[type="text"],
        form input[type="number"],
        form input[type="file"],
        form textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        form textarea {
            height: 80px;
        }

        button {
            background: #6a1b9a;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #8e24aa;
        }

        .image-preview {
            max-width: 200px;
            max-height: 200px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 5px;
        }

        .current-image {
            margin: 10px 0;
        }

        .current-image img {
            max-width: 200px;
            max-height: 200px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2><?= isset($_GET['id']) ? "Edit Product" : "Add Product" ?></h2>
        <form method="post" enctype="multipart/form-data">
            <label>Product Name</label>
            <input type="text" name="name" placeholder="Product Name" value="<?= htmlspecialchars($product['name']) ?>"
                required>

            <label>Description</label>
            <textarea name="description"
                placeholder="Description"><?= htmlspecialchars($product['description']) ?></textarea>

            <label>Price</label>
            <input type="number" step="0.01" name="price" placeholder="Price" value="<?= $product['price'] ?>" required>

            <label>Stock Quantity</label>
            <input type="number" name="stock_quantity" placeholder="Stock Quantity"
                value="<?= $product['stock_quantity'] ?>" required>

            <label>Reorder Level</label>
            <input type="number" name="reorder_level" placeholder="Reorder Level"
                value="<?= $product['reorder_level'] ?>" required>

            <label>Product Image</label>
            <input type="file" name="product_image" id="product_image" accept="image/*">

            <!-- Image preview -->
            <div id="imagePreview" style="display: none;">
                <p>New Image Preview:</p>
                <img id="preview" class="image-preview" src="#" alt="Image Preview">
            </div>

            <!-- Current image display (for edit mode) -->
            <?php if (isset($_GET['id']) && !empty($product['image_path'])): ?>
                <div class="current-image">
                    <p>Current Image:</p>
                    <img src="<?= htmlspecialchars($product['image_path']) ?>" alt="Current Product Image">
                </div>
            <?php endif; ?>

            <button type="submit" name="save"><?= isset($_GET['id']) ? "Update Product" : "Add Product" ?></button>
        </form>
    </div>

    <script>
        // Image preview functionality
        document.getElementById('product_image').addEventListener('change', function (e) {
            const preview = document.getElementById('preview');
            const previewContainer = document.getElementById('imagePreview');

            if (this.files && this.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                }

                reader.readAsDataURL(this.files[0]);
            } else {
                previewContainer.style.display = 'none';
            }
        });
    </script>
</body>

</html>