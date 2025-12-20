/**
 * Pop-up JavaScript
 *
 * Deze JavaScript file beheert de modal/pop-up functionaliteit voor panorama punten.
 * Het opent en sluit modals bij klikken op punten en overlay.
 */

document.addEventListener("DOMContentLoaded", function () {
  // Open modal bij klikken op een punt
  document.querySelectorAll("[data-modal-target]").forEach((button) => {
    button.addEventListener("click", () => {
      const modalId = button.getAttribute("data-modal-target");
      const modal = document.querySelector(modalId);
      const overlay = document.getElementById("overlay");

      if (modal) {
        modal.classList.add("active");
        overlay.classList.add("active");
      }
    });
  });

  // Close modal when clicking close button
  document.querySelectorAll("[data-close-button]").forEach((button) => {
    button.addEventListener("click", () => {
      const modals = document.querySelectorAll(".modal.active");
      const overlay = document.getElementById("overlay");

      modals.forEach((modal) => {
        modal.classList.remove("active");
      });

      overlay.classList.remove("active");
    });
  });

  // FIX: Close modal when clicking on overlay
  const overlay = document.getElementById("overlay");
  if (overlay) {
    overlay.addEventListener("click", function () {
      const modals = document.querySelectorAll(".modal.active");

      modals.forEach((modal) => {
        modal.classList.remove("active");
      });

      this.classList.remove("active");
    });
  }

  // Close modal with Escape key
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      const modals = document.querySelectorAll(".modal.active");
      const overlay = document.getElementById("overlay");

      if (modals.length > 0) {
        modals.forEach((modal) => {
          modal.classList.remove("active");
        });

        if (overlay) overlay.classList.remove("active");
      }
    }
  });

  // Position points on panoramas
  document.querySelectorAll(".punt").forEach((punt) => {
    const x = punt.getAttribute("data-x");
    const y = punt.getAttribute("data-y");

    if (x && y) {
      const parent = punt.parentElement;
      if (parent) {
        const parentWidth = parent.offsetWidth;
        const parentHeight = parent.offsetHeight;

        const posX = (parseFloat(x) / 100) * parentWidth;
        const posY = (parseFloat(y) / 100) * parentHeight;

        punt.style.left = posX + "px";
        punt.style.top = posY + "px";
      }
    }
  });
});
