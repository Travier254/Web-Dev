<?php
require_once 'auth_guard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Schedule - Travi Fitness</title>
    <meta name="description" content="Weekly workout class schedule at Travi Fitness Nairobi. Filter classes by focus (Yoga, HIIT, Strength, Core, Kickboxing, Boxing, Cycling, Pilates) and view trainer bios.">
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
                    <p>Weekly Workout Sessions & Expert Trainers</p>
                </div>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="schedule.php" class="active">Class Schedule</a></li>
                    <li><a href="enroll.php">Enroll Now</a></li>
                    <li><a href="profile.php">My Profile</a></li>
                    <li><a href="logout.php">Log Out</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section style="margin-bottom: 2rem; text-align: center;">
            <h2>Weekly Training Schedule & Classes</h2>
            <p>Select your favorite workouts and explore instructor profiles. Click on any trainer to view their bio!</p>
        </section>

        <div class="filter-container">
            <button type="button" class="filter-btn active" data-filter="all">All Classes</button>
            <button type="button" class="filter-btn" data-filter="yoga">Yoga & Mindfulness</button>
            <button type="button" class="filter-btn" data-filter="hiit">HIIT Cardio</button>
            <button type="button" class="filter-btn" data-filter="strength">Power Strength</button>
            <button type="button" class="filter-btn" data-filter="core">Core Crusher</button>
            <button type="button" class="filter-btn" data-filter="kickboxing">Kickboxing & Boxing</button>
            <button type="button" class="filter-btn" data-filter="cycling">Power Cycling</button>
            <button type="button" class="filter-btn" data-filter="pilates">Pilates & Mobility</button>
        </div>

        <div class="grid-3" style="margin-bottom: 3rem;">
            <div class="schedule-card" data-category="yoga">
                <div>
                    <span style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--primary-orange); margin-bottom: 0.5rem;">Yoga Studio A</span>
                    <h3 style="margin-bottom: 0.25rem;">Morning Sunrise Yoga</h3>
                    <div class="schedule-time">Mon / Wed / Fri | 05:30 AM - 06:30 AM</div>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-top: 0.5rem;">
                        Target Focus: Flexibility, Deep Breathing & Mindfulness
                    </p>
                </div>
                <div>
                    <button type="button" class="schedule-trainer-btn btn-block" data-trainer="Amina Juma">
                        Trainer: Amina Juma (View Bio)
                    </button>
                </div>
            </div>

            <div class="schedule-card" data-category="cycling">
                <div>
                    <span style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--bg-navy); margin-bottom: 0.5rem;">Spin Studio</span>
                    <h3 style="margin-bottom: 0.25rem;">Early Bird Spin Blast</h3>
                    <div class="schedule-time">Tue / Thu | 06:00 AM - 07:00 AM</div>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-top: 0.5rem;">
                        Target Focus: High Rhythm Indoor Cycling & Leg Endurance
                    </p>
                </div>
                <div>
                    <button type="button" class="schedule-trainer-btn btn-block" data-trainer="Samuel Rotich">
                        Trainer: Samuel Rotich (View Bio)
                    </button>
                </div>
            </div>

            <div class="schedule-card" data-category="hiit">
                <div>
                    <span style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--primary-orange); margin-bottom: 0.5rem;">Studio B</span>
                    <h3 style="margin-bottom: 0.25rem;">Morning HIIT Burnout</h3>
                    <div class="schedule-time">Mon / Wed / Fri | 07:30 AM - 08:30 AM</div>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-top: 0.5rem;">
                        Target Focus: High Intensity Interval Cardio & Calorie Burn
                    </p>
                </div>
                <div>
                    <button type="button" class="schedule-trainer-btn btn-block" data-trainer="David Kiprop">
                        Trainer: David Kiprop (View Bio)
                    </button>
                </div>
            </div>

            <div class="schedule-card" data-category="pilates">
                <div>
                    <span style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--bg-navy); margin-bottom: 0.5rem;">Studio A</span>
                    <h3 style="margin-bottom: 0.25rem;">Aqua Aerobics & Swim</h3>
                    <div class="schedule-time">Tue / Thu | 09:00 AM - 10:15 AM</div>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-top: 0.5rem;">
                        Target Focus: Low-Impact Water Resistance & Cardiovascular Conditioning
                    </p>
                </div>
                <div>
                    <button type="button" class="schedule-trainer-btn btn-block" data-trainer="Fatuma Abdi">
                        Trainer: Fatuma Abdi (View Bio)
                    </button>
                </div>
            </div>

            <div class="schedule-card" data-category="kickboxing">
                <div>
                    <span style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--primary-orange); margin-bottom: 0.5rem;">Combat Ring Studio</span>
                    <h3 style="margin-bottom: 0.25rem;">Boxing Fundamentals</h3>
                    <div class="schedule-time">Mon / Wed | 11:30 AM - 12:45 PM</div>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-top: 0.5rem;">
                        Target Focus: Heavy Bag Combinations, Stance & Upper Body Power
                    </p>
                </div>
                <div>
                    <button type="button" class="schedule-trainer-btn btn-block" data-trainer="Mercy Nyambura">
                        Trainer: Mercy Nyambura (View Bio)
                    </button>
                </div>
            </div>

            <div class="schedule-card" data-category="pilates">
                <div>
                    <span style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--bg-navy); margin-bottom: 0.5rem;">Studio B</span>
                    <h3 style="margin-bottom: 0.25rem;">Midday Stretch & Mobility</h3>
                    <div class="schedule-time">Tue / Thu | 01:00 PM - 02:00 PM</div>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-top: 0.5rem;">
                        Target Focus: Joint Decompression, Fascia Release & Posture Reset
                    </p>
                </div>
                <div>
                    <button type="button" class="schedule-trainer-btn btn-block" data-trainer="Brian Oduya">
                        Trainer: Brian Oduya (View Bio)
                    </button>
                </div>
            </div>

            <div class="schedule-card" data-category="hiit">
                <div>
                    <span style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--primary-orange); margin-bottom: 0.5rem;">Main Gym Floor</span>
                    <h3 style="margin-bottom: 0.25rem;">Functional Athletic Training</h3>
                    <div class="schedule-time">Mon / Wed / Fri | 02:30 PM - 03:45 PM</div>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-top: 0.5rem;">
                        Target Focus: Kettlebells, Plyometrics & Agility Ladder Drills
                    </p>
                </div>
                <div>
                    <button type="button" class="schedule-trainer-btn btn-block" data-trainer="Irene Achieng">
                        Trainer: Irene Achieng (View Bio)
                    </button>
                </div>
            </div>

            <div class="schedule-card" data-category="kickboxing">
                <div>
                    <span style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--bg-navy); margin-bottom: 0.5rem;">Studio A</span>
                    <h3 style="margin-bottom: 0.25rem;">Cardio Kickboxing Extreme</h3>
                    <div class="schedule-time">Tue / Thu | 04:00 PM - 05:15 PM</div>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-top: 0.5rem;">
                        Target Focus: High Tempo Kick Strikes, Pad Work & Calorie Shred
                    </p>
                </div>
                <div>
                    <button type="button" class="schedule-trainer-btn btn-block" data-trainer="Chris Onyango">
                        Trainer: Chris Onyango (View Bio)
                    </button>
                </div>
            </div>

            <div class="schedule-card" data-category="strength">
                <div>
                    <span style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--primary-orange); margin-bottom: 0.5rem;">Main Gym Floor</span>
                    <h3 style="margin-bottom: 0.25rem;">Powerlifting & Hypertrophy</h3>
                    <div class="schedule-time">Mon / Wed / Fri | 05:30 PM - 07:00 PM</div>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-top: 0.5rem;">
                        Target Focus: Barbell Squat, Bench Press & Deadlift Form Masterclass
                    </p>
                </div>
                <div>
                    <button type="button" class="schedule-trainer-btn btn-block" data-trainer="John Mwangi">
                        Trainer: John Mwangi (View Bio)
                    </button>
                </div>
            </div>

            <div class="schedule-card" data-category="core">
                <div>
                    <span style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--bg-navy); margin-bottom: 0.5rem;">Studio B</span>
                    <h3 style="margin-bottom: 0.25rem;">Evening Core Crusher</h3>
                    <div class="schedule-time">Tue / Thu | 06:00 PM - 06:45 PM</div>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-top: 0.5rem;">
                        Target Focus: Abdominal Sculpting & Spinal Pillar Stability
                    </p>
                </div>
                <div>
                    <button type="button" class="schedule-trainer-btn btn-block" data-trainer="Grace Wanjiku">
                        Trainer: Grace Wanjiku (View Bio)
                    </button>
                </div>
            </div>

            <div class="schedule-card" data-category="pilates">
                <div>
                    <span style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--primary-orange); margin-bottom: 0.5rem;">Studio A</span>
                    <h3 style="margin-bottom: 0.25rem;">Post-Work Mat Pilates</h3>
                    <div class="schedule-time">Mon / Wed | 07:15 PM - 08:15 PM</div>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-top: 0.5rem;">
                        Target Focus: Core Alignment, Pelvic Balance & Spinal Flexibility
                    </p>
                </div>
                <div>
                    <button type="button" class="schedule-trainer-btn btn-block" data-trainer="Peter Kamau">
                        Trainer: Peter Kamau (View Bio)
                    </button>
                </div>
            </div>

            <div class="schedule-card" data-category="cycling">
                <div>
                    <span style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--bg-navy); margin-bottom: 0.5rem;">Spin Studio</span>
                    <h3 style="margin-bottom: 0.25rem;">Nightfall Sprint Spin</h3>
                    <div class="schedule-time">Friday | 07:30 PM - 08:30 PM</div>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-top: 0.5rem;">
                        Target Focus: High Energy Weekend Kickoff Cardio Spin
                    </p>
                </div>
                <div>
                    <button type="button" class="schedule-trainer-btn btn-block" data-trainer="Samuel Rotich">
                        Trainer: Samuel Rotich (View Bio)
                    </button>
                </div>
            </div>
        </div>

        <section class="card" style="margin-bottom: 2rem;">
            <div class="grid-2" style="align-items: center;">
                <div style="text-align: center;">
                    <img src="images/class.png" alt="Group Training Session at Travi Fitness" style="max-width: 100%; height: auto; border-radius: var(--radius-md); box-shadow: var(--shadow-md);">
                </div>
                <div>
                    <h2>Train Harder, Together</h2>
                    <p>
                        Group workouts build camaraderie, accountability, and boost individual performance. 
                        Reserve your spot in our upcoming sessions and take your training to the next level.
                    </p>
                    <div style="margin-top: 1.5rem;">
                        <a href="enroll.php" class="btn btn-primary">Enroll & Reserve Session</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div class="modal-overlay" id="trainerModal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 id="trainerModalName">Trainer Details</h3>
                <button type="button" class="modal-close" onclick="closeModal(document.getElementById('trainerModal'))">&times;</button>
            </div>
            <div style="margin-bottom: 1rem;">
                <span style="font-weight: 600; color: var(--primary-orange);" id="trainerModalRole">Specialist</span>
                <span style="font-size: 0.85rem; color: var(--text-muted); margin-left: 0.5rem;" id="trainerModalExp"></span>
            </div>
            <p id="trainerModalBio" style="margin-bottom: 1rem;"></p>
            <div style="background-color: var(--bg-main); padding: 0.85rem; border-radius: var(--radius-sm); margin-bottom: 1rem; border: 1px solid var(--border-light);">
                <strong>Certifications:</strong> <span id="trainerModalCerts"></span>
            </div>
            <div style="margin-top: 1.5rem; text-align: right;">
                <button type="button" class="btn btn-secondary" onclick="closeModal(document.getElementById('trainerModal'))">Close Profile</button>
            </div>
        </div>
    </div>

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
