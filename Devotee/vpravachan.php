<?php
session_start();
include('../php/dbConnect.php');

$devotee_id = $_SESSION['devotee_id'];
$category = "pravachan";
$allowed_extensions = array("mp4", "mov", "avi", "wmv", "webm");

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
        $search = '%' . $_GET['search'] . '%'; // Wrap search term in wildcard characters
        $filters[] = "(title LIKE ? OR speaker LIKE ?)"; // Condition to search by title or speaker
        $bind_types .= "ss"; // Add two 's' for two string parameters
        $bind_values[] = $search; // Bind search term for title
        $bind_values[] = $search;
    }

    if (!empty($filters)) {
        $query .= " AND " . implode(" AND ", $filters);
    }
}
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $category, $center_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spiritual Videos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/Devotee/vpravachan.css">
</head>

<body>
    <header>
        <h1>Spiritual Video</h1>
    </header>

    <div class="container">
        <!-- Your existing HTML code -->
        <div id="video-list">
            <?php
            while ($row = $result->fetch_assoc()) {
                $file_path = $row['file_path'];
                $file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

                // Check if the file extension is allowed (video format)
                if (in_array($file_extension, $allowed_extensions)) {
                    $title = $row['title'];
                    $description = $row['description'];
                    $id = $row['id'];
            ?>
                    <!-- Inside the while loop -->
                    <div class='video mb-3'>
                        <h3 class='video-title'><?php echo $title; ?></h3>
                        <p class='video-description'><?php echo $description; ?></p>
                        <button class="btn btn-outline-primary ml-2 my-2" onclick="playVideo(<?php echo $id; ?>, '<?php echo $title; ?>', '<?php echo $description; ?>', '<?php echo $file_path; ?>')">Watch</button>
                    </div>
            <?php
                }
            }
            ?>
        </div>
    </div>

    <script>
        function playVideo(videoId) {
            window.location.href = 'video_player.php?id=' + videoId;
        }
    </script>

    <script>
        function playVideo(id, title, description, filePath) {
            // Redirect to the video player page with video details as URL parameters
            window.location.href = `video_player.php?id=${id}&title=${encodeURIComponent(title)}&description=${encodeURIComponent(description)}&file_path=${encodeURIComponent(filePath)}`;
        }
    </script>
</body>

</html>