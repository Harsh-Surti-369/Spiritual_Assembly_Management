<?php
session_start();
include('../php/dbConnect.php');

function formatFileSize($size)
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $i = 0;
    while ($size >= 1024 && $i < count($units) - 1) {
        $size /= 1024;
        $i++;
    }
    return round($size, 2) . ' ' . $units[$i];
}

function fetchBhajanAudioData()
{
    global $conn;

    $bhajanAudioData = array();

    // Query to fetch bhajan audio data
    $query = "SELECT b.id, b.title, b.speaker, c.center_name, b.file_path
              FROM tbl_content b
              INNER JOIN tbl_center c ON b.center_id = c.center_id
              WHERE b.category = 'Bhajan' AND SUBSTRING(b.file_path, -3) = 'mp3'";

    $result = mysqli_query($conn, $query);

    // Check if query executed successfully
    if ($result) {
        // Fetch data from the result set
        while ($row = mysqli_fetch_assoc($result)) {
            // Calculate file size
            $fileSize = filesize($row['file_path']);
            // Convert file size to human-readable format
            $fileSizeFormatted = formatFileSize($fileSize);

            // Add data to the array
            $bhajanAudioData[] = array(
                'id' => $row['id'],
                'title' => $row['title'],
                'singer' => $row['speaker'],
                'center' => $row['center_name'],
                'audio_url' => $row['file_path'],
                'file_size' => $fileSizeFormatted
            );
        }
    } else {
        // Handle query error
        echo "Error fetching bhajan audio data: " . mysqli_error($conn);
    }

    return $bhajanAudioData;
}

function fetchContentData($category, $extension = null)
{
    global $conn;
    $contentData = array();

    // Query to fetch content data
    $query = "SELECT b.id, b.title, b.speaker, c.center_name, b.file_path, b.category, b.created_at
              FROM tbl_content b
              INNER JOIN tbl_center c ON b.center_id = c.center_id
              WHERE b.category = ?";

    if ($extension !== null) {
        $query .= " AND SUBSTRING(b.file_path, -3) = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $category, $extension);
    } else {
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $category);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if query executed successfully
    if ($result) {
        // Fetch data from the result set
        while ($row = mysqli_fetch_assoc($result)) {
            // Calculate file size
            $fileSize = filesize($row['file_path']);
            // Convert file size to human-readable format
            $fileSizeFormatted = formatFileSize($fileSize);
            // Add data to the array
            $contentData[] = array(
                'id' => $row['id'],
                'title' => $row['title'],
                'speaker' => $row['speaker'],
                'center' => $row['center_name'],
                'file_path' => $row['file_path'],
                'file_size' => $fileSizeFormatted,
                'category' => $row['category'],
                'date' => $row['created_at']
            );
        }
    } else {
        // Handle query error
        echo "Error fetching content data: " . mysqli_error($conn);
    }

    return $contentData;
}

