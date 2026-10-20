
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
require __DIR__ . "/config/connection.php";

// Page size
$limit = (int) ($_GET['size'] ?? 10);

if (!in_array($limit, [5, 10, 25, 50], true)) {
    $limit = 10;
}

// Count categories
$countResult = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM categories"
);

$totalCategories = (int) mysqli_fetch_assoc($countResult)['total'];

// Calculate pagination
$totalPages = max(1, (int) ceil($totalCategories / $limit));

$page = max(1, (int) ($_GET['page'] ?? 1));
$page = min($page, $totalPages);

$offset = ($page - 1) * $limit;

// Fetch categories and count books in each category
$sql = "SELECT c.category_id,
               c.category_name,
               c.description,
               COUNT(b.book_id) AS total_books
        FROM categories c
        LEFT JOIN books b ON c.category_id = b.category_id
        GROUP BY c.category_id, c.category_name, c.description
        ORDER BY c.category_id DESC
        LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

function categoryPageUrl($page, $size) {
    return '?' . http_build_query([
        'page' => $page,
        'size' => $size
    ]);
}

$start = $totalCategories > 0 ? $offset + 1 : 0;
$end = min($offset + $limit, $totalCategories);

require __DIR__ . "/includes/header.php";
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between
                align-items-center mb-3">
        <h3>Categories Management</h3>

        <a href="categories-form.php" class="btn btn-primary">
            + Add Category
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Book Categories</h3>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Category Name</th>
                            <th>Description</th>
                            <th>Total Books</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($category = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>
                                    <?= (int) $category['category_id'] ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($category['category_name'] ?? '') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($category['description'] ?? '') ?>
                                </td>

                                <td>
                                    <span class="badge text-bg-primary">
                                        <?= (int) $category['total_books'] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center p-4">
                                No categories found.
                                <a href="categories-form.php">
                                    Add your first category
                                </a>.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            <div class="d-flex flex-wrap justify-content-between
                        align-items-center gap-3">

                <div class="d-flex align-items-center gap-2">
                    <strong>Page Size</strong>

                    <form method="get">
                        <select name="size" class="form-select"
                                onchange="this.form.submit()">
                            <?php foreach ([5, 10, 25, 50] as $size): ?>
                                <option value="<?= $size ?>"
                                    <?= $limit === $size ? 'selected' : '' ?>>
                                    <?= $size ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>

                <small class="text-body-secondary">
                    Showing <?= $start ?>–<?= $end ?>
                    of <?= $totalCategories ?> categories
                </small>

                <nav aria-label="Category pagination">
                    <ul class="pagination pagination-sm mb-0">

                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link"
                               href="<?= htmlspecialchars(categoryPageUrl(1, $limit)) ?>">
                                First
                            </a>
                        </li>

                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link"
                               href="<?= htmlspecialchars(categoryPageUrl(max(1, $page - 1), $limit)) ?>">
                                Prev
                            </a>
                        </li>

                        <?php
                        $firstPage = max(1, $page - 2);
                        $lastPage = min($totalPages, $page + 2);

                        for ($i = $firstPage; $i <= $lastPage; $i++):
                        ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link"
                                   href="<?= htmlspecialchars(categoryPageUrl($i, $limit)) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link"
                               href="<?= htmlspecialchars(categoryPageUrl(min($totalPages, $page + 1), $limit)) ?>">
                                Next
                            </a>
                        </li>

                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link"
                               href="<?= htmlspecialchars(categoryPageUrl($totalPages, $limit)) ?>">
                                Last
                            </a>
                        </li>

                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>

<?php
mysqli_stmt_close($stmt);
require __DIR__ . "/includes/footer.php";
?>
