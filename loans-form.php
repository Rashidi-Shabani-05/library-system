
<?php
require __DIR__ . "/config/connection.php";
require __DIR__ . "/includes/header.php";

// Load registered members
$members = mysqli_query(
    $conn,
    "SELECT member_id, full_name
     FROM members
     WHERE status = 'Active'
     ORDER BY full_name"
);

// Load books and calculate available copies
$books = mysqli_query(
    $conn,
    "SELECT b.book_id, b.title, b.total_copies,
        b.total_copies - (
            SELECT COUNT(*)
            FROM loans l
            WHERE l.book_id = b.book_id
              AND l.return_date IS NULL
        ) AS available_copies
     FROM books b
     ORDER BY b.title"
);
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Register Book Loan</h3>
        </div>

        <form action="config/process.php" method="post">
            <div class="card-body">

                <div class="mb-3">
                    <label for="member_id" class="form-label">
                        Select Member
                    </label>

                    <select name="member_id" id="member_id"
                            class="form-select" required>
                        <option value="">Choose a member</option>

                        <?php while ($member = mysqli_fetch_assoc($members)): ?>
                            <option value="<?= (int) $member['member_id'] ?>">
                                <?= htmlspecialchars($member['full_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="book_id" class="form-label">
                        Select Book
                    </label>

                    <select name="book_id" id="book_id"
                            class="form-select" required>
                        <option value="">Choose a book</option>

                        <?php while ($book = mysqli_fetch_assoc($books)): ?>
                            <?php if ($book['available_copies'] > 0): ?>
                                <option value="<?= (int) $book['book_id'] ?>">
                                    <?= htmlspecialchars($book['title']) ?>
                                    (<?= (int) $book['available_copies'] ?> available)
                                </option>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="borrow_date" class="form-label">
                        Borrow Date
                    </label>
                    <input type="date" name="borrow_date"
                           id="borrow_date" class="form-control"
                           value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="due_date" class="form-label">
                        Due Date
                    </label>
                    <input type="date" name="due_date"
                           id="due_date" class="form-control"
                           min="<?= date('Y-m-d') ?>" required>
                </div>

            </div>

            <div class="card-footer">
                <button type="submit" name="add_loan"
                        class="btn btn-primary">
                    <i class="bi bi-journal-plus"></i>
                    Register Loan
                </button>

                <a href="loans.php" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . "/includes/footer.php"; ?>
