document.addEventListener("DOMContentLoaded", function () {
  const panoramaImage = document.querySelector(".panorama img");
  const imageUrlInput = document.getElementById("afbeelding_url");
  const points = document.querySelectorAll(".punt");

  // Update image when URL changes
  if (imageUrlInput && panoramaImage) {
    imageUrlInput.addEventListener("change", function () {
      panoramaImage.src = this.value;
      // Trigger point repositioning after image loads
      panoramaImage.onload = function () {
        setTimeout(updatePointPositions, 100);
      };
    });
  }

  // Make points draggable and update form inputs
  if (points.length > 0) {
    points.forEach((point) => {
      // Remove any existing event listeners
      point.removeEventListener("mousedown", startDrag);

      point.addEventListener("mousedown", startDrag);

      // Prevent default button behavior
      point.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
      });
    });
  }

  function startDrag(e) {
    e.preventDefault();
    e.stopPropagation();

    const point = e.currentTarget;
    const pointId = point.getAttribute("data-punt-id");
    const xInput = document.querySelector(`[name="punten[${pointId}][x]"]`);
    const yInput = document.querySelector(`[name="punten[${pointId}][y]"]`);
    const panorama = point.closest(".panorama");
    const img = panorama.querySelector("img");

    // Get current position from data attributes (absolute pixel values)
    const currentX = parseFloat(point.getAttribute("data-x"));
    const currentY = parseFloat(point.getAttribute("data-y"));

    // Get image natural dimensions
    const naturalWidth = img.naturalWidth;
    const naturalHeight = img.naturalHeight;

    // Get displayed image dimensions
    const displayedWidth = img.offsetWidth;
    const displayedHeight = img.offsetHeight;

    // Calculate scale factors
    const scaleX = displayedWidth / naturalWidth;
    const scaleY = displayedHeight / naturalHeight;

    // Convert absolute coordinates to displayed coordinates
    const displayedX = currentX * scaleX;
    const displayedY = currentY * scaleY;

    // Get mouse starting position
    const startMouseX = e.clientX;
    const startMouseY = e.clientY;

    // Get current displayed position (what user sees)
    const currentDisplayedX = displayedX;
    const currentDisplayedY = displayedY;

    function doDrag(e) {
      // Calculate mouse movement
      const deltaX = e.clientX - startMouseX;
      const deltaY = e.clientY - startMouseY;

      // Calculate new displayed position
      let newDisplayedX = currentDisplayedX + deltaX;
      let newDisplayedY = currentDisplayedY + deltaY;

      // Constrain to image bounds
      newDisplayedX = Math.max(0, Math.min(newDisplayedX, displayedWidth));
      newDisplayedY = Math.max(0, Math.min(newDisplayedY, displayedHeight));

      // Convert displayed position back to natural coordinates
      const newNaturalX = newDisplayedX / scaleX;
      const newNaturalY = newDisplayedY / scaleY;

      // Update data attributes with natural coordinates
      point.setAttribute("data-x", Math.round(newNaturalX));
      point.setAttribute("data-y", Math.round(newNaturalY));

      // Update form inputs
      if (xInput) xInput.value = Math.round(newNaturalX);
      if (yInput) yInput.value = Math.round(newNaturalY);

      // Recalculate point position using the existing recoords.js function
      // We'll manually update the point position for immediate feedback
      const xPercent = (newNaturalX / naturalWidth) * 100;
      const yPercent = (newNaturalY / naturalHeight) * 100;

      point.style.left = xPercent + "%";
      point.style.top = yPercent + "%";
      point.style.transform = "translate(-50%, -50%)";
    }

    function stopDrag() {
      document.removeEventListener("mousemove", doDrag);
      document.removeEventListener("mouseup", stopDrag);

      // After dragging, run updatePointPositions to ensure everything is consistent
      setTimeout(updatePointPositions, 10);
    }

    document.addEventListener("mousemove", doDrag);
    document.addEventListener("mouseup", stopDrag);
  }

  // Update data attributes when form inputs change
  document.querySelectorAll(".coord-x-input").forEach((input) => {
    input.removeEventListener("change", updateCoordFromInput);
    input.addEventListener("change", updateCoordFromInput);
  });

  document.querySelectorAll(".coord-y-input").forEach((input) => {
    input.removeEventListener("change", updateCoordFromInput);
    input.addEventListener("change", updateCoordFromInput);
  });

  function updateCoordFromInput(e) {
    const input = e.target;
    const match = input.name.match(/punten\[(\d+)\]\[(x|y)\]/);

    if (match) {
      const pointId = match[1];
      const coordType = match[2];
      const point = document.querySelector(`[data-punt-id="${pointId}"]`);

      if (point) {
        // Update data attribute
        point.setAttribute(`data-${coordType}`, input.value);

        // Re-run point positioning
        setTimeout(updatePointPositions, 10);
      }
    }
  }

  // Also update when image loads
  if (panoramaImage) {
    panoramaImage.addEventListener("load", function () {
      setTimeout(updatePointPositions, 100);
    });
  }
});
