const layout = document.getElementById("layout");
const toggleBtn = document.getElementById("toggleMenu");
const icon = toggleBtn.querySelector(".menu-icon");

function toggleMenu() {
  if (window.innerWidth >= 1024) {
    // Desktop: toggle class menu-hidden-desktop
    const isHidden = layout.classList.toggle("menu-hidden-desktop");
    icon.textContent = isHidden ? "☰" : "☓";
  } else {
    // Mobile: toggle class menu-visible
    const isVisible = layout.classList.toggle("menu-visible");
    icon.textContent = isVisible ? "☓" : "☰";
  }
}

toggleBtn.addEventListener("click", toggleMenu);

// Tutup menu mobile saat klik di luar
document.addEventListener("click", (e) => {
  if (
    window.innerWidth < 1024 &&
    layout.classList.contains("menu-visible") &&
    !document.getElementById("menu").contains(e.target) &&
    e.target !== toggleBtn
  ) {
    toggleMenu();
  }
});
