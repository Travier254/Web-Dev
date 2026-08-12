<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'connectdb.php';

$errors = [];
$first_name = '';
$last_name = '';
$phone = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($first_name) || !preg_match("/^[a-zA-Z\s'-]+$/", $first_name)) {
        $errors['first_name'] = 'Valid first name is required (letters only).';
    }

    if (empty($last_name) || !preg_match("/^[a-zA-Z\s'-]+$/", $last_name)) {
        $errors['last_name'] = 'Valid last name is required (letters only).';
    }

    if (empty($phone) || !preg_match("/^(?:\+254|0)[17]\d{8}$/", $phone)) {
        $errors['phone'] = 'Valid Kenyan phone number required (e.g. 0712345678 or +254712345678).';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Valid email address is required.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors['email'] = 'An account with this email address already exists.';
        }
        mysqli_stmt_close($stmt);
    }

    if (empty($password) || strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters long.';
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = mysqli_prepare($conn, "INSERT INTO users (first_name, last_name, phone, email, password_hash) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssss", $first_name, $last_name, $phone, $email, $password_hash);

        if (mysqli_stmt_execute($stmt)) {
            $user_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

            $_SESSION['user_id'] = $user_id;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;
            $_SESSION['email'] = $email;

            header('Location: index.php');
            exit;
        } else {
            $errors['general'] = 'Registration failed due to a server error. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Travi Fitness</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>

    <header>
        <div class="header-container">
            <div class="brand">
                <div class="brand-icon">TF</div>
                <div class="brand-text">
                    <h1>TRAVI FITNESS</h1>
                    <p>Start Your Fitness Journey Today</p>
                </div>
            </div>
            <nav>
                <ul>
                    <li><a href="login.php">Log In</a></li>
                    <li><a href="register.php" class="active">Register</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div style="max-width: 540px; margin: 2rem auto;">
            <div class="card">
                <h2 style="text-align: center; margin-bottom: 0.5rem;">Create Your Account</h2>
                <p style="text-align: center; color: var(--text-muted); margin-bottom: 1.5rem;">Join Travi Fitness Nairobi today</p>

                <div class="info-notice">
                    <strong>⚠️ Important Notice:</strong> Your first name and last name cannot be edited after account creation. Please double-check for accuracy.
                </div>

                <?php if (!empty($errors['general'])): ?>
                    <div class="alert-error" style="margin-bottom: 1rem;">
                        <?php echo htmlspecialchars($errors['general']); ?>
                    </div>
                <?php endif; ?>

                <form action="register.php" method="POST" novalidate>
                    <div class="grid-2" style="gap: 1rem; margin-bottom: 0;">
                        <div class="form-group <?php echo isset($errors['first_name']) ? 'has-error' : ''; ?>">
                            <label for="first_name" class="form-label">First Name <span style="color: var(--text-error);">*</span></label>
                            <input type="text" id="first_name" name="first_name" class="form-control <?php echo isset($errors['first_name']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($first_name); ?>" required>
                            <div class="error-feedback"><?php echo $errors['first_name'] ?? ''; ?></div>
                        </div>

                        <div class="form-group <?php echo isset($errors['last_name']) ? 'has-error' : ''; ?>">
                            <label for="last_name" class="form-label">Last Name <span style="color: var(--text-error);">*</span></label>
                            <input type="text" id="last_name" name="last_name" class="form-control <?php echo isset($errors['last_name']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($last_name); ?>" required>
                            <div class="error-feedback"><?php echo $errors['last_name'] ?? ''; ?></div>
                        </div>
                    </div>

                    <div class="form-group <?php echo isset($errors['phone']) ? 'has-error' : ''; ?>">
                        <label for="phone" class="form-label">Phone Number <span style="color: var(--text-error);">*</span></label>
                        <input type="text" id="phone" name="phone" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" placeholder="e.g. 0712345678" value="<?php echo htmlspecialchars($phone); ?>" required>
                        <div class="error-feedback"><?php echo $errors['phone'] ?? ''; ?></div>
                    </div>

                    <div class="form-group <?php echo isset($errors['email']) ? 'has-error' : ''; ?>">
                        <label for="email" class="form-label">Email Address <span style="color: var(--text-error);">*</span></label>
                        <input type="email" id="email" name="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" placeholder="e.g. john@example.com" value="<?php echo htmlspecialchars($email); ?>" required>
                        <div class="error-feedback"><?php echo $errors['email'] ?? ''; ?></div>
                    </div>

                    <div class="form-group <?php echo isset($errors['password']) ? 'has-error' : ''; ?>">
                        <label for="password" class="form-label">Password <span style="color: var(--text-error);">*</span></label>
                        <input type="password" id="password" name="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" placeholder="Minimum 8 characters" required>
                        <div class="error-feedback"><?php echo $errors['password'] ?? ''; ?></div>
                    </div>

                    <div class="form-group <?php echo isset($errors['confirm_password']) ? 'has-error' : ''; ?>">
                        <label for="confirm_password" class="form-label">Confirm Password <span style="color: var(--text-error);">*</span></label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control <?php echo isset($errors['confirm_password']) ? 'is-invalid' : ''; ?>" required>
                        <div class="error-feedback"><?php echo $errors['confirm_password'] ?? ''; ?></div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem;">Create Account</button>
                </form>

                <p style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem;">
                    Already have an account? <a href="login.php">Log In</a>
                </p>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-col">
                <h4>TRAVI FITNESS</h4>
                <p>Your ultimate partner in health, muscle development, and cardiovascular endurance in Nairobi.</p>
            </div>
            <div class="footer-col">
                <h4>Quick Links</h4>
                <p><a href="login.php">Log In</a></p>
                <p><a href="register.php">Register</a></p>
            </div>
            <div class="footer-col">
                <h4>Contact Headquarters</h4>
                <address>
                    Ngong Road, Nairobi, Kenya<br>
                    Email: <a href="mailto:info@travifitness.co.ke">info@travifitness.co.ke</a><br>
                    Phone: <a href="tel:+254712345678">+254 712 345 678</a>
                </address>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Travi Fitness. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
