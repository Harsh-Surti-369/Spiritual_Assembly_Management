<?php
session_start();

// Check if the leader is not logged in, redirect to the login page
if (!isset($_SESSION['leader_id']) || !isset($_SESSION['center_id'])) {
    header("Location: login.php"); // Redirect to the login page
    exit(); // Stop further execution
}

// Include your database connection
include('../php/dbConnect.php');

// Initialize message variable
$message = '';

// Fetch content details based on the ID passed in the GET parameter
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "SELECT * FROM tbl_content WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $content = $result->fetch_assoc();
    } else {
        $message = "Content not found.";
    }
} else {
    $message = "ID parameter not provided.";
}

// Handle form submission for updating content
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_content'])) {
    // Retrieve form data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $speaker = $_POST['speaker'];
    $category = $_POST['category'];

    // Update content in the database
    $query = "UPDATE tbl_content SET title = ?, description = ?, speaker = ?, category = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $title, $description, $speaker, $category, $id);

    if ($stmt->execute()) {
        // Content updated successfully
        $message = "Content updated successfully!";
    } else {
        $message = "Error updating content.";
    }

    // Return JSON response with message
    echo json_encode(array("message" => $message));

    // Close database connection
    $stmt->close();
    $conn->close();

    // Exit the script to prevent additional HTML content
    exit();
}

// If the script reaches here, it means it's not a POST request, so simply display the HTML content below
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Content</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/CenterLeader/header.css">
    <style>
        body {
            background-image: url("../images/bACK\ gROUND\ 02.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .container {
            margin-top: 150px;
            max-width: 1200px;
            background-color: #EFECEC;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            font-weight: bold;
            color: #0C2D57;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-control {
            border-color: #0C2D57;
        }

        .form-control:focus {
            border-color: #FC6736;
            box-shadow: 0 0 0 0.2rem rgba(252, 103, 54, 0.25);
        }

        select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #0C2D57;
            border-radius: 4px;
            font-size: inherit;
            color: #0C2D57;
        }

        .btn.btn-primary {
            background-color: #0C2D57;
            color: #EFECEC;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            display: block;
            margin: 1.5rem auto 0;
            width: auto;
            transition: background-color 0.3s ease;
        }

        .btn.btn-primary:hover {
            background-color: #0a2548;
        }

        .content-section {
            background-color: #EFECEC;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .content-section h2 {
            color: #0C2D57;
            margin-bottom: 1.5rem;
        }

        .content-item {
            background-color: #ffffff;
            border-radius: 5px;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
        }

        .content-item h5 {
            color: #FC6736;
            margin-bottom: 0.5rem;
        }

        .content-item p {
            color: #0C2D57;
            margin-bottom: 0.5rem;
        }

        .content-item audio,
        .content-item video {
            width: 100%;
        }

        /* Custom styles for toast container */
        .toast-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            /* Ensure toast appears on top */
        }
    </style>
</head>

<body>
    <header style="margin-bottom: 150px;">
        <?php include('header.php'); ?>
    </header>

    <div class="container">
        <h1 class="text-center mb-4">Edit Content</h1>

        <form id="updateForm" method="POST" action="editContent.php?id=<?= $content['id'] ?>" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= $content['title'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= $content['description'] ?></textarea>
            </div>
            <div class="mb-3">
                <label for="speaker" class="form-label">Speaker</label>
                <input type="text" class="form-control" id="speaker" name="speaker" value="<?= $content['speaker'] ?>">
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category" required>
                    <option value="bhajan" <?= ($content['category'] == 'bhajan') ? 'selected' : '' ?>>Bhajan</option>
                    <option value="pravachan" <?= ($content['category'] == 'pravachan') ? 'selected' : '' ?>>Pravachan</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" id="updateBtn" name="update_content">Update</button>
        </form>

    </div>

    <!-- Toast message -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div class="toast align-items-center text-white bg-success" role="alert" aria-live="assertive" aria-atomic="true" id="toastMessage">
            <div class="d-flex">
                <div class="toast-body">Content updated successfully!</div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // AJAX request to update content
        document.getElementById("updateForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Prevent form submission
            var form = event.target;
            var formData = new FormData(form);

            var xhr = new XMLHttpRequest();
            xhr.open("POST", form.action, true);
            xhr.onload = function() {
                if (xhr.status == 200) {
                    console.log(xhr.responseText); // Log the response
                    try {
                        var response = JSON.parse(xhr.responseText);
                        var toastMessage = document.getElementById("toastMessage");
                        if (response.message) {
                            toastMessage.querySelector('.toast-body').innerHTML = response.message;
                            var toast = new bootstrap.Toast(toastMessage);
                            toast.show();
                        }
                    } catch (error) {
                        console.error("Error parsing JSON response:", error);
                    }
                }
            };
            xhr.send(formData);
        });
    </script>
</body>

</html>