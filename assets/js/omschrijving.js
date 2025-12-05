// Initialize variables
let panoramaData = [];
let panoramaWidth = 1382.4;
let totalPanoramas = 0;
let isInitialized = false;

// Function to initialize everything
function initializePanoramaInfo() {
  console.log("Initializing panorama info...");

  const info = document.getElementById("info");
  if (!info) {
    console.error("Sidebar info element not found!");
    return;
  }

  // Show loading message
  info.innerHTML = `
        <div class="panorama-loading">
            <div>Informatie wordt geladen...</div>
        </div>
    `;

  // Fetch data from PHP
  fetch("assets/includes/omschrijving-data.php")
    .then((response) => {
      if (!response.ok)
        throw new Error(`HTTP error! status: ${response.status}`);
      return response.json();
    })
    .then((jsonData) => {
      console.log("Raw data received:", jsonData);

      // Check data format
      if (jsonData.format === "v2" && jsonData.panoramas) {
        // New format with wrapper object
        panoramaData = jsonData.panoramas;
        console.log("Using v2 format data:", panoramaData);
      } else if (Array.isArray(jsonData)) {
        // Check if it's flat array [title1, desc1, title2, desc2...]
        if (jsonData.length > 0 && typeof jsonData[0] === "string") {
          // Convert flat array to structured objects
          panoramaData = [];
          for (let i = 0; i < jsonData.length; i += 2) {
            panoramaData.push({
              titel: jsonData[i] || `Panorama ${i / 2 + 1}`,
              beschrijving: jsonData[i + 1] || "",
              catalogusnummer: `CT-${String(i / 2 + 1).padStart(3, "0")}`,
              auteur: "Anoniem",
              jaar: "2024",
            });
          }
          console.log("Converted flat array to structured data");
        } else {
          // Already array of objects
          panoramaData = jsonData;
          console.log("Data is already array of objects");
        }
      } else if (jsonData.panoramas && Array.isArray(jsonData.panoramas)) {
        // Alternative format with panoramas property
        panoramaData = jsonData.panoramas;
        console.log("Using panoramas property data");
      } else {
        console.error("Unexpected data format:", jsonData);
        panoramaData = [];
      }

      totalPanoramas = panoramaData.length;
      console.log(`Loaded ${totalPanoramas} panoramas`);

      // Debug: show first few items
      if (panoramaData.length > 0) {
        console.log("First panorama item:", panoramaData[0]);
        console.log("Sample catalog number:", panoramaData[0].catalogusnummer);
      }

      // Try to detect actual panorama width
      detectPanoramaWidth();

      // Show initial info
      updateSidebarInfo();

      // Mark as initialized
      isInitialized = true;

      // Setup scroll listener
      setupScrollListener();
    })
    .catch((error) => {
      console.error("Error fetching data:", error);

      info.innerHTML = `
                <div class="panorama-error">
                    <div>Failed to load data</div>
                </div>
            `;
    });
}

// Function to detect panorama width
function detectPanoramaWidth() {
  const panoramaImg = document.querySelector(".panorama img");
  if (panoramaImg && panoramaImg.width > 0) {
    panoramaWidth = panoramaImg.width;
    console.log(`Panorama width detected: ${panoramaWidth}px`);
  }
}

// Function to calculate current page
function getCurrentPage() {
  const scrollX = window.scrollX;
  const viewportWidth = window.innerWidth;
  const viewportCenter = scrollX + viewportWidth / 4;

  let pageIndex = Math.floor(viewportCenter / panoramaWidth);
  pageIndex = Math.max(0, Math.min(pageIndex, totalPanoramas - 1));

  return pageIndex;
}

