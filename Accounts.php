<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

function e($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS accounts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        account_name VARCHAR(150) NOT NULL,
        account_number VARCHAR(80) NOT NULL,
        bank_name VARCHAR(150) NOT NULL,
        account_type VARCHAR(50) NOT NULL DEFAULT 'Savings',
        balance DECIMAL(12,2) NOT NULL DEFAULT 0.00,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (Throwable $e) {
    error_log($e->getMessage());
}

$message = '';
$editAccount = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'save') {
            $id = (int) ($_POST['id'] ?? 0);
            $accountName = trim($_POST['account_name'] ?? '');
            $accountNumber = trim($_POST['account_number'] ?? '');
            $bankName = trim($_POST['bank_name'] ?? '');
            $accountType = trim($_POST['account_type'] ?? 'Savings');
            $balance = (float) ($_POST['balance'] ?? 0);

            if ($accountName === '' || $accountNumber === '' || $bankName === '') {
                throw new InvalidArgumentException('Account name, number, and bank name are required.');
            }

            if ($id > 0) {
                $stmt = $pdo->prepare('UPDATE accounts SET account_name = ?, account_number = ?, bank_name = ?, account_type = ?, balance = ? WHERE id = ?');
                $stmt->execute([$accountName, $accountNumber, $bankName, $accountType, $balance, $id]);
                $message = 'Account updated successfully.';
            } else {
                $stmt = $pdo->prepare('INSERT INTO accounts (account_name, account_number, bank_name, account_type, balance) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$accountName, $accountNumber, $bankName, $accountType, $balance]);
                $message = 'Account added successfully.';
            }
        }

        if ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id > 0) {
                $stmt = $pdo->prepare('DELETE FROM accounts WHERE id = ?');
                $stmt->execute([$id]);
                $message = 'Account deleted successfully.';
            }
        }
    } catch (Throwable $ex) {
        $message = $ex->getMessage();
    }
}

if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM accounts WHERE id = ?');
    $stmt->execute([$id]);
    $editAccount = $stmt->fetch(PDO::FETCH_ASSOC);
}

$stmt = $pdo->query('SELECT * FROM accounts ORDER BY id DESC');
$accounts = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accounts</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body class="container py-4">
    <h2>Accounts</h2>
    <p>
        <a href="index.php">Dashboard</a> |
        <a href="Products.php">Products</a> |
        <a href="Partners.php">Partners</a> |
        <a href="Company.php">Company</a>
    </p>

    <?php if ($message !== ''): ?>
        <div class="alert alert-info"><?= e($message) ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <h4><?= $editAccount ? 'Edit Account' : 'Add New Account' ?></h4>
            <form method="POST" action="Accounts.php">
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="id" value="<?= e($editAccount['id'] ?? 0) ?>">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>Account name</label>
                        <input type="text" name="account_name" class="form-control" value="<?= e($editAccount['account_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Account number</label>
                        <input type="text" name="account_number" class="form-control" value="<?= e($editAccount['account_number'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Bank name</label>
                        <input type="text" name="bank_name" class="form-control" value="<?= e($editAccount['bank_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Type</label>
                        <select name="account_type" class="form-control">
                            <option value="Savings" <?= (($editAccount['account_type'] ?? 'Savings') === 'Savings') ? 'selected' : '' ?>>Savings</option>
                            <option value="Business" <?= (($editAccount['account_type'] ?? 'Savings') === 'Business') ? 'selected' : '' ?>>Business</option>
                            <option value="Investment" <?= (($editAccount['account_type'] ?? 'Savings') === 'Investment') ? 'selected' : '' ?>>Investment</option>
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Balance</label>
                        <input type="number" name="balance" step="0.01" min="0" class="form-control" value="<?= e($editAccount['balance'] ?? 0) ?>" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><?= $editAccount ? 'Update Account' : 'Save Account' ?></button>
                <?php if ($editAccount): ?>
                    <a href="Accounts.php" class="btn btn-secondary">Cancel</a>
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
                        <th>Name</th>
                        <th>Account No.</th>
                        <th>Bank</th>
                        <th>Type</th>
                        <th>Balance</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($accounts)): ?>
                        <?php foreach ($accounts as $account): ?>
                            <tr>
                                <td><?= e($account['id']) ?></td>
                                <td><?= e($account['account_name']) ?></td>
                                <td><?= e($account['account_number']) ?></td>
                                <td><?= e($account['bank_name']) ?></td>
                                <td><?= e($account['account_type']) ?></td>
                                <td><?= e(number_format((float) $account['balance'], 2)) ?></td>
                                <td>
                                    <a href="Accounts.php?edit=<?= (int) $account['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <form method="POST" action="Accounts.php" style="display:inline;">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= (int) $account['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete account?');">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center">No accounts found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
