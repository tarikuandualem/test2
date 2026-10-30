<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$hosts = ['localhost', '127.0.0.1'];
$users = ['root'];
$passwords = ['', 'root', '123456'];

foreach ($hosts as $host) {
    foreach ($users as $user) {
        foreach ($passwords as $pass) {
            $conn = @new mysqli($host, $user, $pass);
            if ($conn && !$conn->connect_error) {
                echo "Connected as $user@$host with pass: " . ($pass===''?'empty':$pass) . "\n";
                $sql = file_get_contents(__DIR__ . '/database/schema.sql');
                if ($conn->multi_query($sql)) {
                    do {
                        if ($result = $conn->store_result()) {
                            $result->free();
                        }
                    } while ($conn->next_result());
                    echo "Database created successfully!\n";
                    echo "<a href='index.php'>Go to Dashboard</a>";
                } else {
                    echo "Error: " . $conn->error;
                }
                $conn->close();
                exit;
            }
        }
    }
}
echo "Could not connect to MySQL. Check XAMPP MySQL is running.";
