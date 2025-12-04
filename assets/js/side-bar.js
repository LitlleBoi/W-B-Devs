// Wait for DOM to be fully loaded
document.addEventListener("DOMContentLoaded", function () {
  const layout = document.getElementById("layout");
  const openBtn = document.getElementById("openMenu");
  const closeBtn = document.getElementById("closeMenu");
  const overlay = document.querySelector(".overlay");

  // Debug log
  console.log("Sidebar script loaded");
  console.log("Elements:", { layout, openBtn, closeBtn });

  // Open menu function
  function openMenu() {
    console.log("Opening menu");
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
    console.log("Closing menu");
    if (window.innerWidth >= 1024) {
      // Desktop: add hidden class
      layout.classList.add("menu-hidden-desktop");
    } else {
      // Mobile: remove visible class
      layout.classList.remove("menu-visible");
    }
  }

  // Event listeners
  if (openBtn) {
    openBtn.addEventListener("click", openMenu);
  }

  if (closeBtn) {
    closeBtn.addEventListener("click", closeMenu);
  }

  // Close menu when clicking overlay on mobile
  if (overlay) {
    overlay.addEventListener("click", closeMenu);
  }

  // Close menu when pressing Escape key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      closeMenu();
    }
  });

  // Initialize based on screen size
  function initSidebar() {
    if (window.innerWidth >= 1024) {
      // Desktop: show sidebar by default (as overlay)
      layout.classList.remove("menu-visible");
      layout.classList.remove("menu-hidden-desktop");
    } else {
      // Mobile: hide sidebar by default
      layout.classList.remove("menu-visible");
      layout.classList.add("menu-hidden-desktop");
    }
  }

  // Initialize
  initSidebar();

  // Re-initialize on resize
  window.addEventListener("resize", initSidebar);
});
