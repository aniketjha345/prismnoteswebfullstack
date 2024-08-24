<?php
include 'db.php';

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Management System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Prismatic Hat Presents Notes</h1>
    </header>
    <div class="container">
        <h2>Categories</h2>
        <ul>
            <?php foreach ($categories as $category): ?>
                <li><a href="category.php?id=<?= $category['id'] ?>"><?= $category['name'] ?></a></li>
            <?php endforeach; ?>
        </ul>
        <a href="login.php" class="admin-link">Admin Login</a>
    </div>
</body>
</html>
