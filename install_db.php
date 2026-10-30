<?php
error_reporting(0);
$host = '127.0.0.1';
$port = 3007;
$user = 'root';
$pass = '';

$link = mysqli_init();
if (!@mysqli_real_connect($link, $host, $user, $pass, '', $port)) {
    die('Cannot connect on port 3007');
}
$sql = file_get_contents(__DIR__ . '/database/schema.sql');
if (@mysqli_multi_query($link, $sql)) {
    do {
        if ($r = @mysqli_store_result($link)) {
            @mysqli_free_result($r);
        }
    } while (@mysqli_next_result($link));
    echo 'Database installed successfully. <a href="index.php">Go to Dashboard</a>';
} else {
    echo 'Error: ' . mysqli_error($link);
}
mysqli_close($link);
