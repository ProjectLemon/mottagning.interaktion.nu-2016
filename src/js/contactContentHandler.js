/**
* File:     contactContentHandler.js
* Author:   Linus Lagerhjelm
* Last      Modified: 2016-05-17
* Purpose:  The script in this file requests the contact information for the
*           group leaders from the server and then it generates the info
*           elements that it paints to the screen
*/

var leaders = [];
var cardColors = {
  "general": "#1e5e2f",
  "röd": "#c62828",
  "blå": "#3374ba",
  "gul": "#ee8600",
  "red": "#c62828",
  "blue": "#3374ba",
  "yellow": "#ee8600"
}

getContactInfo();

/* Reads group leader info from the server */
function getContactInfo() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (xhttp.readyState == 4 && xhttp.status == 200) {
      var json = JSON.parse(this.response);
      for (var obj in json) {
        leaders.push(json[obj]);
      }

      //We're applying the same trick here as described in cardHandler.js
      paintContactCards();
    }
  };
  xhttp.open("GET", "/content/contacts.json", true);
  xhttp.send();
}

/* Generates HTML-nodes and paints them to the document */
function paintContactCards() {
  for (var i = 0; i < leaders.length; i++) {
    leaders[i].group.toLowerCase();
    var div = document.createElement('div');
    div.classList.add('contact-info');
    div.style.backgroundColor = cardColors[leaders[i].group];
    div.innerHTML = "<div class=\"profile-picture-wrapper\"><h1 class=\"name\">"+leaders[i].name+
    "</h1><img class=\"profile-pic\" src=\""+leaders[i].image+"\"><h2 class=\"general-or-leader\">"+handleRoleParsing(leaders[i].group)+ //
    "</h2><p><a href='tel:"+leaders[i].phone+"'><img class='fa-icon' style='max-height: 15px;' src=\"/resources/img/icons/phone.svg\" alt=\"Ring\">"+leaders[i].phone+
    "</p></a><p><a href='mailto:"+leaders[i].mail+"'><img class='fa-icon' style='max-height: 15px;' src=\"/resources/img/icons/mail.svg\" alt=\"Maila\">"+leaders[i].mail+"</a></p></div>";
    document.getElementById('contact-info-content').appendChild(div);
  }
}

/*
* We're using the role field that we get from the server but
* since that field will be filled in by the user we want to make sure
* it has the format we want since we're injecting it straight to the page
*/
function handleRoleParsing(role) {
  var returnString = document.createTextNode(role);
  returnString = role.charAt(0).toUpperCase();
  returnString += role.substring(1);
  if (returnString === "General") {
    return returnString;
  }
  var translation = {
    "red":"röd",
    "röd":"red",
    "blue":"blå",
    "blå":"blue",
    "yellow":"gul",
    "gul":"yellow"
  }
  return translations[returnString]+" gruppledare";
}
