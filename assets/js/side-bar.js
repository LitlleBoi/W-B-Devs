document.addEventListener("DOMContentLoaded", function () {
  console.log("DOM loaded - initializing sidebar");

  const openBtn = document.getElementById("openMenu");
  const closeBtn = document.getElementById("closeMenu");
  const menu = document.querySelector(".menu");

  // FORCE initial state
  menu.classList.add("active");
  openBtn.classList.add("hidden");

  console.log("Initial state FORCED:", {
    "menu has active class": menu.classList.contains("active"),
    "openBtn has hidden class": openBtn.classList.contains("hidden"),
  });

  // Open sidebar
  openBtn.addEventListener("click", function (e) {
    console.log("Opening sidebar");
    menu.classList.add("active");
    openBtn.classList.add("hidden");
  });

  // Close sidebar
  closeBtn.addEventListener("click", function (e) {
    console.log("Closing sidebar");
    menu.classList.remove("active");
    openBtn.classList.remove("hidden");
  });

  // Close with Escape
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && menu.classList.contains("active")) {
      console.log("Closing sidebar with Escape");
      menu.classList.remove("active");
      openBtn.classList.remove("hidden");
    }
  });
});
