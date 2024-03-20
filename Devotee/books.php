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
        <form action="" method="get">
            <div class="search-bar">
                <input type="text" class="search-input" placeholder="Search by title, author" name="search">
            </div>
            <!-- Filter options -->
            <div class="filter-options">
                <select class="filter-select" id="genre" name="date">
                    <option value="">All Dates</option>
                    <option value="asc">Oldest Fisrt</option>
                    <option value="desc">Newest First</option>
                </select>

                <button type="submit" class="search-button">Apply Filters</button>
        </form>
    </div>

    <div class="book-grid">
        <?php
        while ($row = $result->fetch_assoc()) {
            $book_id = $row['book_id'];
            $title = $row['title'];
            $description = $row['description'];
            $file_path = $row['file_path'];
            $author = $row['author'];

        ?>
            <div class="book-card">
                <div class="book-info">
                    <h2 class="book-title"><?php echo $title; ?></h2>
                    <p class="book-author"><?php echo $author; ?></p>
                    <p class="book-description"><?php echo $description; ?></p>
                    <div class="button-group">
                        <a href="<?php echo $file_path ?>" target="_blank" class="pdf-link">Open PDF</a>
                        <!-- <button class="wishlist-button btn btn-sm mx-2 my-3" onclick="addToFavourites(<?php echo $id ?>)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bookmarks wishlist-icon" viewBox="0 0 16 16">
                                <path d="M2 4a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v11.5a.5.5 0 0 1-.777.416L7 13.101l-4.223 2.815A.5.5 0 0 1 2 15.5zm2-1a1 1 0 0 0-1 1v10.566l3.723-2.482a.5.5 0 0 1 .554 0L11 14.566V4a1 1 0 0 0-1-1z" />
                                <path d="M4.268 1H12a1 1 0 0 1 1 1v11.768l.223.148A.5.5 0 0 0 14 13.5V2a2 2 0 0 0-2-2H6a2 2 0 0 0-1.732 1" />
                            </svg>
                            Wishlist
                        </button> -->
                    </div>
                    <a onclick="downloadAudio('<?php echo $file_path; ?>')" class="download-link btn btn-sm mx-2 my-3">
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
        function downloadAudio(audioUrl) {
            var a = document.createElement('a');
            a.href = audioUrl;
            a.download = audioUrl.split('/').pop();
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    </script>

</body>

</html>