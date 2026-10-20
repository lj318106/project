<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

if (!function_exists('e')) {
    function e($value): string {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS partners (
        id INT AUTO_INCREMENT PRIMARY KEY,
        fullname VARCHAR(150) NOT NULL,
        email VARCHAR(150) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        company VARCHAR(150) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (Throwable $ex) {
    error_log($ex->getMessage());
}

$message = '';
$partnerToEdit = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'save') {
            $id = (int) ($_POST['id'] ?? 0);
            $fullname = trim($_POST['fullname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $company = trim($_POST['company'] ?? '');

            if ($fullname === '' || $email === '' || $phone === '' || $company === '') {
                throw new InvalidArgumentException('All partner fields are required.');
            }

            if ($id > 0) {
                // FIX: column ni fullname (haikuwa name)
                $stmt = $pdo->prepare('UPDATE partners SET fullname = ?, email = ?, phone = ?, company = ? WHERE id = ?');
                $stmt->execute([$fullname, $email, $phone, $company, $id]);
                $message = 'Partner updated successfully.';
            } else {
                // FIX: column ni fullname (haikuwa name)
                $stmt = $pdo->prepare('INSERT INTO partners (fullname, email, phone, company) VALUES (?, ?, ?, ?)');
                $stmt->execute([$fullname, $email, $phone, $company]);
                $message = 'Partner added successfully.';
            }
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id > 0) {
                $stmt = $pdo->prepare('DELETE FROM partners WHERE id = ?');
                $stmt->execute([$id]);
                $message = 'Partner deleted successfully.';
            }
        }
    } catch (Throwable $ex) {
        $message = $ex->getMessage();
    }
}

if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM partners WHERE id = ?');
    $stmt->execute([$id]);
    $partnerToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}

$stmt = $pdo->query('SELECT * FROM partners ORDER BY id DESC');
$partners = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partners</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="container py-4">
    <h2>Partners</h2>
    <p>
        <a href="index.php">Dashboard</a> |
        <a href="Accounts.php">Accounts</a> |
        <a href="Products.php">Products</a> |
        <a href="Company.php">Company</a> |
        <a href="Employees.php">Employees</a> |
        <a href="Customers.php">Customers</a>
    </p>

    <?php if ($message !== ''): ?>
        <div class="alert alert-info"><?= e($message) ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <h4><?= $partnerToEdit ? 'Edit Partner' : 'Add New Partner' ?></h4>
            <form method="POST" action="Partners.php">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" value="<?= e($partnerToEdit['id'] ?? 0) ?>">
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label>Full name</label>
                        <input type="text" name="fullname" class="form-control" value="<?= e($partnerToEdit['fullname'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= e($partnerToEdit['email'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?= e($partnerToEdit['phone'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Company</label>
                        <input type="text" name="company" class="form-control" value="<?= e($partnerToEdit['company'] ?? '') ?>" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><?= $partnerToEdit ? 'Update Partner' : 'Save Partner' ?></button>
                <?php if ($partnerToEdit): ?>
                    <a href="Partners.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fullname</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Company</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($partners)): ?>
                        <?php foreach ($partners as $partner): ?>
                            <tr>
                                <td><?= e($partner['id']) ?></td>
                                <td><?= e($partner['fullname'] ?? '') ?></td>
                                <td><?= e($partner['email']) ?></td>
                                <td><?= e($partner['phone']) ?></td>
                                <td><?= e($partner['company']) ?></td>
                                <td>
                                    <a href="Partners.php?edit=<?= (int) $partner['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <form method="POST" action="Partners.php" style="display:inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $partner['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete partner?');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">No partners found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>