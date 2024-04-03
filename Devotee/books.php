<?php
session_start();
include("../php/dbConnect.php");

$devotee_id = $_SESSION['devotee_id'];
$allowed_extensions = "pdf";

$query_center = "SELECT center_id FROM tbl_devotee WHERE devotee_id = ?";
$stmt_center = $conn->prepare($query_center);
$stmt_center->bind_param("i", $devotee_id);
$stmt_center->execute();
$result_center = $stmt_center->get_result();

if ($row_center = $result_center->fetch_assoc()) {
    $center_id = $row_center['center_id'];

    $query = "SELECT * FROM tbl_books WHERE center_id = ?";
    if (isset($_GET['date']) && !empty($_GET['date'])) {
        $date = $_GET['date'];
        // Apply date filter condition to the SQL query
        if ($date == 'asc') {
            $orderBy = "ORDER BY upload_date ASC";
        } elseif ($date == 'desc') {
            $orderBy = "ORDER BY upload_date DESC";
        }
    }

    if (isset($_GET['singer']) && !empty($_GET['singer'])) {
        $search = '%' . $_GET['search'] . '%'; // Wrap search term in wildcard characters
        $filters[] = "(title LIKE ? OR author LIKE ?)"; // Condition to search by title or speaker
        $bind_types .= "ss"; // Add two 's' for two string parameters
        $bind_values[] = $search; // Bind search term for title
        $bind_values[] = $search;
    }

    if (!empty($filters)) {
        $query .= " AND " . implode(" AND ", $filters);
    }

    if (!empty($orderBy)) {
        $query .= " " . $orderBy;
    }

    $stmt = $conn->prepare($query);

    $stmt->bind_param("i", $center_id);

    $stmt->execute();
    $result = $stmt->get_result();
}


// function addToFavorites($id, $devotee_id, $conn)
// {
//     $query = "INSERT INTO favorites (id, devotee_id) VALUES (?, ?)";
//     $stmt = $conn->prepare($query);
//     $stmt->bind_param("ii", $id, $devotee_id);
//     $stmt->execute();
// }

// if (isset($_POST['add_to_favorites'])) {
//     $id = $_POST['id'];
//     addToFavorites($id, $devotee_id, $conn);
// }
?>
Sure, here's the updated code with the color palette applied:

```html
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        :root {
            --primary-color: #0C2D57;
            --secondary-color: #FC6736;
            --tertiary-color: #FFB0B0;
            --light-color: #EFECEC;
        }

        body {
            background-image: url("/Spiritual_Assembly_Management-main/Devotee/images/0fd3416c.jpeg");
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--secondary-color) !important;
            border-color: var(--secondary-color) !important;
            color: #fff !important;
        }

        .btn-primary:hover {
            background-color: #d65628 !important;
            border-color: #d65628 !important;
        }

        .btn-outline-primary {
            color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color) !important;
            color: #fff !important;
        }

        .text-secondary {
            color: var(--tertiary-color) !important;
        }

        .bg-light {
            background-color: var(--light-color) !important;
        }

        .pagination .page-link {
            color: var(--primary-color) !important;
            background-color: var(--light-color) !important;
            border-color: var(--primary-color) !important;
        }

        .pagination .page-link:hover {
            background-color: var(--primary-color) !important;
            color: #fff !important;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--secondary-color) !important;
            border-color: var(--secondary-color) !important;
            color: #fff !important;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Book List</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="row mb-3">
            <div class="col-md-4">
                <select class="form-select" id="genre">
                    <option value="">All Genres</option>
                    <option value="fiction">Fiction</option>
                    <option value="non-fiction">Non-Fiction</option>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select" id="sort">
                    <option value="title">Sort by Title</option>
                    <option value="author">Sort by Author</option>
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select" id="author">
                    <option value="">All Authors</option>
                    <option value="john-doe">John Doe</option>
                    <option value="jane-doe">Jane Doe</option>
                </select>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <div class="card h-100">
                    <img src="book-cover.jpg" class="card-img-top" alt="Book Cover">
                    <div class="card-body">
                        <h5 class="card-title text-primary">Book Title</h5>
                        <p class="card-text text-secondary">Author Name</p>
                        <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed gravida euismod felis, nec hendrerit urna consequat ut.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <button class="btn btn-outline-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bookmarks me-1" viewBox="0 0 16 16">
                                    <path d="M2 4a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v11.5a.5.5 0 0 1-.777.416L7 13.101l-4.223 2.815A.5.5 0 0 1 2 15.5V4z"></path>
                                    <path d="M4.268 1A2 2 0 0 1 6 0h6a2 2 0 0 1 2 2v11.5a.5.5 0 0 1-.854.354l-2.853-1.855zM2 3a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v11.5a.5.5 0 0 1-.342.474l-2 1.27A.5.5 0 0 1 9.154 16H2a2 2 0 0 1-2-2V3z"></path>
                                </svg>
                                Wishlist
                            </button>
                            <a href="#" class="text-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"></path>
                                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1
                    Download PDF
                </a>
            </div>
        </div>
        <!-- Pagination controls -->
        <div class=" pagination">
                                        <button class="page-link" id="prev-page"> Previous</button>
                                        <span id="current-page">Page 1</span>
                                        <button class="page-link" id="next-page">Next</button>
                        </div>

                    </div>

                    <footer>
                        <p>&copy; 2024 Book List Website</p>
                    </footer>
                    <script>
                        function applyFilters() {
                            // Get selected filter values
                            var genre = document.getElementById('genre').value;
                            var author = document.getElementById('author').value;
                            // Apply filters to book cards
                            var bookCards = document.querySelectorAll('.book-card');
                            bookCards.forEach(function(card) {
                                var cardGenre = card.getAttribute('data-genre');
                                var cardAuthor = card.getAttribute('data-author');
                                if ((genre === '' || cardGenre === genre) && (author === '' || cardAuthor === author)) {
                                    card.style.display = 'block';
                                } else {
                                    card.style.display = 'none';
                                }
                            });
                        }


                        var currentPage = 1;
                        var booksPerPage = 6; // Adjust the number of books per page as needed
                        var totalBooks = 36; // Total number of books (for example)

                        // Function to display books based on pagination
                        function displayBooks(page) {
                            var bookCards = document.querySelectorAll('.book-card');
                            var startIndex = (page - 1) * booksPerPage;
                            var endIndex = startIndex + booksPerPage;
                            for (var i = 0; i < bookCards.length; i++) {
                                if (i >= startIndex && i < endIndex) {
                                    bookCards[i].style.display = 'block';
                                } else {
                                    bookCards[i].style.display = 'none';
                                }
                            }
                            document.getElementById('current-page').textContent = 'Page ' + page;
                        }

                        // Initial display of books on page load
                        displayBooks(currentPage);

                        // Pagination event listeners
                        document.getElementById('prev-page').addEventListener('click', function() {
                            if (currentPage > 1) {
                                currentPage--;
                                displayBooks(currentPage);
                            }
                        });

                        document.getElementById('next-page').addEventListener('click', function() {
                            var totalPages = Math.ceil(totalBooks / booksPerPage);
                            if (currentPage < totalPages) {
                                currentPage++;
                                displayBooks(currentPage);
                            }
                        });
                    </script>

</body>

</html>