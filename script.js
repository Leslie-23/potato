// Simulated database values
const dbValues = {
  activeMembers: 2847,
  weeklyTrainingSessions: 312,
  equipmentUptime: 90,
  avgWeightLoss: 24,
  peopleTrainingNow: 78,
  availableSpots: 14,
  caloriesBurned: 187450,
  nextSessionDay: "Today",
  nextSessionTime: "6:30 PM",
  sessionClass: "HIIT Fusion",
  sessionTrainer: "Sarah Chen",
  sessionSpots: 4,
};

// Counter animation function
function animateCounter(element, target, duration = 2000, startDelay = 0) {
  const start = 0;
  const increment = target / (duration / 16);
  let current = start;

  setTimeout(() => {
    const timer = setInterval(() => {
      current += increment;
      if (current >= target) {
        clearInterval(timer);
        current = target;
      }
      element.textContent = Math.floor(current).toLocaleString();
    }, 16);
  }, startDelay);
}

// Initialize counters when elements are in viewport
function initCounters() {
  const counters = [
    { id: "active-members", value: dbValues.activeMembers, delay: 0 },
    {
      id: "weekly-sessions",
      value: dbValues.weeklyTrainingSessions,
      delay: 200,
    },
    { id: "active-trainers", value: dbValues.peopleTrainingNow, delay: 0 },
    { id: "available-spots", value: dbValues.availableSpots, delay: 100 },
    { id: "calories-burned", value: dbValues.caloriesBurned, delay: 200 },
    { id: "avg-weight-loss", value: dbValues.avgWeightLoss, delay: 500 },
  ];

  counters.forEach((counter) => {
    const element = document.getElementById(counter.id);
    if (element) {
      animateCounter(element, counter.value, 2000, counter.delay);
    }
  });
}

// Testimonial carousel
function initTestimonialCarousel() {
  const testimonials = document.querySelectorAll(".testimonial-card");
  const dots = document.querySelectorAll(".dot");
  let currentIndex = 0;

  function showTestimonial(index) {
    testimonials.forEach((testimonial) => {
      testimonial.classList.remove("active");
    });

    dots.forEach((dot) => {
      dot.classList.remove("active");
    });

    testimonials[index].classList.add("active");
    dots[index].classList.add("active");
    currentIndex = index;
  }

  // Add click event to dots
  dots.forEach((dot, index) => {
    dot.addEventListener("click", () => {
      showTestimonial(index);
    });
  });

  // Auto rotate testimonials
  setInterval(() => {
    let nextIndex = (currentIndex + 1) % testimonials.length;
    showTestimonial(nextIndex);
  }, 5000);
}

// Future stats calculator
function initCalculator() {
  const calculateBtn = document.getElementById("calculate-btn");

  if (calculateBtn) {
    calculateBtn.addEventListener("click", () => {
      const currentWeight =
        parseInt(document.getElementById("current-weight").value) || 180;
      const goalType = document.getElementById("goal-type").value;
      const commitment =
        parseInt(document.getElementById("commitment").value) || 3;

      // Calculate results based on inputs
      let weightLoss3Month, weightLoss6Month, weightLoss12Month;
      let strength3Month, strength6Month, strength12Month;
      let endurance3Month, endurance6Month, endurance12Month;

      // Weight loss calculations (lbs)
      if (goalType === "weight-loss") {
        weightLoss3Month = commitment * 5;
        weightLoss6Month = commitment * 9;
        weightLoss12Month = commitment * 15;
      } else if (goalType === "muscle-gain") {
        weightLoss3Month = -commitment * 3; // Negative for weight gain
        weightLoss6Month = -commitment * 6;
        weightLoss12Month = -commitment * 10;
      } else {
        weightLoss3Month = commitment * 3;
        weightLoss6Month = commitment * 5;
        weightLoss12Month = commitment * 8;
      }

      // Strength improvements (%)
      strength3Month = commitment * 7;
      strength6Month = commitment * 15;
      strength12Month = commitment * 28;

      // Endurance improvements (%)
      endurance3Month = commitment * 10;
      endurance6Month = commitment * 20;
      endurance12Month = commitment * 40;

      // Update UI
      document.getElementById("three-month-weight").textContent =
        (weightLoss3Month > 0 ? "-" : "+") +
        Math.abs(weightLoss3Month) +
        " lbs";
      document.getElementById("six-month-weight").textContent =
        (weightLoss6Month > 0 ? "-" : "+") +
        Math.abs(weightLoss6Month) +
        " lbs";
      document.getElementById("twelve-month-weight").textContent =
        (weightLoss12Month > 0 ? "-" : "+") +
        Math.abs(weightLoss12Month) +
        " lbs";

      document.getElementById("three-month-strength").textContent =
        "+" + strength3Month + "% Strength";
      document.getElementById("six-month-strength").textContent =
        "+" + strength6Month + "% Strength";
      document.getElementById("twelve-month-strength").textContent =
        "+" + strength12Month + "% Strength";

      document.getElementById("three-month-endurance").textContent =
        "+" + endurance3Month + "% Endurance";
      document.getElementById("six-month-endurance").textContent =
        "+" + endurance6Month + "% Endurance";
      document.getElementById("twelve-month-endurance").textContent =
        "+" + endurance12Month + "% Endurance";
    });
  }
}

