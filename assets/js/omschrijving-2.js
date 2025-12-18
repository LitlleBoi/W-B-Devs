// Initialize variables
let panoramaData = [];
let panoramaWidth = 1382.4;
let totalPanoramas = 0;
let isInitialized = false;
let isScrolling = false;
let scrollFrameId = null;
let lastUpdateTime = 0;

// Function to initialize everything
function initializePanoramaInfo() {
  console.log(" Initializing panorama info...");

  const info = document.getElementById("info");
  if (!info) {
    console.error("❌ Sidebar info element not found!");
    return;
  }

  // Show loading message
  info.innerHTML = `
        <div style="text-align: center; padding: 2rem;">
            <div style="margin-bottom: 1rem;">⏳</div>
            <div>Loading panorama information...</div>
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
      panoramaData = jsonData;
      totalPanoramas = Math.floor(panoramaData.length / 2);

      console.log(`✅ Loaded ${totalPanoramas} panoramas`);

      // Try to detect actual panorama width
      detectPanoramaWidth();

      // Show initial info
      updateSidebarInfo();

      // Mark as initialized
      isInitialized = true;

      // Setup scroll listener with immediate updates
      setupImmediateScrollListener();
    })
    .catch((error) => {
      console.error("❌ Error fetching data:", error);

      info.innerHTML = `
                <div style="padding: 2rem; text-align: center; color: #e53e3e;">
                    <div style="margin-bottom: 1rem;">⚠️</div>
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
    console.log(`📏 Panorama width detected: ${panoramaWidth}px`);
  }
}

// Function to calculate current page
function getCurrentPage() {
  const scrollX = window.scrollX;
  const viewportWidth = window.innerWidth;
  const viewportCenter = scrollX + viewportWidth / 2;

  let pageIndex = Math.floor(viewportCenter / panoramaWidth);
  pageIndex = Math.max(0, Math.min(pageIndex, totalPanoramas - 1));

  return pageIndex; // Returns 0-based index
}

// Function to update sidebar info
function updateSidebarInfo() {
  if (!isInitialized || panoramaData.length === 0) return;

  const info = document.getElementById("info");
  if (!info) return;

  const currentPageIndex = getCurrentPage();
  const currentPageNumber = currentPageIndex + 1;

  // Each panorama has 2 data entries: [title, description]
  const dataIndex = currentPageIndex * 2;

  if (dataIndex >= 0 && dataIndex < panoramaData.length) {
    const title = panoramaData[dataIndex] || `Panorama ${currentPageNumber}`;
    const description =
      panoramaData[dataIndex + 1] ||
      `Beschrijving voor panorama ${currentPageNumber}`;
    const progressPercent = Math.round(
      ((currentPageIndex + 1) / totalPanoramas) * 100
    );

    // Only update the content if page changed
    const currentPageElement = document.querySelector(
      ".current-page-indicator"
    );
    if (
      !currentPageElement ||
      currentPageElement.dataset.page != currentPageIndex
    ) {
      info.innerHTML = `
                <div style="margin-bottom: 1.5rem;">
                    <div style="font-size: 0.9rem; color: #4a5568; margin-bottom: 0.5rem;">
                        📍 Huidige Pagina
                    </div>
                    <div class="current-page-indicator" data-page="${currentPageIndex}" 
                         style="font-size: 1.3rem; font-weight: bold; color: #1a365d; margin-bottom: 0.5rem;">
                        Pagina ${currentPageNumber}: ${title}
                    </div>
                    <div style="color: #718096; line-height: 1.5;">
                        ${description}
                    </div>
                </div>
                
                <div style="background: #edf2f7; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="font-size: 0.9rem; color: #4a5568;">Voortgang</span>
                        <span style="font-size: 0.9rem; color: #2d3748; font-weight: bold;">
                            Pagina ${currentPageNumber} van ${totalPanoramas}
                        </span>
                    </div>
                    <div style="height: 6px; background: #cbd5e0; border-radius: 3px; overflow: hidden;">
                        <div style="height: 100%; background: #3b82f6; width: ${progressPercent}%; border-radius: 3px;"></div>
                    </div>
                </div>
                
                <div>
                    <div style="font-size: 0.9rem; color: #4a5568; margin-bottom: 0.75rem;">
                        🗺️ Alle Panorama's
                    </div>
                    <div id="panoramaList" style="max-height: 300px; overflow-y: auto;">
                        ${generatePanoramaList(currentPageIndex)}
                    </div>
                </div>
            `;

      // Setup click listeners for panorama items
      setupPanoramaClickListeners();
    }

    // Update active state in list without recreating entire list
    updateActivePanoramaItem(currentPageIndex);
  }
}

// Update only the active state in panorama list (for performance)
function updateActivePanoramaItem(activeIndex) {
  const panoramaList = document.getElementById("panoramaList");
  if (!panoramaList) return;

  // Remove active class from all items
  document.querySelectorAll(".panorama-item.active").forEach((item) => {
    item.classList.remove("active");
    item.style.background = "white";
    item.style.borderLeftColor = "#e2e8f0";
    const textDiv = item.querySelector("div:first-child");
    if (textDiv) textDiv.style.fontWeight = "normal";
  });

  // Add active class to current item
  const activeItem = document.getElementById(`panorama-item-${activeIndex}`);
  if (activeItem) {
    activeItem.classList.add("active");
    activeItem.style.background = "#ebf8ff";
    activeItem.style.borderLeftColor = "#3b82f6";
    const textDiv = activeItem.querySelector("div:first-child");
    if (textDiv) textDiv.style.fontWeight = "bold";

    // Auto-scroll to active item (but only if not currently scrolling fast)
    if (!isScrolling) {
      setTimeout(() => {
        scrollToCurrentPage(activeIndex);
      }, 50);
    }
  }
}

