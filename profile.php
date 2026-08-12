<?php
require_once 'auth_guard.php';
require_once 'connectdb.php';

$user_id = $_SESSION['user_id'];
$errors = [];
$success_msg = '';

$stmt = mysqli_prepare($conn, "SELECT first_name, last_name, phone, email, created_at FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

$enroll_stmt = mysqli_prepare($conn, "SELECT plan, goal, preferred_times, submitted_at FROM enrollments WHERE user_id = ?");
mysqli_stmt_bind_param($enroll_stmt, "i", $user_id);
mysqli_stmt_execute($enroll_stmt);
$enrollment = mysqli_fetch_assoc(mysqli_stmt_get_result($enroll_stmt));
mysqli_stmt_close($enroll_stmt);

$phone = $user['phone'];
$email = $user['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_new_password = $_POST['confirm_new_password'] ?? '';

    if (empty($current_password)) {
        $errors['current_password'] = 'Current password is required to save changes.';
    } else {
        $pass_stmt = mysqli_prepare($conn, "SELECT password_hash FROM users WHERE id = ?");
        mysqli_stmt_bind_param($pass_stmt, "i", $user_id);
        mysqli_stmt_execute($pass_stmt);
        $res = mysqli_fetch_assoc(mysqli_stmt_get_result($pass_stmt));
        mysqli_stmt_close($pass_stmt);

        if (!password_verify($current_password, $res['password_hash'])) {
            $errors['current_password'] = 'Incorrect current password.';
        }
    }

    if (empty($phone) || !preg_match("/^(?:\+254|0)[17]\d{8}$/", $phone)) {
        $errors['phone'] = 'Valid Kenyan phone number required.';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Valid email address required.';
    } else if ($email !== $user['email']) {
        $check_email = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? AND id != ?");
        mysqli_stmt_bind_param($check_email, "si", $email, $user_id);
        mysqli_stmt_execute($check_email);
        mysqli_stmt_store_result($check_email);
        if (mysqli_stmt_num_rows($check_email) > 0) {
            $errors['email'] = 'Email address is already in use by another account.';
        }
        mysqli_stmt_close($check_email);
    }

    if (!empty($new_password)) {
        if (strlen($new_password) < 8) {
            $errors['new_password'] = 'New password must be at least 8 characters long.';
        }
        if ($new_password !== $confirm_new_password) {
            $errors['confirm_new_password'] = 'New passwords do not match.';
        }
    }

    if (empty($errors)) {
        if (!empty($new_password)) {
            $new_hash = password_hash($new_password, PASSWORD_BCRYPT);
            $up_stmt = mysqli_prepare($conn, "UPDATE users SET phone = ?, email = ?, password_hash = ? WHERE id = ?");
            mysqli_stmt_bind_param($up_stmt, "sssi", $phone, $email, $new_hash, $user_id);
        } else {
            $up_stmt = mysqli_prepare($conn, "UPDATE users SET phone = ?, email = ? WHERE id = ?");
            mysqli_stmt_bind_param($up_stmt, "ssi", $phone, $email, $user_id);
        }

        if (mysqli_stmt_execute($up_stmt)) {
            mysqli_stmt_close($up_stmt);
            $_SESSION['email'] = $email;
            $success_msg = 'Profile details updated successfully!';

            $user['phone'] = $phone;
            $user['email'] = $email;
        } else {
            $errors['general'] = 'Failed to update profile. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Travi Fitness</title>
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
                    <p>Member Profile Management</p>
                </div>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="schedule.php">Class Schedule</a></li>
                    <li><a href="enroll.php">Enroll Now</a></li>
                    <li><a href="profile.php" class="active">My Profile</a></li>
                    <li><a href="logout.php">Log Out</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section style="margin-bottom: 2rem;">
            <h2>User Account Profile</h2>
            <p>Manage your contact details and view your membership status below.</p>
        </section>

        <?php if (!empty($success_msg)): ?>
            <div class="alert-success show" style="margin-bottom: 1.5rem;">
                <h3 style="color: var(--text-success); margin-bottom: 0.25rem;">Success</h3>
                <p><?php echo htmlspecialchars($success_msg); ?></p>
            </div>
        <?php endif; ?>

        <div class="grid-form">
            <div class="card">
                <h3 style="border-bottom: 2px solid var(--border-light); padding-bottom: 0.5rem; margin-bottom: 1.25rem; color: var(--bg-navy);">
                    Edit Account Details
                </h3>

                <form action="profile.php" method="POST" novalidate>
                    <div class="grid-2" style="gap: 1rem; margin-bottom: 1rem;">
                        <div class="form-group">
                            <label class="form-label">First Name (Permanent)</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['first_name']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name (Permanent)</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['last_name']); ?>" readonly>
                        </div>
                    </div>

                    <div class="info-notice" style="margin-bottom: 1.25rem;">
                        ℹ️ Names cannot be edited as per gym policy. Contact reception for official name corrections.
                    </div>

                    <div class="form-group <?php echo isset($errors['phone']) ? 'has-error' : ''; ?>">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($phone); ?>" required>
                        <div class="error-feedback"><?php echo $errors['phone'] ?? ''; ?></div>
                    </div>

                    <div class="form-group <?php echo isset($errors['email']) ? 'has-error' : ''; ?>">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($email); ?>" required>
                        <div class="error-feedback"><?php echo $errors['email'] ?? ''; ?></div>
                    </div>

                    <h4 style="margin-top: 1.75rem; margin-bottom: 1rem; border-top: 1px solid var(--border-light); padding-top: 1rem;">Change Password (Optional)</h4>

                    <div class="form-group <?php echo isset($errors['new_password']) ? 'has-error' : ''; ?>">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" id="new_password" name="new_password" class="form-control <?php echo isset($errors['new_password']) ? 'is-invalid' : ''; ?>" placeholder="Leave blank to keep existing password">
                        <div class="error-feedback"><?php echo $errors['new_password'] ?? ''; ?></div>
                    </div>

                    <div class="form-group <?php echo isset($errors['confirm_new_password']) ? 'has-error' : ''; ?>">
                        <label for="confirm_new_password" class="form-label">Confirm New Password</label>
                        <input type="password" id="confirm_new_password" name="confirm_new_password" class="form-control <?php echo isset($errors['confirm_new_password']) ? 'is-invalid' : ''; ?>">
                        <div class="error-feedback"><?php echo $errors['confirm_new_password'] ?? ''; ?></div>
                    </div>

                    <div style="border-top: 2px solid var(--border-light); padding-top: 1.25rem; margin-top: 1.5rem;">
                        <div class="form-group <?php echo isset($errors['current_password']) ? 'has-error' : ''; ?>">
                            <label for="current_password" class="form-label">Current Password <span style="color: var(--text-error);">* (Required to save changes)</span></label>
                            <input type="password" id="current_password" name="current_password" class="form-control <?php echo isset($errors['current_password']) ? 'is-invalid' : ''; ?>" required>
                            <div class="error-feedback"><?php echo $errors['current_password'] ?? ''; ?></div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Save Profile Changes</button>
                    </div>
                </form>
            </div>

            <div>
                <div class="card" style="margin-bottom: 1.5rem;">
                    <h3 style="margin-bottom: 0.75rem;">Account Overview</h3>
                    <p style="font-size: 0.95rem; margin-bottom: 0.5rem;"><strong>Member ID:</strong> #TF-<?php echo str_pad($user_id, 4, '0', STR_PAD_LEFT); ?></p>
                    <p style="font-size: 0.95rem; margin-bottom: 0.5rem;"><strong>Joined On:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                </div>

                <div class="card">
                    <h3 style="margin-bottom: 0.75rem;">Current Enrollment</h3>
                    <?php if ($enrollment): ?>
                        <div style="margin-top: 0.5rem;">
                            <p style="margin-bottom: 0.4rem;"><strong>Plan:</strong> <span class="badge badge-orange"><?php echo strtoupper($enrollment['plan']); ?> PLAN</span></p>
                            <p style="margin-bottom: 0.4rem;"><strong>Goal:</strong> <?php echo ucwords(str_replace('_', ' ', $enrollment['goal'])); ?></p>
                            <p style="margin-bottom: 0.4rem;"><strong>Times:</strong> <?php echo htmlspecialchars(ucwords(str_replace(',', ', ', $enrollment['preferred_times']))); ?></p>
                            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.75rem;">Updated on <?php echo date('M j, Y g:i A', strtotime($enrollment['submitted_at'])); ?></p>
                            <a href="enroll.php" class="btn btn-secondary btn-block" style="margin-top: 1rem; text-align: center;">Update Enrollment Details</a>
                        </div>
                    <?php else: ?>
                        <p style="color: var(--text-muted); margin-bottom: 1rem;">No active gym enrollment found yet.</p>
                        <a href="enroll.php" class="btn btn-primary btn-block" style="text-align: center;">Fill Enrollment Form Now</a>
                    <?php endif; ?>
                </div>
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
                <p><a href="index.php">Home</a></p>
                <p><a href="schedule.php">Class Schedule</a></p>
                <p><a href="enroll.php">Enroll Now</a></p>
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
