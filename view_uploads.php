<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>View Uploads</title>
    <style>
        
    </style>
    <script>
        function confirmDelete(title) {
            return confirm(`Are you sure you want to delete the post titled "${title}"?`);
        }
    </script>
</head>
<body>
    <h1>All Uploaded Pictures and Videos</h1>
    <?php
    $uploads = json_decode(file_get_contents('uploads.json'), true);
    if ($uploads === null) {
        echo "<p>Failed to read uploads data.</p>";
        error_log("Failed to read uploads data from uploads.json");
    } else if (empty($uploads)) {
        echo "<p>No uploads found.</p>";
    } else {
        echo "<div class='main'>";
        foreach ($uploads as $index => $upload) {
            echo "<div class='postContainer'>";
            echo "<h2>" . htmlspecialchars($upload['title']) . "</h2>";
            echo "<p>" . htmlspecialchars($upload['description']) . "</p>";
            $fileType = strtolower(pathinfo($upload['file'], PATHINFO_EXTENSION));
            if ($fileType == "mp4" || $fileType == "mov") {
                echo "<video controls class='media'>
                        <source src='" . htmlspecialchars($upload['file']) . "' type='video/" . ($fileType == "mp4" ? "mp4" : "quicktime") . "'>
                      Your browser does not support the video tag.
                      </video>";
            } else {
                echo "<img src='" . htmlspecialchars($upload['file']) . "' alt='" . htmlspecialchars($upload['title']) . "' class='media'>";
            }
            ?>
            <div class='buttonContainer'>
            <?php
            echo "<a href='" . htmlspecialchars($upload['file']) . "' download><button><img src='/assets/download.svg'></img></button></a>";
            echo "<form action='delete_upload.php' method='post' onsubmit='return confirmDelete(\"" . htmlspecialchars($upload['title']) . "\");'>";
            echo "<input type='hidden' name='index' value='$index'>";
            echo "<input type='submit' value='' class='delete'>";
            echo "</form>";
            ?>
            </div>
            <?php
            echo "</div>";
        }
        echo "</div>";
    }
    ?>
    <a href="index.php" class="backLink"><img src='/assets/arrow-turn-backward-stroke-rounded.svg'></img></a>
</body>
</html>
