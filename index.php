<?php
// Weka faili lako la database hapa juu kabisa
require_once 'db.php'; 

// Mstari wa 4 (sasa hivi $pdo itafanya kazi)
$stmt = $pdo->query("SELECT * FROM Customers");

$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<?php
session_start();

$loginMessage = $_SESSION["login_success"] ?? "";
unset($_SESSION["login_success"]);
?>

<?php if ($loginMessage !== ""): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= htmlspecialchars($loginMessage) ?>
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
<?php endif; ?>



<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db.php';
require_once 'auth.php';

 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Admin Dashboard</title>
    
    
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">

    
</head>

<body class="hold-transition sidebar-mini layout-fixed">

<div class="wrapper">
    

    <!-- TOP NAVBAR -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
           <ul class="navbar-nav flex-row align-items-center">

    <li class="nav-item d-none d-sm-inline-block">
        <a href="index.html" class="nav-link d-flex align-items-center text-nowrap">Home</a>
    </li>

    <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link d-flex align-items-center text-nowrap">
            <i class="fas fa-phone-alt mr-1"></i>Contact
        </a>
    </li>

    <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link d-flex align-items-center text-nowrap">
            <i class="fas fa-concierge-bell mr-1"></i>Service
        </a>
    </li>

    <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link d-flex align-items-center text-nowrap">
            <i class="fas fa-envelope mr-1"></i>Message
        </a>
    </li>

</ul>
        <form class="form-inline ml-3">
            <div class="input-group input-group-sm">
                <input
                    class="form-control form-control-navbar"
                    type="search"
                    placeholder="Search anything"
                    aria-label="Search">

                <div class="input-group-append">
                    <button class="btn btn-navbar" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>

        <ul class="navbar-nav ml-auto">

            <!-- Notifications -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    <span class="badge badge-warning navbar-badge">10</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header">10 Notifications</span>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-envelope mr-2"></i> 2 new messages
                        <span class="float-right text-muted text-sm">3 mins</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-users mr-2"></i> 6 friend requests
                        <span class="float-right text-muted text-sm">12 hours</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-file mr-2"></i> 2 new reports
                        <span class="float-right text-muted text-sm">2 days</span>
                    </a>
                    <div class="dropdown-divider"></div>
                      <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">

                    <i class="far fa-bell"></i>
                    <span class="badge badge-warning navbar-badge">8</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header">8 Notifications</span>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-envelope mr-2"></i> 2 new messages
                        <span class="float-right text-muted text-sm">3 mins</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-users mr-2"></i> 5 friend requests
                        <span class="float-right text-muted text-sm">12 hours</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-file mr-2"></i> 1 new reports
                        <span class="float-right text-muted text-sm">2 days</span>
                    </a>
                    <div class="dropdown-divider"></div>


                    <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                </div>
            </li>

<?php if (isset($_SESSION['user'])): ?>
    <!-- Admin User + sign out -->
<?php else: ?>
    <!-- sign in -->
<?php endif; ?>
       <!-- User Name -->
<li class="nav-item d-none d-sm-inline-block">
    <span class="nav-link d-flex align-items-center text-nowrap">Admin User</span>
</li>

<!-- sign in Link -->
<li class="nav-item">
    <a class="nav-link d-flex align-items-center" href="login.php" title="sign in">
        <i class="fas fa-sign-in-alt"></i>
    </a>
</li>

<!-- sign out Link -->
<li class="nav-item">
    <a class="nav-link d-flex align-items-center" href="logout.php" title="sign out">
        <i class="fas fa-sign-out-alt"></i>
    </a>
