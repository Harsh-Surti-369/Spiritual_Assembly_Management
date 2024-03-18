<?php
session_start();
include('../php/dbConnect.php');
$devotee_id = $_SESSION['devotee_id'];
$category = "bhajan";
$allowed_extensions = array("mp3", "wav", "ogg");

$query_center = "SELECT center_id FROM tbl_devotee WHERE devotee_id = ?";
$stmt_center = $conn->prepare($query_center);
$stmt_center->bind_param("i", $devotee_id);
$stmt_center->execute();
$result_center = $stmt_center->get_result();

if ($row_center = $result_center->fetch_assoc()) {
    $center_id = $row_center['center_id'];

    // Prepare base SQL query
    $query = "SELECT * FROM tbl_content WHERE category = ? AND center_id = ?";

    $filters = array();
    $bind_values = array();
    $bind_types = "si";
    $orderBy = "";

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
        $singer = $_GET['singer'];
        $filters[] = "speaker = ?";
        $bind_types .= "s";
        $bind_values[] = $singer;
    }

    if (!empty($filters)) {
        $query .= " AND " . implode(" AND ", $filters);
    }


    if (!empty($orderBy)) {
        $query .= " " . $orderBy;
    }

    // Prepare the statement with dynamic parameters
    $stmt = $conn->prepare($query);

    // Bind parameters
    array_unshift($bind_values, $category, $center_id);
    $stmt->bind_param($bind_types, ...$bind_values);

    // Execute the SQL query with applied filters
    $stmt->execute();
    $result = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bhajan List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous" defer></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('../images/bgCanada.png');
        }

        span {
            color: #666;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }

        h1 {
            text-align: center;
            color: #0C2D57;
        }

        .bhajan {
            border-bottom: 1px solid #ddd;
            padding: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .bhajan:hover {
            background-color: #f5f5f5;
        }

        .bhajan:last-child {
            border-bottom: none;
        }

        .bhajan-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #0C2D57;
        }

        .bhajan-description {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
            line-height: 1.5;
        }

        audio {
            width: 100%;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }

        .download-link {
            color: #0C2D57;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .download-link:hover {
            color: #007bff;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container mt-3">
        <h1>Bhajan List</h1>
        <form method="GET" action="">

            <select name="date" id="date-filter">
                <option value="">All Dates</option>
                <option value="asc">Oldest First</option>
                <option value="desc">Newest First</option>
            </select>

            <input type="text" name="singer" id="singer-filter" placeholder="Search by Singer">
            <button type="submit">Apply Filters</button>
        </form>

        <?php

        // Loop through each bhajan and display it
        while ($row = $result->fetch_assoc()) {
            $title = $row['title'];
            $description = $row['description'];
            $file_path = $row['file_path'];

            // Get the file extension
            $file_extension = pathinfo($file_path, PATHINFO_EXTENSION);

            // Check if the file extension is allowed
            if (in_array(strtolower($file_extension), $allowed_extensions)) {
                // Display bhajan details and audio player
                echo "<div class='bhajan mb-3'>";
                echo "<h3 class='bhajan-title'>$title</h3>";
                echo "<p class='bhajan-description'>$description</p>";
                echo "<audio controls class='bhajan-audio'>";
                echo "<source src='$file_path' type='audio/$file_extension'>";
                echo "Your browser does not support the audio element.";
                echo "</audio>";
                echo "</div>";
            }
        }
        ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>

</html>