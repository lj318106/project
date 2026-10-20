
<?php
session_start();
require_once __DIR__ . '/db.php';

$successMessage = $_SESSION['verification_success'] ?? '';
unset($_SESSION['verification_success']);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Email au password inahitajika.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM employees WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ((int) $user['is_verified'] === 0) {
                $_SESSION['verify_email'] = $email;
                header('Location: verify.php');
                exit;
            }

            $_SESSION['employee_id'] = (int) $user['id'];
            $_SESSION['fullname'] = $user['fullname'] ?? $user['full_name'] ?? '';
            $_SESSION['login_success'] = 'Umeingia kwenye dashboard.';
            header('Location: index.php');
            exit;
        }

        $error = 'Email au Password si sahihi!';
    }
}
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="card">
        <div class="card-body login-card-body">
            <h3 class="text-center">Login</h3>

            <?php if ($successMessage !== ''): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

            <p class="mt-3 text-center">
                Create an account? <a href="register.php">Register</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>