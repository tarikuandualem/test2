<?php
error_reporting(0);
ini_set('display_errors', 0);

$conn = @mysqli_connect('localhost', 'root', '', 'mysql');
if (!$conn) {
    $conn = @mysqli_connect('127.0.0.1', 'root', '', 'mysql');
}
if (!$conn) {
    die("Cannot connect. Start MySQL in XAMPP Control Panel.");
}

$sql = file_get_contents(__DIR__ . '/database/schema.sql');
if (mysqli_multi_query($conn, $sql)) {
    do {
        if ($result = mysqli_store_result($conn)) {
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($conn));
    echo "SUCCESS! Database created. <a href='index.php'>Open App</a>";
} else {
    echo "Error: " . mysqli_error($conn);
}
mysqli_close($conn);
