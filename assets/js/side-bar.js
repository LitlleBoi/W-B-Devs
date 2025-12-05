// === DEZE CODE TOEVOEGEN AAN JE BESTAANDE side-bar.js ===

// Bij je andere variabelen bovenaan:
let sidebarOpen = false;
let popupOpen = false;

// In je initialize functie of ergens aan het begin:
function checkPopupState() {
  // Controleer of er een popup open is
  const activeModal = document.querySelector(".modal.active");
  popupOpen = !!activeModal; // true als er een popup open is

  // Als popup open is, sluit sidebar
  if (popupOpen && sidebarOpen) {
    closeSidebar();
  }
}

// Pas je openSidebar functie aan:
function openSidebar() {
  // Eerst controleren of popup open is
  checkPopupState();

  if (popupOpen) {
    console.log("Kan sidebar niet openen - popup is open");
    return;
  }

  // Je bestaande code om sidebar te openen...
  const layout = document.getElementById("layout");
  if (window.innerWidth >= 1024) {
    layout.classList.remove("menu-hidden-desktop");
  } else {
    layout.classList.add("menu-visible");
  }
  sidebarOpen = true;
}

// Pas je closeSidebar functie aan:
function closeSidebar() {
  const layout = document.getElementById("layout");
  if (window.innerWidth >= 1024) {
    layout.classList.add("menu-hidden-desktop");
  } else {
    layout.classList.remove("menu-visible");
  }
  sidebarOpen = false;
}

// Monitor popups (zet dit in je init functie)
function monitorPopups() {
  // Check elke seconde of er popup status verandert
  setInterval(checkPopupState, 1000);

  // Ook checken wanneer er geklikt wordt
  document.addEventListener("click", function (e) {
    // Als iemand op een popup knop klikt
    if (e.target.closest("[data-modal-target]")) {
      console.log("Popup knop geklikt - sidebar sluiten");
      closeSidebar();
    }
  });
}

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
