<?php 
require __DIR__. '/includes/login-header.php';
?>
<div class="d-flex justify-content-center align-items-center min-vh-100 p-3">

    <div class="card card-outline card-primary shadow-sm"
         style="width: 100%; max-width: 400px;">

        <div class="card-header text-center">
            <h3 class="mb-0"><b>Library</b> Login</h3>
            <small class="text-secondary">Library Management System</small>
        </div>

        <div class="card-body">

            <p class="text-center text-secondary">
                Sign in to start your session
            </p>

            <form action="config/process.php" method="POST">

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text"
                           name="username"
                           class="form-control"
                           placeholder="Enter username"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password"
                           name="password"
                           class="form-control"
                           placeholder="Enter password"
                           required>
                </div>

                <button type="submit"
                        name="login"
                        class="btn btn-primary w-100">
                    Login
                </button>

            </form>

            <hr>

            <p class="text-center mb-0">
                Don't have an account?
                <a href="register.php">Register here</a>
            </p>

        </div>
    </div>

</div>
  <?php
  require __DIR__. '/includes/footer.php';
  ?>