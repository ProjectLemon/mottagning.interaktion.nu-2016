
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
    var _this = this;

    _this.newActivityCard = function(config) {
      _this._attributes = config;
      var card = document.createElement('div');
      var headerImg = "<img src='"+config.img+"' alt=''>"
      var titleText = "<h1 class='titleText'>"+config.title+"</h1>"
      var startTime = "<h2 class='startTime'>"+config.startTime+"</h2>"
      var startDate = "<h2 class='startTime'>"+config.date+"</h2>"
      card.classList.add('mo-card', 'mo-card-activity');
      card.innerHTML += headerImg + titleText + startTime + startDate;


      card.addEventListener("click", function(){
        this.classList.toggle('mo-card-expanded');
      });
      return card;
    }
  }
}());