// Initialize next session data
function initNextSession() {
  document.getElementById("next-session-day").textContent =
    dbValues.nextSessionDay;
  document.getElementById("next-session-time").textContent =
    dbValues.nextSessionTime;
  document.getElementById("session-class").textContent = dbValues.sessionClass;
  document.getElementById("session-trainer").textContent =
    dbValues.sessionTrainer;
  document.getElementById("session-spots").textContent = dbValues.sessionSpots;
}

// Intersection Observer for animations
function initIntersectionObserver() {
  const elements = document.querySelectorAll(
    ".feature-card, .trainer-card, .result-card"
  );

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = "0";
          entry.target.style.transform = "translateY(20px)";

          setTimeout(() => {
            entry.target.style.transition =
              "opacity 0.5s ease, transform 0.5s ease";
            entry.target.style.opacity = "1";
            entry.target.style.transform = "translateY(0)";
          }, 100);

          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.1 }
  );

  elements.forEach((element) => {
    observer.observe(element);
  });
}

// Initialize live stats ticker animation
function initLiveStatsTicker() {
  // Randomly update people training now
  setInterval(() => {
    const element = document.getElementById("active-trainers");
    const currentValue = parseInt(element.textContent);
    const change = Math.floor(Math.random() * 5) - 2; // Random change between -2 and +2
    const newValue = Math.max(currentValue + change, 65); // Don't go below 65
    element.textContent = newValue;
  }, 5000);

  // Randomly update available spots
  setInterval(() => {
    const element = document.getElementById("available-spots");
    const currentValue = parseInt(element.textContent);
    const change = Math.floor(Math.random() * 3) - 1; // Random change between -1 and +1
    const newValue = Math.max(Math.min(currentValue + change, 20), 0); // Between 0 and 20
    element.textContent = newValue;
  }, 8000);

  // Continuously update calories burned
  setInterval(() => {
    const element = document.getElementById("calories-burned");
    const currentValue = parseInt(element.textContent.replace(/,/g, ""));
    const newValue = currentValue + Math.floor(Math.random() * 50) + 10;
    element.textContent = newValue.toLocaleString();
  }, 3000);
}

// Initialize all functions when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
  initCounters();
  initTestimonialCarousel();
  initCalculator();
  initNextSession();
  initIntersectionObserver();
  initLiveStatsTicker();
}); // Set current year in footer
document.getElementById("current-year").textContent = new Date().getFullYear();

// Mobile menu toggle
const mobileMenuToggle = document.querySelector(".mobile-menu-toggle");
const nav = document.querySelector(".nav");

if (mobileMenuToggle && nav) {
  mobileMenuToggle.addEventListener("click", () => {
    nav.classList.toggle("active");
    mobileMenuToggle.classList.toggle("active");
  });
}

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();

    const targetId = this.getAttribute("href");
    if (targetId === "#") return;

    const targetElement = document.querySelector(targetId);
    if (targetElement) {
      window.scrollTo({
        top: targetElement.offsetTop - 80, // Adjust for header height
        behavior: "smooth",
      });

      // Close mobile menu if open
      if (nav && nav.classList.contains("active")) {
        nav.classList.remove("active");
        mobileMenuToggle.classList.remove("active");
      }
    }
  });
});

// Add active class to nav links based on scroll position
function setActiveNavLink() {
  const sections = document.querySelectorAll("section[id]");
  const navLinks = document.querySelectorAll(".nav-link");

  let currentSection = "";

  sections.forEach((section) => {
    const sectionTop = section.offsetTop - 100;
    const sectionHeight = section.offsetHeight;
    const sectionId = section.getAttribute("id");

    if (
      window.scrollY >= sectionTop &&
      window.scrollY < sectionTop + sectionHeight
    ) {
      currentSection = sectionId;
    }
  });

  navLinks.forEach((link) => {
    link.classList.remove("active");
    if (link.getAttribute("href") === `#${currentSection}`) {
      link.classList.add("active");
    }
  });
}

// Add scroll event listener
window.addEventListener("scroll", setActiveNavLink);

// Initialize active nav link on page load
document.addEventListener("DOMContentLoaded", setActiveNavLink);

// Add CSS for active nav link
const style = document.createElement("style");
style.textContent = `
  .nav-link.active {
    color: var(--primary);
    font-weight: 600;
  }
  
  .nav.active {
    display: flex;
    flex-direction: column;
    position: absolute;
    top: 4rem;
    left: 0;
    right: 0;
    background-color: var(--background);
    padding: 1rem;
    border-bottom: 1px solid var(--border);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  }
  
  .mobile-menu-toggle.active span:nth-child(1) {
    transform: translateY(8px) rotate(45deg);
  }
  
  .mobile-menu-toggle.active span:nth-child(2) {
    opacity: 0;
  }
  
  .mobile-menu-toggle.active span:nth-child(3) {
    transform: translateY(-8px) rotate(-45deg);
  }
`;
document.head.appendChild(style);
