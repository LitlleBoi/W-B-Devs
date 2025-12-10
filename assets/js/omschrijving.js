// omschrijving.js - Fixed for horizontal scrolling
// Initialize variables
let panoramaData = [];
let panoramaWidth = 1182.4;
let totalPanoramas = 0;
let isInitialized = false;
let scrollContainer = null;

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
  async function name(params) {}
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

      // Find the scroll container
      findScrollContainer();

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

// Function to find which container is scrolling
function findScrollContainer() {
  // Check common scrolling containers
  const possibleContainers = [
    ".panorama-strip",
    ".panorama-container",
    "main.panorama-container",
    "main",
  ];

  for (const selector of possibleContainers) {
    const element = document.querySelector(selector);
    if (element) {
      // Check if element is scrollable horizontally
      const styles = window.getComputedStyle(element);
      const overflowX = styles.overflowX;

      if (overflowX === "auto" || overflowX === "scroll") {
        console.log(`Found scroll container: ${selector}`);
        scrollContainer = element;
        return element;
      }
    }
  }

  // If no container found, default to window
  console.log("No scroll container found, defaulting to window");
  scrollContainer = window;
  return window;
}

// Function to detect panorama width
function detectPanoramaWidth() {
  const panoramaImg = document.querySelector(".panorama img");
  if (panoramaImg && panoramaImg.width > 0) {
    panoramaWidth = panoramaImg.width;
    console.log(`Panorama width detected: ${panoramaWidth}px`);
  } else {
    // Try to get width from CSS or calculate
    const panoramaDiv = document.querySelector(".panorama");
    if (panoramaDiv) {
      panoramaWidth = panoramaDiv.offsetWidth;
      console.log(`Panorama width from offset: ${panoramaWidth}px`);
    }
  }
}

// Function to get scroll position from the correct element
function getCurrentPage() {
  let scrollX = 0;

  if (scrollContainer === window) {
    // Window scrolling
    scrollX = window.scrollX || window.pageXOffset || 0;
    console.log(`Window scrollX: ${scrollX}`);
  } else {
    // Element scrolling
    scrollX = scrollContainer.scrollLeft || 0;
    console.log(
      `${
        scrollContainer.className || scrollContainer.tagName
      } scrollLeft: ${scrollX}`
    );
  }

  const viewportWidth = window.innerWidth;
  const totalWidth = panoramaWidth * totalPanoramas;

  // Calculate which panorama is in view
  // Use 50% (center of viewport) for better accuracy
  const viewportPosition = 0.5;
  const referencePoint = scrollX + viewportWidth * viewportPosition;

  let pageIndex = Math.floor(referencePoint / panoramaWidth);
  pageIndex = Math.max(0, Math.min(pageIndex, totalPanoramas - 1));

  console.log(
    `Scroll position: ${scrollX}, Reference point: ${referencePoint.toFixed(
      0
    )}, Page: ${pageIndex + 1} of ${totalPanoramas}`
  );
  return pageIndex;
}

