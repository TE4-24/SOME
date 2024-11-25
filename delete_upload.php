<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $index = $_POST['index'];
    $uploads = json_decode(file_get_contents('uploads.json'), true);

    if (isset($uploads[$index])) {
        
        unlink($uploads[$index]['file']);
        
        array_splice($uploads, $index, 1);
        
        file_put_contents('uploads.json', json_encode($uploads));
    }
}

header('Location: view_uploads.php');
exit();
