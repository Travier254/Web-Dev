<?php
require_once 'auth_guard.php';
require_once 'connectdb.php';

$user_id = $_SESSION['user_id'];
$errors = [];
$success = false;

$stmt = mysqli_prepare($conn, "SELECT first_name, last_name, email FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

$fullname = $user['first_name'] . ' ' . $user['last_name'];
$email = $user['email'];

$enroll_stmt = mysqli_prepare($conn, "SELECT plan, goal, preferred_times, health_notes FROM enrollments WHERE user_id = ?");
mysqli_stmt_bind_param($enroll_stmt, "i", $user_id);
mysqli_stmt_execute($enroll_stmt);
$existing = mysqli_fetch_assoc(mysqli_stmt_get_result($enroll_stmt));
mysqli_stmt_close($enroll_stmt);

$plan = $existing['plan'] ?? '';
$goal = $existing['goal'] ?? '';
$preferred_times_arr = isset($existing['preferred_times']) ? explode(',', $existing['preferred_times']) : [];
$health_notes = $existing['health_notes'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan = trim($_POST['plan'] ?? '');
    $goal = trim($_POST['goal'] ?? '');
    $times = $_POST['times'] ?? [];
    $health_notes = trim($_POST['health_notes'] ?? '');

    if (empty($plan) || !in_array($plan, ['basic', 'premium', 'vip'])) {
        $errors['plan'] = 'Please select a valid membership plan.';
    }

    if (empty($goal) || !in_array($goal, ['weight_loss', 'muscle_gain', 'endurance'])) {
        $errors['goal'] = 'Please select a primary fitness goal.';
    }

    if (empty($times) || !is_array($times)) {
        $errors['times'] = 'Please select at least one preferred training time.';
    }

    if (empty($errors)) {
        $preferred_times_str = implode(',', $times);

        $save_stmt = mysqli_prepare($conn, "INSERT INTO enrollments (user_id, plan, goal, preferred_times, health_notes) 
            VALUES (?, ?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE plan = VALUES(plan), goal = VALUES(goal), preferred_times = VALUES(preferred_times), health_notes = VALUES(health_notes), submitted_at = CURRENT_TIMESTAMP");
        mysqli_stmt_bind_param($save_stmt, "issss", $user_id, $plan, $goal, $preferred_times_str, $health_notes);

        if (mysqli_stmt_execute($save_stmt)) {
            $success = true;
            $preferred_times_arr = $times;
        } else {
            $errors['general'] = 'Failed to save enrollment details. Please try again.';
        }
        mysqli_stmt_close($save_stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enroll Now - Travi Fitness</title>
    <meta name="description" content="Enroll in Travi Fitness Nairobi membership plans. Select Basic, Premium, or VIP plans and customize your training schedule.">
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
                    <li><a href="index.php">Home</a></li>
                    <li><a href="schedule.php">Class Schedule</a></li>
                    <li><a href="enroll.php" class="active">Enroll Now</a></li>
                    <li><a href="profile.php">My Profile</a></li>
                    <li><a href="logout.php">Log Out</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section style="margin-bottom: 2rem; text-align: center;">
            <h2>Membership Enrollment Form</h2>
            <p>Fill in the form below to register your membership and reserve your gym training sessions.</p>
        </section>

        <?php if ($success): ?>
            <div class="alert-success show" id="successAlert">
                <h3 style="color: var(--text-success); margin-bottom: 0.5rem;">Enrollment Submitted Successfully!</h3>
                <p>Thank you for signing up with Travi Fitness. Your registration details are recorded below:</p>
                <div id="successDetails" style="background-color: white; padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-success); margin-top: 0.75rem;">
                    <strong>Member Name:</strong> <?php echo htmlspecialchars($fullname); ?><br>
                    <strong>Email:</strong> <?php echo htmlspecialchars($email); ?><br>
                    <strong>Selected Plan:</strong> <?php echo htmlspecialchars(strtoupper($plan) . ' PLAN'); ?><br>
                    <strong>Fitness Goal:</strong> <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $goal))); ?><br>
                    <strong>Preferred Times:</strong> <?php echo htmlspecialchars(ucwords(implode(', ', $preferred_times_arr))); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid-form">
            <div class="card">
                <form id="enrollmentForm" action="enroll.php" method="POST" novalidate>
                    <div style="margin-bottom: 2rem;">
                        <h3 style="border-bottom: 2px solid var(--border-light); padding-bottom: 0.5rem; margin-bottom: 1.25rem; color: var(--bg-navy);">
                            1. Personal Details (Pre-filled from Account)
                        </h3>
                        
                        <div class="form-group">
                            <label for="fullname" class="form-label">Full Name</label>
                            <input type="text" id="fullname" name="fullname" class="form-control" value="<?php echo htmlspecialchars($fullname); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" readonly>
                        </div>
                    </div>

                    <div style="margin-bottom: 2rem;">
                        <h3 style="border-bottom: 2px solid var(--border-light); padding-bottom: 0.5rem; margin-bottom: 1.25rem; color: var(--bg-navy);">
                            2. Membership & Preferences
                        </h3>

                        <div class="form-group <?php echo isset($errors['plan']) ? 'has-error' : ''; ?>">
                            <label for="plan" class="form-label">Select Membership Plan <span style="color: var(--text-error);">*</span></label>
                            <select id="plan" name="plan" class="form-control <?php echo isset($errors['plan']) ? 'is-invalid' : ''; ?>" required>
                                <option value="">-- Choose Plan --</option>
                                <option value="basic" <?php echo $plan === 'basic' ? 'selected' : ''; ?>>Basic Plan (3 Days / Week) - KES 3,500/mo</option>
                                <option value="premium" <?php echo $plan === 'premium' ? 'selected' : ''; ?>>Premium Plan (Unlimited Gym Access) - KES 6,000/mo</option>
                                <option value="vip" <?php echo $plan === 'vip' ? 'selected' : ''; ?>>VIP Plan (Unlimited Access + Personal Trainer) - KES 10,000/mo</option>
                            </select>
                            <div class="error-feedback" id="planError"><?php echo $errors['plan'] ?? ''; ?></div>
                        </div>

                        <div class="form-group <?php echo isset($errors['goal']) ? 'has-error' : ''; ?>" id="goalGroup">
                            <label class="form-label">Primary Fitness Goal <span style="color: var(--text-error);">*</span></label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" id="goal-loss" name="goal" value="weight_loss" <?php echo $goal === 'weight_loss' ? 'checked' : ''; ?>>
                                    <span>Weight Loss & Body Fat Reduction</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" id="goal-gain" name="goal" value="muscle_gain" <?php echo $goal === 'muscle_gain' ? 'checked' : ''; ?>>
                                    <span>Muscle Building & Hypertrophy</span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" id="goal-endurance" name="goal" value="endurance" <?php echo $goal === 'endurance' ? 'checked' : ''; ?>>
                                    <span>Endurance & Athletic Conditioning</span>
                                </label>
                            </div>
                            <div class="error-feedback" id="goalError"><?php echo $errors['goal'] ?? ''; ?></div>
                        </div>

                        <div class="form-group <?php echo isset($errors['times']) ? 'has-error' : ''; ?>" id="timesGroup">
                            <label class="form-label">Preferred Training Times (Select all that apply) <span style="color: var(--text-error);">*</span></label>
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" id="time-morning" name="times[]" value="morning" <?php echo in_array('morning', $preferred_times_arr) ? 'checked' : ''; ?>>
                                    <span>Morning Session (05:00 AM - 11:00 AM)</span>
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" id="time-afternoon" name="times[]" value="afternoon" <?php echo in_array('afternoon', $preferred_times_arr) ? 'checked' : ''; ?>>
                                    <span>Afternoon Session (12:00 PM - 04:00 PM)</span>
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" id="time-evening" name="times[]" value="evening" <?php echo in_array('evening', $preferred_times_arr) ? 'checked' : ''; ?>>
                                    <span>Evening Session (05:00 PM - 09:00 PM)</span>
                                </label>
                            </div>
                            <div class="error-feedback" id="timesError"><?php echo $errors['times'] ?? ''; ?></div>
                        </div>
                    </div>

                    <div style="margin-bottom: 2rem;">
                        <h3 style="border-bottom: 2px solid var(--border-light); padding-bottom: 0.5rem; margin-bottom: 1.25rem; color: var(--bg-navy);">
                            3. Additional Information
                        </h3>
                        <div class="form-group">
                            <label for="health_notes" class="form-label">Health Declarations / Special Requests:</label>
                            <textarea id="health_notes" name="health_notes" class="form-control" rows="4" placeholder="List any medical conditions, prior injuries, or specific trainer preferences..."><?php echo htmlspecialchars($health_notes); ?></textarea>
                        </div>
                    </div>

                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">Submit Enrollment</button>
                        <button type="reset" class="btn btn-secondary" onclick="setTimeout(initMembershipCalculator, 50)">Reset Form</button>
                    </div>
                </form>
            </div>

            <div class="sticky-summary" id="summaryCard">
                <div class="summary-header">
                    <h3 id="summaryPlanName" style="margin-bottom: 0.5rem; color: var(--bg-navy);">Select a Plan</h3>
                    <div>
                        <span class="price-tag" id="summaryPrice">KES 0</span>
                        <span class="price-period" id="summaryPeriod">/ month</span>
                    </div>
                </div>
                <h4 style="font-size: 1rem; margin-bottom: 0.75rem;">Included Features & Perks:</h4>
                <ul class="perks-list" id="summaryPerks">
                    <li>Choose a plan from the dropdown to calculate your pricing and view included benefits.</li>
                </ul>
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
