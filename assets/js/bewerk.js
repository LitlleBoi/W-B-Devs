// bewerk.js - VEREENVOUDIGDE WERKENDE VERSIE
/**
 * Bewerk JavaScript
 *
 * Deze JavaScript file beheert de functionaliteit voor het bewerken van panorama's.
 * Het omvat uploaden, slepen van punten, toevoegen van bronnen en formulier validatie.
 */

// Wacht tot de DOM volledig geladen is voordat we scripts uitvoeren
document.addEventListener("DOMContentLoaded", function () {
  // console.log("Bewerk.js geladen");

  // ==================== FONT AWESOME LAZY LOAD ====================
  // Controleer of Font Awesome al geladen is, zo niet, laad het dan
  if (!document.querySelector('link[href*="font-awesome"]')) {
    const faLink = document.createElement("link");
    faLink.rel = "stylesheet";
    faLink.href =
      "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css";
    document.head.appendChild(faLink);
  }

  // ==================== UPLOAD BUTTON FUNCTIONALITY ====================
  // Event listener voor klikken op upload knoppen
  document.addEventListener("click", function (e) {
    // Check of er geklikt is op een upload knop of een element erin
    if (
      e.target.classList.contains("upload-btn") ||
      e.target.closest(".upload-btn")
    ) {
      // Zoek de upload knop
      const uploadBtn = e.target.classList.contains("upload-btn")
        ? e.target
        : e.target.closest(".upload-btn");
      const container = uploadBtn.closest(".file-upload-container");

      // Trigger het verborgen file input element
      if (container) {
        const fileInput = container.querySelector('input[type="file"]');
        if (fileInput) {
          fileInput.click();
        }
      }
    }
  });

  // ==================== FILE SELECTION HANDLING ====================
  // Toon bestandsnaam en maak preview wanneer een bestand is geselecteerd
  document.addEventListener("change", function (e) {
    if (e.target.type === "file") {
      const fileInput = e.target;
      const container = fileInput.closest(".file-upload-container");

      // Update bestandsnaam weergave
      if (container) {
        const fileNameSpan = container.querySelector(".file-name");
        if (fileNameSpan && fileInput.files.length > 0) {
          fileNameSpan.textContent = fileInput.files[0].name;
          fileNameSpan.style.color = "#333";
        }
      }

      // Maak een preview van het afbeeldingsbestand
      const file = fileInput.files[0];
      if (file && file.type.match("image.*")) {
        const url = URL.createObjectURL(file);
        const sourceCard = fileInput.closest(
          ".new-source-card, .source-edit-card"
        );
        if (sourceCard) {
          const previewDiv = sourceCard.querySelector(".bron-image-preview");
          if (previewDiv) {
            // Toon de preview afbeelding
            previewDiv.innerHTML = `<img src="${url}" alt="Preview" style="max-width: 200px; margin-top: 10px;">`;
          }
        }
      }
    }
  });

  // ==================== ADD NEW POINT FUNCTIONALITY ====================
  const addPointButton = document.getElementById("addPointButton");
  if (addPointButton) {
    addPointButton.addEventListener("click", function () {
      // console.log("Add point clicked");
      addNewPoint();
    });
  }

  // Functie om een nieuw punt toe te voegen
  function addNewPoint() {
    const newPointsContainer = document.getElementById("newPointsContainer");
    const template = document.getElementById("newPointTemplate");

    if (!template || !newPointsContainer) {
      console.error("Template of container niet gevonden");
      return;
    }

    // Genereer een unieke tijdelijke ID voor het nieuwe punt
    const tempId =
      "new_" + Date.now() + "_" + Math.random().toString(36).substr(2, 9);
    const newPointHTML = template.innerHTML.replace(/TEMP_ID/g, tempId);

    const pointDiv = document.createElement("div");
    pointDiv.innerHTML = newPointHTML;
    const pointCard = pointDiv.querySelector(".new-point-card");
    pointCard.setAttribute("data-temp-id", tempId);

    // Voeg het nieuwe punt toe aan de container
    newPointsContainer.appendChild(pointCard);

    // Voeg event listeners toe aan het nieuwe punt
    setupNewPointEvents(tempId);

    // Voeg visueel punt toe aan het panorama
    addPointToPanorama(tempId, 100, 100, "Nieuw Punt");

    // console.log("Nieuw punt toegevoegd met ID:", tempId);
  }

  // Functie om event listeners te installeren voor een nieuw punt
  function setupNewPointEvents(pointTempId) {
    const pointCard = document.querySelector(`[data-temp-id="${pointTempId}"]`);
    if (!pointCard) return;

    // Verwijder knop voor punt
    const removeBtn = pointCard.querySelector(".remove-new-point");
    if (removeBtn) {
      removeBtn.addEventListener("click", function () {
        if (confirm("Weet je zeker dat je dit punt wilt verwijderen?")) {
          pointCard.remove();
          removePointFromPanorama(pointTempId);
        }
      });
    }

    // Voeg bron toe knop voor dit nieuwe punt
    const addBronBtn = pointCard.querySelector(".add-new-bron-to-point");
    if (addBronBtn) {
      addBronBtn.addEventListener("click", function () {
        addNewBronToPoint(pointTempId, "new");
      });
    }

    // Coördinaat inputs - update panorama punt wanneer gewijzigd
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
  // Voeg event listeners toe aan alle "voeg bron toe" knoppen voor bestaande punten
  document.querySelectorAll(".add-bron-to-existing").forEach((button) => {
    button.addEventListener("click", function () {
      const pointId = this.getAttribute("data-point-id");
      // console.log("Add bron to existing point:", pointId);
      addNewBronToPoint(pointId, "existing");
    });
  });

  // Functie om een nieuwe bron toe te voegen aan een punt
  function addNewBronToPoint(pointId, type) {
    // Bepaal welke template te gebruiken
    const templateId =
      type === "new" ? "newBronTemplate" : "newBronToExistingTemplate";
    const template = document.getElementById(templateId);

    if (!template) {
      console.error("Template niet gevonden:", templateId);
      return;
    }

    // Genereer unieke tijdelijke ID voor de bron
    const tempId =
      "bron_" + Date.now() + "_" + Math.random().toString(36).substr(2, 9);
    let container;

    if (type === "new") {
      // Voor nieuwe punten
      const pointCard = document.querySelector(`[data-temp-id="${pointId}"]`);
      if (!pointCard) return;
      container = pointCard.querySelector(".point-bronnen-container");
    } else {
      // Voor bestaande punten
      container = document.querySelector(
        `.new-bronnen-to-existing-container[data-point-id="${pointId}"]`
      );
    }

    if (!container) {
      console.error("Container niet gevonden voor punt:", pointId);
      return;
    }

    // Vervang placeholders in template met echte IDs
    const newBronHTML = template.innerHTML
      .replace(/TEMP_POINT_ID/g, pointId)
      .replace(/TEMP_BRON_ID/g, tempId)
      .replace(/POINT_ID/g, pointId);

    const bronDiv = document.createElement("div");
    bronDiv.innerHTML = newBronHTML;
    const bronCard = bronDiv.querySelector(".new-source-card");
    bronCard.setAttribute("data-temp-id", tempId);

    // Voeg de bron toe aan de container
    container.appendChild(bronCard);

    // Installeer event listeners voor deze bron
    setupNewBronEvents(tempId, type);

    // console.log("Nieuwe bron toegevoegd:", tempId, "aan punt:", pointId);
  }

  // Functie om event listeners te installeren voor een nieuwe bron
  function setupNewBronEvents(bronTempId, type) {
    const bronCard = document.querySelector(`[data-temp-id="${bronTempId}"]`);
    if (!bronCard) return;

    // Verwijder knop
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
  // Functie om een visueel punt toe te voegen aan het panorama
  function addPointToPanorama(pointTempId, x, y, title) {
    const panorama = document.querySelector(".panorama");
    if (!panorama) return;

    // Maak een nieuwe punt knop aan
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

    // Maak het sleepbaar
    setupPointDragging(button, pointTempId);

    // Voeg toe aan panorama
    panorama.appendChild(button);

    // Update positie na korte vertraging
    setTimeout(() => {
      updatePointPositions();
    }, 100);
  }

  // Functie om een punt te verwijderen uit het panorama
  function removePointFromPanorama(pointTempId) {
    const point = document.querySelector(
      `.punt[data-punt-id="${pointTempId}"]`
    );
    if (point) {
      point.remove();
    }
  }

  // Functie om de positie van een panorama punt bij te werken
  function updatePanoramaPointPosition(pointTempId, x, y) {
    const point = document.querySelector(
      `.punt[data-punt-id="${pointTempId}"]`
    );
    if (point) {
      point.setAttribute("data-x", x);
      point.setAttribute("data-y", y);
      // Update visuele positie na korte vertraging
      setTimeout(updatePointPositions, 10);
    }
  }

  // Functie om de titel van een panorama punt bij te werken
  function updatePanoramaPointTitle(pointTempId, title) {
    const point = document.querySelector(
      `.punt[data-punt-id="${pointTempId}"]`
    );
    if (point) {
      point.setAttribute("title", title);
    }
  }

  // ==================== DRAGGING FUNCTIONALITY ====================
  // Functie om sleepfunctionaliteit in te stellen voor een punt
  function setupPointDragging(point, pointId) {
    // Start slepen bij muisklik
    point.addEventListener("mousedown", function (e) {
      startDrag(e, pointId);
    });

    // Voorkom default gedrag bij klikken
    point.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
    });
  }

  // Installeer sleepfunctionaliteit voor bestaande punten
  document
    .querySelectorAll(".punt:not(.new-panorama-point)")
    .forEach((point) => {
      const pointId = point.getAttribute("data-punt-id");
      if (pointId && !pointId.startsWith("new_")) {
        setupPointDragging(point, pointId);
      }
    });

  // Functie om het slepen te starten
  function startDrag(e, pointId) {
    e.preventDefault();
    e.stopPropagation();

    const point = e.currentTarget;
    const panorama = point.closest(".panorama");
    const img = panorama.querySelector("img");

    // Haal huidige positie op
    const currentX = parseFloat(point.getAttribute("data-x"));
    const currentY = parseFloat(point.getAttribute("data-y"));

    // Haal dimensies op
    const naturalWidth = img.naturalWidth;
    const naturalHeight = img.naturalHeight;
    const displayedWidth = img.offsetWidth;
    const displayedHeight = img.offsetHeight;

    // Bereken schaal
    const scaleX = displayedWidth / naturalWidth;
    const scaleY = displayedHeight / naturalHeight;

    // Converteer naar weergegeven coördinaten
    const displayedX = currentX * scaleX;
    const displayedY = currentY * scaleY;

    // Startpositie van muis
    const startMouseX = e.clientX;
    const startMouseY = e.clientY;

    // Functie die tijdens het slepen wordt uitgevoerd
    function doDrag(e) {
      // Bereken beweging
      const deltaX = e.clientX - startMouseX;
      const deltaY = e.clientY - startMouseY;

      // Nieuwe weergegeven positie
      let newDisplayedX = displayedX + deltaX;
      let newDisplayedY = displayedY + deltaY;

      // Beperk tot grenzen van afbeelding
      newDisplayedX = Math.max(0, Math.min(newDisplayedX, displayedWidth));
      newDisplayedY = Math.max(0, Math.min(newDisplayedY, displayedHeight));

      // Converteer terug naar natuurlijke coördinaten
      const newNaturalX = newDisplayedX / scaleX;
      const newNaturalY = newDisplayedY / scaleY;

      // Update punt attributen
      point.setAttribute("data-x", Math.round(newNaturalX));
      point.setAttribute("data-y", Math.round(newNaturalY));

      // Update formulier inputs
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

      // Update visuele positie
      const xPercent = (newNaturalX / naturalWidth) * 100;
      const yPercent = (newNaturalY / naturalHeight) * 100;
      point.style.left = xPercent + "%";
      point.style.top = yPercent + "%";
      point.style.transform = "translate(-50%, -50%)";
    }

    // Functie die wordt uitgevoerd bij stoppen met slepen
    function stopDrag() {
      document.removeEventListener("mousemove", doDrag);
      document.removeEventListener("mouseup", stopDrag);
      setTimeout(updatePointPositions, 10);
    }

    // Voeg event listeners toe voor slepen
    document.addEventListener("mousemove", doDrag);
    document.addEventListener("mouseup", stopDrag);
  }

  // ==================== UPDATE IMAGE WHEN URL CHANGES ====================
  const imageUrlInput = document.getElementById("afbeelding_url");
  const panoramaImage = document.querySelector(".panorama img");

  if (imageUrlInput && panoramaImage) {
    imageUrlInput.addEventListener("change", function () {
      // Update afbeelding bron
      panoramaImage.src = this.value;
      panoramaImage.onload = function () {
        // Update punt posities na laden van afbeelding
        setTimeout(updatePointPositions, 100);
      };
    });
  }

  // ==================== FORM VALIDATION ====================
  const form = document.getElementById("editPanoramaForm");
  if (form) {
    form.addEventListener("submit", function (e) {
      // Form submission validation can be added here if needed
      return true; // Sta formulier verzending toe
    });
  }

  // console.log("Bewerk.js initialization complete");
});
