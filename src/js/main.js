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
let colors = ["#7A1EA1", "#EE6B00", "#1E5E2F", "#A85BA4", "#3374BA", "#455A64"];
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
      //Because the cards will be painted chronologically
      activities.sort(function(a,b) {
        var dateA = new Date(a.startTime);
        var dateB = new Date(b.startTime);
        return dateA.getTime() - dateB.getTime();
      });

      paintHighLightCard();
      paintActiviyCards();
    }
  };
  xhttp.open("GET", "/edit/content/activities.json", true);
  xhttp.send();
}

function paintHighLightCard() {
  activities[0].color = getRandomColor();
  var container = document.getElementById("next-activity");
  var card = cardFactory.newStaticCard(activities[0]); //The cards are sorted and therefore, the first activity will be "next" activity
  container.appendChild(card);
  CountDownTimer(activities[0].startTime, "clock");
}

function paintActiviyCards() {
  for (let index in activities) {
    activities[index].color = getRandomColor();
    document.body.appendChild(cardFactory.newActivityCard(activities[index]));
  }
}

function CountDownTimer(dt, id) {
  var end = dt;

  var _second = 1000;
  var _minute = _second * 60;
  var _hour = _minute * 60;
  var _day = _hour * 24;
  var timer;

  function showRemaining() {
    var now = new Date();
    var distance = end - now;

    if (distance < 0) {
      clearInterval(timer);
      document.getElementById(id).innerHTML = 'EXPIRED!';
      return;
    }

    var days = Math.floor(distance / _day);
    var hours = Math.floor((distance % _day) / _hour);
    var minutes = Math.floor((distance % _hour) / _minute);
    var seconds = Math.floor((distance % _minute) / _second);
    document.getElementById(id).innerHTML = days + 'days ';
    document.getElementById(id).innerHTML += hours + 'hrs ';
    document.getElementById(id).innerHTML += minutes + 'mins ';
    document.getElementById(id).innerHTML += seconds + 'secs';
  }
  timer = setInterval(showRemaining, 1000);
}

function getRandomColor() {
  var min = 0;
  var max = colors.length;
  var index = Math.round(Math.random() * (max - min) + min);
  return colors[index];
}

getActivityContent();
