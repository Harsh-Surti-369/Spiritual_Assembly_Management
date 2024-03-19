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

    $stmt = $conn->prepare($query);

    array_unshift($bind_values, $category, $center_id);
    $stmt->bind_param($bind_types, ...$bind_values);

    $stmt->execute();
    $result = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pravachan List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('../images/bgCanada.png');
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background-color: #FFFFFF;
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

        .audio-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
        }

        .audio-control-btn {
            margin: 0 10px;
            background-color: #FC6736;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .play {
            margin: 0 10px;
            background-color: #0C2D57;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .audio-control-btn:hover {
            background-color: #FFB0B0;
        }

        .progress-bar-container {
            width: 80%;
            margin: 20px auto;
        }

        .progress-bar {
            height: 10px
        }



        .download-btn {
            margin: 0 10px;
            background-color: #0C2D57;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        .download-btn:hover {
            background-color: #FFB0B0;
        }
    </style>
</head>

<body>
    <div class="container mt-3">
        <h1>Pravachan List</h1>
        <div id="bhajan-list">
            <?php
            while ($row = $result->fetch_assoc()) {
                $title = $row['title'];
                $description = $row['description'];
                $file_path = $row['file_path'];
            ?>
                <div class='bhajan mb-3'>
                    <h3 class='bhajan-title'><?php echo $title; ?></h3>
                    <p class='bhajan-description'><?php echo $description; ?></p>
                    <audio class='bhajan-audio'>
                        <source src='<?php echo $file_path; ?>' type='audio/mpeg'>
                        Your browser does not support the audio element.
                    </audio>
                    <button class='download-btn' onclick="downloadAudio('<?php echo $file_path; ?>')">Download</button>
                </div>
            <?php } ?>
        </div>
        <div class="audio-controls">
            <button id="prev-btn" class="audio-control-btn">&#10094; Previous</button>
            <button id="play-pause-btn" class="audio-control-btn play">► Play</button>
            <button id="next-btn" class="audio-control-btn">Next &#10095;</button>
        </div>
        <div class="progress-bar-container">
            <div class="progress-bar"></div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            var audioElements = $('audio.bhajan-audio');

            // Play/pause button click event
            $('#play-pause-btn').click(function() {
                var currentAudio = audioElements.filter(function() {
                    return !this.paused;
                })[0];

                if (!currentAudio) {
                    currentAudio = audioElements.eq(0)[0];
                }

                if (currentAudio.paused) {
                    currentAudio.play();
                    $(this).text('|| Pause');
                } else {
                    currentAudio.pause();
                    $(this).text('► Play');
                }
            });

            // Previous button click event
            $('#prev-btn').click(function() {
                var currentAudio = audioElements.filter(function() {
                    return !this.paused;
                })[0];

                var currentIndex = audioElements.index(currentAudio);
                var prevIndex = currentIndex - 1;
                if (prevIndex < 0) {
                    prevIndex = audioElements.length - 1;
                }

                var prevAudio = audioElements.eq(prevIndex);
                currentAudio.pause();
                currentAudio.currentTime = 0;
                prevAudio.trigger('play');
            });

            // Next button click event
            $('#next-btn').click(function() {
                var currentAudio = audioElements.filter(function() {
                    return !this.paused;
                })[0];

                var currentIndex = audioElements.index(currentAudio);
                var nextIndex = currentIndex + 1;
                if (nextIndex >= audioElements.length) {
                    nextIndex = 0;
                }

                var nextAudio = audioElements.eq(nextIndex);
                currentAudio.pause();
                currentAudio.currentTime = 0;
                nextAudio.trigger('play');
            });

            // Update progress bar as audio is played
            audioElements.on('timeupdate', function() {
                var audio = $(this)[0];
                var progress = (audio.currentTime / audio.duration) * 100;
                $('.progress-bar').css('width', progress + '%');
            });

            // Change audio playback position when progress bar is clicked
            $('.progress-bar-container').on('click', function(e) {
                var audio = audioElements.filter(function() {
                    return !this.paused;
                })[0];

                var offset = $(this).offset();
                var xPos = e.pageX - offset.left;
                var progress = (xPos / $(this).width()) * 100;
                var seekTime = (audio.duration * (progress / 100));
                audio.currentTime = seekTime;
            });
        });

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