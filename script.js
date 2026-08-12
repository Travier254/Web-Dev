document.addEventListener('DOMContentLoaded', () => {
  initWelcomeMessage();
  initFormValidation();
  initMembershipCalculator();
  initScheduleFiltersAndTrainerModal();
  initFAQAccordion();
});

function initWelcomeMessage() {
  const welcomeModal = document.getElementById('welcomeModal');
  const welcomeForm = document.getElementById('welcomeForm');
  const modalNameInput = document.getElementById('modalNameInput');
  const welcomeGreeting = document.getElementById('welcomeGreeting');
  const btnChangeName = document.getElementById('btnChangeName');
  const nameErrorFeedback = document.getElementById('modalNameError');

  if (!welcomeGreeting) return;

  const savedName = localStorage.getItem('travi_user_name');

  if (savedName) {
    updateGreetingBanner(savedName);
  } else if (welcomeModal) {
    openModal(welcomeModal);
  }

  if (welcomeForm) {
    welcomeForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const inputVal = modalNameInput.value.trim();

      if (!inputVal || inputVal.length < 2) {
        if (nameErrorFeedback) {
          nameErrorFeedback.style.display = 'block';
          nameErrorFeedback.textContent = 'Please enter a valid name (at least 2 letters).';
        }
        modalNameInput.classList.add('is-invalid');
        return;
      }

      localStorage.setItem('travi_user_name', inputVal);
      updateGreetingBanner(inputVal);
      closeModal(welcomeModal);
    });

    modalNameInput.addEventListener('input', () => {
      modalNameInput.classList.remove('is-invalid');
      if (nameErrorFeedback) nameErrorFeedback.style.display = 'none';
    });
  }

  if (btnChangeName && welcomeModal) {
    btnChangeName.addEventListener('click', () => {
      if (modalNameInput) {
        modalNameInput.value = localStorage.getItem('travi_user_name') || '';
      }
      openModal(welcomeModal);
    });
  }
}

function updateGreetingBanner(name) {
  const welcomeGreeting = document.getElementById('welcomeGreeting');
  if (welcomeGreeting) {
    welcomeGreeting.innerHTML = `Welcome to Travi Fitness, <span style="color: var(--primary-orange);">${escapeHTML(name)}</span>! Ready to crush your goals today?`;
  }
}


