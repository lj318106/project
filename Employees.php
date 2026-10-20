<?php
// Employees.php - CRUD kwa menu ya AdminLTE
include 'db.php'; // inatengeneza $conn

$action = $_GET['action'] ?? 'view';   // view | add | update | delete
$msg = '';

// ---------- ADD (save) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['do'] ?? '') === 'add') {
    $st = $conn->prepare("INSERT INTO employees (fullname,email,phone,position,salary) VALUES (?,?,?,?,?)");
    $st->bind_param("ssssd", $_POST['fullname'], $_POST['email'], $_POST['phone'], $_POST['position'], $_POST['salary']);
    $st->execute();
    $msg = "Employee ameongezwa.";
    $action = 'view';
}

// ---------- UPDATE (save) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['do'] ?? '') === 'update') {
    $st = $conn->prepare("UPDATE employees SET fullname=?, email=?, phone=?, position=?, salary=? WHERE id=?");
    $st->bind_param("ssisii", $_POST['fullname'], $_POST['email'], $_POST['phone'], $_POST['position'], $_POST['salary'], $_POST['id']);
    $st->execute();
    $msg = "Employee amebadilishwa.";
    $action = 'view';
}

// ---------- DELETE ----------
if ($action === 'remove' && isset($_GET['id'])) {
    $st = $conn->prepare("DELETE FROM employees WHERE id=?");
    $st->bind_param("i", $_GET['id']);
    $st->execute();
    $msg = "Employee amefutwa.";
    $action = 'delete';
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Employees</title>
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- SIDEBAR -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-item menu-open">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-user-tie"></i>
              <p>Employees <i class="right fas fa-angle-left"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item"><a href="Employees.php?action=view"   class="nav-link"><i class="far fa-circle nav-icon text-info"></i><p>View Employees</p></a></li>
              <li class="nav-item"><a href="Employees.php?action=add"    class="nav-link"><i class="far fa-circle nav-icon text-success"></i><p>Add New Employees</p></a></li>
              <li class="nav-item"><a href="Employees.php?action=update" class="nav-link"><i class="far fa-circle nav-icon text-warning"></i><p>Update Employees</p></a></li>
              <li class="nav-item"><a href="Employees.php?action=delete" class="nav-link"><i class="far fa-circle nav-icon text-danger"></i><p>Delete Employees</p></a></li>
            </ul>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- CONTENT -->
  <div class="content-wrapper">
    <section class="content pt-3">
      <div class="container-fluid">

        <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

        <?php if ($action === 'add'): ?>
        <!-- ===== ADD FORM ===== -->
        <div class="card card-success">
          <div class="card-header"><h3 class="card-title">Add New Employee</h3></div>
          <form method="post" action="Employees.php?action=add">
            <input type="hidden" name="do" value="add">
            <div class="card-body">
              <div class="form-group"><label>Fullname</label><input name="fullname" class="form-control" required></div>
              <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" required></div>
              <div class="form-group"><label>Phone</label><input name="phone" class="form-control"></div>
              <div class="form-group"><label>Position</label><input name="position" class="form-control"></div>
              <div class="form-group"><label>Salary</label><input type="number" step="0.01" name="salary" class="form-control"></div>
            </div>
            <div class="card-footer"><button class="btn btn-success">Save</button></div>
          </form>
        </div>

        <?php elseif ($action === 'edit' && isset($_GET['id'])):
          $st = $conn->prepare("SELECT * FROM employees WHERE id=?");
          $st->bind_param("i", $_GET['id']); $st->execute();
          $e = $st->get_result()->fetch_assoc(); ?>
        <!-- ===== EDIT FORM ===== -->
        <div class="card card-warning">
          <div class="card-header"><h3 class="card-title">Update Employee #<?= $e['id'] ?></h3></div>
          <form method="post" action="Employees.php">
            <input type="hidden" name="do" value="update">
            <input type="hidden" name="id" value="<?= $e['id'] ?>">
            <div class="card-body">
              <div class="form-group"><label>fullname</label><input name="fullname" class="form-contro" value="<?= htmlspecialchars($e['fullname']) ?>" required></div>
              <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($e['email']) ?>" required></div>
              <div class="form-group"><label>Phone</label><input name="phone" class="form-control" value="<?= htmlspecialchars($e['phone']) ?>"></div>
              <div class="form-group"><label>Position</label><input name="position" class="form-control" value="<?= htmlspecialchars($e['position']) ?>"></div>
              <div class="form-group"><label>Salary</label><input type="number" step="0.01" name="salary" class="form-control" value="<?= $e['salary'] ?>"></div>
            </div>
            <div class="card-footer"><button class="btn btn-warning">Update</button></div>
          </form>
        </div>

        <?php else:
          // ===== TABLE (view / update / delete) =====
          $titles = ['view'=>'Employees List','update'=>'Select Employee to Update','delete'=>'Select Employee to Delete'];
          $result = $conn->query("SELECT * FROM employees ORDER BY id DESC"); ?>
        <div class="card">
          <div class="card-header"><h3 class="card-title"><?= $titles[$action] ?? 'Employees' ?></h3></div>
          <div class="card-body table-responsive p-0">
            <table class="table table-hover">
              <thead><tr><th>ID</th><th>fullname</th><th>Email</th><th>Phone</th><th>Position</th><th>Salary</th>
                <?php if ($action !== 'view'): ?><th>Action</th><?php endif; ?></tr></thead>
              <tbody>
              <?php while ($r = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= $r['id'] ?></td>
                  <td><?= htmlspecialchars($r['fullname']) ?></td>
                  <td><?= htmlspecialchars($r['email']) ?></td>
                  <td><?= htmlspecialchars($r['phone']) ?></td>
                  <td><?= htmlspecialchars($r['position']) ?></td>
                  <td><?= number_format($r['salary'], 2) ?></td>
                  <?php if ($action === 'update'): ?>
                    <td><a class="btn btn-sm btn-warning" href="Employees.php?action=edit&id=<?= $r['id'] ?>">Edit</a></td>
                  <?php elseif ($action === 'delete'): ?>
                    <td><a class="btn btn-sm btn-danger" onclick="return confirm('Una uhakika unataka kufuta?')" href="Employees.php?action=remove&id=<?= $r['id'] ?>">Delete</a></td>
                  <?php endif; ?>
                </tr>
              <?php endwhile; ?>
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