</li>

              
        </ul>

    </nav>


    <!-- LEFT SIDEBAR -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        

        <a href="index.html" class="brand-link">
            <i class="fas fa-tachometer-alt ml-3"></i>
            <span class="brand-text font-weight-light">
                FJ Codder
            </span>
        </a>
           <!-- SIDEBAR -->
        <div class="sidebar">
            

            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <i class="fas fa-user-shield fa-2x text-white"></i>
                </div>

                <div class="info">
                    <a href="#" class="d-block">FJ Administrator</a>
                </div>
            </div>

            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column"
                    data-widget="treeview" role="menu">

                    <li class="nav-item">
                        <a href="index.html" class="nav-link active">
                            <i class="nav-icon fas fa-home"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>

                    <li class="nav-header">MANAGEMENT</li>

                    <li class="nav-item">
                        <a href="#companies" class="nav-link">
                            <i class="nav-icon fas fa-building"></i>
                            <p>Companies</p>
                        </a>
                    </li>
                       
                    
                        
                        <!-- CUSTOMERS DROPDOWN MENU -->
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-users"></i>
                            <p>
                                Customers
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview pl-3">
                            <li class="nav-item">
                                <a href="#view-customers" class="nav-link">
                                    <i class="far fa-circle nav-icon text-info"></i>
                                    <p>Customer List</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#add-customer" class="nav-link">
                                    <i class="far fa-circle nav-icon text-success"></i>
                                    <p>Add Customer</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#delete-customer" class="nav-link">
                                    <i class="far fa-circle nav-icon text-danger"></i>
                                    <p>Delete Customer</p>
                                </a>
                            </li>
                           
                        </ul>
                    </li>
                    </li>
                       

            
                    <!-- PRODUCTS DROPDOWN MENU -->
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-box"></i>
                            <p>
                                Products
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview pl-3">
                            <li class="nav-item">
                                <a href="#view-products" class="nav-link">
                                    <i class="far fa-circle nav-icon text-info"></i>
                                    <p>View Products</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#add-product" class="nav-link">
                                    <i class="far fa-circle nav-icon text-success"></i>
                                    <p>Add New Product</p>
                                </a>
                            </li>
                              <li class="nav-item">
                                <a href="#add-product" class="nav-link">
                                    <i class="far fa-circle nav-icon text-warning"></i>
                                    <p>Update Product</p>
                                </a>
                            </li>
                              <li class="nav-item">
                                <a href="#delete-product" class="nav-link">
                                    <i class="far fa-circle nav-icon text-danger"></i>
                                    <p>Delete  Product</p>
                                </a>
                            </li>
                           
                        </ul>
                    </li>

                    </li>

 <!--EMPLOYEES DROPDOWN-->
        <li class="nav-item has-treeview">
                <a href="#" class="nav-link">
        <i class="nav-icon fas fa-user-tie"></i>
             <p>
            Employees
            <i class="right fas fa-angle-left"></i>
             </p>
         </a>

    <ul class="nav nav-treeview pl-3">

        <!-- View Employees -->
        <li class="nav-item">
            <a href="#view-employees" class="nav-link">
                <i class="far fa-circle nav-icon text-info"></i>
                <p>View Employees</p>
            </a>
        </li>

        <!-- Add New Employees -->
        <li class="nav-item">
            <a href="#add-employees" class="nav-link">
                <i class="far fa-circle nav-icon text-success"></i>
                <p>Add New Employees</p>
            </a>
        </li>

        <!-- Update Employees -->
        <li class="nav-item">
            <a href="#update-employees" class="nav-link">
                <i class="far fa-circle nav-icon text-warning"></i>
                <p>Update Employees</p>
            </a>
        </li>

        <!-- Delete Employees -->
        <li class="nav-item">
            <a href="#delete-employees" class="nav-link">
                <i class="far fa-circle nav-icon text-danger"></i>
                <p>Delete Employees</p>
            </a>
            </li>

            </ul>
         </li>
         

    <!--PARTNERS DROPDOWN-->
        <li class="nav-item has-treeview">
     <a href="#" class="nav-link">
        <i class="nav-icon fas fa-user-tie"></i>
        <p>
             Partners
            <i class="right fas fa-angle-left"></i>
        </p>
     </a>

    <ul class="nav nav-treeview pl-3">

        <!-- View Partners -->
        <li class="nav-item">
            <a href="#view-Partners" class="nav-link">
                <i class="far fa-circle nav-icon text-info"></i>
                <p>View Partners</p>
            </a>
        </li>

        <!-- Add New Partners -->
        <li class="nav-item">
            <a href="#add-Partners" class="nav-link">
                <i class="far fa-circle nav-icon text-success"></i>
                <p>Add New Partners</p>
            </a>
        </li>

        <!-- Update Partners -->
        <li class="nav-item">
            <a href="#update-Partners" class="nav-link">
                <i class="far fa-circle nav-icon text-warning"></i>
                <p>Update Partners</p>
            </a>
        </li>

        <!-- Delete Partners -->
        <li class="nav-item">
            <a href="#delete-Partners" class="nav-link">
                <i class="far fa-circle nav-icon text-danger"></i>
                <p>Delete Partners</p>
            </a>
        </li>

          </ul>
         </li>

                <!--ACCOUNTS DROPDOWN-->
                    <li class="nav-item">
                        <a href="#accounts" class="nav-link">
                            <i class="nav-icon fas fa-wallet"></i>
                            <p>
                                Accounts
                            <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview pl-3">
                            <li class="nav-item">
                                <a href="#view-Accounts" class="nav-link">
                                    <i class="far fa-circle nav-icon text-info"></i>
                                    <p>View Accounts</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#add-Accounts" class="nav-link">
                                    <i class="far fa-circle nav-icon text-success"></i>
                                    <p>Add New Accounts</p>
                                </a>
                            </li>
                              <li class="nav-item">
                                <a href="#add-Accounts" class="nav-link">
                                    <i class="far fa-circle nav-icon text-warning"></i>
                                    <p>Update Accounts</p>
                                </a>
                            </li>
                              <li class="nav-item">
                                <a href="#delete-Accounts" class="nav-link">
                                    <i class="far fa-circle nav-icon text-danger"></i>
                                    <p>Delete  Accounts</p>
                                </a>
                            </li>
                        </ul>
                    </li>    
                    </li>
                

                    <li class="nav-header">SYSTEM</li>

                    <li class="nav-item">
                        <a href="#settings" class="nav-link">
                            <i class="nav-icon fas fa-cog"></i>
                            <ul>

                            </ul>
                            <p>Settings@</p>
                        </a>
                    </li>
                 
                </ul>
            </nav>

        </div>
    </aside>
    
    
    


    <!-- MAIN CONTENT -->
    <div class="content-wrapper">

        <section class="content-header">
            <div class="container-fluid">
                <h1>FJ Company</h1>
                <p>Welcome to your administration system.</p>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                
                <!-- STATISTICS CARDS -->
                <div class="row">

                    <div class="col-lg-2 col-2">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>7</h3>
                                <p>Companies</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <a href="#companies" class="small-box-footer">
                                More info
                                <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-2 col-2">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>120</h3>
                                <p>Customers</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <a href="#customers" class="small-box-footer">
                                More info
                                <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-2 col-2">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>40</h3>
                                <p>Products</p>
                            </div>
                            <div class="icon">
                                <i class="fa-brands fa-product-hunt"></i>
                            </div>
                            <a href="#products" class="small-box-footer">
                                More info
                                <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-2">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>15</h3>
                                <p>Partners</p>
                            </div>
                            <div class="icon">
                                <i class="fa-solid fa-handshake"></i>
                            </div>
                            <a href="#products" class="small-box-footer">
                                More info
                                <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-2">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>100</h3>
                                <p>Accounts</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <a href="#products" class="small-box-footer">
                                More info
                                <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-2 col-2">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>60</h3>
                                <p>Employees</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <a href="#employees" class="small-box-footer">
                                More info
                                <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                </div>
                <!-- COMPANY TABLE -->
                <div class="row" id="companies">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Recent Companies</h3>
                            </div>

                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Company Name</th>
                                            <th>Industry</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>FJ Technologies</td>
                                            <td>Technology</td>
                                            <td>info@fjtechnologies.example</td>
                                            <td><span class="badge badge-success">Active</span></td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Green Valley Supplies</td>
                                            <td>Wholesale</td>
                                            <td>contact@greenvalley.example</td>
                                            <td><span class="badge badge-success">Active</span></td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td>Coastal Services</td>
                                            <td>Professional Services</td>
                                            <td>hello@coastalservices.example</td>
                                            <td><span class="badge badge-secondary">Pending</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- CUSTOMER TABLE -->
                