// Function to update sidebar info
function updateSidebarInfo() {
  const info = document.getElementById("info");
  if (!info || panoramaData.length === 0) return;

  const currentPageIndex = getCurrentPage();
  const currentPageNumber = currentPageIndex + 1;

  // Get current panorama item
  const panoramaItem = panoramaData[currentPageIndex];

  if (!panoramaItem) {
    console.error(`No data for page ${currentPageNumber}`);
    return;
  }

  // Extract data with fallbacks
  const title =
    panoramaItem.titel || panoramaItem.title || `Panorama ${currentPageNumber}`;
  const description =
    panoramaItem.beschrijving ||
    panoramaItem.description ||
    `Beschrijving voor panorama ${currentPageNumber}`;
  const catalogNumber =
    panoramaItem.catalogusnummer ||
    panoramaItem.catalogNumber ||
    `CT-${String(currentPageNumber).padStart(3, "0")}`;
  const auteursrechtlicentie = panoramaItem.auteursrechtlicentie || "Onbekend";
  const vervaardiger = panoramaItem.vervaardiger || "Onbekend";
  const year = panoramaItem.jaar || panoramaItem.year || "2024";
  const location = panoramaItem.locatie || panoramaItem.location;
  const medium = panoramaItem.medium;
  const progressPercent = Math.round(
    ((currentPageIndex + 1) / totalPanoramas) * 100
  );

  info.innerHTML = `
        <div class="panorama-info">
            <div class="panorama-current">
                <div class="panorama-section-title">Pagina Informatie</div>
                <div class="panorama-title">${title}</div>
                <div class="panorama-description">${description}</div>
                
                <div class="panorama-details">
                    <div class="panorama-details-grid">
                        <div class="panorama-detail-label">Catalogusnummer:</div>
                        <div class="panorama-detail-value">${catalogNumber}</div>
                        
                        <div class="panorama-detail-label">Pagina:</div>
                        <div class="panorama-detail-value">${currentPageNumber} van ${totalPanoramas}</div>
                        
                        <div class="panorama-detail-label">Auteursrechtlicentie:</div>
                        <div class="panorama-detail-value">${auteursrechtlicentie}</div>
                        
                        <div class="panorama-detail-label">Vervaardiger:</div>
                        <div class="panorama-detail-value">${vervaardiger}</div>
                        
                        <div class="panorama-detail-label">Jaar:</div>
                        <div class="panorama-detail-value">${year}</div>
                        
                        ${
                          location
                            ? `
                        <div class="panorama-detail-label">Locatie:</div>
                        <div class="panorama-detail-value">${location}</div>
                        `
                            : ""
                        }
                        
                        ${
                          medium
                            ? `
                        <div class="panorama-detail-label">Medium:</div>
                        <div class="panorama-detail-value">${medium}</div>
                        `
                            : ""
                        }
                        
                        <div class="panorama-detail-label">Status:</div>
                        <div class="panorama-detail-value">Geverifieerd</div>
                        
                        <div class="panorama-detail-label">Toegevoegd:</div>
                        <div class="panorama-detail-value">Januari 2024</div>
                    </div>
                </div>
            </div>
            
            
        </div>
    `;
}

// Setup scroll listener (simplified since no list to scroll)
function setupScrollListener() {
  console.log("Setting up scroll listener...");

  let ticking = false;

  function handleScroll() {
    if (!isInitialized || ticking) return;

    ticking = true;

    requestAnimationFrame(() => {
      updateSidebarInfo();
      ticking = false;
    });
  }

  window.addEventListener("scroll", handleScroll, { passive: true });
}

// Initialize when page loads
document.addEventListener("DOMContentLoaded", initializePanoramaInfo);

// If DOM is already loaded
if (
  document.readyState === "interactive" ||
  document.readyState === "complete"
) {
  setTimeout(initializePanoramaInfo, 100);
}

// Debug functions
window.debugPanorama = function () {
  console.log("=== DEBUG INFO ===");
  console.log("Current scrollX:", window.scrollX);
  console.log("Current page index:", getCurrentPage());
  console.log("Current page number:", getCurrentPage() + 1);
  console.log("Panorama width:", panoramaWidth);
  console.log("Total panoramas:", totalPanoramas);
  console.log("Panorama data sample:", panoramaData.slice(0, 3));
  console.log("==================");
};

window.forceUpdate = updateSidebarInfo;
window.testClick = function (pageNumber) {
  const pageIndex = pageNumber - 1;
  const scrollPosition = pageIndex * panoramaWidth;

  console.log(
    `Testing scroll to page ${pageNumber} (position: ${scrollPosition}px)`
  );

  window.scrollTo({
    left: scrollPosition,
    behavior: "smooth",
  });
};
