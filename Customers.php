
<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$message = '';
$messageType = 'success';
$editCustomer = null;
$viewCustomer = null;

// PROCESS FORM ACTIONS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'save') {
            $id = (int) ($_POST['id'] ?? 0);
            $fullname = trim($_POST['fullname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');

            // VALIDATION
            if ($fullname === '') {
                throw new InvalidArgumentException(
                    'Tafadhali weka jina kamili.'
                );
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException(
                    'Tafadhali weka email sahihi.'
                );
            }

            // CHECK WHETHER EMAIL IS USED BY ANOTHER CUSTOMER
            $checkSql = 'SELECT id FROM customers WHERE email = ?';
            $checkParams = [$email];

            if ($id > 0) {
                $checkSql .= ' AND id != ?';
                $checkParams[] = $id;
            }

            $checkStmt = $pdo->prepare($checkSql);
            $checkStmt->execute($checkParams);

            if ($checkStmt->fetch(PDO::FETCH_ASSOC)) {
                throw new InvalidArgumentException(
                    'Email hii tayari inatumiwa na customer mwingine.'
                );
            }

            // UPDATE EXISTING CUSTOMER
            if ($id > 0) {
                $sql = 'UPDATE customers
                        SET fullname = ?, email = ?, phone = ?
                        WHERE id = ?';

                $stmt = $pdo->prepare($sql);
                $stmt->execute([$fullname, $email, $phone, $id]);

                $message = 'Customer updated successfully!';
            } else {
                // INSERT NEW CUSTOMER
                $sql = 'INSERT INTO customers
                        (fullname, email, phone)
                        VALUES (?, ?, ?)';

                $stmt = $pdo->prepare($sql);
                $stmt->execute([$fullname, $email, $phone]);

                $message = 'Customer added successfully!';
            }
        } elseif ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);

            if ($id <= 0) {
                throw new InvalidArgumentException(
                    'Customer ID si sahihi.'
                );
            }

            $stmt = $pdo->prepare(
                'DELETE FROM customers WHERE id = ?'
            );
            $stmt->execute([$id]);

            $message = $stmt->rowCount()
                ? 'Customer deleted successfully!'
                : 'Customer not found.';

        } else {
            throw new InvalidArgumentException(
                'Kitendo ulicochagua hakitambuliki.'
            );
        }
    } catch (InvalidArgumentException $e) {
        $message = $e->getMessage();
        $messageType = 'error';

    } catch (PDOException $e) {
        error_log($e->getMessage());

        if ($e->getCode() === '23000') {
            $message =
                'Kuna mgongano wa data. Hakikisha email haijarudiwa '
                . 'na taarifa zinakidhi masharti ya database.';
        } else {
            $message =
                'Database imeshindwa kuhifadhi taarifa. '
                . 'Angalia error log ya PHP.';
        }

        $messageType = 'error';
    }
}

// LOAD CUSTOMER FOR EDIT
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];

    if ($id > 0) {
        $stmt = $pdo->prepare(
            'SELECT * FROM customers WHERE id = ?'
        );
        $stmt->execute([$id]);
        $editCustomer = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$editCustomer) {
            $message = 'Customer not found.';
            $messageType = 'error';
        }
    }
}

// LOAD CUSTOMER FOR VIEW
if (isset($_GET['view'])) {
    $id = (int) $_GET['view'];

    if ($id > 0) {
        $stmt = $pdo->prepare(
            'SELECT * FROM customers WHERE id = ?'
        );
        $stmt->execute([$id]);
        $viewCustomer = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$viewCustomer) {
            $message = 'Customer not found.';
            $messageType = 'error';
        }
    }
}

