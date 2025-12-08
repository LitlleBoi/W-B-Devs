// === DEZE CODE TOEVOEGEN AAN JE BESTAANDE side-bar.js ===

// Bij je andere variabelen bovenaan:
let sidebarOpen = false;

// Wait for DOM to be fully loaded
document.addEventListener("DOMContentLoaded", function () {
  const layout = document.getElementById("layout");
  const openBtn = document.getElementById("openMenu");
  const closeBtn = document.getElementById("closeMenu");

  // Debug log
  console.log("Sidebar script loaded");
  console.log("Elements:", { layout, openBtn, closeBtn });

  // Check if popup is open
  function isPopupOpen() {
    return document.querySelector(".modal.active") !== null;
  }

  // Open menu function
  function openMenu() {
    // Don't open if popup is open
    if (isPopupOpen()) {
      console.log("Popup is open, not opening sidebar");
      return;
    }

    console.log("Opening menu");
    if (window.innerWidth >= 1024) {
      // Desktop: remove hidden class
      layout.classList.remove("menu-hidden-desktop");
    } else {
      // Mobile: add visible class
      layout.classList.add("menu-visible");
    }
    sidebarOpen = true;
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
    sidebarOpen = false;
  }

  // Event listeners
  if (openBtn) {
    openBtn.addEventListener("click", function (e) {
      e.stopPropagation(); // Prevent event from bubbling
      openMenu();
    });
  }

  if (closeBtn) {
    closeBtn.addEventListener("click", function (e) {
      e.stopPropagation(); // Prevent event from bubbling
      closeMenu();
    });
  }

  // Close sidebar when Escape key is pressed (only if no popup is open)
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      if (!isPopupOpen()) {
        closeMenu();
      }
      // If popup is open, let pop-up.js handle the Escape key
    }
  });

  // Close sidebar when clicking on content area (only on mobile)
  document.querySelector(".content").addEventListener("click", function (e) {
    if (window.innerWidth < 1024 && sidebarOpen && !isPopupOpen()) {
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
