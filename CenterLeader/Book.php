<?php
session_start();
include('../php/dbConnect.php');

// Fetch books uploaded by the center
$center_id = $_SESSION['center_id'];
$sql = "SELECT * FROM tbl_books WHERE center_id = '$center_id'";
$result = $conn->query($sql);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_id'])) {
    $bookId = $_POST['book_id'];

    // Delete the book from the database
    $sql = "DELETE FROM tbl_books WHERE book_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bookId);

    $response = array();

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Book deleted successfully.';
    } else {
        $response['success'] = false;
        $response['message'] = 'Error: Unable to delete book. Please try again later.';
    }

    $stmt->close();
    echo json_encode($response);
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/CenterLeader/header.css">
    <style>
        .container {
            max-width: 800px;
            background-color: #EFECEC;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .book-card {
            border: 1px solid #dee2e6;
            border-radius: .25rem;
            margin-bottom: 1rem;
        }

        .book-card-header {
            padding: .75rem 1.25rem;
            margin-bottom: 0;
            background-color: rgba(0, 0, 0, 0.03);
            border-bottom: 1px solid #dee2e6;
        }

        .book-card-body {
            padding: 1.25rem;
        }

        /* CSS for Centering Toast */
        .toast-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .toast {
            max-width: 300px;
            /* Adjust the max-width as needed */
        }
    </style>
</head>

<body>
    <header style="margin-bottom: 150px;">
        <?php include('header.php'); ?>
    </header>
    <div class="container mt-3">
        <h1 class="text-center mb-4">Uploaded Books</h1>
        <div class="row" id="bookList">
            <?php while ($row = $result->fetch_assoc()) : ?>
                <div class="col-md-6">
                    <div class="card book-card">
                        <div class="card-header book-card-header">
                            <?php echo $row['title']; ?>
                        </div>
                        <div class="card-body book-card-body">
                            <p class="card-text">Author: <?php echo $row['author']; ?></p>
                            <p class="card-text">Description: <?php echo $row['description']; ?></p>
                            <a href="<?php echo $row['file_path']; ?>" class="btn btn-primary" target="_blank">Open</a>
                            <a href="editBook.php?id=<?php echo $row['book_id']; ?>" class="btn btn-warning">Edit</a>
                            <button class="btn btn-danger delete-book-btn" data-book-id="<?php echo $row['book_id']; ?>">Delete</button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>

        </div>
    </div>
    <!-- Confirmation Toast -->
    <div id="confirmationToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
        <div class="toast-header">
            <strong class="me-auto">Confirm Deletion</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Are you sure you want to delete this book?
            <button type="button" class="btn btn-success btn-sm ms-2" onclick="deleteBook()">Yes</button>
            <button type="button" class="btn btn-danger btn-sm ms-2" data-bs-dismiss="toast">No</button>
        </div>
    </div>

    <!-- Success/Failure Toast -->
    <div id="resultToast" class="toast align-items-center" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true">
        <div class="d-flex">
            <div class="toast-body" id="resultMessage"></div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>

    <!-- Hidden input field for book ID -->
    <input type="hidden" id="bookId" name="book_id">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        // Event delegation for delete buttons
        document.getElementById('bookList').addEventListener('click', function(event) {
            if (event.target.classList.contains('delete-book-btn')) {
                var bookId = event.target.getAttribute('data-book-id');
                confirmDelete(bookId);
            }
        });

        // Function to confirm deletion
        function confirmDelete(bookId) {
            var confirmationToast = new bootstrap.Toast(document.getElementById('confirmationToast'));
            confirmationToast.show();
            // Store book ID in a hidden input field
            document.getElementById('bookId').value = bookId;
        }

        // Function to delete book
        function deleteBook() {
            var bookId = document.getElementById('bookId').value;
            // AJAX request to delete book
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "Book.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var response = JSON.parse(xhr.responseText);
                        var resultToast = new bootstrap.Toast(document.getElementById('resultToast'));
                        var resultMessage = document.getElementById('resultMessage');
                        if (response.success) {
                            resultMessage.innerHTML = response.message;
                            resultToast.classList.add('bg-success');
                        } else {
                            resultMessage.innerHTML = response.message;
                            resultToast.classList.add('bg-danger');
                        }
                        resultToast.show();
                        setTimeout(function() {
                            window.location.reload();
                        }, 5000); // Reload page after 5 seconds
                    } else {
                        console.error("Error:", xhr.status);
                    }
                }
            };
            xhr.send("book_id=" + bookId);
        }
    </script>

</body>

</html>