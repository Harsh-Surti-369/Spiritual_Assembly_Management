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

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        :root {
            --primary-color: #0C2D57;
            --secondary-color: #FC6736;
            --tertiary-color: #FFB0B0;
            --light-color: #EFECEC;
        }

        body {
            background-image: url("../images/BG-2.png");
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

        /* Additional Styles */
        .container {
            margin-top: 50px;
            /* Adjust the margin-top value according to your needs */
        }

        .card {
            height: 100%;
        }

        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-title,
        .card-text {
            margin-bottom: 15px;
        }

        .card-text:last-child {
            margin-bottom: 0;
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>

    <div class="container">
        <div class="row row-cols-1 row-cols-md-3 g-4 justify-content-center">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title text-primary"><?php echo $row['title']; ?></h5>
                            <p class="card-text text-secondary"><?php echo $row['author']; ?></p>
                            <p class="card-text"><?php echo $row['description']; ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="<?php echo $row['file_path']; ?>" class="text-primary" download>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"></path>
                                        <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"></path>
                                    </svg>
                                    Download PDF
                                </a>
                                <a href="<?php echo $row['file_path']; ?>" target="_blank" class="btn btn-primary">View PDF</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

</body>

</html>