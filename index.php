<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: member-dashboard.php");
    exit;
}

require_once __DIR__ . "/config/connection.php";

// Get a single total from the database.
function getTotal($conn, $sql) {
    $result = $conn->query($sql);

    if (!$result) {
        die("Dashboard query failed: " . $conn->error);
    }

    $row = $result->fetch_row();
    return (int) $row[0];
}

// Dashboard totals.
$totalBooks = getTotal(
    $conn,
    "SELECT COALESCE(SUM(total_copies), 0) FROM books"
);

$totalMembers = getTotal(
    $conn,
    "SELECT COUNT(*) FROM members"
);

$activeMembers = getTotal(
    $conn,
    "SELECT COUNT(*) FROM members WHERE status = 'active'"
);

$borrowedBooks = getTotal(
    $conn,
    "SELECT COUNT(*) FROM loans WHERE return_date IS NULL"
);

$overdueBooks = getTotal(
    $conn,
    "SELECT COUNT(*) FROM loans
     WHERE return_date IS NULL AND due_date < CURDATE()"
);

$totalCategories = getTotal(
    $conn,
    "SELECT COUNT(*) FROM categories"
);

$availableCopies = getTotal(
    $conn,
    "SELECT COALESCE(SUM(b.total_copies), 0)
       - COALESCE((
           SELECT COUNT(*)
           FROM loans l
           WHERE l.return_date IS NULL
       ), 0)
     FROM books b"
);

// Monthly borrowing totals for the current year.
$monthlyLoans = array_fill(1, 12, 0);

$sql = "SELECT MONTH(borrow_date) AS month_number,
               COUNT(*) AS loan_count
        FROM loans
        WHERE YEAR(borrow_date) = YEAR(CURDATE())
        GROUP BY MONTH(borrow_date)";

$result = $conn->query($sql);

if (!$result) {
    die("Chart query failed: " . $conn->error);
}

while ($row = $result->fetch_assoc()) {
    $monthlyLoans[(int) $row['month_number']] =
        (int) $row['loan_count'];
}

$monthNames = [
    1 => 'Jan', 2 => 'Feb', 3 => 'Mar',
    4 => 'Apr', 5 => 'May', 6 => 'Jun',
    7 => 'Jul', 8 => 'Aug', 9 => 'Sep',
    10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
];

$maxLoans = max(1, max($monthlyLoans));

require_once __DIR__ . "/includes/header.php";
?>

<div class="container-fluid">

    <div class="mb-4">
        <h2>Library Dashboard</h2>
        <p class="text-secondary">
            Overview of books, members and borrowing activities.
        </p>
    </div>

    <!-- Summary cards -->
    <div class="row">

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="card text-bg-primary shadow-sm">
                <div class="card-body">
                    <h6>Total Book Copies</h6>
                    <h2><?= number_format($totalBooks) ?></h2>
                    <a href="books.php"
                       class="text-white">
                        Manage books
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="card text-bg-success shadow-sm">
                <div class="card-body">
                    <h6>Total Members</h6>
                    <h2><?= number_format($totalMembers) ?></h2>
                    <a href="members.php"
                       class="text-white">
                        View members
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="card text-bg-warning shadow-sm">
                <div class="card-body">
                    <h6>Borrowed Books</h6>
                    <h2><?= number_format($borrowedBooks) ?></h2>
                    <a href="loans.php" class="text-dark">
                        View loans
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="card text-bg-danger shadow-sm">
                <div class="card-body">
                    <h6>Overdue Loans</h6>
                    <h2><?= number_format($overdueBooks) ?></h2>
                    <a href="loans.php" class="text-white">
                        Check overdue loans
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Active Members</h6>
                    <h3><?= number_format($activeMembers) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Book Categories</h6>
                    <h3><?= number_format($totalCategories) ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Available Copies</h6>
                    <h3><?= number_format(max(0, $availableCopies)) ?></h3>
                </div>
            </div>
        </div>

    </div>

    <!-- Monthly borrowing chart -->
    <div class="card shadow-sm mt-3">
        <div class="card-header">
            <h5 class="mb-0">Monthly Borrowing Activity</h5>
        </div>

        <div class="card-body">

            <?php foreach ($monthNames as $number => $month): ?>
                <?php
                    $count = $monthlyLoans[$number];
                    $width = ($count / $maxLoans) * 100;
                ?>

                <div class="d-flex align-items-center mb-3">
                    <div style="width: 45px;">
                        <?= $month ?>
                    </div>

                    <div class="flex-grow-1 bg-body-secondary rounded"
                         style="height: 25px;">

                        <div class="bg-success rounded text-white text-end pe-2"
                             style="width: <?= $width ?>%;
                                    min-width: <?= $count > 0 ? '35px' : '0' ?>;
                                    height: 25px;
                                    line-height: 25px;">
                            <?= $count > 0 ? $count : '' ?>
                        </div>

                    </div>
                </div>

            <?php endforeach; ?>

            <p class="text-secondary small mb-0">
                Number of loan records created in
                <?= date('Y') ?>.
            </p>

        </div>
    </div>

</div>

<?php require_once __DIR__ . "/includes/footer.php"; ?>