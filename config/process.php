<?php
session_start();

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'member'
) {
    header("Location: login.php");
    exit;
}

require __DIR__ . "/connection.php";

/* ===== START: ADD BOOK ===== */

if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $auther = $_POST['author'];
    $publisher = $_POST['publisher'];
    $p_year = $_POST['publication_year'];
    $t_copies = $_POST['total_copies'];
    $c_id = $_POST['category_id'];

    $s_data = "INSERT INTO books
    (category_id, title, author, publisher, publication_year, total_copies)
    VALUES('$c_id', '$title', '$auther', '$publisher', '$p_year', '$t_copies')";

    mysqli_query($conn, $s_data);

    header("location: ../book-form.php");
    exit;
}

/* ===== END: ADD BOOK ===== */


/* ===== START: ADD MEMBER ===== */

if (isset($_POST['submit1'])) {
    $fullname = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $r_date = $_POST['registration_date'];
    $status = $_POST['status'];

    $m_data = "INSERT INTO members
    (full_name, email, phone, address, registration_date, status)
    VALUES('$fullname', '$email', '$phone', '$address', '$r_date', '$status')";

    mysqli_query($conn, $m_data);

    header("location: ../member-form.php");
    exit;
}

/* ===== END: ADD MEMBER ===== */


/* ===== START: ADD CATEGORY ===== */

if (isset($_POST['add_category'])) {
    $c_name = $_POST['category_name'];
    $descript = $_POST['description'];

    if ($c_name === "") {
        exit("Enter category name");
    }

    $cate_data = "INSERT INTO categories
    (category_name, description)
    VALUES('$c_name', '$descript')";

    mysqli_query($conn, $cate_data);

    header("location: ../categories-form.php");
    exit;
}

/* ===== END: ADD CATEGORY ===== */


/* ===== START: ADD LOAN ===== */

if (isset($_POST['add_loan'])) {

    $member_id = (int) ($_POST['member_id'] ?? 0);
    $book_id = (int) ($_POST['book_id'] ?? 0);
    $borrow_date = $_POST['borrow_date'] ?? '';
    $due_date = $_POST['due_date'] ?? '';

    if (
        $member_id <= 0 ||
        $book_id <= 0 ||
        $borrow_date === '' ||
        $due_date === '' ||
        $due_date < $borrow_date
    ) {
        exit("Please provide valid loan details and dates.");
    }

    // Confirm that the selected member exists and is active.
    $memberStmt = $conn->prepare(
        "SELECT member_id FROM members
         WHERE member_id = ? AND status = 'Active'"
    );

    $memberStmt->bind_param("i", $member_id);
    $memberStmt->execute();

    $memberResult = $memberStmt->get_result();

    if ($memberResult->num_rows === 0) {
        exit("The selected member is not active or does not exist.");
    }

    $memberStmt->close();

    // Check available copies of the selected book.
    $bookStmt = $conn->prepare(
        "SELECT b.total_copies -
            (SELECT COUNT(*) FROM loans l
             WHERE l.book_id = b.book_id
               AND l.return_date IS NULL) AS available
         FROM books b
         WHERE b.book_id = ?"
    );

    $bookStmt->bind_param("i", $book_id);
    $bookStmt->execute();

    $bookResult = $bookStmt->get_result();
    $book = $bookResult->fetch_assoc();

    $bookStmt->close();

    if (!$book || $book['available'] <= 0) {
        exit("This book has no available copies.");
    }

    // Save the loan record.
    $loanStatus = 'Borrowed';

    $stmt = $conn->prepare(
        "INSERT INTO loans
         (book_id, member_id, borrow_date, due_date, status)
         VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "iisss",
        $book_id,
        $member_id,
        $borrow_date,
        $due_date,
        $loanStatus
    );

    if ($stmt->execute()) {
        header("Location: ../loans.php");
        exit;
    }

    die("Unable to register loan: " . htmlspecialchars($stmt->error));
}

/* ===== END: ADD LOAN ===== */


/* ===== START: USER LOGIN ===== */

if (isset($_POST['login'])) {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Find the account by username.
    $stmt = $conn->prepare(
        "SELECT user_id, username, passworg_hash,
                role, account_status
         FROM users
         WHERE username = ?"
    );

    if (!$stmt) {
        die("Login query failed: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $stmt->close();

    // Verify the entered password against the stored hash.
    if (
        $user &&
        password_verify($password, $user['passworg_hash']) &&
        strtolower($user['account_status']) === 'active'
    ) {

        // Regenerate the session ID after successful login.
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect according to the user's role.
        if ($user['role'] === 'admin') {
            header("Location: ../index.php");
            exit;
        } elseif ($user['role'] === 'member') {
            header("Location: ../member-dashboard.php");
            exit;
        } else {
            session_unset();
            session_destroy();
            exit("Unknown account role.");
        }

    } else {
        header(
            "Location: ../login.php?error=" .
            urlencode("Invalid username or password, or inactive account.")
        );
        exit;
    }
}

/* ===== END: USER LOGIN ===== */


/* ===== START: USER REGISTRATION ===== */

if (isset($_POST['register'])) {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Check that the passwords match.
    if ($password !== $confirm_password) {
        header(
            "Location: ../register.php?error=" .
            urlencode("Passwords do not match.")
        );
        exit;
    }

    // Require a password of at least eight characters.
    if (strlen($password) < 8) {
        header(
            "Location: ../register.php?error=" .
            urlencode("Password must be at least 8 characters.")
        );
        exit;
    }

    if ($username === '') {
        header(
            "Location: ../register.php?error=" .
            urlencode("Username is required.")
        );
        exit;
    }

    // Check whether the username already exists.
    $stmt = $conn->prepare(
        "SELECT user_id FROM users WHERE username = ?"
    );

    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt->close();

        header(
            "Location: ../register.php?error=" .
            urlencode("Username already exists.")
        );
        exit;
    }

    $stmt->close();

    // Hash the password before saving it.
    $password_hash = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    // Public registration always creates a normal member account.
    $role = "member";
    $account_status = "active";

    $stmt = $conn->prepare(
        "INSERT INTO users
         (username, passworg_hash, role, account_status)
         VALUES (?, ?, ?, ?)"
    );

    if (!$stmt) {
        die("Registration query failed: " . $conn->error);
    }

    $stmt->bind_param(
        "ssss",
        $username,
        $password_hash,
        $role,
        $account_status
    );

    if ($stmt->execute()) {
        $stmt->close();

        header("Location: ../login.php");
        exit;
    }

    $error = $stmt->error;
    $stmt->close();

    header(
        "Location: ../register.php?error=" .
        urlencode("Registration failed: " . $error)
    );
    exit;
}

/* ===== END: USER REGISTRATION ===== */

?>
