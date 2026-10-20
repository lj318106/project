<?php
require_once __DIR__ . '/auth.php';

$action = $_GET['action'] ?? 'view';
$allowedActions = ['view', 'add', 'update', 'delete', 'edit'];
if (!in_array($action, $allowedActions, true)) {
    $action = 'view';
}

$message = $_SESSION['product_message'] ?? '';
unset($_SESSION['product_message']);
$error = '';
$editProduct = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';

    try {
        if ($postAction === 'save') {
            $id = (int) ($_POST['id'] ?? 0);
            $name = trim($_POST['product_name'] ?? '');
            $category = trim($_POST['category'] ?? '');
            $price = $_POST['price'] ?? '';
            $stock = $_POST['stock'] ?? '';
            $status = trim($_POST['status'] ?? 'Available');

            if (
                $name === '' ||
                $category === '' ||
                !is_numeric($price) ||
                (float) $price < 0 ||
                filter_var($stock, FILTER_VALIDATE_INT) === false ||
                (int) $stock < 0
            ) {
                throw new InvalidArgumentException('Jaza taarifa sahihi. Bei na stock haviwezi kuwa hasi.');
            }

            if (!in_array($status, ['Available', 'Low Stock', 'Out of Stock'], true)) {
                throw new InvalidArgumentException('Chagua status sahihi ya bidhaa.');
            }

            if ($id > 0) {
                $stmt = $pdo->prepare(
                    'UPDATE products
                     SET product_name = ?, category = ?, price = ?, stock = ?, status = ?
                     WHERE id = ?'
                );
                $stmt->execute([$name, $category, $price, (int) $stock, $status, $id]);
                $_SESSION['product_message'] = 'Bidhaa imesasishwa.';
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO products (product_name, category, price, stock, status)
                     VALUES (?, ?, ?, ?, ?)'
                );
                $stmt->execute([$name, $category, $price, (int) $stock, $status]);
                $_SESSION['product_message'] = 'Bidhaa imeongezwa.';
            }

            header('Location: Products.php');
            exit;
        }

        if ($postAction === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new InvalidArgumentException('Bidhaa iliyochaguliwa si sahihi.');
            }

            $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
            $stmt->execute([$id]);
            $_SESSION['product_message'] = $stmt->rowCount()
                ? 'Bidhaa imefutwa.'
                : 'Bidhaa haikupatikana.';

            header('Location: Products.php?action=delete');
            exit;
        }

        throw new InvalidArgumentException('Kitendo ulichochagua si sahihi.');
    } catch (InvalidArgumentException $exception) {
        $error = $exception->getMessage();
    } catch (PDOException $exception) {
        error_log($exception->getMessage());
        $error = 'Kuna tatizo la kuhifadhi bidhaa. Hakikisha jedwali products lipo na ujaribu tena.';
    }
}

