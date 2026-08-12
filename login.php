<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'connectdb.php';

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in both email and password.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, first_name, last_name, email, password_hash FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['email'] = $user['email'];

                header('Location: index.php');
                exit;
            }
        }
        $error = 'Invalid email or password.';
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - Travi Fitness</title>
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
                    <li><a href="login.php" class="active">Log In</a></li>
                    <li><a href="register.php">Register</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div style="max-width: 440px; margin: 3rem auto;">
            <div class="card">
                <h2 style="text-align: center; margin-bottom: 0.5rem;">Welcome Back</h2>
                <p style="text-align: center; color: var(--text-muted); margin-bottom: 1.5rem;">Log in to access your Travi Fitness profile</p>

                <?php if (!empty($error)): ?>
                    <div class="alert-error" style="margin-bottom: 1.25rem;">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST" novalidate>
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required autofocus>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem;">Log In</button>
                </form>

                <p style="text-align: center; margin-top: 1.5rem; font-size: 0.9rem;">
                    Don't have an account? <a href="register.php">Register Now</a>
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
