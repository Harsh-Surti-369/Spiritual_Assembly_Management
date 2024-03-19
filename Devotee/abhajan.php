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

    $stmt = $conn->prepare($query);

    array_unshift($bind_values, $category, $center_id);
    $stmt->bind_param($bind_types, ...$bind_values);

    $stmt->execute();
    $result = $stmt->get_result();
}
?>
<!-- <!DOCTYPE html>
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

        while ($row = $result->fetch_assoc()) {
            $title = $row['title'];
            $description = $row['description'];
            $file_path = $row['file_path'];

            $file_extension = pathinfo($file_path, PATHINFO_EXTENSION);

            if (in_array(strtolower($file_extension), $allowed_extensions)) {
        ?>

                <div class='bhajan mb-3'>
                    <h3 class='bhajan-title'><? $title ?></h3>
                    <p class='bhajan-description'><? $description ?></p>
                    <audio controls class='bhajan-audio'>
                        <source src='<? $file_path ?>' type='audio/$file_extension'>
                        Your browser does not support the audio element.
                    </audio>
                </div>
        <?php
            }
        }
        ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>

</html> -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Music Player</title>
    <style>
        /* Basic styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            background-image: url("/Spiritual_Assembly_Management/images/bACK\ gROUND\ 01.jpg");
        }

        #player {
            max-width: 400px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        /* Audio player */
        #audio {
            width: 100%;
            margin-bottom: 20px;
        }

        /* Playlist */
        #playlist {
            list-style-type: none;
            padding: 0;
            text-align: left;
        }

        #playlist li {
            cursor: pointer;
            padding: 10px;
            margin: 5px 0;
            background-color: #f0f0f0;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            display: flex;
            justify-content: space-between;
        }

        #playlist li:hover {
            background-color: #e0e0e0;
        }

        /* Play button */
        #playPauseBtn {
            font-size: 1.2em;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #playPauseBtn:hover {
            background-color: #45a049;
        }

        /* Volume control */
        #volumeControl {
            width: 100%;
        }

        /* Progress bar */
        #progressBar {
            width: 100%;
        }

        /* Playlist section */
        #playlistSection {
            margin-top: 20px;
        }

        #playlistSection h2 {
            margin-bottom: 10px;
        }

        .playlistItem {
            margin-bottom: 5px;
        }

        .playlistBtn {
            padding: 5px 10px;
            background-color: #008CBA;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .playlistBtn:hover {
            background-color: #005A79;
        }

        /* Add to playlist button */
        .addToPlaylistBtn {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .addToPlaylistBtn:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div id="player">
        <!-- Audio player -->
        <audio id="audio" controls autoplay></audio>

        <!-- Song information -->
        <div id="songInfo">
            <h2>Title</h2>
            <p id="artist">Artist</p>
            <p id="album">Album</p>
            <p id="time">0:00 / 0:00</p>
            <img id="coverArt" src="" alt="Cover Art">
        </div>

        <!-- Playlist -->
        <ul id="playlist">
            <!-- Songs will be added dynamically -->
        </ul>

        <!-- Play/Pause button -->
        <button id="playPauseBtn">Play/Pause</button>

        <!-- Volume control -->
        <input type="range" id="volumeControl" min="0" max="1" step="0.1" value="1">
        <span>Volume:</span>

        <!-- Progress bar -->
        <input type="range" id="progressBar" min="0" max="100" value="0">
        <span>Timeline:</span><br>

        <!-- Download button -->
        <button id="downloadBtn">Download</button>

        <!-- Share button -->
        <button id="shareBtn">Share</button>

        <!-- Play next button -->
        <button id="playNextBtn">Play Next</button>

        <!-- Add to playlist button -->
        <button id="addToPlaylistBtn">Add to Playlist</button>

        <!-- Logo -->
        <img src="logo.jpg" alt="Logo">
    </div>

    <script>
        // Get references to elements
        const audio = document.getElementById('audio');
        const playlist = document.getElementById('playlist');
        const playPauseBtn = document.getElementById('playPauseBtn');
        const volumeControl = document.getElementById('volumeControl');
        const progressBar = document.getElementById('progressBar');
        const songInfo = document.getElementById('songInfo');
        const titleElement = document.querySelector('#songInfo h2');
        const artistElement = document.getElementById('artist');
        const albumElement = document.getElementById('album');
        const coverArtElement = document.getElementById('coverArt');
        const timeElement = document.getElementById('time');
        const downloadBtn = document.getElementById('downloadBtn');
        const shareBtn = document.getElementById('shareBtn');
        const playNextBtn = document.getElementById('playNextBtn');
        const addToPlaylistBtn = document.getElementById('addToPlaylistBtn');

        // Song data
        const songs = [{
                title: 'Song 1',
                artist: 'Artist 1',
                album: 'Album 1',
                coverArt: 'cover1.jpg',
                url: 'song1.mp3'
            },
            {
                title: 'Song 2',
                artist: 'Artist 2',
                album: 'Album 2',
                coverArt: 'cover2.jpg',
                url: 'song2.mp3'
            }
            // Add more songs as needed
        ];

        let currentSongIndex = 0;

        // Load initial song
        loadSong(songs[currentSongIndex]);

        // Play/pause button functionality
        playPauseBtn.addEventListener('click', function() {
            if (audio.paused) {
                audio.play();
            } else {
                audio.pause();
            }
        });

        // Volume control functionality
        volumeControl.addEventListener('input', function() {
            audio.volume = this.value;
        });

        // Progress bar functionality
        audio.addEventListener('timeupdate', function() {
            progressBar.value = (audio.currentTime / audio.duration) * 100;
            updateTime();
        });

        progressBar.addEventListener('input', function() {
            audio.currentTime = (this.value / 100) * audio.duration;
            updateTime();
        });
        const currentMinutes = Math.floor(audio.currentTime / 60);
        const currentSeconds = Math.floor(audio.currentTime % 60);
        // Update time display
        // function updateTime() {

        //     const durationMinutes = Math.floor(audio.duration / 60);
        //     const durationSeconds = Math.floor(audio.duration % 60);
        //     const currentTimeString = $ {
        //         currentMinutes
        //     }: $ {
        //         currentSeconds < 10 ? '0' : ''
        //     }
        //     $ {
        //         currentSeconds
        //     };
        //     const durationTimeString = $ {
        //         durationMinutes
        //     }: $ {
        //         durationSeconds < 10 ? '0' : ''
        //     }
        //     $ {
        //         durationSeconds
        //     };
        //     timeElement.textContent = $ {
        //         currentTimeString
        //     }
        //     / ${durationTimeString};
        // }

        // Function to load a song
        function loadSong(song) {
            titleElement.textContent = song.title;
            artistElement.textContent = song.artist;
            albumElement.textContent = song.album;
            coverArtElement.src = song.coverArt;
            audio.src = song.url;
            audio.play();
        }

        // Function to play next song
        function playNextSong() {
            currentSongIndex = (currentSongIndex + 1) % songs.length;
            loadSong(songs[currentSongIndex]);
        }

        // Download button functionality
        downloadBtn.addEventListener('click', function() {
            // Implement download functionality here
            // Example: window.location.href = audio.src;
            console.log('Downloading...');
        });

        // Share button functionality
        shareBtn.addEventListener('click', function() {
            // Implement share functionality here
            console.log('Sharing...');
        });

        // Play next button functionality
        playNextBtn.addEventListener('click', function() {
            playNextSong();
        });

        // Add to playlist button functionality
        addToPlaylistBtn.addEventListener('click', function() {
            // Implement add to playlist functionality here
            console.log('Adding to playlist...');
        });
    </script>
</body>

</html>