<div class="card">
    <div class="card-header">
        <h3>Customer List</h3>
        <a href="customers.php" class="btn btn-primary">
            Add Customer
        </a>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Position</th>
                    <th>Salary</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
               <div class="card">
    <div class="card-header">
        <h3 class="card-title">Employees <strong>List</strong></h3>
    </div>
    
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <h2>Recent customers</h2>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Position</th>
                    <th>Salary</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
             <tr>
                                            <td>1</td>
                                            <td>Frenk juma</td>
                                            <td>frenk@gmail.com</td>
                                            <td>255765363435</td>
                                            <td>Manager</td>
                                            <td>U$D 12.7</td>
                                            <td><span class="badge badge-success">Active</span></td>
                                        </tr>
                                             <td>2</td>
                                            <td>Alex juma</td>
                                            <td>Alexjuma@gmail.com</td>
                                            <td>255756565435</td>
                                            <td>Designer</td>
                                            <td>U$D 12.7</td>
                                            <td><span class="badge badge-success">Active</span></td>
                                        <tr>
                                             <td>3</td>
                                            <td>Frenk Amani</td>
                                            <td>Amani@gmail.com</td>
                                            <td>255765987635</td>
                                            <td>Programmer</td>
                                            <td>U$D 10</td>
                                            <td><span class="badge badge-success">Active</span></td>
                                        </tr> 
                                             <td>4</td>
                                            <td>Frenk kelvin</td>
                                            <td>frenkelvin@gmail.com</td>
                                            <td>25576512652</td>
                                            <td>Assistant manager</td>
                                            <td>U$D 7.7</td>
                                            <td><span class="badge badge-warning">pending</span></td>       
                               </tbody>
        </table>
    </div>
