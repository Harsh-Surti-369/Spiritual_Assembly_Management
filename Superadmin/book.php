<?php
session_start();
include('../php/dbConnect.php');

// Fetch all books from the database
function fetchAllBooks()
{
    global $conn;
    $query = "SELECT b.*, c.center_name FROM tbl_books b INNER JOIN tbl_center c ON b.center_id = c.center_id";
    $result = mysqli_query($conn, $query);
    $books = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $books;
}

// Function to delete a book
function deleteBook($bookId)
{
    global $conn;
    $query = "DELETE FROM tbl_books WHERE book_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $bookId);
    $success = mysqli_stmt_execute($stmt);
    return $success;
}

// Check if delete request is sent
if (isset($_POST['delete_id'])) {
    $bookId = $_POST['delete_id'];
    $deletionSuccess = deleteBook($bookId);
    // Return JSON response for AJAX handling
    echo json_encode(array('success' => $deletionSuccess));
    exit();
}

// Fetch all books
$books = fetchAllBooks();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        /* Add your custom CSS styles here */
    </style>
</head>

<body>
    <?php include('header.php'); ?>
    <style>
        body {
            background-color: #EFECEC;
            font-family: 'Roboto', sans-serif;
        }

        h1 {
            font-family: 'Montserrat', sans-serif;
            color: #0C2D57;
        }

        .btn-custom {
            background-color: #0C2D57;
            color: #EFECEC;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #EFECEC;
            color: #0C2D57;
        }

        .table-custom {
            border-color: #0C2D57;
        }

        .table-custom th {
            background-color: #0C2D57;
            color: #EFECEC;
            font-weight: bold;
        }

        .table-custom tbody tr:hover {
            background-color: #FFB0B0;
        }

        .section-header {
            background-color: #FC6736;
            color: #0C2D57;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
        }

        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
    </head>

    <body>
        <div aria-live="polite" aria-atomic="true" class="toast-container">
            <div id="successToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-success text-white">
                    <strong class="me-auto">Success</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    Book deleted successfully.
                </div>
            </div>
        </div>

        <div class="container mt-5">
            <h1 class="text-center mb-4">Book Management</h1>

            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Center Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book) : ?>
                            <tr>
                                <td><?php echo $book['title']; ?></td>
                                <td><?php echo $book['description']; ?></td>
                                <td><?php echo $book['center_name']; ?></td>
                                <td>
                                    <a class="btn btn-sm btn-custom m-1" href="<?php echo $book['file_path']; ?>" target="_blank">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="edit_book.php?book_id=<?php echo $book['book_id']; ?>" class="btn btn-warning btn-sm m-1">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button class="btn btn-danger btn-sm m-1 delete-btn" data-id="<?php echo $book['book_id']; ?>">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bootstrap Toast for Delete Confirmation -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 5">
            <div id="deleteToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <strong class="me-auto">Delete Confirmation</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    Are you sure you want to delete this book?
                    <button class="btn btn-danger btn-confirm-delete ms-2 m-3">Delete</button>
                </div>
            </div>
        </div>


        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- jQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <!-- Custom JavaScript -->
        <script>
            // JavaScript code for deleting a book
            $(document).ready(function() {
                $('.delete-btn').click(function() {
                    var bookId = $(this).data('id');
                    // Show delete confirmation toast
                    $('#deleteToast').removeClass('hide').toast('show');
                    // Confirm deletion on toast confirmation button click
                    $('.btn-confirm-delete').click(function() {
                        // AJAX request to delete the book
                        $.ajax({
                            url: window.location.href,
                            method: 'POST',
                            data: {
                                delete_id: bookId
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    // Show success toast message
                                    $('.toast-body').text('Book deleted successfully.');
                                    $('#deleteToast').toast('show');
                                    // Reload the page after showing the success message
                                    setTimeout(function() {
                                        window.location.reload();
                                    }, 2000); // Redirect after 2 seconds
                                } else {
                                    // Show error toast message
                                    $('.toast-body').text('Error deleting book. Please try again.');
                                    $('#deleteToast').toast('show');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error:', error);
                            }
                        });
                    });
                });
            });
        </script>
    </body>

</html>