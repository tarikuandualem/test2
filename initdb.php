<?php
$host = '127.0.0.1';
$port = 3306;
$user = 'root';
$pass = '';

$link = mysqli_init();
mysqli_options($link, MYSQLI_OPT_CONNECT_TIMEOUT, 5);
if (!@mysqli_real_connect($link, $host, $user, $pass, '', $port)) {
    die('fail');
}
$sql = file_get_contents('database/schema.sql');
mysqli_multi_query($link, $sql);
do { if ($r = mysqli_store_result($link)) mysqli_free_result($r); } while (mysqli_more_results($link) && mysqli_next_result($link));
echo 'done';
mysqli_close($link);
