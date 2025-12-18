// bewerk.js - SIMPLIFIED WORKING VERSION
document.addEventListener("DOMContentLoaded", function () {
  console.log("Bewerk.js loaded");

  // Add Font Awesome if not already loaded
  if (!document.querySelector('link[href*="font-awesome"]')) {
    const faLink = document.createElement("link");
    faLink.rel = "stylesheet";
    faLink.href =
      "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css";
    document.head.appendChild(faLink);
  }

  // ==================== UPLOAD BUTTON FUNCTIONALITY ====================
  document.addEventListener("click", function (e) {
    if (
      e.target.classList.contains("upload-btn") ||
      e.target.closest(".upload-btn")
    ) {
      const uploadBtn = e.target.classList.contains("upload-btn")
        ? e.target
        : e.target.closest(".upload-btn");
      const container = uploadBtn.closest(".file-upload-container");
      if (container) {
        const fileInput = container.querySelector('input[type="file"]');
        if (fileInput) {
          fileInput.click();
        }
      }
    }
  });

  // Show file name when selected and create preview
  document.addEventListener("change", function (e) {
    if (e.target.type === "file") {
      const fileInput = e.target;
      const container = fileInput.closest(".file-upload-container");

      // Update file name display
      if (container) {
        const fileNameSpan = container.querySelector(".file-name");
        if (fileNameSpan && fileInput.files.length > 0) {
          fileNameSpan.textContent = fileInput.files[0].name;
          fileNameSpan.style.color = "#333";
        }
      }

      // Create preview
      const file = fileInput.files[0];
      if (file && file.type.match("image.*")) {
        const reader = new FileReader();
        reader.onload = function (e) {
          const sourceCard = fileInput.closest(
            ".new-source-card, .source-edit-card"
          );
          if (sourceCard) {
            const previewDiv = sourceCard.querySelector(".bron-image-preview");
            if (previewDiv) {
              previewDiv.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width: 200px; margin-top: 10px;">`;
            }
          }
        };
        reader.readAsDataURL(file);
      }
    }
  });

  // ==================== ADD NEW POINT FUNCTIONALITY ====================
  const addPointButton = document.getElementById("addPointButton");
  if (addPointButton) {
    addPointButton.addEventListener("click", function () {
      console.log("Add point clicked");
      addNewPoint();
    });
  }

  function addNewPoint() {
    const newPointsContainer = document.getElementById("newPointsContainer");
    const template = document.getElementById("newPointTemplate");

    if (!template || !newPointsContainer) {
      console.error("Template or container not found");
      return;
    }

    const tempId =
      "new_" + Date.now() + "_" + Math.random().toString(36).substr(2, 9);
    const newPointHTML = template.innerHTML.replace(/TEMP_ID/g, tempId);

    const pointDiv = document.createElement("div");
    pointDiv.innerHTML = newPointHTML;
    const pointCard = pointDiv.querySelector(".new-point-card");
    pointCard.setAttribute("data-temp-id", tempId);

    newPointsContainer.appendChild(pointCard);

    // Add event listeners to the new point
    setupNewPointEvents(tempId);

    // Add visual point to panorama
    addPointToPanorama(tempId, 100, 100, "Nieuw Punt");

    console.log("New point added with ID:", tempId);
  }

  function setupNewPointEvents(pointTempId) {
    const pointCard = document.querySelector(`[data-temp-id="${pointTempId}"]`);
    if (!pointCard) return;

    // Remove point button
    const removeBtn = pointCard.querySelector(".remove-new-point");
    if (removeBtn) {
      removeBtn.addEventListener("click", function () {
        if (confirm("Weet je zeker dat je dit punt wilt verwijderen?")) {
          pointCard.remove();
          removePointFromPanorama(pointTempId);
        }
      });
    }

    // Add bron to this new point button
    const addBronBtn = pointCard.querySelector(".add-new-bron-to-point");
    if (addBronBtn) {
      addBronBtn.addEventListener("click", function () {
        addNewBronToPoint(pointTempId, "new");
      });
    }

    // Coordinate inputs - update panorama point when changed
    const xInput = pointCard.querySelector(".new-punt-x");
    const yInput = pointCard.querySelector(".new-punt-y");
    const titleInput = pointCard.querySelector(".new-punt-titel");

    if (xInput) {
      xInput.addEventListener("change", function () {
        updatePanoramaPointPosition(
          pointTempId,
          this.value,
          yInput ? yInput.value : 100
        );
      });
    }

    if (yInput) {
      yInput.addEventListener("change", function () {
        updatePanoramaPointPosition(
          pointTempId,
          xInput ? xInput.value : 100,
          this.value
        );
      });
    }

    if (titleInput) {
      titleInput.addEventListener("input", function () {
        updatePanoramaPointTitle(pointTempId, this.value);
      });
    }
  }

  // ==================== ADD NEW BRON TO EXISTING POINTS ====================
  document.querySelectorAll(".add-bron-to-existing").forEach((button) => {
    button.addEventListener("click", function () {
      const pointId = this.getAttribute("data-point-id");
      console.log("Add bron to existing point:", pointId);
      addNewBronToPoint(pointId, "existing");
    });
  });

  function addNewBronToPoint(pointId, type) {
    const templateId =
      type === "new" ? "newBronTemplate" : "newBronToExistingTemplate";
    const template = document.getElementById(templateId);

    if (!template) {
      console.error("Template not found:", templateId);
      return;
    }

    const tempId =
      "bron_" + Date.now() + "_" + Math.random().toString(36).substr(2, 9);
    let container;

    if (type === "new") {
      // For new points
      const pointCard = document.querySelector(`[data-temp-id="${pointId}"]`);
      if (!pointCard) return;
      container = pointCard.querySelector(".point-bronnen-container");
    } else {
      // For existing points
      container = document.querySelector(
        `.new-bronnen-to-existing-container[data-point-id="${pointId}"]`
      );
    }

    if (!container) {
      console.error("Container not found for point:", pointId);
      return;
    }

    const newBronHTML = template.innerHTML
      .replace(/TEMP_ID/g, tempId)
      .replace(/POINT_ID/g, pointId);

    const bronDiv = document.createElement("div");
    bronDiv.innerHTML = newBronHTML;
    const bronCard = bronDiv.querySelector(".new-source-card");
    bronCard.setAttribute("data-temp-id", tempId);

    container.appendChild(bronCard);

    // Setup event listeners for this bron
    setupNewBronEvents(tempId, type);

    console.log("New bron added:", tempId, "to point:", pointId);
  }

  function setupNewBronEvents(bronTempId, type) {
    const bronCard = document.querySelector(`[data-temp-id="${bronTempId}"]`);
    if (!bronCard) return;

    // Remove button
    const removeBtn = bronCard.querySelector(
      type === "new" ? ".remove-new-bron" : ".remove-existing-new-bron"
    );
    if (removeBtn) {
      removeBtn.addEventListener("click", function () {
        if (confirm("Weet je zeker dat je deze bron wilt verwijderen?")) {
          bronCard.remove();
        }
      });
    }
  }

  // ==================== PANORAMA POINT MANAGEMENT ====================
  function addPointToPanorama(pointTempId, x, y, title) {
    const panorama = document.querySelector(".panorama");
    if (!panorama) return;

    const button = document.createElement("button");
    button.className = "punt new-panorama-point";
    button.setAttribute("data-x", x);
    button.setAttribute("data-y", y);
    button.setAttribute(
      "data-panorama-id",
      panorama.getAttribute("data-panorama-id")
    );
    button.setAttribute("data-punt-id", pointTempId);
    button.setAttribute("title", title);
    button.setAttribute("type", "button");
    button.innerHTML = '<span class="punt-dot"></span>';

    // Make it draggable
    setupPointDragging(button, pointTempId);

    panorama.appendChild(button);

    // Position it
    setTimeout(() => {
      updatePointPositions();
    }, 100);
  }

  function removePointFromPanorama(pointTempId) {
    const point = document.querySelector(
      `.punt[data-punt-id="${pointTempId}"]`
    );
    if (point) {
      point.remove();
    }
  }

  function updatePanoramaPointPosition(pointTempId, x, y) {
    const point = document.querySelector(
      `.punt[data-punt-id="${pointTempId}"]`
    );
    if (point) {
      point.setAttribute("data-x", x);
      point.setAttribute("data-y", y);
      setTimeout(updatePointPositions, 10);
    }
  }

  function updatePanoramaPointTitle(pointTempId, title) {
    const point = document.querySelector(
      `.punt[data-punt-id="${pointTempId}"]`
    );
    if (point) {
      point.setAttribute("title", title);
    }
  }

  // ==================== DRAGGING FUNCTIONALITY ====================
  function setupPointDragging(point, pointId) {
    point.addEventListener("mousedown", function (e) {
      startDrag(e, pointId);
    });

    point.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
    });
  }

  // Setup dragging for existing points
  document
    .querySelectorAll(".punt:not(.new-panorama-point)")
    .forEach((point) => {
      const pointId = point.getAttribute("data-punt-id");
      if (pointId && !pointId.startsWith("new_")) {
        setupPointDragging(point, pointId);
      }
    });

  function startDrag(e, pointId) {
    e.preventDefault();
    e.stopPropagation();

    const point = e.currentTarget;
    const panorama = point.closest(".panorama");
    const img = panorama.querySelector("img");

    // Get current position
    const currentX = parseFloat(point.getAttribute("data-x"));
    const currentY = parseFloat(point.getAttribute("data-y"));

    // Get dimensions
    const naturalWidth = img.naturalWidth;
    const naturalHeight = img.naturalHeight;
    const displayedWidth = img.offsetWidth;
    const displayedHeight = img.offsetHeight;

    // Calculate scale
    const scaleX = displayedWidth / naturalWidth;
    const scaleY = displayedHeight / naturalHeight;

    // Convert to displayed coordinates
    const displayedX = currentX * scaleX;
    const displayedY = currentY * scaleY;

    // Mouse start position
    const startMouseX = e.clientX;
    const startMouseY = e.clientY;

    function doDrag(e) {
      // Calculate movement
      const deltaX = e.clientX - startMouseX;
      const deltaY = e.clientY - startMouseY;

      // New displayed position
      let newDisplayedX = displayedX + deltaX;
      let newDisplayedY = displayedY + deltaY;

      // Constrain bounds
      newDisplayedX = Math.max(0, Math.min(newDisplayedX, displayedWidth));
      newDisplayedY = Math.max(0, Math.min(newDisplayedY, displayedHeight));

      // Convert back to natural coordinates
      const newNaturalX = newDisplayedX / scaleX;
      const newNaturalY = newDisplayedY / scaleY;

      // Update point
      point.setAttribute("data-x", Math.round(newNaturalX));
      point.setAttribute("data-y", Math.round(newNaturalY));

      // Update form inputs
      const isNewPoint = pointId.startsWith("new_");
      const actualId = isNewPoint ? pointId : pointId;

      if (isNewPoint) {
        const xInput = document.querySelector(
          `[name="new_punten[${actualId}][x]"]`
        );
        const yInput = document.querySelector(
          `[name="new_punten[${actualId}][y]"]`
        );
        if (xInput) xInput.value = Math.round(newNaturalX);
        if (yInput) yInput.value = Math.round(newNaturalY);
      } else {
        const xInput = document.querySelector(`[name="punten[${pointId}][x]"]`);
        const yInput = document.querySelector(`[name="punten[${pointId}][y]"]`);
        if (xInput) xInput.value = Math.round(newNaturalX);
        if (yInput) yInput.value = Math.round(newNaturalY);
      }

      // Update visual position
      const xPercent = (newNaturalX / naturalWidth) * 100;
      const yPercent = (newNaturalY / naturalHeight) * 100;
      point.style.left = xPercent + "%";
      point.style.top = yPercent + "%";
      point.style.transform = "translate(-50%, -50%)";
    }

    function stopDrag() {
      document.removeEventListener("mousemove", doDrag);
      document.removeEventListener("mouseup", stopDrag);
      setTimeout(updatePointPositions, 10);
    }

    document.addEventListener("mousemove", doDrag);
    document.addEventListener("mouseup", stopDrag);
  }

  // ==================== UPDATE IMAGE WHEN URL CHANGES ====================
  const imageUrlInput = document.getElementById("afbeelding_url");
  const panoramaImage = document.querySelector(".panorama img");

  if (imageUrlInput && panoramaImage) {
    imageUrlInput.addEventListener("change", function () {
      panoramaImage.src = this.value;
      panoramaImage.onload = function () {
        setTimeout(updatePointPositions, 100);
      };
    });
  }

  // ==================== FORM VALIDATION ====================
  const form = document.getElementById("editPanoramaForm");
  if (form) {
    form.addEventListener("submit", function (e) {
      console.log("Form submitted - checking status values");

      // Ensure all status selects have a value
      document.querySelectorAll(".status-select").forEach(function (select) {
        if (!select.value) {
          select.value = "concept";
          console.log("Fixed empty status for:", select.name);
        }
      });

      return true; // Allow form submission
    });
  }

  console.log("Bewerk.js initialization complete");
});
