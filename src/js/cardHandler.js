/**
* File:           cardHandler.js
* Author:         Linus Lagerhjelm
* Last Modified:  2016-05-17
*
* Purpose:        This file is responsible for everything related to displaying
*                 activity cards on the front page except generating the actual
*                 HTML-code which is done in card.js. This includes reading
*                 activity data from the server, setting up and executing the
*                 countdown timer, calculate date offset and painting the cards
*                 to the screen.
*
* Activity card:  An activity card is a HTML structure containing information
*                 about an event. This information include: Name of the event,
*                 start time and start date, fatured image of the event and
*                 location data. Both as lat-long coordinates and in clear text.
*                 In the original design, these objects looked like Material
*                 cards, hence the name.
*/


//Global variables

//Background colors of the cards
var colors = ["#7A1EA1", "#EE6B00", "#1E5E2F", "#A85BA4", "#3374BA", "#455A64", "#C62828"];

//Object that creates our html-code
var cardFactory = new CardFactory();

//This list will be filled with activity data when read from server
var activities = [];

getActivityContent(); //Start everything

/*
* getActivityContent performs a query for the activities file on the server
* and upon success, it will call the neccessary paint-functions
*/
function getActivityContent() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (xhttp.readyState == 4 && xhttp.status == 200) {
      var json = JSON.parse(this.response);
      for (var obj in json) {
        var currentTime = new Date().getTime();

        //====================
        /* This block is needed because from the server, date and time are two
        *  different fields while our card library assumes a correctly formated
        *  JavaScript Date-string
        */
        var activityDate = new Date(json[obj].startDate);
        var startTime = json[obj].startTime.split(':'); // assumes proper format of time
        var hours = parseInt(startTime[0]);
        var minutes = parseInt(startTime[1]);
        activityDate.setHours(hours);
        activityDate.setMinutes(minutes);

        //combine date and time to one attribute
        delete json[obj].startDate;
        delete json[obj].startTime;
        json[obj].startDateTime = activityDate.toString();
        //====================

        //Do not paint activites that has already happened
        if (activityDate.getTime() >= currentTime) {
            activities.push(json[obj]);
        }
      }
      //Because the cards will be painted chronologically
      activities.sort(function(a,b) {
        var dateA = new Date(a.startDateTime);
        var dateB = new Date(b.startDateTime);
        return dateA.getTime() - dateB.getTime();
      });

      //since we're not using a highlight card on screens < 800px
      if (document.body.clientWidth >= 800) {
        paintHighLightCard();
      }

      //In order to escape out of callback hell, we're calling the following
      //functions here to simulate a sequential execution in order to preventing
      //the code from crashing if this function call takes longer than expected
      paintActiviyCards();
      addOffsetFields();
    }
  };
  xhttp.open("GET", "/edit/content/activities.json", true);
  xhttp.send();
}

/*
* Gets the first entry of the activities array and paint's it as a
* highlighted card. It is also responsible for initalizing the timer
*/
function paintHighLightCard() {
  activities[0].color = getRandomColor();
  var container = document.getElementById("highlighted-card");
  var card = cardFactory.newStaticCard(activities[0]); //The cards are sorted and therefore, the first activity will be "next" activity
  container.appendChild(card);
  CountDownTimer(activities[0].startDateTime, "clock");
  activities.shift();
}

/*
* This function read every entry from the global list of activities and
* generates a new card for every entry. It will also generate conainer element
* using the day as its id in order to show the cards in rows based on day.
* This function will paint elements to screen
*/
function paintActiviyCards() {
  for (var index = 0; index < activities.length; index++) {
    var startDateTime = new Date(activities[index].startDateTime);
    var container = document.getElementById(startDateTime.toDateString().replace(/ /g,"")); //.replace() will remove all blanks in string
    if (!container) {
      container = document.createElement('div');
      container.setAttribute("data", startDateTime);
      container.classList.add('mo-card-date-container');
      container.id = startDateTime.toDateString().replace(/ /g,"");
      var cardContainer = document.getElementById('activity-cards');
      cardContainer.appendChild(container);
    }
    activities[index].color = getRandomColor();
    container.appendChild(cardFactory.newActivityCard(activities[index]));
  }
}

/*
* Creates and implements the timer. When the timer reaches 0 it will cause a
* page reload in order to re-initialize the timer.
*/
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

  //Update timer every second
  timer = setInterval(showRemaining, 1000);
}

/*
* This code will append the offset in days to the activity cards container
* This function will also handle the styling of the label.
*/
function addOffsetFields() {
  var rows = document.getElementsByClassName('mo-card-date-container');
  for (var i = 0; i < rows.length; i++) {
    var date = new Date(rows[i].getAttribute("data"));
    var div = document.createElement('div');
    var span = document.createElement('span');


    div.classList.add('offset-day');
    div.style.width = rows[i].clientWidth+"px";
    div.style.borderBottom = "1px solid white";
    div.style.maxWidth = "100%";

    span.innerHTML = calculateDateOffset(date);

    div.appendChild(span);
    rows[i].insertBefore(div, rows[i].childNodes[0]);
    div.style.paddingTop = Math.round((div.parentElement.clientHeight - div.clientHeight)/2);
  }
}

//Helpers
/* Gets a random color from the globally specified color array */
function getRandomColor() {
  var min = 0;
  var max = colors.length;
  var index = Math.round(Math.random() * (max - min) + min);
  return colors[index];
}

/*
* This will compute the amount of days from now to the specified date and then
* return a string with more comprehensive format than just date.
*/
function calculateDateOffset(toDate) {
  var now = new Date();
  var offsett = Math.abs(toDate.getDate()-now.getDate());
  if (offsett === 0) {
    return "Senare idag"
  }
  offsett = Math.floor(offsett);
  if (offsett === 1) {
    return "Imorgon";
  } else if (offsett < 7) {
    var days = ["Söndag", "Måndag", "Tisdag", "Onsdag", "Torsdag", "Fredag", "Lördag"];
    return days[toDate.getDay()];
  }
  return ""+toDate.getDate()+"/"+(toDate.getMonth() + 1);
}
