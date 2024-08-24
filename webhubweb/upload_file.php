<?php
session_start();
include 'db.php';
include 'sidebar.php';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $category_id = $_POST['category_id'];
    $file_name = $_FILES['file']['name'];
    $file_path = 'uploads/' . basename($file_name);

    if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
        $stmt = $pdo->prepare("INSERT INTO files (category_id, file_name, file_path) VALUES (:category_id, :file_name, :file_path)");
        $stmt->execute([
            'category_id' => $category_id,
            'file_name' => $file_name,
            'file_path' => $file_path
        ]);
    } else {
        echo "File upload failed.";
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
    <title>Upload File</title>
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="content">
        <h1>Upload File</h1>
        <form method="post" enctype="multipart/form-data">
            <select name="category_id" required>
                <option value="" disabled selected>Select Category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="file" name="file" accept=".pdf" required>
            <button type="submit">Upload File</button>
        </form>
    </div>
</body>
</html>
