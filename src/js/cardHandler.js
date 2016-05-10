//Global variables
var colors = ["#7A1EA1", "#EE6B00", "#1E5E2F", "#A85BA4", "#3374BA", "#455A64", "#C62828"];
var cardFactory = new CardFactory();
var activities = [];

//getActivityContent performs a query for the activities file in the server
//and upon success, it will call the neccessary paint-functions
function getActivityContent() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (xhttp.readyState == 4 && xhttp.status == 200) {
      var json = JSON.parse(this.response);
      for (var obj in json) {
        if (new Date(json[obj].startDateTime).getTime() >= new Date().getTime()) {
            activities.push(json[obj]);
        }
      }
      //Because the cards will be painted chronologically
      activities.sort(function(a,b) {
        var dateA = new Date(a.startDateTime);
        var dateB = new Date(b.startDateTime);
        return dateA.getTime() - dateB.getTime();
      });

      paintHighLightCard();
      paintActiviyCards();
    }
  };
  xhttp.open("GET", "/edit/content/activities.json", true);
  xhttp.send();
}

//Gets the first entry of the activities array and paint's it as a
//highlighted card. It is also responsible for initalizing the timer
function paintHighLightCard() {
  var currentHighLight = document.getElementById('spotlight-card');
  if (currentHighLight) {
    currentHighLight.parentNode.removeChild(currentHighLight);
  }
  activities[0].color = getRandomColor();
  var container = document.getElementById("next-activity");
  var card = cardFactory.newStaticCard(activities[0]); //The cards are sorted and therefore, the first activity will be "next" activity
  container.appendChild(card);
  CountDownTimer(activities[0].startDateTime, "clock");
}

//will take all activity data and paint them as activity cards
function paintActiviyCards() {
  for (var index = 1; index < activities.length; index++) {
    var container = document.getElementById(activities[index].startDateTime.split(" ").join(""));
    if (!container) {
      container = document.createElement('div');
      container.classList.add('mo-card-date-container');
      container.id = activities[index].startDateTime.split(" ").join("");
      document.body.appendChild(container);
    }
    activities[index].color = getRandomColor();
    container.appendChild(cardFactory.newActivityCard(activities[index]));
  }
}

//Creates and implements the timer. It will also re-initialize itself whenever
//the countdown reaches its goal.
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
      activities.shift();
      location.reload();
      return;
    }

    var days = Math.floor(distance / _day);
    var hours = Math.floor((distance % _day) / _hour);
    var minutes = Math.floor((distance % _hour) / _minute);
    var seconds = Math.floor((distance % _minute) / _second);
    document.querySelector("#clock .clock-days div").innerHTML = days;
    document.querySelector("#clock .clock-hours div").innerHTML = hours;
    document.querySelector("#clock .clock-minutes div").innerHTML = minutes;
    document.querySelector("#clock .clock-seconds div").innerHTML = seconds;
  }
  timer = setInterval(showRemaining, 1000);
}


//Helpers
function getRandomColor() {
  var min = 0;
  var max = colors.length;
  var index = Math.round(Math.random() * (max - min) + min);
  return colors[index];
}
