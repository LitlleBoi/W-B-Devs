// Wait for DOM to be fully loaded
document.addEventListener("DOMContentLoaded", function () {
  const layout = document.getElementById("layout");
  const openBtn = document.getElementById("openMenu");
  const closeBtn = document.getElementById("closeMenu");
  const overlay = document.querySelector(".overlay");

  // Check if elements exist before proceeding
  if (!layout || !openBtn || !closeBtn) {
    console.error("Required elements not found. Check your HTML structure.");
    return;
  }

  // Open menu function
  function openMenu() {
    if (window.innerWidth >= 1024) {
      // Desktop: remove hidden class
      layout.classList.remove("menu-hidden-desktop");
    } else {
      // Mobile: add visible class
      layout.classList.add("menu-visible");
    }
  }

  // Close menu function
  function closeMenu() {
    if (window.innerWidth >= 1024) {
      // Desktop: add hidden class
      layout.classList.add("menu-hidden-desktop");
    } else {
      // Mobile: remove visible class
      layout.classList.remove("menu-visible");
    }
  }

  // Event listeners
  openBtn.addEventListener("click", openMenu);
  closeBtn.addEventListener("click", closeMenu);

  // Close menu when clicking overlay on mobile
  if (overlay) {
    overlay.addEventListener("click", () => {
      if (window.innerWidth < 1024) {
        closeMenu();
      }
    });
  }

  // Optional: Close menu when pressing Escape key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      closeMenu();
    }
  });

  // Optional: Handle window resize
  window.addEventListener("resize", () => {
    if (window.innerWidth >= 1024) {
      // On desktop, ensure proper state
      if (layout.classList.contains("menu-visible")) {
        layout.classList.remove("menu-visible");
        layout.classList.remove("menu-hidden-desktop");
      }
    } else {
      // On mobile, ensure proper state
      if (
        !layout.classList.contains("menu-visible") &&
        !layout.classList.contains("menu-hidden-desktop")
      ) {
        layout.classList.add("menu-hidden-desktop");
      }
    }
  });

  // Initialize based on current screen size
  if (window.innerWidth >= 1024) {
    // Desktop: sidebar visible by default
    layout.classList.remove("menu-visible");
    layout.classList.remove("menu-hidden-desktop");
  } else {
    // Mobile: sidebar hidden by default
    if (
      !layout.classList.contains("menu-visible") &&
      !layout.classList.contains("menu-hidden-desktop")
    ) {
      layout.classList.add("menu-hidden-desktop");
    }
  }
});
