<?php
require_once __DIR__ . '/config/auth.php';

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$loginError = '';
$registerError = '';
$registerSuccess = '';
$activeTab = 'login';
$regFullName = '';
$regUsername = '';
$regRole = 'Receptionist';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login';

    if ($action === 'register') {
        $activeTab = 'register';
        $regFullName = trim($_POST['fullName'] ?? '');
        $regUsername = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirmPassword'] ?? '';
        $regRole = $_POST['role'] ?? 'Receptionist';
        $allowedRoles = ['Receptionist', 'Technician'];

        if ($regFullName === '' || $regUsername === '' || $password === '') {
            $registerError = 'All fields are required.';
        } elseif (strlen($password) < 6) {
            $registerError = 'Password must be at least 6 characters.';
        } elseif ($password !== $confirm) {
            $registerError = 'Passwords do not match.';
        } elseif (!in_array($regRole, $allowedRoles, true)) {
            $registerError = 'Invalid role selected.';
        } else {
            $conn = getDB();
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (Username, PasswordHash, FullName, Role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $regUsername, $hash, $regFullName, $regRole);
            if ($stmt->execute()) {
                $newId = $conn->insert_id;
                $stmt->close();
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id' => $newId,
                    'username' => $regUsername,
                    'name' => $regFullName,
                    'role' => $regRole,
                    'technician_id' => null,
                ];
                header('Location: ' . BASE_URL . '/index.php');
                exit;
            }
            if ($stmt->errno === 1062) {
                $registerError = 'That username is already taken.';
            } else {
                $registerError = 'Could not create account: ' . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $loginError = 'Please enter both username and password.';
        } else {
            $user = authenticate($username, $password);
            if ($user) {
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'id' => $user['UserID'],
                    'username' => $user['Username'],
                    'name' => $user['FullName'],
                    'role' => $user['Role'],
                    'technician_id' => $user['TechnicianID'],
                ];
                header('Location: ' . BASE_URL . '/index.php');
                exit;
            }
            $loginError = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cylinder Workshop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0"><i class="bi bi-wrench"></i> Cylinder Workshop</h4>
                    </div>
                    <div class="card-body p-4">
                        <ul class="nav nav-tabs mb-4" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php echo $activeTab === 'login' ? 'active' : ''; ?>" data-bs-toggle="tab" data-bs-target="#loginPane" type="button">Sign In</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php echo $activeTab === 'register' ? 'active' : ''; ?>" data-bs-toggle="tab" data-bs-target="#registerPane" type="button">Create Account</button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade <?php echo $activeTab === 'login' ? 'show active' : ''; ?>" id="loginPane">
                                <?php if ($loginError !== ''): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>
                                <form method="POST">
                                    <input type="hidden" name="action" value="login">
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" name="username" class="form-control" autofocus required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right"></i> Login</button>
                                </form>
                            </div>

                            <div class="tab-pane fade <?php echo $activeTab === 'register' ? 'show active' : ''; ?>" id="registerPane">
                                <?php if ($registerError !== ''): ?>
                                <div class="alert alert-danger"><?php echo htmlspecialchars($registerError, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>
                                <?php if ($registerSuccess !== ''): ?>
                                <div class="alert alert-success"><?php echo htmlspecialchars($registerSuccess, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>
                                <form method="POST">
                                    <input type="hidden" name="action" value="register">
                                    <div class="mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="fullName" class="form-control" value="<?php echo htmlspecialchars($regFullName, ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($regUsername, ENT_QUOTES, 'UTF-8'); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Role</label>
                                        <select name="role" class="form-select">
                                            <option value="Receptionist" <?php echo $regRole === 'Receptionist' ? 'selected' : ''; ?>>Receptionist</option>
                                            <option value="Technician" <?php echo $regRole === 'Technician' ? 'selected' : ''; ?>>Technician</option>
                                        </select>
                                        <small class="text-muted">Admin and Manager accounts are created by an administrator.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Confirm Password</label>
                                        <input type="password" name="confirmPassword" class="form-control" required>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100"><i class="bi bi-person-plus"></i> Create Account</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