function initFormValidation() {
  const form = document.getElementById('enrollmentForm');
  const successAlert = document.getElementById('successAlert');
  const successDetails = document.getElementById('successDetails');

  if (!form) return;

  const fields = {
    fullname: document.getElementById('fullname'),
    email: document.getElementById('email'),
    age: document.getElementById('age'),
    plan: document.getElementById('plan')
  };

  Object.keys(fields).forEach(key => {
    const el = fields[key];
    if (el) {
      el.addEventListener('input', () => validateField(key, false));
      el.addEventListener('change', () => validateField(key, false));
    }
  });

  document.querySelectorAll('input[name="goal"]').forEach(radio => {
    radio.addEventListener('change', () => clearGroupError('goalGroup'));
  });

  document.querySelectorAll('input[name="times[]"]').forEach(cb => {
    cb.addEventListener('change', () => clearGroupError('timesGroup'));
  });

  form.addEventListener('submit', (e) => {
    let isValid = true;
    let firstErrorEl = null;

    ['fullname', 'email', 'age', 'plan'].forEach(key => {
      const fieldValid = validateField(key, true);
      if (!fieldValid) {
        isValid = false;
        if (!firstErrorEl) firstErrorEl = fields[key];
      }
    });

    const selectedGoal = document.querySelector('input[name="goal"]:checked');
    if (!selectedGoal) {
      isValid = false;
      showGroupError('goalGroup', 'Please select a primary fitness goal.');
      if (!firstErrorEl) firstErrorEl = document.getElementById('goalGroup');
    } else {
      clearGroupError('goalGroup');
    }

    const selectedTimes = document.querySelectorAll('input[name="times[]"]:checked');
    if (selectedTimes.length === 0) {
      isValid = false;
      showGroupError('timesGroup', 'Please select at least one preferred training time.');
      if (!firstErrorEl) firstErrorEl = document.getElementById('timesGroup');
    } else {
      clearGroupError('timesGroup');
    }

    if (!isValid) {
      e.preventDefault();
      if (firstErrorEl) {
        firstErrorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }
  });
}

function validateField(key, isSubmitting) {
  const el = document.getElementById(key);
  const errorEl = document.getElementById(`${key}Error`);
  if (!el || !errorEl) return true;

  const val = el.value.trim();
  let errorMsg = '';

  if (key === 'fullname') {
    if (!val) {
      errorMsg = 'Full name is required.';
    } else if (val.length < 3) {
      errorMsg = 'Full name must be at least 3 characters long.';
    } else if (!/^[a-zA-Z\s]+$/.test(val)) {
      errorMsg = 'Full name can only contain letters and spaces.';
    }
  } else if (key === 'email') {
    if (!val) {
      errorMsg = 'Email address is required.';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
      errorMsg = 'Please enter a valid email address (e.g. name@domain.com).';
    }
  } else if (key === 'age') {
    const ageNum = parseInt(val, 10);
    if (!val) {
      errorMsg = 'Age is required.';
    } else if (isNaN(ageNum) || ageNum < 15 || ageNum > 100) {
      errorMsg = 'Age must be between 15 and 100 years.';
    }
  } else if (key === 'plan') {
    if (!val) {
      errorMsg = 'Please select a membership plan.';
    }
  }

  const groupContainer = el.closest('.form-group');

  if (errorMsg) {
    el.classList.add('is-invalid');
    errorEl.textContent = errorMsg;
    if (groupContainer) groupContainer.classList.add('has-error');
    return false;
  } else {
    el.classList.remove('is-invalid');
    errorEl.textContent = '';
    if (groupContainer) groupContainer.classList.remove('has-error');
    return true;
  }
}

function showGroupError(groupId, msg) {
  const group = document.getElementById(groupId);
  if (!group) return;
  const errorEl = group.querySelector('.error-feedback');
  group.classList.add('has-error');
  if (errorEl) {
    errorEl.textContent = msg;
    errorEl.style.display = 'block';
  }
}

function clearGroupError(groupId) {
  const group = document.getElementById(groupId);
  if (!group) return;
  const errorEl = group.querySelector('.error-feedback');
  group.classList.remove('has-error');
  if (errorEl) {
    errorEl.textContent = '';
    errorEl.style.display = 'none';
  }
}


function initMembershipCalculator() {
  const planSelect = document.getElementById('plan');
  const priceDisplay = document.getElementById('summaryPrice');
  const planNameDisplay = document.getElementById('summaryPlanName');
  const perksList = document.getElementById('summaryPerks');

  if (!planSelect || !priceDisplay || !planNameDisplay || !perksList) return;

  const planData = {
    basic: {
      name: 'Basic Plan',
      price: 'KES 3,500',
      period: '/ month',
      perks: [
        '3 Days / Week Access',
        'Standard Gym Equipment Floor',
        'Locker Room Access',
        'Mobile App Progress Tracker'
      ]
    },
    premium: {
      name: 'Premium Plan',
      price: 'KES 6,000',
      period: '/ month',
      perks: [
        'Unlimited Gym Access (7 Days/Week)',
        'Full Equipment & Free Weights Zone',
        'All Group Fitness Classes Included',
        'Sauna & Steam Room Access',
        '1 Monthly Trainer Check-in'
      ]
    },
    vip: {
      name: 'VIP Plan',
      price: 'KES 10,000',
      period: '/ month',
      perks: [
        'All Premium Plan Features Included',
        'Dedicated Personal Trainer (2 Sessions/wk)',
        'Customized Nutrition & Meal Plan',
        'Priority Class Reservations',
        'Free Guest Pass (2 per month)'
      ]
    }
  };

  function updateSummary() {
    const selected = planSelect.value;
    const data = planData[selected] || {
      name: 'Select a Plan',
      price: 'KES 0',
      period: '/ month',
      perks: ['Choose a plan to see included features and pricing perks.']
    };

    priceDisplay.textContent = data.price;
    planNameDisplay.textContent = data.name;
    perksList.innerHTML = data.perks.map(p => `<li>${escapeHTML(p)}</li>`).join('');
  }

  planSelect.addEventListener('change', updateSummary);
  updateSummary(); // Initialize state
}


function initScheduleFiltersAndTrainerModal() {
  const filterBtns = document.querySelectorAll('.filter-btn');
  const scheduleCards = document.querySelectorAll('.schedule-card');
  const trainerModal = document.getElementById('trainerModal');

  if (filterBtns.length > 0 && scheduleCards.length > 0) {
    filterBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        filterBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const category = btn.getAttribute('data-filter');

        scheduleCards.forEach(card => {
          const cardCat = card.getAttribute('data-category');
          if (category === 'all' || cardCat === category) {
            card.style.display = 'flex';
          } else {
            card.style.display = 'none';
          }
        });
      });
    });
  }

  const trainerProfiles = {
    'Amina Juma': {
      role: 'Head Yoga & Mindful Movement Specialist',
      experience: '7+ Years Experience',
      bio: 'Amina specializes in Vinyasa and Hatha Yoga, focusing on breath control, core alignment, and stress reduction for high-performance athletes.',
      certifications: 'KEFOSA Certified Yoga Instructor & TVET Accredited Fitness Specialist'
    },
    'David Kiprop': {
      role: 'Lead HIIT & Conditioning Coach',
      experience: '9+ Years Experience',
      bio: 'David brings high-energy cardio metabolic workouts that burn maximum calories while building functional muscle endurance.',
      certifications: 'Kenya Sports Academy (KSA) Certified Strength & Conditioning Coach'
    },
    'John Mwangi': {
      role: 'Master Strength & Powerlifting Coach',
      experience: '12+ Years Experience',
      bio: 'John guides lifters from foundational barbell posture to advanced powerlifting technique with strict emphasis on safety and hyper-growth.',
      certifications: 'Kenya Powerlifting Association (KPA) Master Coach & TVET Certified Trainer'
    },
    'Grace Wanjiku': {
      role: 'Core & Pillar Stability Trainer',
      experience: '5+ Years Experience',
      bio: 'Grace designs laser-focused core exercises that eliminate lower back stiffness while building rock-solid abs.',
      certifications: 'Kenya National Fitness Association (KNFA) Certified Group Instructor'
    },
    'Chris Onyango': {
      role: 'Cardio Kickboxing & Agility Specialist',
      experience: '8+ Years Experience',
      bio: 'Chris blends martial arts striking drills with rhythmic endurance footwork for explosive stress release and agility.',
      certifications: 'Kenya Kickboxing Federation (KKBF) Licensed Instructor & KSA Fitness Practitioner'
    },
    'Fatuma Abdi': {
      role: 'Swim Conditioning & Aqua Aerobics Specialist',
      experience: '6+ Years Experience',
      bio: 'Fatuma leads high-intensity, low-impact water workouts designed to enhance lung capacity and joint mobility.',
      certifications: 'Kenya Swimming Association (KSA) Certified Aquatic Fitness Instructor'
    },
    'Brian Oduya': {
      role: 'Sports Recovery & Mobility Practitioner',
      experience: '10+ Years Experience',
      bio: 'Brian specializes in myofascial release, joint decompression, and active recovery routines for fatigue management.',
      certifications: 'Kenya Physiotherapy Association (KPA) Accredited Movement Specialist'
    },
    'Mercy Nyambura': {
      role: 'Boxing Fundamentals & Combat Instructor',
      experience: '7+ Years Experience',
      bio: 'Mercy teaches precision boxing combinations, footwork defense, and conditioning drills for mental toughness.',
      certifications: 'Boxing Federation of Kenya (BFK) Certified Coach & KSA Instructor'
    },
    'Samuel Rotich': {
      role: 'Power Cycling & Cardio Endurance Coach',
      experience: '8+ Years Experience',
      bio: 'Samuel brings high-cadence spin intervals tailored to simulate elevation climbs and stamina building.',
      certifications: 'Kenya Cycling Federation (KCF) Certified Endurance Trainer'
    },
    'Irene Achieng': {
      role: 'Functional Athletic Conditioning Trainer',
      experience: '6+ Years Experience',
      bio: 'Irene focuses on kinetic chain movements, kettlebell flows, and plyometrics for overall athletic speed.',
      certifications: 'Kenya National Fitness Association (KNFA) Certified Functional Specialist'
    },
    'Peter Kamau': {
      role: 'Posture Correction & Mat Pilates Instructor',
      experience: '9+ Years Experience',
      bio: 'Peter helps members strengthen deep core stabilizer muscles and resolve postural imbalances caused by sedentary work.',
      certifications: 'Kenya Pilates & Movement Arts Guild Certified Instructor'
    }
  };

  const trainerBtns = document.querySelectorAll('.schedule-trainer-btn');
  if (trainerBtns.length > 0 && trainerModal) {
    trainerBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        const trainerName = btn.getAttribute('data-trainer');
        const data = trainerProfiles[trainerName];

        if (data) {
          document.getElementById('trainerModalName').textContent = trainerName;
          document.getElementById('trainerModalRole').textContent = data.role;
          document.getElementById('trainerModalExp').textContent = data.experience;
          document.getElementById('trainerModalBio').textContent = data.bio;
          document.getElementById('trainerModalCerts').textContent = data.certifications;

          openModal(trainerModal);
        }
      });
    });
  }
}


function initFAQAccordion() {
  const faqQuestions = document.querySelectorAll('.faq-question');
  if (faqQuestions.length === 0) return;

  faqQuestions.forEach(q => {
    q.addEventListener('click', () => {
      const item = q.parentElement;
      const isOpen = item.classList.contains('open');

      document.querySelectorAll('.faq-item.open').forEach(openItem => {
        if (openItem !== item) {
          openItem.classList.remove('open');
        }
      });

      if (isOpen) {
        item.classList.remove('open');
      } else {
        item.classList.add('open');
      }
    });
  });
}


function openModal(modalEl) {
  if (!modalEl) return;
  modalEl.classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeModal(modalEl) {
  if (!modalEl) return;
  modalEl.classList.remove('active');
  document.body.style.overflow = '';
}

document.addEventListener('click', (e) => {
  if (e.target.classList.contains('modal-close') || e.target.classList.contains('modal-overlay')) {
    const activeModal = document.querySelector('.modal-overlay.active');
    if (activeModal) {
      closeModal(activeModal);
    }
  }
});

function escapeHTML(str) {
  if (!str) return '';
  return str.replace(/[&<>"']/g, (match) => {
    const map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    return map[match];
  });
}
