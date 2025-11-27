// 📁 assets/js/recoords.js - MET -6% CORRECTIE

function updatePointPositions() {
  const points = document.querySelectorAll(".punt");
  const panorama = document.querySelector(".panorama img");

  if (!panorama) {
    console.log("Geen panorama afbeelding gevonden");
    return;
  }

  // Haal de actuele afmetingen van de afbeelding op
  const imgWidth = panorama.offsetWidth;
  const imgHeight = panorama.offsetHeight;

  console.log("Huidige afbeelding grootte:", imgWidth, "x", imgHeight);

  // 🎯 BELANGRIJK: Pas dit aan naar de WERKELIJKE grootte van je panorama!
  const originalWidth = 1704; // originele breedte in pixels
  const originalHeight = 756; // originele hoogte in pixels

  points.forEach((point) => {
    // Lees de originele coordinaten uit de data attributen
    const originalX = parseInt(point.getAttribute("data-x"));
    const originalY = parseInt(point.getAttribute("data-y"));

    // Bereken percentages
    let xPercent = (originalX / originalWidth) * 100;
    let yPercent = (originalY / originalHeight) * 100;

    // 🎯 -6% CORRECTIE
    xPercent = xPercent - 6; // 6% naar links
    yPercent = yPercent - 1; // 6% naar links
    // yPercent = yPercent - 6; // Uncomment als je ook verticale correctie wilt

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
  console.log("DOM geladen - update punten posities");
  updatePointPositions();
});

// Voer uit wanneer scherm van grootte verandert
window.addEventListener("resize", function () {
  console.log("Scherm grootte veranderd - update punten posities");
  setTimeout(updatePointPositions, 100);
});

// Voer uit wanneer afbeelding geladen is
document.querySelector(".panorama img")?.addEventListener("load", function () {
  console.log("Afbeelding geladen - update punten posities");
  updatePointPositions();
});
