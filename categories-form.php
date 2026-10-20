<?php
session_start();

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    header("Location: login.php");
    exit;
}
require __DIR__ . "/includes/header.php";
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Add Book Category</h3>
    </div>

    <form action="./config/process.php" method="post">
        <div class="card-body">

            <div class="mb-3">
                <label for="category_name" class="form-label">
                    Category Name
                </label>

                <input type="text"
                       name="category_name"
                       id="category_name"
                       class="form-control"
                       placeholder="e.g. Programming"
                       required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">
                    Description
                </label>

                <textarea name="description"
                          id="description"
                          class="form-control"
                          rows="3"
                          placeholder="Describe this category"></textarea>
            </div>

        </div>

        <div class="card-footer">
            <button type="submit"
                    name="add_category"
                    class="btn btn-primary">
                Add Category
            </button>
        </div>
    </form>
</div>

<?php
require __DIR__ . "/includes/footer.php";
?>