
<?php
session_start();
require_once "db.php";

$message = "";
$messageType = "danger";

if (empty($_SESSION["verify_email"])) {
    header("Location: register.php");
    exit;
}

$email = $_SESSION["verify_email"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = trim($_POST["verification_code"] ?? "");

    if (!preg_match('/^[0-9]{6}$/', $code)) {
        $message = "Ingiza OTP yenye tarakimu 6.";
    } else {
        try {
            $sql = "SELECT id FROM employees
                    WHERE email = :email
                    AND verification_code = :code
                    AND is_verified = 0
                    LIMIT 1";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ":email" => $email,
                ":code" => $code
            ]);

            $employee = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($employee) {
                $update = $pdo->prepare(
                    "UPDATE employees
                     SET is_verified = 1,
                         verification_code = NULL
                     WHERE id = :id"
                );

                $update->execute([":id" => $employee["id"]]);

                unset($_SESSION["verify_email"]);

                $messageType = "success";
                $message = "Email imethibitishwa! Unaweza kuendelea.";

                // Redirect baada ya kuonyesha ujumbe
                $_SESSION["verification_success"] = $message;

                header("Location: login.php");
                exit;
            } else {
                $message = "OTP si sahihi au akaunti tayari imethibitishwa.";
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $message = "Kumetokea kosa. Jaribu tena.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Account</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
</head>

<body class="hold-transition login-page">

<div class="login-box">
    <div class="card">
        <div class="card-body login-card-body">

            <h4 class="text-center">Verify Your Account</h4>

            <p class="login-box-msg">
                Ingiza OTP ya tarakimu 6 kwa akaunti yako.
            </p>

            <?php if ($message !== ""): ?>
                <div class="alert alert-<?= htmlspecialchars($messageType) ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="verify.php">
                <div class="form-group">
                    <input type="text"
                           name="verification_code"
                           class="form-control"
                           placeholder="Enter 6-digit OTP"
                           inputmode="numeric"
                           pattern="[0-9]{6}"
                           maxlength="6"
                           autocomplete="one-time-code"
                           required>
                </div>

                <button type="submit"
                        class="btn btn-primary btn-block">
                    Verify Account
                </button>
            </form>

        </div>
    </div>
</div>

</body>
</html>
