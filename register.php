
<?php
session_start();
require_once "db.php";

$alertMessage = "";
$alertType = "danger";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST["fullname"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $plainPassword = $_POST["password"] ?? "";
    $phone = trim($_POST["phone"] ?? "");
    $position = trim($_POST["position"] ?? "");
    $salary = $_POST["salary"] ?? "";

    if (
        $fullname === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($plainPassword) < 8 ||
        $phone === "" ||
        $position === "" ||
        !is_numeric($salary) ||
        (float)$salary < 0
    ) {
        $alertMessage = "Tafadhali jaza taarifa sahihi. Password iwe na angalau herufi 8.";
    } else {
        try {
            // Angalia kama email imeshatumika
            $check = $pdo->prepare(
                "SELECT id FROM employees WHERE email = :email LIMIT 1"
            );
            $check->execute([":email" => $email]);

            if ($check->fetch()) {
                $alertMessage = "Email hii tayari imesajiliwa.";
            } else {
                // Tengeneza OTP ya tarakimu 6
                $verification_code = (string) random_int(100000, 999999);

                $password = password_hash(
                    $plainPassword,
                    PASSWORD_DEFAULT
                );

                $sql = "INSERT INTO employees
                    (fullname, email, password, phone, position,
                     salary, verification_code, is_verified)
                    VALUES
                    (:fullname, :email, :password, :phone, :position,
                     :salary, :verification_code, 0)";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ":fullname" => $fullname,
                    ":email" => $email,
                    ":password" => $password,
                    ":phone" => $phone,
                    ":position" => $position,
                    ":salary" => $salary,
                    ":verification_code" => $verification_code
                ]);

                // Hifadhi email kwenye session
                $_SESSION["verify_email"] = $email;

                // Nenda kwenye ukurasa wa kuingiza OTP
                header("Location: verify.php");
                exit;
            }
        } catch (PDOException $e) {
            // Andika kosa kwenye PHP error log
            error_log($e->getMessage());
            $alertMessage = "Usajili umeshindikana. Tafadhali jaribu tena.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register </title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="hold-transition register-page">

<div class="register-box">
    <div class="register-logo">
        <b>Create</b> Account
    </div>

    <div class="card">
        <div class="card-body register-card-body">
            <p class="login-box-msg">Register a new account</p>

            <?php if ($alertMessage !== ""): ?>
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Kosa!',
                        text: <?= json_encode($alertMessage) ?>
                    });
                </script>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <div class="form-group">
                    <input type="text" name="fullname"
                           class="form-control"
                           placeholder="Full name" required>
                </div>

                <div class="form-group">
                    <input type="email" name="email"
                           class="form-control"
                           placeholder="Email address" required>
                </div>

                <div class="form-group">
                    <input type="password" name="password"
                           class="form-control"
                           placeholder="Password (at least 8 characters)"
                           minlength="8" autocomplete="new-password"
                           required>
                </div>

                <div class="form-group">
                    <input type="tel" name="phone"
                           class="form-control"
                           placeholder="Phone number" required>
                </div>

                <div class="form-group">
                    <input type="text" name="position"
                           class="form-control"
                           placeholder="Position" required>
                </div>

                <div class="form-group">
                    <input type="number" name="salary"
                           class="form-control"
                           placeholder="Salary" min="0"
                           step="0.01" required>
                </div>

                <button type="submit"
                        class="btn btn-primary btn-block">
                    Register
                </button>
            </form>

            <p class="mt-3 mb-0">
                <a href="login.php">I already have an account</a>
            </p>
        </div>
    </div>
</div>

</body>
</html>
