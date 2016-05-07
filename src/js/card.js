
var ExpandableCardFactory = (function Card() {
  var _attributes = {
    title: "",
    startTime: "",
    date: "",
    description: "",
    place: "",
    lat: "",
    long: "",
    img: ""
  };

  return function cardConstructor() {
    var _this = this; //Cache the value for this

    //New activity card returns a new activity card containing
    //data that has been provided through the config object
    _this.newActivityCard = function(config) {
      _this._attributes = config;
      var card = document.createElement('div');
      var headerImg = "<img src='"+config.img+"' alt=''>";
      var titleText = "<h1 class='titleText'>"+config.title+"</h1>";
      var startTime = "<h2 class='startTime'><img src='/resources/img/icons/clock.svg' class='fa-icon'>"+config.startTime+"</h2>";
      var startDate = "<h2 class='date'><img src='/resources/img/icons/calendar.svg' class='fa-icon'>"+config.date+"</h2>";
      var location = "<h5 class='location'><img src='/resources/img/icons/marker.svg' class='fa-icon'>"+config.place+"</h5>";
      var description = "<div class='description animate'>"+config.description+"</div>";
      var directions = "<a onclick='exampleCard.openDirections(event)'><img src='/resources/img/icons/directions.svg' class='directions'></a>"

      card.classList.add('mo-card', 'mo-card-activity');
      card.innerHTML += headerImg + titleText + startTime + startDate + location + description + directions;
      card.addEventListener("click", function(e){
          this.classList.toggle('mo-card-expanded');
      }, false);

      return card;
    };

    _this.openDirections = function(e) {
      e.stopPropagation();
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
          var lat = pos.coords.latitude;
          var long = pos.coords.longitude;
          window.location.href = "https://www.google.com/maps/dir/'"+lat+","+long+"'/'"+_this._attributes.lat+","+_this._attributes.long+"'";
        });
      } else {
        window.location.href = "https://www.google.com/maps/preview/@-15.623037,18.388672,8z"
      }
    }
  }
}());

//https://www.google.com/maps/dir/'<latitude>,<longitude>'/'52.49083837044266,13.369826049804715'\