// Function to update sidebar info
function updateSidebarInfo() {
  const info = document.getElementById("info");
  if (!info || panoramaData.length === 0) return;

  const currentPageIndex = getCurrentPage();
  const currentPageNumber = currentPageIndex + 1;

  console.log(
    `Updating sidebar for page ${currentPageNumber} (index ${currentPageIndex})`
  );

  // Get current panorama item
  const panoramaItem = panoramaData[currentPageIndex];

  if (!panoramaItem) {
    console.error(`No data for page ${currentPageNumber}`);
    info.innerHTML = `
      <div class="panorama-error">
        <div>No data available for panorama ${currentPageNumber}</div>
      </div>
    `;
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

// Setup scroll listener for horizontal scrolling
function setupScrollListener() {
  console.log("Setting up horizontal scroll listener...");

  let ticking = false;

  function handleScroll() {
    if (!isInitialized || ticking) return;

    ticking = true;

    requestAnimationFrame(() => {
      console.log("Scroll detected, updating info...");
      updateSidebarInfo();
      ticking = false;
    });
  }

  // Attach scroll listener to the found container
  if (scrollContainer && scrollContainer !== window) {
    console.log(`Attaching scroll listener to:`, scrollContainer);
    scrollContainer.addEventListener("scroll", handleScroll, { passive: true });
  } else {
    console.log("Attaching scroll listener to window");
    window.addEventListener("scroll", handleScroll, { passive: true });
  }

  // Also add resize listener in case viewport changes
  window.addEventListener("resize", handleScroll, { passive: true });

  // Add wheel listener for better detection
  document.addEventListener(
    "wheel",
    function (e) {
      if (Math.abs(e.deltaX) > Math.abs(e.deltaY)) {
        // Horizontal wheel scroll detected
        handleScroll();
      }
    },
    { passive: true }
  );
}

// Initialize when DOM is fully loaded
document.addEventListener("DOMContentLoaded", function () {
  console.log("DOM loaded, initializing panorama info...");
  // Small delay to ensure all elements are rendered
  setTimeout(initializePanoramaInfo, 500);
});

// If DOM is already loaded
if (
  document.readyState === "interactive" ||
  document.readyState === "complete"
) {
  setTimeout(initializePanoramaInfo, 500);
}

// Debug functions
window.debugPanorama = function () {
  console.log("=== DEBUG INFO ===");
  console.log("Panorama Data:", panoramaData);
  console.log("Total Panoramas:", totalPanoramas);
  console.log("Panorama Width:", panoramaWidth);
  console.log("Scroll Container:", scrollContainer);

  // Check which element is actually scrollable
  const checkElementScroll = (selector) => {
    const el = document.querySelector(selector);
    if (el) {
      const styles = window.getComputedStyle(el);
      return {
        selector,
        element: el,
        overflowX: styles.overflowX,
        scrollLeft: el.scrollLeft,
        scrollWidth: el.scrollWidth,
        clientWidth: el.clientWidth,
        isScrollable: el.scrollWidth > el.clientWidth,
      };
    }
    return null;
  };

  const elementsToCheck = [
    ".panorama-strip",
    ".panorama-container",
    "main",
    "body",
    "html",
  ];

  elementsToCheck.forEach((selector) => {
    const info = checkElementScroll(selector);
    if (info) {
      console.log(`Element ${selector}:`, info);
    }
  });

  console.log("Window scrollX:", window.scrollX || window.pageXOffset);
  console.log("Window innerWidth:", window.innerWidth);
  console.log("Current page index:", getCurrentPage());
  console.log("Current page number:", getCurrentPage() + 1);
  console.log("==================");
};

window.forceUpdate = updateSidebarInfo;

window.testClick = function (pageNumber) {
  const pageIndex = pageNumber - 1;
  const scrollPosition = pageIndex * panoramaWidth;

  console.log(
    `Testing scroll to page ${pageNumber} (position: ${scrollPosition}px)`
  );

  // Scroll the found container
  if (scrollContainer && scrollContainer !== window) {
    scrollContainer.scrollTo({
      left: scrollPosition,
      behavior: "smooth",
    });
  } else {
    window.scrollTo({
      left: scrollPosition,
      behavior: "smooth",
    });
  }
};

// Manual test function to verify scroll detection
window.testScrollDetection = function () {
  console.log("=== TESTING SCROLL DETECTION ===");

  // Simulate scrolling by changing scroll position
  const testScrollPositions = [
    0,
    panoramaWidth * 0.5,
    panoramaWidth,
    panoramaWidth * 1.5,
  ];

  testScrollPositions.forEach((pos, index) => {
    console.log(`\nTest ${index + 1}: Setting scroll to ${pos}px`);

    if (scrollContainer && scrollContainer !== window) {
      scrollContainer.scrollLeft = pos;
    } else {
      window.scrollTo(pos, 0);
    }

    // Wait a bit then check
    setTimeout(() => {
      console.log(`Current page after scroll: ${getCurrentPage() + 1}`);
      updateSidebarInfo();
    }, 100);
  });
};
