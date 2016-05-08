/*var config1 = {
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

var config3 = {
  title: "Test large",
  startTime: "13:00",
  date: "31/5",
  description: "This is a super masive card!",
  place: "Campusängarna",
  lat: "63.821171",
  long: "20.310395",
  img: "/resources/img/DSC0477_small.jpg"
}

var exampleCard = new CardFactory();
document.body.appendChild(exampleCard.newActivityCard(config1));
document.body.appendChild(exampleCard.newActivityCard(config2));
document.body.appendChild(exampleCard.newStaticCard(config3));*/
var cardFactory = new CardFactory();
var activities = [];

function getActivityContent() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (xhttp.readyState == 4 && xhttp.status == 200) {
      var json = JSON.parse(this.response);
      for (let obj in json) {
        activities.push(json[obj]);
      }
      paintActiviyCards();
    }
  };
  xhttp.open("GET", "/edit/content/activities.json", true);
  xhttp.send();
}

function paintActiviyCards() {
  //Because the cards will be painted chronologically
  activities.sort(function(a,b) {
    var dateA = new Date(a.startTime);
    var dateB = new Date(b.startTime);
    return dateA.getTime() - dateB.getTime();
  });

  for (let index in activities) {
    document.body.appendChild(cardFactory.newActivityCard(activities[index]));
  }
}

getActivityContent();
