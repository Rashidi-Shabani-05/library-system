
<?php
require_once __DIR__ . "/config/connection.php";

$message = "";

// Check whether an administrator already exists
$result = $conn->query(
    "SELECT user_id FROM users WHERE role = 'admin' LIMIT 1"
);

$adminExists = $result && $result->num_rows > 0;

if ($_SERVER["REQUEST_METHOD"] === "POST" && !$adminExists) {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || strlen($password) < 8) {
        $message = "Enter a username and a password of at least 8 characters.";
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $role = "admin";
        $status = "active";

        $stmt = $conn->prepare(
            "INSERT INTO users
             (username, passworg_hash, role, account_status)
             VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "ssss",
            $username,
            $passwordHash,
            $role,
            $status
        );

        if ($stmt->execute()) {
            $message = "Administrator account created successfully.";
            $adminExists = true;
        } else {
            $message = $stmt->errno === 1062
                ? "That username already exists. Choose another."
                : "Account creation failed. Check your database.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Administrator</title>
</head>
<body>

<h2>Create Administrator Account</h2>

<p><?= htmlspecialchars($message) ?></p>

<?php if ($adminExists): ?>

    <p>An administrator account already exists.</p>

<?php else: ?>

    <form method="POST">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Password (minimum 8 characters):</label><br>
        <input type="password" name="password"
               minlength="8" required><br><br>

        <button type="submit">Create Administrator</button>
    </form>

<?php endif; ?>

</body>
</html>
