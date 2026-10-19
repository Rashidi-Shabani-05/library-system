
<?php
require __DIR__ . "/includes/header.php";
require __DIR__ . "/config/connection.php";

// Number of records to display per page
$limit = isset($_GET['size']) ? (int) $_GET['size'] : 10;

if (!in_array($limit, [5, 10, 25, 50], true)) {
    $limit = 10;
}

// Get total number of books
$countResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM books");
$totalBooks = (int) mysqli_fetch_assoc($countResult)['total'];

// Calculate total pages
$totalPages = max(1, (int) ceil($totalBooks / $limit));

// Current page
$page = max(1, (int) ($_GET['page'] ?? 1));
$page = min($page, $totalPages);

$offset = ($page - 1) * $limit;

// Fetch books for the current page
$sql = "SELECT book_id, title, author, publisher,
               publication_year, total_copies
        FROM books
        ORDER BY book_id DESC
        LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Pagination link helper
function pageUrl($pageNumber, $size) {
    return '?' . http_build_query([
        'page' => $pageNumber,
        'size' => $size
    ]);
}

$start = $totalBooks > 0 ? $offset + 1 : 0;
$end = min($offset + $limit, $totalBooks);
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Books Management</h3>

        <a href="book-form.php" class="btn btn-primary">
            + Add Book
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Books List</h3>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Book Title</th>
                            <th>Author</th>
                            <th>Publisher</th>
                            <th>Year</th>
                            <th>Total Copies</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($book = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= (int) $book['book_id'] ?></td>
                                <td><?= htmlspecialchars($book['title'] ?? '') ?></td>
                                <td><?= htmlspecialchars($book['author'] ?? '') ?></td>
                                <td><?= htmlspecialchars($book['publisher'] ?? '') ?></td>
                                <td><?= htmlspecialchars((string) ($book['publication_year'] ?? '—')) ?></td>
                                <td><?= (int) $book['total_copies'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center p-4">
                                No books found.
                                <a href="book-form.php">Add your first book</a>.
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
                    of <?= $totalBooks ?> books
                </small>

                <nav aria-label="Books pagination">
                    <ul class="pagination pagination-sm mb-0">

                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link"
                               href="<?= htmlspecialchars(pageUrl(1, $limit)) ?>">
                                First
                            </a>
                        </li>

                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link"
                               href="<?= htmlspecialchars(pageUrl(max(1, $page - 1), $limit)) ?>">
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
                                   href="<?= htmlspecialchars(pageUrl($i, $limit)) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link"
                               href="<?= htmlspecialchars(pageUrl(min($totalPages, $page + 1), $limit)) ?>">
                                Next
                            </a>
                        </li>

                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link"
                               href="<?= htmlspecialchars(pageUrl($totalPages, $limit)) ?>">
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
require __DIR__. "/includes/footer.php";
?>