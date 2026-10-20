
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/config/connection.php";

$userId = (int) $_SESSION['user_id'];

// Find the library member linked to this account.
$stmt = $conn->prepare(
    "SELECT member_id, full_name
     FROM members
     WHERE user_id = ?"
);

$stmt->bind_param("i", $userId);
$stmt->execute();

$memberResult = $stmt->get_result();
$member = $memberResult->fetch_assoc();
$stmt->close();

if (!$member) {
    require_once __DIR__ . "/includes/header.php";
    echo "Checkpoint 2: Dashboard queries completed.";
    ?>
    <div class="container-fluid">
        <div class="alert alert-warning">
            <h5>Member profile not linked</h5>
            Your account does not have a linked library member
            record yet. Please contact the librarian.
        </div>
    </div>
    <?php
    require_once __DIR__ . "/includes/footer.php";
    exit;
}

$memberId = (int) $member['member_id'];

// Helper for dashboard totals.
function memberTotal($conn, $sql, $memberId) {
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        exit("Dashboard query preparation failed: " . $conn->error);
    }

    $stmt->bind_param("i", $memberId);

    if (!$stmt->execute()) {
        exit("Dashboard query execution failed: " . $stmt->error);
    }

    $result = $stmt->get_result();

    if (!$result) {
        exit("Dashboard result failed: " . $stmt->error);
    }

    $row = $result->fetch_row();
    $stmt->close();

    return (int) $row[0];
}

// Count this member's current loans.
$borrowed = memberTotal(
    $conn,
    "SELECT COUNT(*)
     FROM loans
     WHERE member_id = ? AND return_date IS NULL",
    $memberId
);

// Count overdue, unreturned loans.
$overdue = memberTotal(
    $conn,
    "SELECT COUNT(*)
     FROM loans
     WHERE member_id = ?
       AND return_date IS NULL
       AND due_date < CURDATE()",
    $memberId
);

// Count loans due today or in the next 7 days.
$dueSoon = memberTotal(
    $conn,
    "SELECT COUNT(*)
     FROM loans
     WHERE member_id = ?
       AND return_date IS NULL
       AND due_date >= CURDATE()
       AND due_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)",
    $memberId
);

// Total borrowing history.
$totalLoans = memberTotal(
    $conn,
    "SELECT COUNT(*) FROM loans WHERE member_id = ?",
    $memberId
);

// Recent loans belonging to this member.
$stmt = $conn->prepare(
    "SELECT b.title, l.borrow_date, l.due_date,
            l.return_date,
            CASE
                WHEN l.return_date IS NOT NULL THEN 'Returned'
                WHEN l.due_date < CURDATE() THEN 'Overdue'
                ELSE 'Borrowed'
            END AS loan_status
     FROM loans l
     JOIN books b ON l.book_id = b.book_id
     WHERE l.member_id = ?
     ORDER BY l.borrow_date DESC, l.loan_id DESC
     LIMIT 5"
);

$stmt->bind_param("i", $memberId);
$stmt->execute();
$recentLoans = $stmt->get_result();
$stmt->close();

// Monthly borrowing chart for this member.
$monthly = array_fill(1, 12, 0);

$stmt = $conn->prepare(
    "SELECT MONTH(borrow_date) AS month_number,
            COUNT(*) AS total
     FROM loans
     WHERE member_id = ?
       AND YEAR(borrow_date) = YEAR(CURDATE())
     GROUP BY MONTH(borrow_date)"
);

$stmt->bind_param("i", $memberId);
$stmt->execute();
$chartResult = $stmt->get_result();

while ($row = $chartResult->fetch_assoc()) {
    $monthly[(int) $row['month_number']] = (int) $row['total'];
}

$stmt->close();

$months = [
    1 => 'Jan', 2 => 'Feb', 3 => 'Mar',
    4 => 'Apr', 5 => 'May', 6 => 'Jun',
    7 => 'Jul', 8 => 'Aug', 9 => 'Sep',
    10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
];

$maxMonthly = max(1, max($monthly));

require_once __DIR__ . "/includes/header.php";
?>

<div class="container-fluid">

    <div class="mb-4">
        <h2>
            Welcome, <?= htmlspecialchars($member['full_name']) ?>!
        </h2>
        <p class="text-secondary">
            View your borrowed books and library activity.
        </p>
    </div>

    <!-- Member summary cards -->
    <div class="row">

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="card text-bg-primary shadow-sm">
                <div class="card-body">
                    <h6>Currently Borrowed</h6>
                    <h2><?= $borrowed ?></h2>
                    <a href="my-loans.php" class="text-white">
                        View my loans
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="card text-bg-danger shadow-sm">
                <div class="card-body">
                    <h6>Overdue Books</h6>
                    <h2><?= $overdue ?></h2>
                    <a href="my-loans.php" class="text-white">
                        Check due dates
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="card text-bg-warning shadow-sm">
                <div class="card-body">
                    <h6>Due Within 7 Days</h6>
                    <h2><?= $dueSoon ?></h2>
                    <a href="my-loans.php" class="text-dark">
                        View return dates
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 mb-3">
            <div class="card text-bg-success shadow-sm">
                <div class="card-body">
                    <h6>Total Loans</h6>
                    <h2><?= $totalLoans ?></h2>
                    <span>All borrowing records</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Quick links -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Quick Actions</h5>
        </div>

        <div class="card-body">
            <a href="catalog.php" class="btn btn-primary me-2 mb-2">
                Browse Books
            </a>

            <a href="my-loans.php" class="btn btn-outline-primary mb-2">
                My Borrowing History
            </a>
        </div>
    </div>

    <!-- Personal borrowing chart -->
    <div class="card shadow-sm mt-3">
        <div class="card-header">
            <h5 class="mb-0">My Monthly Borrowing Activity</h5>
        </div>

        <div class="card-body">
            <?php foreach ($months as $number => $month): ?>
                <?php
                    $count = $monthly[$number];
                    $width = ($count / $maxMonthly) * 100;
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

            <small class="text-secondary">
                Your loan records for <?= date('Y') ?>.
            </small>
        </div>
    </div>

    <!-- Recent loans -->
    <div class="card shadow-sm mt-3">
        <div class="card-header">
            <h5 class="mb-0">My Recent Loans</h5>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Borrowed</th>
                        <th>Due Date</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($recentLoans->num_rows > 0): ?>
                        <?php while ($loan = $recentLoans->fetch_assoc()): ?>
                            <?php
                                $badge = 'text-bg-primary';

                                if ($loan['loan_status'] === 'Returned') {
                                    $badge = 'text-bg-success';
                                } elseif ($loan['loan_status'] === 'Overdue') {
                                    $badge = 'text-bg-danger';
                                }
                            ?>

                            <tr>
                                <td>
                                    <?= htmlspecialchars($loan['title']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($loan['borrow_date']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($loan['due_date']) ?>
                                </td>

                                <td>
                                    <span class="badge <?= $badge ?>">
                                        <?= htmlspecialchars($loan['loan_status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">
                                You have no borrowing records yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require_once __DIR__ . "/includes/footer.php"; ?>