// LOAD CUSTOMER LIST
$stmt = $pdo->query(
    'SELECT * FROM customers ORDER BY id DESC'
);
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Customers Management</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 25px;
            color: #1e293b;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 22px;
            margin-bottom: 22px;
            border-radius: 10px;
            box-shadow: 0 2px 9px #0001;
        }

        h2 {
            color: #1d4ed8;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 7px 0 14px;
            box-sizing: border-box;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
        }

        button, .btn {
            padding: 9px 13px;
            border: 0;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            cursor: pointer;
            display: inline-block;
            font-size: 14px;
        }

        .save, .view {
            background: #2563eb;
        }

        .edit {
            background: #d97706;
        }

        .delete {
            background: #dc2626;
        }

        .cancel {
            background: #64748b;
        }

        .message {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        .success {
            background: #dcfce7;
            color: #166534;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 11px;
            border: 1px solid #e2e8f0;
            text-align: left;
        }

        th {
            background: #1e40af;
            color: white;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            align-items: center;
        }

        .actions form {
            margin: 0;
        }

        .details p {
            overflow-wrap: anywhere;
        }

        @media (max-width: 700px) {
            body {
                padding: 10px;
            }
        }
    </style>
</head>

<body>
<div class="container">

    <h2>Customers Management</h2>

    <p>
        <a href="index.php">Dashboard</a> |
        <a href="Products.php">Products</a> |
        <a href="Employees.php">Employees</a> |
        <a href="Accounts.php">Accounts</a>
    </p>

    <?php if ($message !== ''): ?>
        <div class="message <?= e($messageType) ?>">
            <?= e($message) ?>
        </div>
    <?php endif; ?>

    <?php if ($viewCustomer): ?>
        <div class="card details">
            <h3>Customer Details</h3>

            <p><strong>ID:</strong>
                <?= e($viewCustomer['id'] ?? '') ?></p>

            <p><strong>Fullname:</strong>
                <?= e($viewCustomer['fullname'] ?? '') ?></p>

            <p><strong>Email:</strong>
                <?= e($viewCustomer['email'] ?? '') ?></p>

            <p><strong>Phone:</strong>
                <?= e($viewCustomer['phone'] ?? '') ?></p>

            <a href="Customers.php" class="btn cancel">
                Back to Customers
            </a>
        </div>
    <?php endif; ?>

    <div class="card">
        <h3>
            <?= $editCustomer ? 'Edit Customer' : 'Add New Customer' ?>
        </h3>

        <form method="POST" action="Customers.php">
            <input type="hidden" name="action" value="save">

            <input type="hidden" name="id"
                value="<?= e($editCustomer['id'] ?? 0) ?>">

            <label for="fullname">Fullname</label>
            <input
                type="text"
                id="fullname"
                name="fullname"
                required
                value="<?= e($editCustomer['fullname'] ?? '') ?>"
            >

            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                required
                value="<?= e($editCustomer['email'] ?? '') ?>"
            >

            <label for="phone">Phone</label>
            <input
                type="text"
                id="phone"
                name="phone"
                value="<?= e($editCustomer['phone'] ?? '') ?>"
            >

            <button class="save" type="submit">
                <?= $editCustomer ? 'Update Customer' : 'Save Customer' ?>
            </button>

            <?php if ($editCustomer): ?>
                <a href="Customers.php" class="btn cancel">
                    Cancel Edit
                </a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <h3>Customer List</h3>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fullname</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (empty($customers)): ?>
                    <tr>
                        <td colspan="5">No customers found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($customers as $row): ?>
                        <tr>
                            <td><?= e($row['id'] ?? '') ?></td>
                            <td><?= e($row['fullname'] ?? '') ?></td>
                            <td><?= e($row['email'] ?? '') ?></td>
                            <td><?= e($row['phone'] ?? '') ?></td>

                            <td>
                                <div class="actions">

                                    <a
                                        href="Customers.php?edit=<?= (int) $row['id'] ?>"
                                        class="btn edit"
                                    >Edit</a>

                                    <a
                                        href="Customers.php?view=<?= (int) $row['id'] ?>"
                                        class="btn view"
                                    >View</a>

                                    <form method="POST" action="Customers.php"
                                        onsubmit="return confirm('Delete this customer?');">

                                        <input type="hidden"
                                            name="action" value="delete">

                                        <input type="hidden"
                                            name="id"
                                            value="<?= (int) $row['id'] ?>">

                                        <button type="submit" class="delete">
                                            Delete
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
