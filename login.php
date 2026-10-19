<?php 
require __DIR__. '/includes/login-header.php';
?>
  <form action="config/process.php" method="POST">

    <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username"
               class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password"
               class="form-control" required>
    </div>

    <button type="submit" name="login"
            class="btn btn-primary w-100">
        Login
    </button>
</form>
  <?php
  require __DIR__. '/includes/footer.php';
  ?>