</div>
            </tbody>
        </table>
    </div>
</div>

            </div>
        </section>
    </div>

     <!-- STATISTICS CARDS -->
                       <!-- STATISTICS CARDS -->
                <div class="row">
                    <!-- ... Zile kadi 4 za takwimu tulizoweka mwanzo zipo hapa ... -->
                </div>
                <!-- /.row -->


                <!-- =========================================================== -->
                <!-- SEHEMU MPYA: PRODUCTS DASHBOARD -->
                <!-- =========================================================== -->
                <div class="row mt-4" id="products">
                    <div class="col-12">
                        <div class="card card-primary card-outline">
                            
                            <!-- Kichwa cha Sehemu ya Product -->
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-box mr-1"></i> Products Dashboard
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Ndani ya Sehemu ya Product -->
                            <div class="card-body">
                                <h5>Manage Your Products</h5>
                                <p>Click this place if you want to add product.</p>
                                
                                <!-- Mfano wa Kitufe cha Kuongeza Bidhaa -->
                                <button class="btn btn-success mb-3">
                                    <i class="fas fa-plus-circle mr-1"></i> Add New Product
                                </button>

                                 <button class="btn btn-success mb-3">
                                    <i class="fas fa-plus-circle mr-1"></i> View New Product
                                </button>

                                 <button class="btn btn-warning mb-3">
                                    <i class="fas fa-plus-circle mr-1"></i> Update  Product
                                </button>

                                 <button class="btn btn-danger mb-3">
                                    <i class="fas fa-plus-circle mr-1"></i> Delete Product
                                </button>

                                <!-- Unaweza kuweka Table au Grid hapa chini baadae -->
                                <div class="p-5 text-center bg-light border rounded">
                                    <p class="text-muted m-0">No products added yet. Click "Add New Product" to start.</p>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <!-- /.row (Products Dashboard) -->


            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->


    <!-- FOOTER -->
    <footer class="main-footer">
        <strong>FJ Dashboard</strong>
        &copy; 2026 All rights reserved not allow anyone to copy.
    </footer>

</div>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>

</body>
</html>
