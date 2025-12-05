function updatePointPositions() {
  const points = document.querySelectorAll(".punt");
  const panorama = document.querySelector(".panorama img");

  if (!panorama) {
    console.log("Geen panorama afbeelding gevonden");
    return;
  }

  const imgWidth = panorama.offsetWidth;
  const imgHeight = panorama.offsetHeight;

  // console.log("Huidige afbeelding grootte:", imgWidth, "x", imgHeight);

  const originalWidth = 1382.4; // originele breedte in pixels
  const originalHeight = 756; // originele hoogte in pixels

  points.forEach((point) => {
    const originalX = parseInt(point.getAttribute("data-x"));
    const originalY = parseInt(point.getAttribute("data-y"));

    // Bereken percentages
    let xPercent = (originalX / originalWidth) * 100;
    let yPercent = (originalY / originalHeight) * 100;

    xPercent = xPercent - 6;
    yPercent = yPercent - 1;
    if (window.innerWidth >= 1200) {
      xPercent = xPercent - 10;
      yPercent = yPercent - 1;
    }
    console.log(
      `Punt ${point.getAttribute("data-modal-target")}:`,
      `Origineel: ${originalX},${originalY} ->`,
      `Gecorrigeerd: ${xPercent.toFixed(1)}%, ${yPercent.toFixed(1)}%`
    );

    // Pas positie aan
    point.style.left = xPercent + "%";
    point.style.top = yPercent + "%";
  });
}

// Voer uit wanneer pagina laadt
document.addEventListener("DOMContentLoaded", function () {
  // console.log("DOM geladen - update punten posities");
  updatePointPositions();
});

// Voer uit wanneer scherm van grootte verandert
window.addEventListener("resize", function () {
  // console.log("Scherm grootte veranderd - update punten posities");
  setTimeout(updatePointPositions, 100);
});

// Voer uit wanneer afbeelding geladen is
document.querySelector(".panorama img")?.addEventListener("load", function () {
  // console.log("Afbeelding geladen - update punten posities");
  updatePointPositions();
});
