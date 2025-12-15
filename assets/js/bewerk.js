document.addEventListener("DOMContentLoaded", function () {
  const panoramaImage = document.querySelector(".panorama img");
  const imageUrlInput = document.getElementById("afbeelding_url");
  const points = document.querySelectorAll(".punt");

  // Update image when URL changes
  imageUrlInput.addEventListener("change", function () {
    panoramaImage.src = this.value;
    // Trigger point repositioning after image loads
    panoramaImage.onload = function () {
      setTimeout(updatePointPositions, 100);
    };
  });

  // Make points draggable and update form inputs
  points.forEach((point) => {
    point.addEventListener("mousedown", startDrag);

    // Prevent default button behavior
    point.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
    });
  });

  function startDrag(e) {
    e.preventDefault();
    e.stopPropagation();

    const point = e.currentTarget;
    const pointId = point.getAttribute("data-punt-id");
    const xInput = document.querySelector(`[name="punten[${pointId}][x]"]`);
    const yInput = document.querySelector(`[name="punten[${pointId}][y]"]`);
    const panorama = point.closest(".panorama");
    const img = panorama.querySelector("img");

    const startX = e.clientX;
    const startY = e.clientY;

    // Get initial point position in percentages
    const style = window.getComputedStyle(point);
    const initialLeft = parseFloat(style.left);
    const initialTop = parseFloat(style.top);

    function doDrag(e) {
      const dx = e.clientX - startX;
      const dy = e.clientY - startY;

      // Calculate new position in pixels
      const panoramaRect = panorama.getBoundingClientRect();
      const newLeftPx = (initialLeft / 100) * panoramaRect.width + dx;
      const newTopPx = (initialTop / 100) * panoramaRect.height + dy;

      // Convert to percentages
      const newLeftPercent = (newLeftPx / panoramaRect.width) * 100;
      const newTopPercent = (newTopPx / panoramaRect.height) * 100;

      // Constrain to image bounds (0-100%)
      const constrainedLeft = Math.max(0, Math.min(newLeftPercent, 100));
      const constrainedTop = Math.max(0, Math.min(newTopPercent, 100));

      // Update point position
      point.style.left = constrainedLeft + "%";
      point.style.top = constrainedTop + "%";

      // Update data attributes
      if (img.complete && img.naturalWidth > 0) {
        const xAbsolute = (constrainedLeft / 100) * img.naturalWidth;
        const yAbsolute = (constrainedTop / 100) * img.naturalHeight;

        point.setAttribute("data-x", xAbsolute);
        point.setAttribute("data-y", yAbsolute);

        // Update form inputs
        if (xInput) xInput.value = Math.round(xAbsolute);
        if (yInput) yInput.value = Math.round(yAbsolute);
      }
    }

    function stopDrag() {
      document.removeEventListener("mousemove", doDrag);
      document.removeEventListener("mouseup", stopDrag);
    }

    document.addEventListener("mousemove", doDrag);
    document.addEventListener("mouseup", stopDrag);
  }

  // Update data attributes when form inputs change
  document.querySelectorAll(".coord-x-input").forEach((input) => {
    input.addEventListener("change", function () {
      const pointId = this.name.match(/punten\[(\d+)\]\[x\]/)[1];
      const point = document.querySelector(`[data-punt-id="${pointId}"]`);
      const img = point.closest(".panorama").querySelector("img");

      if (img.complete && img.naturalWidth > 0) {
        const x = parseFloat(this.value);
        point.setAttribute("data-x", x);
        updatePointPositions(); // Recalculate position
      }
    });
  });

  document.querySelectorAll(".coord-y-input").forEach((input) => {
    input.addEventListener("change", function () {
      const pointId = this.name.match(/punten\[(\d+)\]\[y\]/)[1];
      const point = document.querySelector(`[data-punt-id="${pointId}"]`);
      const img = point.closest(".panorama").querySelector("img");

      if (img.complete && img.naturalWidth > 0) {
        const y = parseFloat(this.value);
        point.setAttribute("data-y", y);
        updatePointPositions(); // Recalculate position
      }
    });
  });
});
