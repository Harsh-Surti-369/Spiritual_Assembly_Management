<?php
session_start();
include('../php/dbConnect.php');

$devotee_id = $_SESSION['devotee_id'];

$category = "pravachan";

$allowed_extensions = array("mp4", "avi", "webm", "mov", "wmv", "mpeg");

$query_center = "SELECT center_id FROM tbl_devotee WHERE devotee_id = ?";
$stmt_center = $conn->prepare($query_center);
$stmt_center->bind_param("i", $devotee_id);
$stmt_center->execute();
$result_center = $stmt_center->get_result();

if ($row_center = $result_center->fetch_assoc()) {
    $center_id = $row_center['center_id'];

    $query = "SELECT * FROM tbl_content WHERE category = ? AND center_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $category, $center_id);
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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #EFECEC;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }

        span {
            color: #666;
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
        <h1>Pravachan List</h1>
        <!-- Example filter options -->
        <select id="duration-filter">
            <option value="short">Short</option>
            <option value="medium">Medium</option>
            <option value="long">Long</option>
        </select>

        <select id="date-filter">
            <option value="asc">Oldest First</option>
            <option value="desc">Newest First</option>
        </select>

        <input type="text" id="singer-filter" placeholder="Search by Singer">

        <div id="media-container">
            <?php
            while ($row = $result->fetch_assoc()) {
                $title = $row['title'];
                $description = $row['description'];
                $file_path = $row['file_path'];
                $speaker = $row['speaker'];

                $file_extension = pathinfo($file_path, PATHINFO_EXTENSION);

                if (in_array(strtolower($file_extension), $allowed_extensions)) {
                    echo "<div class='bhajan mb-3'>";
                    echo "<h3 class='bhajan-title'>$title</h3>";
                    echo "<p class='bhajan-description'>$description</p>";
                    echo "<audio controls class='bhajan-audio'>";
                    echo "<source src='$file_path' type='audio/$file_extension'>";
                    echo "Your browser does not support the audio element.";
                    echo "</audio>";
                    echo "<span>By: $speaker</span>";
                    echo "</div>";
                }
            }
            ?>
        </div>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            // Filter change event handling
            $('#duration-filter, #date-filter, #singer-filter').change(function() {
                // Fetch filtered data based on selected filters
                fetchData();
            });

            // Function to fetch filtered data
            function fetchData() {
                // Get selected filter values
                var duration = $('#duration-filter').val();
                var date = $('#date-filter').val();
                var singer = $('#singer-filter').val();


                $.ajax({
                    url: 'fetch_data.php',
                    method: 'POST',
                    data: {
                        duration: duration,
                        date: date,
                        singer: singer
                    },
                    success: function(response) {
                        // Update UI with filtered data
                    },
                    error: function(xhr, status, error) {
                        // Handle errors
                    }
                });
            }
        });
    </script>
</body>

</html>