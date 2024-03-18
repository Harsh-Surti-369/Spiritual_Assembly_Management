<?php
session_start();
include('../php/dbConnect.php');

// Get the devotee's ID from the session
$devotee_id = $_SESSION['devotee_id'];

// Define the category for bhajans
$category = "bhajan";

// Define the allowed audio file extensions
$allowed_extensions = array("mp3", "wav", "ogg");

// Query to fetch the center ID based on the devotee's ID
$query_center = "SELECT center_id FROM tbl_devotee WHERE devotee_id = ?";
$stmt_center = $conn->prepare($query_center);
$stmt_center->bind_param("i", $devotee_id);
$stmt_center->execute();
$result_center = $stmt_center->get_result();

// Check if the center ID is found
if ($row_center = $result_center->fetch_assoc()) {
    $center_id = $row_center['center_id'];

    // Query to fetch bhajans based on the center ID and category
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
    <!-- Bootstrap CSS -->
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
            /* Light gray on hover */
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

        audio::-webkit-media-controls-timeline {
            background-color: #564141;
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
        <div id="media-container">
            <?php
            // Your PHP code here
            // Assuming $result is your result set from the database query
            // Assuming $allowed_extensions is an array of allowed file extensions

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
    </div>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/js/bootstrap.min.js"></script>
    <script>
        const bhajans = document.querySelectorAll('.bhajan');

        bhajans.forEach(bhajan => {
            const audio = bhajan.querySelector('.bhajan-audio');
            if (audio.querySelector('source')) {
                audio.style.display = 'block';
            }
        });
    </script>
</body>

</html>