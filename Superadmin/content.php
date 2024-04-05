<?php
session_start();
include('../php/dbConnect.php');

function fetchContentData($category, $extension)
{
    global $conn;
    $contentData = array();

    // Query to fetch content data
    $query = "SELECT c.id, c.title, c.speaker, c.upload_date, ce.center_name, c.file_path
              FROM tbl_content c
              INNER JOIN tbl_center ce ON c.center_id = ce.center_id
              WHERE c.category = ? AND SUBSTRING(c.file_path, -3) = ?";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $category, $extension);

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if query executed successfully
    if ($result) {
        // Fetch data from the result set
        while ($row = mysqli_fetch_assoc($result)) {
            // Add data to the array
            $contentData[] = array(
                'id' => $row['id'],
                'title' => $row['title'],
                'speaker' => $row['speaker'],
                'upload_date' => $row['upload_date'],
                'center_name' => $row['center_name'],
                'file_path' => $row['file_path']
            );
        }
    } else {
        // Handle query error
        echo "Error fetching content data: " . mysqli_error($conn);
    }

    return $contentData;
}

// Fetch Bhajan Audio Data
$bhajanAudioData = fetchContentData('Bhajan', 'mp3');

// Fetch Pravachan Audio Data
$pravachanAudioData = fetchContentData('Pravachan', 'mp3');

// Fetch Bhajan Video Data
$bhajanVideoData = fetchContentData('Bhajan', 'mp4');

// Fetch Pravachan Video Data
$pravachanVideoData = fetchContentData('Pravachan', 'mp4');

// Function to delete content
function deleteContent($contentId)
{
    global $conn;
    // Prepare and execute the deletion query
    $query = "DELETE FROM tbl_content WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $contentId);
    $success = mysqli_stmt_execute($stmt);
    // Check if deletion was successful
    if ($success) {
        return true;
    } else {
        return false;
    }
}

