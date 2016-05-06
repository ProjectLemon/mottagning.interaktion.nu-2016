var config1 = {
  title: "Test",
  startTime: "19:00",
  date: "27/5",
  description: "This is an example card",
  place: "Campusängarna",
  lat: "63.821171",
  long: "20.310395",
  img: "/resources/img/DSC0477_small.jpg"
}

var config2 = {
  title: "Test2",
  startTime: "19:00",
  date: "27/5",
  description: "This is an example card",
  place: "Campusängarna",
  lat: "63.821171",
  long: "20.310395",
  img: "/resources/img/DSC0477_small.jpg"
}

var exampleCard = new ExpandableCardFactory();
document.body.appendChild(exampleCard.newActivityCard(config1));
document.body.appendChild(exampleCard.newActivityCard(config2));
