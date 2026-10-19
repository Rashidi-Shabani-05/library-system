<?php
require __DIR__. "/connection.php";

if(isset($_POST['submit'])){
    $title = $_POST['title'];
    $auther = $_POST['author'];
    $publisher = $_POST['publisher'];
    $p_year = $_POST['publication_year'];
    $t_copies = $_POST['total_copies'];
    $c_id = $_POST['category_id'];

    $s_data = "INSERT INTO books(category_id, title, author, publisher, publication_year, total_copies) 
    VALUES($c_id, '$title', '$auther', '$publisher', $p_year, $t_copies)";
    mysqli_query($conn, $s_data);
    header("location: /library%20system/book-form.php");
}

if(isset($_POST['submit1'])){
    $fullname = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $r_date = $_POST['registration_date'];
    $status = $_POST['status'];

    $m_data = "INSERT INTO members(full_name, email, phone, address, registration_date, status) 
    VALUES('$fullname', '$email', '$phone', '$address', '$r_date', '$status')";
    mysqli_query($conn, $m_data);
    header("location: /library%20system/member-form.php");
}

if(isset($_POST['add_category'])){
    $c_name = $_POST['category_name'];
    $descript = $_POST['description'];

    if($c_name === ""){
        echo("Enter category name");
    }
    $cate_data = "INSERT INTO categories(category_name, description) VALUES('$c_name', '$descript')";
    mysqli_query($conn, $cate_data);
    header("location: /library%20system/categories-form.php");
}


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

    // Confirm the member exists and is active.
    $memberStmt = mysqli_prepare(
        $conn,
        "SELECT member_id FROM members
         WHERE member_id = ? AND status = 'Active'"
    );
    mysqli_stmt_bind_param($memberStmt, "i", $member_id);
    mysqli_stmt_execute($memberStmt);
    $memberResult = mysqli_stmt_get_result($memberStmt);

    if (mysqli_num_rows($memberResult) === 0) {
        exit("The selected member is not active or does not exist.");
    }

    // Check that the book exists and has an available copy.
    $bookStmt = mysqli_prepare(
        $conn,
        "SELECT b.total_copies -
                (SELECT COUNT(*) FROM loans l
                 WHERE l.book_id = b.book_id
                   AND l.return_date IS NULL) AS available
         FROM books b
         WHERE b.book_id = ?"
    );
    mysqli_stmt_bind_param($bookStmt, "i", $book_id);
    mysqli_stmt_execute($bookStmt);
    $bookResult = mysqli_stmt_get_result($bookStmt);
    $book = mysqli_fetch_assoc($bookResult);

    if (!$book || $book['available'] <= 0) {
        exit("This book has no available copies.");
    }

    $loanStatus = 'Borrowed';

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO loans
         (book_id, member_id, borrow_date, due_date, status)
         VALUES (?, ?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt, "iisss",
        $book_id, $member_id,
        $borrow_date, $due_date, $loanStatus
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../loans.php");
        exit;
    }

    echo "Unable to register loan: "
         . htmlspecialchars(mysqli_stmt_error($stmt));
}
  
$stmt = $conn->prepare(
    "SELECT user_id, username, passworg_hash, role, account_status
     FROM users
     WHERE username = ?"
);

if (!$stmt) {
    die("Login query failed: " . $conn->error);
}

$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {

    if (
        password_verify($password, $user['passworg_hash']) &&
        $user['account_status'] === 'active'
    ) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header("Location: ../index.php");
        exit;

    } else {
        echo "Invalid username or password, or account is inactive.";
    }

} else {
    echo "Invalid username or password.";
}

$stmt->close();