<?php
include 'db.php';

$category_id = $_GET['id'];
$files = $pdo->prepare("SELECT * FROM files WHERE category_id = :category_id");
$files->execute(['category_id' => $category_id]);
$files = $files->fetchAll();

$category_name = $pdo->prepare("SELECT name FROM categories WHERE id = :id");
$category_name->execute(['id' => $category_id]);
$category_name = $category_name->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($category_name) ?> Files</title>
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="container">
        <h1>Files in <?= htmlspecialchars($category_name) ?></h1>
        <ul class="document-list">
            <?php foreach ($files as $file): ?>
                <li>
                    <span><?= htmlspecialchars($file['file_name']) ?></span>
                    <a href="<?= htmlspecialchars($file['file_path']) ?>" class="download-button" download>Download</a>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="index.php" class="back-link">Back to Categories</a>
    </div>
</body>
</html>
