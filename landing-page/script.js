document.addEventListener('DOMContentLoaded', () => {
  // Dynamic text animation
  const phrases = [
    "Admins who command",
    "Managers who execute",
    "Teams who collaborate"
  ];

  const dynamicText = document.querySelector('.hero__dynamic-text');
  let currentPhraseIndex = 0;
  let currentCharIndex = 0;
  let isDeleting = false;
  const typingSpeed = 100; // 0.1s per character

  function typeText() {
    const currentPhrase = phrases[currentPhraseIndex];

    if (isDeleting) {
      dynamicText.textContent = currentPhrase.substring(0, currentCharIndex - 1);
      currentCharIndex--;
    } else {
      dynamicText.textContent = currentPhrase.substring(0, currentCharIndex + 1);
      currentCharIndex++;
    }

    // Add blinking cursor
    dynamicText.classList.add('hero__dynamic-text--blinking');

    if (!isDeleting && currentCharIndex === currentPhrase.length) {
      // Pause at the end of typing
      isDeleting = true;
      setTimeout(typeText, 2000); // 2s pause
      return;
    } else if (isDeleting && currentCharIndex === 0) {
      // Move to next phrase
      isDeleting = false;
      currentPhraseIndex = (currentPhraseIndex + 1) % phrases.length;
    }

    setTimeout(typeText, typingSpeed);
  }

  // Start the typing animation
  typeText();

  // Smooth scroll for navigation links
  document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const target = document.querySelector(link.getAttribute('href'));
      if (target) {
        target.scrollIntoView({ behavior: 'smooth' });
      }
    });
  });

  // Add active class to navigation links on scroll
  const sections = document.querySelectorAll('section[id]');
  const navLinks = document.querySelectorAll('.nav-link');

  function updateActiveLink() {
    let current = '';
    sections.forEach(section => {
      const sectionTop = section.offsetTop;
      const sectionHeight = section.clientHeight;
      if (window.scrollY >= sectionTop - 200) {
        current = section.getAttribute('id');
      }
    });

    navLinks.forEach(link => {
      link.classList.remove('active');
      if (link.getAttribute('href').slice(1) === current) {
        link.classList.add('active');
      }
    });
  }

  window.addEventListener('scroll', updateActiveLink);
  updateActiveLink(); // Initial check
});

// Navbar scroll effect
document.addEventListener('scroll', () => {
  const navbar = document.querySelector('.navbar');
  if (window.scrollY > 50) {
    navbar.classList.add('scrolled');
  } else {
    navbar.classList.remove('scrolled');
  }
});

// Mobile navigation toggle
document.querySelector('.nav__mobile-toggle').addEventListener('click', () => {
  document.querySelector('.nav__links').classList.toggle('active');
  document.querySelector('.nav__mobile-toggle').classList.toggle('active');
});

// Close mobile menu when clicking outside
document.addEventListener('click', (e) => {
  const nav = document.querySelector('.nav__links');
  const toggle = document.querySelector('.nav__mobile-toggle');
  if (nav.classList.contains('active') && !e.target.closest('.nav__links') && !e.target.closest('.nav__mobile-toggle')) {
    nav.classList.remove('active');
    toggle.classList.remove('active');
  }
});

// Close mobile menu when clicking a link
document.querySelectorAll('.nav__link').forEach(link => {
  link.addEventListener('click', () => {
    document.querySelector('.nav__links').classList.remove('active');
    document.querySelector('.nav__mobile-toggle').classList.remove('active');
  });
});