// Generate panorama list with clickable items
function generatePanoramaList(currentIndex) {
  let listHTML = "";

  for (let i = 0; i < totalPanoramas; i++) {
    const title = panoramaData[i * 2] || `Panorama ${i + 1}`;
    const isActive = i === currentIndex;

    listHTML += `
            <div id="panorama-item-${i}" 
                 data-page="${i}"
                 class="panorama-item ${isActive ? "active" : ""}" 
                 style="padding: 0.75rem; margin-bottom: 0.5rem; 
                        background: ${isActive ? "#ebf8ff" : "white"}; 
                        border-left: 3px solid ${
                          isActive ? "#3b82f6" : "#e2e8f0"
                        };
                        border-radius: 4px; 
                        cursor: pointer;
                        transition: all 0.2s ease;">
                <div style="font-weight: ${
                  isActive ? "bold" : "normal"
                }; color: #2d3748;">
                    Pagina ${i + 1}. ${title}
                </div>
                <div style="font-size: 0.85rem; color: #718096; margin-top: 0.25rem;">
                    ${
                      isActive
                        ? "📍 Huidige pagina"
                        : "Klik om naar deze pagina te gaan →"
                    }
                </div>
            </div>
        `;
  }

  return listHTML;
}

// Setup click listeners for panorama items
function setupPanoramaClickListeners() {
  // Add click listeners using event delegation for better performance
  const panoramaList = document.getElementById("panoramaList");
  if (panoramaList) {
    panoramaList.onclick = function (e) {
      const item = e.target.closest(".panorama-item");
      if (item) {
        e.stopPropagation();
        const pageIndex = parseInt(item.getAttribute("data-page"));
        if (!isNaN(pageIndex)) {
          console.log(`🎯 Clicked on page ${pageIndex + 1}`);
          scrollToPanorama(pageIndex);
        }
      }
    };
  }
}

// Function to scroll to current page in the sidebar list
function scrollToCurrentPage(pageIndex) {
  const panoramaList = document.getElementById("panoramaList");
  if (!panoramaList) return;

  const activeItem = document.getElementById(`panorama-item-${pageIndex}`);
  if (activeItem) {
    // Only scroll if item is not visible
    const itemRect = activeItem.getBoundingClientRect();
    const listRect = panoramaList.getBoundingClientRect();

    const isVisible =
      itemRect.top >= listRect.top && itemRect.bottom <= listRect.bottom;

    if (!isVisible) {
      activeItem.scrollIntoView({
        behavior: "smooth",
        block: "center",
      });
    }
  }
}

// Function to scroll to panorama in main view
function scrollToPanorama(pageIndex) {
  const scrollPosition = pageIndex * panoramaWidth;

  console.log(
    `🎯 Scrolling to page ${pageIndex + 1} (position: ${scrollPosition}px)`
  );

  window.scrollTo({
    left: scrollPosition,
    behavior: "smooth",
  });

  // Update sidebar after scroll completes
  setTimeout(updateSidebarInfo, 300);
}

// Setup scroll listener with immediate updates using requestAnimationFrame
function setupImmediateScrollListener() {
  console.log("🔧 Setting up immediate scroll listener...");

  let ticking = false;
  let lastScrollX = window.scrollX;

  function handleScroll() {
    const currentScrollX = window.scrollX;
    const scrollDelta = Math.abs(currentScrollX - lastScrollX);
    lastScrollX = currentScrollX;

    // Only update if user actually scrolled (not just tiny movements)
    if (scrollDelta > 1 && isInitialized && !ticking) {
      ticking = true;

      // Use requestAnimationFrame for smooth updates
      requestAnimationFrame(() => {
        updateSidebarInfo();
        ticking = false;
      });

      // Mark as scrolling
      isScrolling = true;

      // Clear any previous timeout
      if (window.scrollStopTimeout) {
        clearTimeout(window.scrollStopTimeout);
      }

      // Set timeout to mark when scrolling stops
      window.scrollStopTimeout = setTimeout(() => {
        isScrolling = false;
        // Final update when scrolling stops completely
        updateSidebarInfo();
        console.log("🛑 Scrolling stopped");
      }, 150);
    }
  }

  window.addEventListener("scroll", handleScroll, { passive: true });

  console.log("✅ Immediate scroll listener setup complete");
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
  console.log("Is scrolling:", isScrolling);
  console.log("==================");
};

window.forceUpdate = updateSidebarInfo;
window.scrollToPanorama = scrollToPanorama;
window.testClick = function (pageNumber) {
  const pageIndex = pageNumber - 1;
  console.log(`🧪 Testing click on page ${pageNumber} (index ${pageIndex})`);
  scrollToPanorama(pageIndex);
};
