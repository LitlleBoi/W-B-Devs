document.addEventListener("DOMContentLoaded", function () {
  let pointCounter = 0;
  let bronCounter = 0;

  // Function to handle file uploads
  function handleFileUpload(
    fileInput,
    hiddenInput,
    previewElement,
    fileNameSpan
  ) {
    const file = fileInput.files[0];
    if (file) {
      // Update file name display
      fileNameSpan.textContent = file.name;
      fileNameSpan.style.color = "#333";

      // Create preview
      const reader = new FileReader();
      reader.onload = function (e) {
        // Set the hidden input value (this will be the base64 data URL)
        hiddenInput.value = e.target.result;

        // Show preview
        previewElement.innerHTML = "";
        const img = document.createElement("img");
        img.src = e.target.result;
        img.style.maxWidth = "200px";
        img.style.marginTop = "10px";
        img.style.border = "1px solid #ddd";
        img.style.borderRadius = "4px";
        img.style.padding = "5px";
        img.style.background = "white";
        previewElement.appendChild(img);
      };
      reader.readAsDataURL(file);
    }
  }

  // Add event listeners to all upload buttons
  document.addEventListener("click", function (e) {
    if (e.target.closest(".upload-btn")) {
      const uploadBtn = e.target.closest(".upload-btn");
      const container = uploadBtn.closest(".file-upload-container");
      const fileInput = container.querySelector(".file-upload-input");
      fileInput.click();
    }
  });

  // Handle file selection
  document.addEventListener("change", function (e) {
    if (e.target.classList.contains("file-upload-input")) {
      const fileInput = e.target;
      const container = fileInput.closest(".file-upload-container");
      const fileNameSpan = container.querySelector(".file-name");

      // Find the closest new-source-card or source-edit-card
      const sourceCard = fileInput.closest(
        ".new-source-card, .source-edit-card"
      );

      if (sourceCard) {
        const hiddenInput =
          sourceCard.querySelector(
            'input[type="hidden"].new-bron-afbeelding-url'
          ) || sourceCard.querySelector('input[name*="bron-afbeelding"]');
        const previewElement = sourceCard.querySelector(".bron-image-preview");

        if (hiddenInput && previewElement) {
          handleFileUpload(
            fileInput,
            hiddenInput,
            previewElement,
            fileNameSpan
          );
        }
      }
    }
  });

  // Add new point button
  document
    .getElementById("addPointButton")
    .addEventListener("click", function () {
      pointCounter++;

      const template = document
        .getElementById("newPointTemplate")
        .cloneNode(true);
      const newPoint = template.content || template;
      const pointHTML = newPoint.querySelector(".new-point-card").outerHTML;

      // Replace placeholders
      const finalHTML = pointHTML
        .replace(/TEMP_ID/g, "temp_" + pointCounter)
        .replace(/POINT_ID/g, "temp_" + pointCounter);

      const container = document.getElementById("newPointsContainer");
      const div = document.createElement("div");
      div.innerHTML = finalHTML;
      container.appendChild(div);

      // Add event listeners to the new point
      const pointElement = div.querySelector(".new-point-card");
      pointElement
        .querySelector(".remove-new-point")
        .addEventListener("click", function () {
          pointElement.remove();
        });

      // Add event listener for adding bronnen to this new point
      pointElement
        .querySelector(".add-new-bron-to-point")
        .addEventListener("click", function () {
          addNewBronToPoint(
            "temp_" + pointCounter,
            pointElement.querySelector(".point-bronnen-container")
          );
        });

      // Add file upload functionality for the new point
      pointElement.querySelectorAll(".file-upload-input").forEach((input) => {
        input.addEventListener("change", function () {
          const file = this.files[0];
          if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
              const hiddenInput = this.parentElement.querySelector(
                'input[type="hidden"]'
              );
              if (hiddenInput) {
                hiddenInput.value = e.target.result;
              }
            }.bind(this);
            reader.readAsDataURL(file);
          }
        });
      });
    });

  // Add new bron to existing point
  document.querySelectorAll(".add-bron-to-existing").forEach((button) => {
    button.addEventListener("click", function () {
      const pointId = this.getAttribute("data-point-id");
      const container = document.querySelector(
        '.new-bronnen-to-existing-container[data-point-id="' + pointId + '"]'
      );
      if (!container) {
        const newContainer = document.createElement("div");
        newContainer.className = "new-bronnen-to-existing-container";
        newContainer.setAttribute("data-point-id", pointId);
        newContainer.style.marginTop = "15px";
        this.parentElement.appendChild(newContainer);
        addNewBronToExistingPoint(pointId, newContainer);
      } else {
        addNewBronToExistingPoint(pointId, container);
      }
    });
  });

  // Function to add new bron to a point
  function addNewBronToPoint(pointId, container) {
    bronCounter++;

    const template = document.getElementById("newBronTemplate").cloneNode(true);
    const newBron = template.content || template;
    const bronHTML = newBron.querySelector(".new-source-card").outerHTML;

    // Replace placeholders
    const finalHTML = bronHTML
      .replace(/TEMP_ID/g, "temp_bron_" + bronCounter)
      .replace(/POINT_ID/g, pointId);

    const div = document.createElement("div");
    div.innerHTML = finalHTML;
    container.appendChild(div);

    // Add event listener to remove button
    const bronElement = div.querySelector(".new-source-card");
    bronElement
      .querySelector(".remove-new-bron")
      .addEventListener("click", function () {
        bronElement.remove();
      });

    // Add click handler for upload button
    bronElement
      .querySelector(".upload-btn")
      .addEventListener("click", function (e) {
        e.preventDefault();
        const fileInput = bronElement.querySelector(".file-upload-input");
        fileInput.click();
      });

    // Add file upload functionality
    bronElement
      .querySelector(".file-upload-input")
      .addEventListener("change", function () {
        const file = this.files[0];
        if (file) {
          const fileNameSpan = this.closest(
            ".file-upload-container"
          ).querySelector(".file-name");
          const hiddenInput = bronElement.querySelector(
            ".new-bron-afbeelding-url"
          );
          const previewElement = bronElement.querySelector(
            ".bron-image-preview"
          );

          handleFileUpload(this, hiddenInput, previewElement, fileNameSpan);
        }
      });
  }

  // Function to add new bron to existing point
  function addNewBronToExistingPoint(pointId, container) {
    bronCounter++;

    // Create a simple form for new bron to existing point
    const bronDiv = document.createElement("div");
    bronDiv.className = "new-source-card";
    bronDiv.innerHTML = `
                <div class="action-header">
                    <h5>Nieuwe Bron</h5>
                    <div class="point-actions">
                        <select class="status-select" name="new_bronnen_to_existing[${pointId}][temp_bron_${bronCounter}][status]">
                            <option value="concept">Concept</option>
                            <option value="gepubliceerd">Gepubliceerd</option>
                            <option value="gearchiveerd">Gearchiveerd</option>
                        </select>
                        <button type="button" class="delete-btn remove-existing-bron">× Verwijder</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Titel:</label>
                            <input type="text" class="form-control" name="new_bronnen_to_existing[${pointId}][temp_bron_${bronCounter}][titel]" placeholder="Bron titel">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Auteur:</label>
                            <input type="text" class="form-control" name="new_bronnen_to_existing[${pointId}][temp_bron_${bronCounter}][auteur]" placeholder="Auteur">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Type:</label>
                            <select class="form-control" name="new_bronnen_to_existing[${pointId}][temp_bron_${bronCounter}][bron_type]">
                                <option value="boek">Boek</option>
                                <option value="artikel">Artikel</option>
                                <option value="website" selected>Website</option>
                                <option value="video">Video</option>
                                <option value="document">Document</option>
                                <option value="ander">Ander</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Publicatiejaar:</label>
                            <input type="text" class="form-control" name="new_bronnen_to_existing[${pointId}][temp_bron_${bronCounter}][publicatie_jaar]" placeholder="2023">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Catalogusnummer:</label>
                            <input type="text" class="form-control" name="new_bronnen_to_existing[${pointId}][temp_bron_${bronCounter}][catalogusnummer]" placeholder="12345">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Referentie tekst:</label>
                    <textarea class="form-control" name="new_bronnen_to_existing[${pointId}][temp_bron_${bronCounter}][referentie_tekst]" rows="2" placeholder="Referentie tekst"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Bron Afbeelding:</label>
                    <div class="file-upload-container" style="margin-bottom: 10px;">
                        <input type="file" class="file-upload-input" 
                               accept="image/*" 
                               style="display: none;">
                        <button type="button" class="btn btn-secondary upload-btn" style="padding: 8px 15px;">
                            <i class="fas fa-upload"></i> Kies afbeelding
                        </button>
                        <span class="file-name" style="margin-left: 10px; color: #666;">Geen bestand gekozen</span>
                    </div>
                    <input type="hidden" class="new-bron-afbeelding-url"
                        name="new_bronnen_to_existing[${pointId}][temp_bron_${bronCounter}][bron-afbeelding]" value="">
                    <div class="bron-image-preview" style="margin-top: 10px;">
                        <p class="text-muted" style="font-size: 12px;">
                            Geen afbeelding geselecteerd
                        </p>
                    </div>
                    <small class="text-muted">Upload een bestand</small>
                </div>
            `;

    container.appendChild(bronDiv);

    // Add event listener to remove button
    bronDiv
      .querySelector(".remove-existing-bron")
      .addEventListener("click", function () {
        bronDiv.remove();
      });

    // Add file upload functionality
    const uploadBtn = bronDiv.querySelector(".upload-btn");
    const fileInput = bronDiv.querySelector(".file-upload-input");
    const fileNameSpan = bronDiv.querySelector(".file-name");
    const hiddenInput = bronDiv.querySelector(".new-bron-afbeelding-url");
    const previewElement = bronDiv.querySelector(".bron-image-preview");

    uploadBtn.addEventListener("click", function (e) {
      e.preventDefault();
      fileInput.click();
    });

    fileInput.addEventListener("change", function () {
      const file = this.files[0];
      if (file) {
        handleFileUpload(this, hiddenInput, previewElement, fileNameSpan);
      }
    });
  }

  // Update coordinates when points are dragged
  if (typeof setupPointDragging === "function") {
    setupPointDragging();
  }
});
