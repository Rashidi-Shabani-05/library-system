
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] !== 'member') {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . "/config/connection.php";

// Find the member profile connected to this user account
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT member_id, full_name
     FROM members
     WHERE user_id = ?"
);

if (!$stmt) {
    die("Member query failed: " . $conn->error);
} 

$stmt->bind_param("i", $user_id);
$stmt->execute();

$member = $stmt->get_result()->fetch_assoc();
$stmt->close();

$loans = null;

if ($member) {
    $member_id = $member['member_id'];

    // Retrieve only this member's loan records
    $stmt = $conn->prepare(
        "SELECT
            loans.loan_id,
            books.title,
            books.author,
            loans.borrow_date,
            loans.due_date,
            loans.return_date,
            loans.status
         FROM loans
         INNER JOIN books
            ON loans.book_id = books.book_id
         WHERE loans.member_id = ?
         ORDER BY loans.borrow_date DESC"
    );

    if (!$stmt) {
        die("Loan query failed: " . $conn->error);
    }

    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $loans = $stmt->get_result();
}

require_once __DIR__ . "/includes/header.php";
?>

<div class="app-content">
    <div class="container-fluid">

        <div class="row mb-3">
            <div class="col-12">
                <h3>My Loans</h3>
                <p class="text-muted">
                    View your borrowed books and their due dates.
                </p>
            </div>
        </div>

        <?php if (!$member): ?>

            <div class="alert alert-warning">
                Your member profile is not linked to your account.
                Please contact the librarian.
            </div>

        <?php else: ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Loan Records - <?= htmlspecialchars($member['full_name']) ?>
                    </h3>
                </div>

                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Book Title</th>
                                <th>Author</th>
                                <th>Borrow Date</th>
                                <th>Due Date</th>
                                <th>Return Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php if ($loans->num_rows > 0): ?>
                            <?php $number = 1; ?>

                            <?php while ($loan = $loans->fetch_assoc()): ?>
                                <?php
                                if ($loan['return_date'] !== null) {
                                    $status = 'Returned';
                                    $badge = 'success';
                                } elseif ($loan['due_date'] < date('Y-m-d')) {
                                    $status = 'Overdue';
                                    $badge = 'danger';
                                } else {
                                    $status = 'Borrowed';
                                    $badge = 'primary';
                                }
                                ?>

                                <tr>
                                    <td><?= $number++ ?></td>

                                    <td>
                                        <?= htmlspecialchars($loan['title']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($loan['author']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($loan['borrow_date']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($loan['due_date']) ?>
                                    </td>

                                    <td>
                                        <?= $loan['return_date'] !== null
                                            ? htmlspecialchars($loan['return_date'])
                                            : 'Not returned' ?>
                                    </td>

                                    <td>
                                        <span class="badge bg-<?= $badge ?>">
                                            <?= $status ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>

                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">
                                    You have no loan records yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php endif; ?>

        <a href="member-dashboard.php" class="btn btn-secondary mt-3">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>

    </div>
</div>

<?php
if ($member) {
    $stmt->close();
}

require_once __DIR__ . "/includes/footer.php";
?>
