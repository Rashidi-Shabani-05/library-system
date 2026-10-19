
<?php
require __DIR__ . "/config/connection.php";

$sql = "SELECT l.loan_id, b.title, m.full_name,
               l.borrow_date, l.due_date,
               l.return_date, l.status
        FROM loans l
        INNER JOIN books b ON l.book_id = b.book_id
        INNER JOIN members m ON l.member_id = m.member_id
        ORDER BY l.loan_id DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    exit("Unable to load loans: " . htmlspecialchars(mysqli_error($conn)));
}

require __DIR__ . "/includes/header.php";
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between
                align-items-center mb-3">
        <h3>Loan Management</h3>

        <a href="loan-form.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i>
            Register Loan
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Book Loan Records</h3>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Loan ID</th>
                            <th>Book</th>
                            <th>Member</th>
                            <th>Borrow Date</th>
                            <th>Due Date</th>
                            <th>Return Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($loan = mysqli_fetch_assoc($result)): ?>
                            <?php
                            $status = $loan['status'];

                            if ($loan['return_date'] !== null) {
                                $badge = 'text-bg-success';
                                $label = 'Returned';
                            } elseif (strtotime($loan['due_date']) < strtotime(date('Y-m-d'))) {
                                $badge = 'text-bg-danger';
                                $label = 'Overdue';
                            } else {
                                $badge = 'text-bg-primary';
                                $label = 'Borrowed';
                            }
                            ?>

                            <tr>
                                <td><?= (int) $loan['loan_id'] ?></td>
                                <td><?= htmlspecialchars($loan['title']) ?></td>
                                <td><?= htmlspecialchars($loan['full_name']) ?></td>
                                <td><?= htmlspecialchars($loan['borrow_date']) ?></td>
                                <td><?= htmlspecialchars($loan['due_date']) ?></td>
                                <td>
                                    <?= htmlspecialchars($loan['return_date'] ?? 'Not returned') ?>
                                </td>
                                <td>
                                    <span class="badge <?= $badge ?>">
                                        <?= $label ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center p-4">
                                No loan records found.
                                <a href="loan-form.php">Register the first loan</a>.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . "/includes/footer.php"; ?>
