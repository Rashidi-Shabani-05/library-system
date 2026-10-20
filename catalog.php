
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (($_SESSION['role'] ?? '') !== 'member') {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . "/config/connection.php";

// Search and category filter.
$search = trim($_GET['search'] ?? '');
$categoryId = filter_input(
    INPUT_GET,
    'category_id',
    FILTER_VALIDATE_INT
);

if (!$categoryId || $categoryId < 1) {
    $categoryId = 0;
}

// Get categories for the dropdown.
$categories = $conn->query(
    "SELECT category_id, category_name
     FROM categories
     ORDER BY category_name"
);

if (!$categories) {
    die("Could not load categories: " . $conn->error);
}

// Retrieve books and calculate available copies.
$sql = "SELECT
            b.book_id,
            b.title,
            b.author,
            b.publisher,
            b.publication_year,
            b.total_copies,
            c.category_name,
            GREATEST(
                b.total_copies - COALESCE(borrowed.active_count, 0),
                0
            ) AS available_copies
        FROM books b
        LEFT JOIN categories c
            ON b.category_id = c.category_id
        LEFT JOIN (
            SELECT book_id, COUNT(*) AS active_count
            FROM loans
            WHERE return_date IS NULL
            GROUP BY book_id
        ) borrowed ON borrowed.book_id = b.book_id
        WHERE (
            b.title LIKE ?
            OR b.author LIKE ?
            OR b.publisher LIKE ?
        )";

if ($categoryId > 0) {
    $sql .= " AND b.category_id = ?";
}

$sql .= " ORDER BY b.title ASC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Catalogue query failed: " . $conn->error);
}

$searchTerm = "%" . $search . "%";

if ($categoryId > 0) {
    $stmt->bind_param(
        "sssi",
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $categoryId
    );
} else {
    $stmt->bind_param(
        "sss",
        $searchTerm,
        $searchTerm,
        $searchTerm
    );
}

$stmt->execute();
$books = $stmt->get_result();

require_once __DIR__ . "/includes/header.php";
?>

<div class="container-fluid">

    <div class="mb-4">
        <h2>Book Catalogue</h2>
        <p class="text-secondary">
            Discover books available in our library.
        </p>
    </div>

    <!-- Search and category filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <form method="GET" action="catalog.php">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Search books</label>
                        <input
                            type="search"
                            name="search"
                            class="form-control"
                            placeholder="Enter title, author or publisher"
                            value="<?= htmlspecialchars($search) ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="0">All categories</option>

                            <?php while ($category = $categories->fetch_assoc()): ?>
                                <option
                                    value="<?= (int) $category['category_id'] ?>"
                                    <?= $categoryId === (int) $category['category_id']
                                        ? 'selected' : '' ?>>

                                    <?= htmlspecialchars(
                                        $category['category_name']
                                    ) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit"
                                class="btn btn-primary w-100">
                            Search
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <!-- Book cards -->
    <div class="row">

        <?php if ($books->num_rows > 0): ?>

            <?php while ($book = $books->fetch_assoc()): ?>

                <div class="col-sm-6 col-lg-4 col-xl-3 mb-4">

                    <div class="card h-100 shadow-sm">

                        <div class="card-body">

                            <div class="mb-3 text-primary">
                                <i class="bi bi-book-half fs-1"></i>
                            </div>

                            <span class="badge text-bg-light mb-2">
                                <?= htmlspecialchars(
                                    $book['category_name'] ?? 'Uncategorized'
                                ) ?>
                            </span>

                            <h5 class="card-title">
                                <?= htmlspecialchars($book['title']) ?>
                            </h5>

                            <p class="text-secondary mb-1">
                                Author:
                                <?= htmlspecialchars($book['author']) ?>
                            </p>

                            <p class="text-secondary mb-1">
                                Publisher:
                                <?= htmlspecialchars(
                                    $book['publisher'] ?? 'Not specified'
                                ) ?>
                            </p>

                            <p class="text-secondary mb-3">
                                Year:
                                <?= htmlspecialchars(
                                    (string) ($book['publication_year'] ?? 'N/A')
                                ) ?>
                            </p>

                            <?php if ((int) $book['available_copies'] > 0): ?>

                                <span class="badge text-bg-success">
                                    Available:
                                    <?= (int) $book['available_copies'] ?>
                                </span>

                            <?php else: ?>

                                <span class="badge text-bg-danger">
                                    Currently unavailable
                                </span>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="col-12">
                <div class="alert alert-info">
                    No books found. Try another title or category.
                </div>
            </div>

        <?php endif; ?>

    </div>

</div>

<?php
$stmt->close();
require_once __DIR__ . "/includes/footer.php";
?>
