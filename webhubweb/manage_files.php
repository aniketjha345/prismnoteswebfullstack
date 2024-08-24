<?php
session_start();
include 'db.php';
include 'sidebar.php';

// Function to display messages
function showMessage($message) {
    echo "<div class='alert'>$message</div>";
}

// Handle file deletion
if (isset($_GET['delete_file'])) {
    $file_id = intval($_GET['delete_file']); // Ensure file_id is an integer
    $file_stmt = $pdo->prepare("SELECT file_path FROM files WHERE id = :id");
    $file_stmt->execute(['id' => $file_id]);
    $file = $file_stmt->fetch();

    if ($file) {
        if (unlink($file['file_path'])) {
            $stmt = $pdo->prepare("DELETE FROM files WHERE id = :id");
            $stmt->execute(['id' => $file_id]);
            showMessage("File deleted successfully.");
        } else {
            showMessage("Failed to delete file. Please try again later.");
        }
    } else {
        showMessage("File not found.");
    }
}

// Handle file update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_file'])) {
    $file_id = intval($_POST['file_id']); // Ensure file_id is an integer
    $file_name = htmlspecialchars($_POST['file_name'], ENT_QUOTES, 'UTF-8');
    $current_file_path = $_POST['current_file_path'];

    // Check if a new file is being uploaded
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $new_file_name = basename($_FILES['file']['name']);
        $new_file_path = 'uploads/' . $new_file_name;

        // Validate file type (for example, only PDFs)
        $file_type = mime_content_type($_FILES['file']['tmp_name']);
        if ($file_type === 'application/pdf') {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $new_file_path)) {
                // Remove old file if a new one is successfully uploaded
                if (file_exists($current_file_path)) {
                    unlink($current_file_path);
                }
                $current_file_path = $new_file_path;
                showMessage("File updated successfully.");
            } else {
                showMessage("File upload failed.");
            }
        } else {
            showMessage("Invalid file type. Only PDF files are allowed.");
        }
    } else {
        // Handle cases where no new file is uploaded but file name is changed
        showMessage("File name updated successfully.");
    }

    // Update the file information in the database
    $stmt = $pdo->prepare("UPDATE files SET file_name = :file_name, file_path = :file_path WHERE id = :id");
    $stmt->execute([
        'file_name' => $file_name,
        'file_path' => $current_file_path,
        'id' => $file_id
    ]);
}

// Fetch files
$files = $pdo->query("SELECT f.id, f.file_name, c.name AS category, f.file_path FROM files f JOIN categories c ON f.category_id = c.id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Files</title>
    <link rel="stylesheet" href="admin-styles.css">
    <style>
        .alert {
            padding: 15px;
            background-color: #f44336;
            color: white;
            margin-bottom: 15px;
        }
        .close-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="content">
        <h1>Manage Files</h1>

        <?php
        if (isset($_SESSION['message'])) {
            echo "<div class='alert'>{$_SESSION['message']}</div>";
            unset($_SESSION['message']);
        }
        ?>

        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>File Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($files as $file): ?>
                    <tr>
                        <td><?= htmlspecialchars($file['category']) ?></td>
                        <td><?= htmlspecialchars($file['file_name']) ?></td>
                        <td>
                            <a href="manage_files.php?delete_file=<?= $file['id'] ?>" onclick="return confirm('Are you sure you want to delete this file?');">Delete</a>
                            |
                            <a href="#" onclick="editFile(<?= $file['id'] ?>, '<?= htmlspecialchars($file['file_name']) ?>', '<?= htmlspecialchars($file['file_path']) ?>')">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Edit File Modal -->
        <div id="editFileModal" style="display: none;">
            <div class="modal-content">
                <span class="close" onclick="closeModal('editFileModal')">&times;</span>
                <h2>Edit File</h2>
                <form method="post" enctype="multipart/form-data" id="editFileForm">
                    <input type="hidden" name="file_id" id="file_id">
                    <input type="text" name="file_name" id="file_name" placeholder="File Name" required>
                    <input type="file" name="file" id="file_input" accept=".pdf">
                    <input type="hidden" name="current_file_path" id="current_file_path">
                    <button type="submit" name="update_file">Update File</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editFile(id, name, path) {
            document.getElementById('file_id').value = id;
            document.getElementById('file_name').value = name;
            document.getElementById('current_file_path').value = path;
            document.getElementById('editFileModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Close the modal when clicking outside of it
        window.onclick = function(event) {
            let modal = document.getElementById('editFileModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
