<?php
include("db_connect.php");

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu và trim
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validation đơn giản
    if ($username === '') {
        $errors[] = 'Username is required.';
    } elseif (strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if ($password !== $password_confirm) {
        $errors[] = 'Password and confirmation do not match.';
    }

    if (empty($errors)) {
        // Kiểm tra username đã tồn tại chưa (prepared statement)
        $stmt = mysqli_prepare($link, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = 'Username already taken. Please choose another.';
            mysqli_stmt_close($stmt);
        } else {
            mysqli_stmt_close($stmt);
            // Hash password an toàn
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insert user mới
            $insert = mysqli_prepare($link, "INSERT INTO users (username, password) VALUES (?, ?)");
            mysqli_stmt_bind_param($insert, "ss", $username, $password_hash);
            $ok = mysqli_stmt_execute($insert);

            if ($ok) {
                $success = 'Registration successful! You can <a href="login.php">login now</a>.';
            } else {
                $errors[] = 'Registration failed. Please try again later.';
            }
            mysqli_stmt_close($insert);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Register</title>
</head>
<body>
    <h2>Register</h2>

    <?php if ($success): ?>
        <p style="color:green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <ul style="color:red;">
            <?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?>
        </ul>
    <?php endif; ?>

    <form action="" method="post">
        <label>User name</label><br>
        <input type="text" name="username" required value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>"><br><br>

        <label>Password</label><br>
        <input type="password" name="password" required><br><br>

        <label>Confirm password</label><br>
        <input type="password" name="password_confirm" required><br><br>

        <input type="submit" value="Register">
    </form>

    <p>Already have an account? <a href="login.php">Login here</a>.</p>
</body>
</html>
