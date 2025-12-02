document.addEventListener("scroll", (event) => {
  //console.log(window.scrollX);
  let info = document.getElementById("info");
  let place = Math.floor((window.scrollX + window.innerWidth / 4) / 1382.4);
  if (data && data.length > 0) {
    place = Math.min(place, data.length - 1);
    info.innerHTML = data[place] + "<br>" + place;
  }
  console.log(place);
});

fetch("assets/includes/omschrijving-data.php")
  .then((response) => {
    if (!response.ok) {
      throw new Error("Network response was not ok");
    }
    return response.json();
  })
  .then((jsonData) => {
    data = jsonData;
    // console.log("Fetched data:", jsonData);
  })
  .catch((error) => {
    console.error("Error fetching data:", error);
    // Keep the fallback data
  });

// let data = [
//   "Welkom bij panorama 1: Dit is een prachtige locatie met adembenemende uitzichten.",
//   "Welkom bij panorama 2: Hier kunt u genieten van de serene schoonheid van de natuur.",
// ];
