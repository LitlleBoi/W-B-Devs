function updatePointPositions() {
  console.log("=== DEBUG: Positioning points ===");
  console.log(
    "Number of panoramas:",
    document.querySelectorAll(".panorama").length
  );

  const panoramas = document.querySelectorAll(".panorama");

  panoramas.forEach((panorama, pIndex) => {
    const panoramaId = panorama.getAttribute("data-panorama-id");
    console.log(`\n--- Panorama ${pIndex + 1} (ID: ${panoramaId}) ---`);

    const img = panorama.querySelector("img");
    if (!img) {
      console.log("❌ No image found");
      return;
    }

    // Check if image is loaded
    if (!img.complete) {
      console.log("⏳ Image not loaded yet");
      return;
    }

    const naturalWidth = img.naturalWidth;
    const naturalHeight = img.naturalHeight;
    const displayedWidth = img.offsetWidth;
    const displayedHeight = img.offsetHeight;

    console.log(
      `📐 Image dimensions - Natural: ${naturalWidth}x${naturalHeight}, Displayed: ${displayedWidth}x${displayedHeight}`
    );

    // Find all points for this panorama
    const points = panorama.querySelectorAll(".punt");
    console.log(`📍 Found ${points.length} points`);

    if (points.length === 0) return;

    points.forEach((point, index) => {
      // Debug: Check data attributes
      const dataX = point.getAttribute("data-x");
      const dataY = point.getAttribute("data-y");
      const panoramaIdAttr = point.getAttribute("data-panorama-id");

      console.log(`\n  Point ${index + 1}:`);
      console.log(`    data-x="${dataX}" (type: ${typeof dataX})`);
      console.log(`    data-y="${dataY}" (type: ${typeof dataY})`);
      console.log(`    panorama ID: ${panoramaIdAttr}`);

      // Parse coordinates
      const x = parseFloat(dataX);
      const y = parseFloat(dataY);

      console.log(`    Parsed: x=${x}, y=${y}`);

      if (isNaN(x) || isNaN(y)) {
        console.log("    ❌ Invalid coordinates");
        return;
      }

      if (naturalWidth === 0 || naturalHeight === 0) {
        console.log("    ❌ Image has no dimensions");
        return;
      }

      // Calculate percentages
      const xPercent = (x / naturalWidth) * 100;
      const yPercent = (y / naturalHeight) * 100;

      console.log(
        `    📊 Percentage: ${xPercent.toFixed(2)}%, ${yPercent.toFixed(2)}%`
      );

      // Apply positioning
      point.style.position = "absolute";
      point.style.left = xPercent + "%";
      point.style.top = yPercent + "%";
      point.style.transform = "translate(-50%, -50%)";

      // Make it visible
      point.style.backgroundColor = "red";
      point.style.width = "30px";
      point.style.height = "30px";
      point.style.borderRadius = "50%";
      point.style.border = "2px solid white";
      point.style.zIndex = "1000";
      point.style.opacity = "1";

      console.log(
        `    ✅ Positioned at: ${xPercent.toFixed(2)}%, ${yPercent.toFixed(2)}%`
      );
    });
  });

  console.log("\n=== DEBUG COMPLETE ===");
}

// Run immediately when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
  console.log("🚀 DOMContentLoaded - Starting point positioning");

  // Run multiple times to ensure images are loaded
  setTimeout(updatePointPositions, 100);
  setTimeout(updatePointPositions, 500);
  setTimeout(updatePointPositions, 1000);
  setTimeout(updatePointPositions, 2000);
});

// Also run when window loads
window.addEventListener("load", function () {
  console.log("🖼️ Window loaded - Running point positioning");
  setTimeout(updatePointPositions, 300);
});

// Update on resize
window.addEventListener("resize", function () {
  console.log("📱 Window resized - Updating point positions");
  setTimeout(updatePointPositions, 100);
});

// Image load listeners
document.querySelectorAll(".panorama img").forEach((img) => {
  img.addEventListener("load", function () {
    console.log("📸 Image loaded - Updating points");
    setTimeout(updatePointPositions, 100);
  });
});