$bhajanAudioData = fetchBhajanAudioData();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Content Management</title>
    <style>
        .bg-primary {
            background-color: #0C2D57 !important;
        }

        .bg-secondary {
            background-color: #EFECEC !important;
        }

        .text-primary {
            color: #0C2D57 !important;
        }

        .text-secondary {
            color: #EFECEC !important;
        }

        .btn-primary {
            background-color: #FC6736 !important;
            border-color: #FC6736 !important;
        }

        .btn-primary:hover {
            background-color: #d65527 !important;
            border-color: #d65527 !important;
        }

        .btn-secondary {
            background-color: #EFECEC !important;
            border-color: #EFECEC !important;
            color: #0C2D57 !important;
        }

        .btn-secondary:hover {
            background-color: #dfdcdc !important;
            border-color: #dfdcdc !important;
            color: #0C2D57 !important;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Centers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Devotees</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="content.php">Content</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Reports</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Settings</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <h1 class="text-primary">Content Management</h1>

        <div class="card bg-secondary mb-4">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-2">Bhajan Audio</h2>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white">Order By</span>
                            <select class="form-select" id="orderBy">
                                <option value="date_asc">Date (Ascending)</option>
                                <option value="date_desc">Date (Descending)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white">Center</span>
                            <select class="form-select" id="centerFilter">
                                <option value="all">All Centers</option>
                                <?php
                                // Fetch center names from the database
                                $query = "SELECT DISTINCT center_name FROM tbl_center";
                                $result = mysqli_query($conn, $query);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '<option value="' . $row['center_name'] . '">' . $row['center_name'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary" id="filterBtn">Apply Filters</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Singer</th>
                            <th>Center</th>
                            <th>Audio</th>
                            <th>Size</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bhajanAudioData as $bhajan) : ?>
                            <tr>
                                <td><?php echo $bhajan['title']; ?></td>
                                <td><?php echo $bhajan['singer']; ?></td>
                                <td><?php echo $bhajan['center']; ?></td>
                                <td>
                                    <audio controls>
                                        <source src="<?php echo $bhajan['audio_url']; ?>" type="audio/mpeg">
                                        Your browser does not support the audio element.
                                    </audio>
                                </td>
                                <td><?php echo $bhajan['file_size']; ?></td>
                                <td>
                                    <a href="edit_bhajan.php?id=<?php echo $bhajan['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="delete_bhajan.php?id=<?php echo $bhajan['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this bhajan?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tab-pane fade" id="pravachanAudio" role="tabpanel" aria-labelledby="pravachanAudio-tab">
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white">Order By</span>
                        <select class="form-select" id="pravachanOrderBy">
                            <option value="date_asc">Date (Ascending)</option>
                            <option value="date_desc">Date (Descending)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white">Center</span>
                        <select class="form-select" id="pravachanCenterFilter">
                            <option value="all">All Centers</option>
                            <?php
                            // Fetch center names from the database
                            $query = "SELECT DISTINCT center_name FROM tbl_center";
                            $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<option value="' . $row['center_name'] . '">' . $row['center_name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary" id="pravachanFilterBtn">Apply Filters</button>
                </div>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Speaker</th>
                        <th>Center</th>
                        <th>Audio</th>
                        <th>Size</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            var bhajanAudioData = <?php echo json_encode($bhajanAudioData); ?>;

            function renderTable(data) {
                var tableBody = '';
                for (var i = 0; i < data.length; i++) {
                    tableBody += '<tr>';
                    tableBody += '<td>' + data[i].title + '</td>';
                    tableBody += '<td>' + data[i].singer + '</td>';
                    tableBody += '<td>' + data[i].center + '</td>';
                    tableBody += '<td><audio controls><source src="' + data[i].audio_url + '" type="audio/mpeg">Your browser does not support the audio element.</audio></td>';
                    tableBody += '<td>' + data[i].file_size + '</td>';
                    tableBody += '<td><a href="edit_bhajan.php?id=' + data[i].id + '" class="btn btn-primary btn-sm">Edit</a> <a href="delete_bhajan.php?id=' + data[i].id + '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this bhajan?\');">Delete</a></td>';
                    tableBody += '</tr>';
                }
                $('tbody').html(tableBody);
            }

            renderTable(bhajanAudioData);

            $('#filterBtn').click(function() {
                var orderBy = $('#orderBy').val();
                var centerFilter = $('#centerFilter').val();

                var filteredData = bhajanAudioData;

                // Filter by center
                if (centerFilter !== 'all') {
                    filteredData = filteredData.filter(function(item) {
                        return item.center === centerFilter;
                    });
                }

                // Order by date
                if (orderBy === 'date_asc') {
                    filteredData.sort(function(a, b) {
                        return new Date(a.date) - new Date(b.date);
                    });
                } else if (orderBy === 'date_desc') {
                    filteredData.sort(function(a, b) {
                        return new Date(b.date) - new Date(a.date);
                    });
                }

                renderTable(filteredData);
            });
        });

        var pravachanAudioData = <?php echo json_encode(fetchContentData('Pravachan', 'mp3')); ?>;

        renderTable(pravachanAudioData, '#pravachanAudio');

        $('#pravachanFilterBtn').click(function() {
            var orderBy = $('#pravachanOrderBy').val();
            var centerFilter = $('#pravachanCenterFilter').val();

            var filteredData = pravachanAudioData;

            // Filter by center
            if (centerFilter !== 'all') {
                filteredData = filteredData.filter(function(item) {
                    return item.center === centerFilter;
                });
            }

            // Order by date
            if (orderBy === 'date_asc') {
                filteredData.sort(function(a, b) {
                    return new Date(a.date) - new Date(b.date);
                });
            } else if (orderBy === 'date_desc') {
                filteredData.sort(function(a, b) {
                    return new Date(b.date) - new Date(a.date);
                });
            }

            renderTable(filteredData, '#pravachanAudio');
        });
    </script>
</body>

</html>