<?php
session_start();
include 'db.php';
include 'sidebar.php';

// Handle category addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['category_name']);
    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
        $stmt->execute(['name' => $name]);
    }
}

// Handle category deletion
if (isset($_GET['delete_category'])) {
    $category_id = $_GET['delete_category'];

    // Delete associated files first
    $files_stmt = $pdo->prepare("SELECT file_path FROM files WHERE category_id = :category_id");
    $files_stmt->execute(['category_id' => $category_id]);
    $files = $files_stmt->fetchAll();

    foreach ($files as $file) {
        if (file_exists($file['file_path'])) {
            unlink($file['file_path']); // Delete file from server
        }
    }

    $stmt = $pdo->prepare("DELETE FROM files WHERE category_id = :category_id");
    $stmt->execute(['category_id' => $category_id]);

    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
    $stmt->execute(['id' => $category_id]);
}

// Handle category update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_category'])) {
    $category_id = $_POST['category_id'];
    $category_name = trim($_POST['category_name']);

    if (!empty($category_name)) {
        $stmt = $pdo->prepare("UPDATE categories SET name = :name WHERE id = :id");
        $stmt->execute([
            'name' => $category_name,
            'id' => $category_id
        ]);
    }
}

// Fetch categories
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="content">
        <h1>Manage Categories</h1>

        <!-- Add Category Form -->
        <h2>Add Category</h2>
        <form method="post">
            <input type="text" name="category_name" placeholder="Category Name" required>
            <button type="submit" name="add_category">Add Category</button>
        </form>

        <!-- Manage Categories -->
        <h2>Existing Categories</h2>
        <table>
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= htmlspecialchars($category['name']) ?></td>
                        <td>
                            <a href="manage_categories.php?delete_category=<?= $category['id'] ?>" onclick="return confirm('Are you sure you want to delete this category? This will also delete all associated files.');">Delete</a>
                            <!-- You can add an Edit link if needed -->
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
