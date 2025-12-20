// omschrijving.js - Opgelost voor horizontaal scrollen
/**
 * Omschrijving JavaScript
 *
 * Deze JavaScript file laadt en beheert panorama beschrijvingsdata.
 * Het update de zijbalk informatie gebaseerd op het huidige zichtbare panorama.
 */

// Initialiseer globale variabelen
let panoramaData = []; // Array om panorama data op te slaan
let panoramaWidth = 1182.4; // Standaard breedte van een panorama (wordt later gedetecteerd)
let totalPanoramas = 0; // Totaal aantal geladen panorama's
let isInitialized = false; // Status van initialisatie
let scrollContainer = null; // Referentie naar het scrollende container element

// Hoofdfunctie om alles te initialiseren
function initializePanoramaInfo() {
  // console.log("Initializing panorama info...");

  const info = document.getElementById("info");
  if (!info) {
    console.error("Sidebar info element not found!");
    return;
  }

  // Toon laadbericht terwijl data wordt opgehaald
  info.innerHTML = `
        <div class="panorama-loading">
            <div>Informatie wordt geladen...</div>
        </div>
    `;

  // Haal data op van PHP endpoint
  fetch("assets/includes/omschrijving-data.php")
    .then((response) => {
      if (!response.ok)
        throw new Error(`HTTP error! status: ${response.status}`);
      return response.json();
    })
    .then((jsonData) => {
      // console.log("Raw data received:", jsonData);

      // Controleer data formaat en verwerk het correct
      if (jsonData.format === "v2" && jsonData.panoramas) {
        // Nieuw formaat met wrapper object
        panoramaData = jsonData.panoramas;
        // console.log("Using v2 format data:", panoramaData);
      } else if (Array.isArray(jsonData)) {
        // Controleer of het een platte array is [titel1, desc1, titel2, desc2...]
        if (jsonData.length > 0 && typeof jsonData[0] === "string") {
          // Converteer platte array naar gestructureerde objecten
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
          // console.log("Converted flat array to structured data");
        } else {
          // Is al een array van objecten
          panoramaData = jsonData;
          // console.log("Data is already array of objects");
        }
      } else if (jsonData.panoramas && Array.isArray(jsonData.panoramas)) {
        // Alternatief formaat met panoramas property
        panoramaData = jsonData.panoramas;
        // console.log("Using panoramas property data");
      } else {
        console.error("Unexpected data format:", jsonData);
        panoramaData = [];
      }

      totalPanoramas = panoramaData.length;
      // console.log(`Loaded ${totalPanoramas} panoramas`);

      // Debug: toon eerste paar items
      if (panoramaData.length > 0) {
        // console.log("First panorama item:", panoramaData[0]);
        // console.log("Sample catalog number:", panoramaData[0].catalogusnummer);
      }

      // Probeer werkelijke panorama breedte te detecteren
      detectPanoramaWidth();

      // Zoek het scroll container element
      findScrollContainer();

      // Toon initiële informatie
      updateSidebarInfo();

      // Markeer als geïnitialiseerd
      isInitialized = true;

      // Stel scroll listener in
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

// Functie om het scrollende container element te vinden
function findScrollContainer() {
  // Controleer veelvoorkomende scroll containers
  const possibleContainers = [
    ".panorama-strip",
    ".panorama-container",
    "main.panorama-container",
    "main",
  ];

  for (const selector of possibleContainers) {
    const element = document.querySelector(selector);
    if (element) {
      // Controleer of element horizontaal scrollbaar is
      const styles = window.getComputedStyle(element);
      const overflowX = styles.overflowX;

      if (overflowX === "auto" || overflowX === "scroll") {
        // console.log(`Found scroll container: ${selector}`);
        scrollContainer = element;
        return element;
      }
    }
  }

  // Als geen container gevonden, standaard naar window
  // console.log("No scroll container found, defaulting to window");
  scrollContainer = window;
  return window;
}

// Functie om panorama breedte te detecteren
function detectPanoramaWidth() {
  const panoramaImg = document.querySelector(".panorama img");
  if (panoramaImg && panoramaImg.width > 0) {
    panoramaWidth = panoramaImg.width;
    // console.log(`Panorama width detected: ${panoramaWidth}px`);
  } else {
    // Probeer breedte te krijgen van CSS of bereken het
    const panoramaDiv = document.querySelector(".panorama");
    if (panoramaDiv) {
      panoramaWidth = panoramaDiv.offsetWidth;
      // console.log(`Panorama width from offset: ${panoramaWidth}px`);
    }
  }
}

// Functie om huidige scroll positie en pagina te berekenen
function getCurrentPage() {
  let scrollX = 0;

  if (scrollContainer === window) {
    // Window scrolling
    scrollX = window.scrollX || window.pageXOffset || 0;
    // console.log(`Window scrollX: ${scrollX}`);
  } else {
    // Element scrolling
    scrollX = scrollContainer.scrollLeft || 0;
    // console.log(
    //   `${scrollContainer.className || scrollContainer.tagName} scrollLeft: ${scrollX}`
    // );
  }

  const viewportWidth = window.innerWidth;
  const totalWidth = panoramaWidth * totalPanoramas;

  // Bereken welk panorama in beeld is
  // Gebruik 50% (midden van viewport) voor betere nauwkeurigheid
  const viewportPosition = 0.5;
  const referencePoint = scrollX + viewportWidth * viewportPosition;

  let pageIndex = Math.floor(referencePoint / panoramaWidth);
  pageIndex = Math.max(0, Math.min(pageIndex, totalPanoramas - 1));

  // console.log(
  //   `Scroll position: ${scrollX}, Reference point: ${referencePoint.toFixed(0)}, Page: ${pageIndex + 1} of ${totalPanoramas}`
  // );
  return pageIndex;
}

// Functie om zijbalk informatie bij te werken
function updateSidebarInfo() {
  const info = document.getElementById("info");
  if (!info || panoramaData.length === 0) return;

  const currentPageIndex = getCurrentPage();
  const currentPageNumber = currentPageIndex + 1;

  // console.log(`Updating sidebar for page ${currentPageNumber} (index ${currentPageIndex})`);

  // Haal huidig panorama item op
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

  // Extraheer data met fallback waarden
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

  // Bouw de HTML voor de zijbalk
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

// Stel scroll listener in voor horizontaal scrollen
function setupScrollListener() {
  // console.log("Setting up horizontal scroll listener...");

  let ticking = false; // Flag om te voorkomen dat te veel events worden verwerkt

  function handleScroll() {
    if (!isInitialized || ticking) return;

    ticking = true;

    // Gebruik requestAnimationFrame voor smooth updates
    requestAnimationFrame(() => {
      // console.log("Scroll detected, updating info...");
      updateSidebarInfo();
      ticking = false;
    });
  }

  // Voeg scroll listener toe aan het gevonden container element
  if (scrollContainer && scrollContainer !== window) {
    // console.log(`Attaching scroll listener to:`, scrollContainer);
    scrollContainer.addEventListener("scroll", handleScroll, { passive: true });
  } else {
    // console.log("Attaching scroll listener to window");
    window.addEventListener("scroll", handleScroll, { passive: true });
  }

  // Voeg ook resize listener toe voor geval viewport verandert
  window.addEventListener("resize", handleScroll, { passive: true });

  // Voeg wheel listener toe voor betere detectie
  document.addEventListener(
    "wheel",
    function (e) {
      if (Math.abs(e.deltaX) > Math.abs(e.deltaY)) {
        // Horizontaal wiel scroll gedetecteerd
        handleScroll();
      }
    },
    { passive: true }
  );
}

// Initialiseer wanneer DOM volledig geladen is
document.addEventListener("DOMContentLoaded", function () {
  // console.log("DOM loaded, initializing panorama info...");
  // Korte vertraging om zeker te weten dat alle elementen gerenderd zijn
  setTimeout(initializePanoramaInfo, 500);
});

// Als DOM al geladen is
if (
  document.readyState === "interactive" ||
  document.readyState === "complete"
) {
  setTimeout(initializePanoramaInfo, 500);
}

// ==================== DEBUG FUNCTIES ====================

// Debug functie om alle info te tonen
window.debugPanorama = function () {
  // console.log("=== DEBUG INFO ===");
  // console.log("Panorama Data:", panoramaData);
  // console.log("Total Panoramas:", totalPanoramas);
  // console.log("Panorama Width:", panoramaWidth);
  // console.log("Scroll Container:", scrollContainer);

  // Controleer welk element daadwerkelijk scrollbaar is
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
      // console.log(`Element ${selector}:`, info);
    }
  });

  // console.log("Window scrollX:", window.scrollX || window.pageXOffset);
  // console.log("Window innerWidth:", window.innerWidth);
  // console.log("Current page index:", getCurrentPage());
  // console.log("Current page number:", getCurrentPage() + 1);
  // console.log("==================");
};

// Forceer update van zijbalk
window.forceUpdate = updateSidebarInfo;

// Testfunctie om naar specifieke pagina te scrollen
window.testClick = function (pageNumber) {
  const pageIndex = pageNumber - 1;
  const scrollPosition = pageIndex * panoramaWidth;

  // console.log(`Testing scroll to page ${pageNumber} (position: ${scrollPosition}px)`);

  // Scroll het gevonden container element
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

// Handmatige testfunctie om scroll detectie te verifiëren
window.testScrollDetection = function () {
  // console.log("=== TESTING SCROLL DETECTION ===");

  // Simuleer scrollen door scroll positie te veranderen
  const testScrollPositions = [
    0,
    panoramaWidth * 0.5,
    panoramaWidth,
    panoramaWidth * 1.5,
  ];

  testScrollPositions.forEach((pos, index) => {
    // console.log(`\nTest ${index + 1}: Setting scroll to ${pos}px`);

    if (scrollContainer && scrollContainer !== window) {
      scrollContainer.scrollLeft = pos;
    } else {
      window.scrollTo(pos, 0);
    }

    // Wacht even en controleer dan
    setTimeout(() => {
      // console.log(`Current page after scroll: ${getCurrentPage() + 1}`);
      updateSidebarInfo();
    }, 100);
  });
};
// ==================== END OF FILE ====================
