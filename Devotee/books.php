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
    $stmt = $conn->prepare($query);

    $stmt->bind_param("i", $center_id);

    $stmt->execute();
    $result = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/devotee/books.css">
    <title>Book List</title>

</head>

<body>

    <header>
        <h1>Book List</h1>
    </header>

    <div class="container">

        <div class="search-bar">
            <input type="text" class="search-input" placeholder="Search books...">
            <button class="search-button">Search</button>
        </div>
        <!-- Filter options -->
        <div class="filter-options">
            <select class="filter-select" id="genre">
                <option value="">All Genres</option>
                <option value="fiction">Fiction</option>
                <option value="non-fiction">Non-Fiction</option>
                <!-- Add more genres as needed -->
            </select>

            <div>
                <label for="sort">Sort by:</label>
                <select class="sort-select" id="sort">
                    <option value="title">Title</option>
                    <option value="author">Author</option>
                    <!-- Add more sorting options as needed -->
                </select>
            </div>

            <select class="filter-select" id="author">
                <option value="">All Authors</option>
                <option value="john-doe">John Doe</option>
                <option value="jane-doe">Jane Doe</option>
                <!-- Add more authors as needed -->
            </select>
        </div>

        <div class="book-grid">
            <!-- Example book card -->
            <?php
            while ($row = $result->fetch_assoc()) {
                $book_id = $row['book_id'];
                $title = $row['title'];
                $description = $row['description'];
                $file_path = $row['file_path'];
                $author = $row['author'];

            ?>
                <div class="book-card">
                    <img src="book-cover.jpg" alt="Book Cover" class="book-cover">
                    <div class="book-info">
                        <h2 class="book-title"><?php echo $title; ?></h2>
                        <p class="book-author"><?php echo $author; ?></p>
                        <p class="book-description"><?php echo $description; ?></p>
                        <div class="button-group">
                            <a href="<?php echo $file_path ?>" target="_blank" class="pdf-link">Open PDF</a>
                            <button class="wishlist-button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bookmarks wishlist-icon" viewBox="0 0 16 16">
                                    <path d="M2 4a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v11.5a.5.5 0 0 1-.777.416L7 13.101l-4.223 2.815A.5.5 0 0 1 2 15.5zm2-1a1 1 0 0 0-1 1v10.566l3.723-2.482a.5.5 0 0 1 .554 0L11 14.566V4a1 1 0 0 0-1-1z" />
                                    <path d="M4.268 1H12a1 1 0 0 1 1 1v11.768l.223.148A.5.5 0 0 0 14 13.5V2a2 2 0 0 0-2-2H6a2 2 0 0 0-1.732 1" />
                                </svg>
                                Wishlist
                            </button>
                        </div>
                        <span class="favorite-icon">&#9733;</span>
                        <a href="<?php echo $file_path; ?>" target="_blank" class="download-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download download-icon" viewBox="0 0 16 16">
                                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                                <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z" />
                            </svg>
                            Download PDF
                        </a>

                    </div>
                </div>
            <?php
            }
            ?>
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