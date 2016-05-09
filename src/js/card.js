
var CardFactory = (function Card() {
  var _attributes = {
    title: "",
    startDateTime: "",
    description: "",
    place: "",
    lat: "",
    long: "",
    img: "",
    color: ""
  };

  return function cardConstructor() {
    var _this = this; //Cache the value for this

    //Returns a base card containing a html strucure and data provided by config
    _this.newCard = function(config) {
      config.startDateTime = new Date(config.startDateTime);
      var card = document.createElement('div');
      var headerImg = "<img src='"+config.image+"' alt=''>";
      var titleText = "<h1 class='titleText'>"+config.title+"</h1>";
      var startTime = "<h2 class='startTime'><img src='/resources/img/icons/clock.svg' class='fa-icon'>"+_this.formatTime(config.startDateTime)+"</h2>";
      var startDate = "<h2 class='date'><img src='/resources/img/icons/calendar.svg' class='fa-icon'>"+_this.formatDate(config.startDateTime)+"</h2>";
      var location = "<h3 class='location'><img src='/resources/img/icons/marker.svg' class='fa-icon'>"+config.place+"</h5>";
      var description = "<div class='description animate'>"+config.description+"</div>";
      var directions = "<a onclick='cardFactory.openDirections(event)'><img src='/resources/img/icons/map-directions.svg' class='directions'></a>"

      card.style.backgroundColor = config.color;
      card.innerHTML += headerImg + titleText + startTime + startDate + location + description + directions;

      return card;
    }

    //New activity card returns a new activity card containing
    //data that has been provided through the config object
    _this.newActivityCard = function(config) {
      _this._attributes = config;
      var card = _this.newCard(config);

      card.classList.add('mo-card', 'mo-card-activity', 'no-select');
      card.addEventListener("click", function(e){
          this.classList.toggle('mo-card-expanded');
      }, false);

      return card;
    };

    //Creates a headline card filled with data from config
    _this.newStaticCard = function(config) {
      _this._attributes = config;
      var card = _this.newCard(config);
      var wrapper = document.createElement('div');
      var headline = document.createElement('h2');
      card.id = "spotlight-card";
      headline.id = "spotlight-card-headline"
      headline.innerHTML = "Next activity:";
      card.classList.add('mo-card', 'mo-card-spotlight');;
      wrapper.appendChild(headline);
      wrapper.appendChild(card);

      return wrapper;
    }

    //This is called whenever the directions icon is pressed. It redirects the
    //user to google maps with directions from their localtion to the activity
    _this.openDirections = function(e) {
      e.stopPropagation();
      showOverlay("Awaiting google maps");
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function successCallback(pos) {
          var lat = pos.coords.latitude;
          var long = pos.coords.longitude;
          window.location.href = "https://www.google.com/maps/dir/'"+lat+","+long+"'/'"+_this._attributes.lat+","+_this._attributes.long+"'";
        }, function failureCallback(error) {
          window.location.href = "https://www.google.com/maps/place/"+_this._attributes.lat+","+_this._attributes.long+"/@"+_this._attributes.lat+","+_this._attributes.long+",15z"
        }, {timeout:3000});
      } else {
        window.location.href = "https://www.google.com/maps/place/"+_this._attributes.lat+","+_this._attributes.long+"/@"+_this._attributes.lat+","+_this._attributes.long+",15z"
      }

    }

    //Helper methods
    _this.formatTime = function(date) {
      var time = ""+date.getHours()+":";
      if (date.getMinutes() < 10) {
        return time+"0"+date.getMinutes();
      }
      return time+date.getMinutes();
    }
    _this.formatDate = function(date) {
      return ""+date.getDate()+"/"+(date.getMonth() + 1);
    }
  }
}());
