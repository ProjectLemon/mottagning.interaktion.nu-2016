var leaders = [];
var cardColors = {
  "General": "#1e5e2f",
  "Red": "#c62828",
  "Blue": "#3374ba",
  "Yellow": "#ee8600"
}

getContactInfo();

function getContactInfo() {
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (xhttp.readyState == 4 && xhttp.status == 200) {
      var json = JSON.parse(this.response);
      for (var obj in json) {
        leaders.push(json[obj]);
      }
      paintContactCards();
    }
  };
  xhttp.open("GET", "/edit/content/contacts.json", true);
  xhttp.send();
}

function paintContactCards() {
  for (var i = 0; i < leaders.length; i++) {
    var div = document.createElement('div');
    div.classList.add('contact-info');
    div.style.backgroundColor = cardColors[leaders[i].group];
    div.innerHTML = "<div class=\"profile-picture-wrapper\"><h1 class=\"name\">"+leaders[i].name+
    "</h1><img class=\"profile-pic\" src=\""+leaders[i].image+"\"><h2 class=\"general-or-leader\">"+leaders[i].group+
    "</h2><p><a href='tel:"+leaders[i].phone+"'><img class='fa-icon' src=\"/resources/img/icons/phone.svg\" alt=\"Ring\">"+leaders[i].phone+
    "</p></a><p><a href='mailto:"+leaders[i].mail+"'><img class='fa-icon' src=\"/resources/img/icons/mail.svg\" alt=\"Maila\">"+leaders[i].mail+"</a></p></div>";
    document.getElementById('contact-info-content').appendChild(div);
  }
}