if ($action === 'edit') {
    $id = (int) ($_GET['id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$id]);
        $editProduct = $stmt->fetch();
    }

    if (!$editProduct) {
        $error = 'Bidhaa haikupatikana.';
        $action = 'update';
    }
}

try {
    $stmt = $pdo->query('SELECT * FROM products ORDER BY id DESC');
    $products = $stmt->fetchAll();
} catch (PDOException $exception) {
    error_log($exception->getMessage());
    http_response_code(500);
    exit('Imeshindikana kusoma bidhaa. Hakikisha database company_management na jedwali products vipo.');
}

$pageTitle = [
    'view' => 'Orodha ya bidhaa',
    'add' => 'Ongeza bidhaa',
    'update' => 'Chagua bidhaa ya kubadilisha',
    'delete' => 'Chagua bidhaa ya kufuta',
    'edit' => 'Sasisha bidhaa',
][$action];
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bidhaa | Company Management</title>
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="Accounts.php">Accounts</a></li>
            <li class="nav-item"><a class="nav-link" href="Company.php">Company</a></li>
            <li class="nav-item"><a class="nav-link" href="Partners.php">Partners</a></li>
            <li class="nav-item"><a class="nav-link" href="Customers.php">Customers</a></li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="logout.php" title="Toka"><i class="fas fa-sign-out-alt"></i> Sign out</a></li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="index.php" class="brand-link">
            <span class="brand-text font-weight-light ml-3">Company Management</span>
        </a>
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
                    <li class="nav-item"><a href="Products.php?action=view" class="nav-link <?= $action === 'view' ? 'active' : '' ?>"><i class="nav-icon fas fa-box"></i><p>Bidhaa</p></a></li>
                    <li class="nav-item"><a href="Products.php?action=add" class="nav-link <?= $action === 'add' ? 'active' : '' ?>"><i class="nav-icon fas fa-plus"></i><p>Ongeza bidhaa</p></a></li>
                    <li class="nav-item"><a href="Products.php?action=update" class="nav-link <?= in_array($action, ['update', 'edit'], true) ? 'active' : '' ?>"><i class="nav-icon fas fa-edit"></i><p>Sasisha bidhaa</p></a></li>
                    <li class="nav-item"><a href="Products.php?action=delete" class="nav-link <?= $action === 'delete' ? 'active' : '' ?>"><i class="nav-icon fas fa-trash"></i><p>Futa bidhaa</p></a></li>
                    
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <section class="content pt-3">
            <div class="container-fluid">
                <h1 class="mb-3"><?= e($pageTitle) ?></h1>

                <?php if ($message !== ''): ?>
                    <div class="alert alert-success"><?= e($message) ?></div>
                <?php endif; ?>
                <?php if ($error !== ''): ?>
                    <div class="alert alert-danger"><?= e($error) ?></div>
                <?php endif; ?>

                <?php if (in_array($action, ['add', 'edit'], true)): ?>
                    <div class="card card-primary">
                        <form method="post" action="Products.php">
                            <input type="hidden" name="action" value="save">
                            <input type="hidden" name="id" value="<?= e($editProduct['id'] ?? 0) ?>">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="product_name">Jina la bidhaa</label>
                                    <input id="product_name" type="text" name="product_name" class="form-control" maxlength="150" value="<?= e($editProduct['product_name'] ?? '') ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="category">Aina</label>
                                    <input id="category" type="text" name="category" class="form-control" maxlength="100" value="<?= e($editProduct['category'] ?? '') ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="price">Bei</label>
                                    <input id="price" type="number" name="price" class="form-control" min="0" step="0.01" value="<?= e($editProduct['price'] ?? '0.00') ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="stock">Idadi iliyopo</label>
                                    <input id="stock" type="number" name="stock" class="form-control" min="0" step="1" value="<?= e($editProduct['stock'] ?? 0) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="status">Hali</label>
                                    <select id="status" name="status" class="form-control">
                                        <?php foreach (['Available' => 'Inapatikana', 'Low Stock' => 'Imebakia kidogo', 'Out of Stock' => 'Imeisha'] as $value => $label): ?>
                                            <option value="<?= e($value) ?>" <?= ($editProduct['status'] ?? 'Available') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary"><?= $editProduct ? 'Sasisha' : 'Hifadhi' ?></button>
                                <a href="Products.php" class="btn btn-secondary">Ghairi</a>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?= e($pageTitle) ?></h3>
                            <a href="Products.php?action=add" class="btn btn-success btn-sm float-right"><i class="fas fa-plus"></i> Ongeza bidhaa</a>
                        </div>
                        <div class="card-body table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr><th>ID</th><th>Jina</th><th>Aina</th><th>Bei</th><th>Idadi</th><th>Hali</th><th>Vitendo</th></tr>
                                </thead>
                                <tbody>
                                <?php if ($products): ?>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td><?= e($product['id']) ?></td>
                                            <td><?= e($product['product_name']) ?></td>
                                            <td><?= e($product['category']) ?></td>
                                            <td><?= e(number_format((float) $product['price'], 2)) ?></td>
                                            <td><?= e($product['stock']) ?></td>
                                            <td><?= e($product['status']) ?></td>
                                            <td class="text-nowrap">
                                                <a href="Products.php?action=edit&id=<?= (int) $product['id'] ?>" class="btn btn-sm btn-warning">Sasisha</a>
                                                <form method="post" action="Products.php?action=delete" class="d-inline" onsubmit="return confirm('Una uhakika unataka kufuta bidhaa hii?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">Futa</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center">Hakuna bidhaa zilizohifadhiwa.</td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
