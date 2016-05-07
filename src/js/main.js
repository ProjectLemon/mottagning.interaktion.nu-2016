var config1 = {
  title: "Vinbrännboll",
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
  startTime: "13:00",
  date: "31/5",
  description: "This is an example card that has a lot of text. I mean it has a huge amount of text! HOw can it even be that it can contain this much text. It's so incredible much! <p>Utgång: Origo</p>",
  place: "Campusängarna",
  lat: "63.821171",
  long: "20.310395",
  img: "/resources/img/DSC0477_small.jpg"
}

var exampleCard = new ExpandableCardFactory();
document.body.appendChild(exampleCard.newActivityCard(config1));
document.body.appendChild(exampleCard.newActivityCard(config2));
