function updatePointPositions() {
  console.log("=== Positioning points ===");

  const panoramas = document.querySelectorAll(".panorama");

  panoramas.forEach((panorama) => {
    const panoramaId = panorama.getAttribute("data-panorama-id");
    const img = panorama.querySelector("img");

    if (!img) return;

    const imgWidth = img.offsetWidth;
    const imgHeight = img.offsetHeight;

    console.log(`Panorama ${panoramaId}: ${imgWidth}x${imgHeight}`);

    const points = panorama.querySelectorAll(".punt");

    if (points.length === 0) {
      console.log(`No points for panorama ${panoramaId}`);
      return;
    }

    console.log(`${points.length} points for panorama ${panoramaId}`);

    points.forEach((point, index) => {
      const dbX = parseFloat(point.getAttribute("data-x"));
      const dbY = parseFloat(point.getAttribute("data-y"));

      console.log(`Point ${index}: ${dbX}, ${dbY}`);

      if (!isNaN(dbX) && !isNaN(dbY)) {
        // SIMPLE: If coordinates are big (like 50000), scale down
        // If coordinates are small (like 0-800), use directly

        let x, y;

        if (dbX > 2000) {
          // Big coordinates - scale down by 22 (50000/2287 ≈ 22)
          x = dbX / 22;
          y = dbY / 22;
        } else {
          // Small coordinates - use directly
          x = dbX;
          y = dbY;
        }

        // Ensure within bounds
        x = Math.max(0, Math.min(x, imgWidth));
        y = Math.max(0, Math.min(y, imgHeight));

        console.log(`Position: ${x}px, ${y}px`);

        point.style.left = x + "px";
        point.style.top = y + "px";
        point.style.backgroundColor = "red";
        point.style.border = "2px solid yellow";
      }
    });
  });
}

// Run multiple times
document.addEventListener("DOMContentLoaded", function () {
  setTimeout(updatePointPositions, 100);
  setTimeout(updatePointPositions, 500);
  setTimeout(updatePointPositions, 1000);
});

window.addEventListener("resize", function () {
  setTimeout(updatePointPositions, 100);
});

document.querySelectorAll(".panorama img").forEach((img) => {
  img.addEventListener("load", function () {
    setTimeout(updatePointPositions, 100);
  });
});