// Check if the delete request is sent
if (isset($_POST['delete_id'])) {
    $contentId = $_POST['delete_id'];
    $deletionSuccess = deleteContent($contentId);
    // Return JSON response for AJAX handling
    echo json_encode(array('success' => $deletionSuccess));
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Management</title>
    <link rel="shortcut icon" href="../images/Logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        body {
            background-color: #EFECEC;
            font-family: Arial, sans-serif;
        }

        .container {
            margin-top: 50px;
        }

        h2 {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        table {
            border: 3px solid #EFECEC;
            border-top: none;
        }

        .btn-custom {
            background-color: #0C2D57;
            color: #EFECEC;
        }

        .btn-custom:hover {
            background-color: #EFECEC;
            color: #0C2D57;
        }

        .table-custom th,
        .table-custom td {
            border-color: #0C2D57;
        }

        .table-custom th {
            background-color: #0C2D57;
            color: white;
            font-weight: bold;
        }

        .table-custom tbody tr:hover {
            background-color: #FFB0B0;
        }

        .toast-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .btn-confirm-delete {
            margin: auto;
        }
    </style>
</head>

<body>
    <div aria-live="polite" aria-atomic="true" class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
        <div id="successToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Content deleted successfully.
            </div>
        </div>
    </div>
    <?php include('header.php'); ?>
    <div class="container my-5">
        <h1 class="text-center" style="color: #0C2D57; font-weight:900;">Content Management</h1>

        <h2 class="mt-5 text-center py-2 mb-0 " style="font-weight:700; background-color:#FC6736; color: #0C2D57;">Bhajan Audio</h2>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Singer</th>
                        <th>Audio</th>
                        <th>Upload Date</th>
                        <th>Center Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Display Bhajan Audio Data -->
                    <?php foreach ($bhajanAudioData as $content) : ?>
                        <tr>
                            <td><?php echo $content['title']; ?></td>
                            <td><?php echo $content['speaker']; ?></td>
                            <td>
                                <audio controls>
                                    <source src="<?php echo $content['file_path']; ?>">
                                </audio>
                            </td>
                            <td><?php echo $content['upload_date']; ?></td>
                            <td><?php echo $content['center_name']; ?></td>
                            <td>

                                <a class="btn btn-custom m-1 edit-btn" href="edit_content.php?id=<?php echo $content['id']; ?>">Edit</a>
                                <button class="btn btn-danger m-1 delete-btn" data-id="<?php echo $content['id']; ?>">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Repeat the above structure for other content types -->

        <h2 class="mt-2 text-center py-2 mb-0 " style="font-weight:700; background-color:#FC6736; color: #0C2D57;">Pravachan Audio</h2>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Speaker</th>
                        <th>Play</th>
                        <th>Upload Date</th>
                        <th>Center Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Display Bhajan Audio Data -->
                    <?php foreach ($pravachanAudioData as $content) : ?>
                        <tr>
                            <td><?php echo $content['title']; ?></td>
                            <td><?php echo $content['speaker']; ?></td>
                            <td>
                                <audio controls>
                                    <source src="<?php echo $content['file_path']; ?>">
                                </audio>
                            </td>
                            <td><?php echo $content['upload_date']; ?></td>
                            <td><?php echo $content['center_name']; ?></td>
                            <td>

                                <a class="btn btn-custom m-1 edit-btn" href="edit_content.php?id=<?php echo $content['id']; ?>">Edit</a>
                                <button class="btn btn-danger m-1 delete-btn" data-id="<?php echo $content['id']; ?>">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h2 class="mt-5 text-center py-2 mb-0 " style="font-weight:700; background-color:#FC6736; color: #0C2D57;">Bhajan Video</h2>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Singer</th>
                        <th>Play as Audio</th>
                        <th>Upload Date</th>
                        <th>Center Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Display Bhajan Audio Data -->
                    <?php foreach ($bhajanVideoData as $content) : ?>
                        <tr>
                            <td><?php echo $content['title']; ?></td>
                            <td><?php echo $content['speaker']; ?></td>
                            <td>
                                <audio controls>
                                    <source src="<?php echo $content['file_path']; ?>">
                                </audio>
                            </td>
                            <td><?php echo $content['upload_date']; ?></td>
                            <td><?php echo $content['center_name']; ?></td>
                            <td>

                                <a class="btn btn-custom m-1 edit-btn" href="edit_content.php?id=<?php echo $content['id']; ?>">Edit</a>
                                <button class="btn btn-danger m-1 delete-btn" data-id="<?php echo $content['id']; ?>">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h2 class="mt-2 text-center py-2 mb-0 " style="font-weight:700; background-color:#FC6736; color: #0C2D57;">Pravachan Video</h2>
        <div class="table-responsive">
            <table class="table table-custom">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Speaker</th>
                        <th>Play as Audio</th>
                        <th>Upload Date</th>
                        <th>Center Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Display Bhajan Audio Data -->
                    <?php foreach ($pravachanVideoData as $content) : ?>
                        <tr>
                            <td><?php echo $content['title']; ?></td>
                            <td><?php echo $content['speaker']; ?></td>
                            <td>
                                <audio controls>
                                    <source src="<?php echo $content['file_path']; ?>">
                                </audio>
                            </td>
                            <td><?php echo $content['upload_date']; ?></td>
                            <td><?php echo $content['center_name']; ?></td>
                            <td>
                                <a class="btn btn-custom m-1 edit-btn" href="edit_content.php?id=<?php echo $content['id']; ?>">Edit</a>
                                <button class="btn btn-danger m-1 delete-btn" data-id="<?php echo $content['id']; ?>">Delete</button>
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
                <div>Are you sure you want to delete this content?</div>
                <button class="btn btn-danger btn-confirm-delete ms-2 m-3">Delete</button>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const deleteButtons = document.querySelectorAll('.delete-btn');
                const deleteToast = new bootstrap.Toast(document.getElementById('deleteToast'));
                const successToastEl = document.getElementById('successToast');

                deleteButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const contentId = this.dataset.id;

                        // Show delete confirmation toast
                        deleteToast.show();

                        // Confirm deletion on toast confirmation button click
                        const confirmDeleteBtn = document.querySelector('.btn-confirm-delete');
                        confirmDeleteBtn.addEventListener('click', function() {
                            // Send AJAX request to delete content
                            fetch(window.location.href, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                    body: new URLSearchParams({
                                        delete_id: contentId
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    console.log(data); // Log response data
                                    // Show success toast message if deletion was successful
                                    if (data.success) {
                                        const successToast = new bootstrap.Toast(successToastEl);
                                        successToast.show(); // Show the success toast
                                        // Reload the page after showing the success message
                                        setTimeout(() => {
                                            window.location.reload();
                                        }, 2000); // Delay the reload for 2 seconds
                                    } else {
                                        console.error('Error deleting content');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                });
                        });
                    });
                });
            });
        </script>
</body>

</html>