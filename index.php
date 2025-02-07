<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Media Upload</title>
    <style>
        div {
            margin: 10px;
            padding: 10px;
            width: 90%;
            max-width: 400px;
            justify-content: center;
            align-items: center;
            display: flex;
            flex-direction: column;
            background-color: rgba(128,128,128,0.5);
            border-radius: 10px;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
            background-image: linear-gradient(45deg, #810636, #cd15e6);
        }
        form {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 10px;
            max-width: 80%;
        }
        a {
            margin-top: 30px;
            background-color: white;
            cursor: pointer;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.2em;
            padding: 5px;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        label {
            margin-top: 10px;
            font-weight: bold;
        }
        .media {
            width: 100%;
            height: auto;
            border-radius: 10px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div>
    <h1>Upload a Picture or Video</h1>
    <form id="uploadForm" action="index.php" method="post" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required><br><br>
        
        <label for="description">Description:</label>
        <textarea name="description" id="description" required></textarea><br><br>
        
        <label for="image">Select image or video to upload:</label>
        <input type="file" name="image" id="image" required><br><br>
        
        <input type="submit" name="submit" value="Upload">
    </form>
    <progress id="progressBar" value="0" max="100" style="width: 100%; display: none;"></progress>
    <a href="view_uploads.php">View All Uploads</a>
    </div>
    <script>
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', this.action, true);
        xhr.upload.onprogress = function(event) {
            if (event.lengthComputable) {
                var percentComplete = (event.loaded / event.total) * 100;
                var progressBar = document.getElementById('progressBar');
                progressBar.style.display = 'block';
                progressBar.value = percentComplete;
            }
        };
        xhr.onload = function() {
            if (xhr.status === 200) {
                alert('Upload complete!');
                location.reload();
            } else {
                alert('Upload failed!');
            }
        };
        xhr.send(formData);
    });
    </script>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        
        if (!is_dir($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                echo "Failed to create uploads directory.";
                error_log("Failed to create uploads directory.");
                $uploadOk = 0;
            }
        }

        
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false || $imageFileType == "mp4" || $imageFileType == "mov") {
            $uploadOk = 1;
        } else {
            echo "File is not an image or video.";
            $uploadOk = 0;
        }

        
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }

        
        if ($_FILES["image"]["size"] > 5000000000) { 
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "mp4" && $imageFileType != "mov") {
            echo "Sorry, only JPG, JPEG, PNG, GIF, MP4 & MOV files are allowed.";
            $uploadOk = 0;
        }

        
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                echo "The file ". htmlspecialchars(basename($_FILES["image"]["name"])). " has been uploaded.";
                echo "<h2>$title</h2>";
                echo "<p>$description</p>";
                echo "<a href='$target_file' download>Download File</a>";
                echo "<img src='" . htmlspecialchars($target_file) . "' alt='" . htmlspecialchars($title) . "' class='media'>";

                
                $uploads = json_decode(file_get_contents('uploads.json'), true);
                if ($uploads === null) {
                    $uploads = [];
                }
                $uploads[] = [
                    'title' => $title,
                    'description' => $description,
                    'file' => $target_file
                ];
                if (file_put_contents('uploads.json', json_encode($uploads)) === false) {
                    echo "Failed to save upload data.";
                    error_log("Failed to save upload data to uploads.json");
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
                error_log("Error uploading file: " . $_FILES["image"]["error"]);
            }
        }
    }
    ?>
</body>
</html>