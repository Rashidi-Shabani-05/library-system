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

// Count all members
$countResult = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM members"
);

$totalMembers = (int) mysqli_fetch_assoc($countResult)['total'];

// Calculate pages
$totalPages = max(1, (int) ceil($totalMembers / $limit));

$page = max(1, (int) ($_GET['page'] ?? 1));
$page = min($page, $totalPages);

$offset = ($page - 1) * $limit;

// Fetch current page of members
$sql = "SELECT member_id, full_name, email, phone,
               status, registration_date
        FROM members
        ORDER BY member_id DESC
        LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

function memberPageUrl($page, $size) {
    return '?' . http_build_query([
        'page' => $page,
        'size' => $size
    ]);
}

$start = $totalMembers > 0 ? $offset + 1 : 0;
$end = min($offset + $limit, $totalMembers);

require __DIR__ . "/includes/header.php";
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between
                align-items-center mb-3">
        <h3>Members Management</h3>

        <a href="member-form.php" class="btn btn-primary">
            + Register Member
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Registered Members</h3>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Registration Date</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($member = mysqli_fetch_assoc($result)): ?>
                            <?php
                            $status = $member['status'];
                            $badgeClass = ($status === 'Active')
                                ? 'text-bg-success'
                                : 'text-bg-secondary';
                            ?>

                            <tr>
                                <td>
                                    <?= (int) $member['member_id'] ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($member['full_name'] ?? '') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($member['email'] ?? '') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($member['phone'] ?? '') ?>
                                </td>

                                <td>
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= htmlspecialchars($status ?? '') ?>
                                    </span>
                                </td>

                                <td>
                                    <?= htmlspecialchars($member['registration_date'] ?? '') ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center p-4">
                                No members registered yet.
                                <a href="member-form.php">
                                    Register the first member
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
                    of <?= $totalMembers ?> members
                </small>

                <nav aria-label="Members pagination">
                    <ul class="pagination pagination-sm mb-0">

                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link"
                               href="<?= htmlspecialchars(memberPageUrl(1, $limit)) ?>">
                                First
                            </a>
                        </li>

                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link"
                               href="<?= htmlspecialchars(memberPageUrl(max(1, $page - 1), $limit)) ?>">
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
                                   href="<?= htmlspecialchars(memberPageUrl($i, $limit)) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link"
                               href="<?= htmlspecialchars(memberPageUrl(min($totalPages, $page + 1), $limit)) ?>">
                                Next
                            </a>
                        </li>

                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link"
                               href="<?= htmlspecialchars(memberPageUrl($totalPages, $limit)) ?>">
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
