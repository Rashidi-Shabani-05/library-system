
<?php
session_start();

require_once "config/connection.php";

$message = "";

if (isset($_GET['error'])) {
    $message = $_GET['error'];
}
require __DIR__. '/includes/login-header.php';
?>

<div class="login-box">
    <div class="card card-outline card-primary">

        <div class="card-header text-center">
            <h3><b>Library</b> Registration</h3>
        </div>

        <div class="card-body">

            <p class="login-box-msg">Create your account</p>

            <?php if ($message !== ""): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form action="config/process.php" method="POST">

                <div class="input-group mb-3">
                    <input type="text"
                           name="username"
                           class="form-control"
                           placeholder="Username"
                           maxlength="50"
                           required>
                </div>

                <div class="input-group mb-3">
                    <input type="password"
                           name="password"
                           class="form-control"
                           placeholder="Password"
                           minlength="8"
                           required>
                </div>

                <div class="input-group mb-3">
                    <input type="password"
                           name="confirm_password"
                           class="form-control"
                           placeholder="Confirm Password"
                           minlength="8"
                           required>
                </div>

                <button type="submit"
                        name="register"
                        class="btn btn-primary w-100">
                    Register
                </button>

            </form>

            <p class="mt-3 mb-0 text-center">
                Already have an account?
                <a href="login.php">Login</a>
            </p>

        </div>
    </div>
</div>
<?php
require __DIR__. "/includes/footer.php";
?>