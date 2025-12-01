document.addEventListener("scroll", (event) => {
  //console.log(window.scrollX);
  let info = document.getElementById("info");
  let place = Math.floor(window.scrollX / 1382.4);
  info.innerHTML = data[place];
  console.log(place);
});
let data = [
  "Welkom bij panorama 1: Dit is een prachtige locatie met adembenemende uitzichten.",
  "Welkom bij panorama 2: Hier kunt u genieten van de serene schoonheid van de natuur.",
];
