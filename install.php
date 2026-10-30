<?php
// Database installer
error_reporting(0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install Database - Cylinder Workshop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Car Cylinder Maintenance Workshop</h4>
                        <p class="mb-0">Database Installation</p>
                    </div>
                    <div class="card-body">
                        <?php
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            $dbHost = $_POST['db_host'] ?? '127.0.0.1';
                            $dbUser = $_POST['db_user'] ?? 'root';
                            $dbPass = $_POST['db_pass'] ?? '';
                            
                            $conn = @mysqli_connect($dbHost, $dbUser, $dbPass);
                            if (!$conn) {
                                echo '<div class="alert alert-danger">Connection failed: ' . htmlspecialchars(mysqli_connect_error()) . '</div>';
                                echo '<a href="install.php" class="btn btn-secondary">Try Again</a>';
                            } else {
                                @mysqli_query($conn, "SET SESSION sql_mode=''");
                                $sql = @file_get_contents(__DIR__ . '/database/schema.sql');
                                if ($sql && @mysqli_multi_query($conn, $sql)) {
                                    do {
                                        if ($result = @mysqli_store_result($conn)) {
                                            @mysqli_free_result($result);
                                        }
                                    } while (@mysqli_next_result($conn));
                                    echo '<div class="alert alert-success">Database installed successfully!</div>';
                                    echo '<a href="index.php" class="btn btn-primary">Go to Dashboard</a>';
                                } else {
                                    echo '<div class="alert alert-danger">Error: ' . htmlspecialchars(mysqli_error($conn)) . '</div>';
                                    echo '<a href="install.php" class="btn btn-secondary">Try Again</a>';
                                }
                                @mysqli_close($conn);
                            }
                        } else {
                        ?>
                        <p>Enter your MySQL credentials to create the database and tables.</p>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Host</label>
                                <input type="text" name="db_host" class="form-control" value="127.0.0.1" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="db_user" class="form-control" value="root" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="db_pass" class="form-control" value="">
                                <small class="text-muted">Leave empty if no password</small>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Install Database</button>
                        </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
