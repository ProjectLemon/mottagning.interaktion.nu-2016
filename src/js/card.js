
/**
* File:     card.js
* Author:   Linus Lagerhjelm
* Last      Modified: 2016-05-17
* Purpose:  This file contains a constructor object for a card factory which
*           can be used to generate activity cards. No method in this file will
*           print anything to the screen. It will only generate dom objects
*           and return them for usage elsewhere.
*
* Config:   Most public functions in this library requires a configuration
*           object to work. This object has to include the following fields:
*
*           startDateTime: A string represenatation of javascript's Date object
*                          representing when the activity will start.
*           image:         A search path to the featured image
*           title:         This will be the card's headline
*           description:   A description of the activity
*           lat:           Latitude coordinates for the activitys location
*           long:          Longitude coordinates for the activitys location
*           color:         A CSS color value. This will be the card's color
*/
var CardFactory = (function Card() {

  /*
  * This object is returned when new Card() is called.
  * @constructor
  */
  return function cardConstructor() {
    var _this = this; //Cache the value for this

    /*
    * This method will create the html structure of the card using the info-card
    * provided via the config object.
    * @param {object} config - The activity information
    * @return {HTML-node} - The card as a HTML-node
    */
    _this.newCard = function(config) {
      config.startDateTime = new Date(config.startDateTime);
      var card = document.createElement('div');
      var headerImg

      //Since images won't be displayed on mobile screens we're preventing them
      //from being downloaded in the first place to improve performance
      if(document.body.clientWidth <= 740) {
        headerImg = "<img class='featured-image' src='' alt=''>";
      } else {
        headerImg = "<img class='featured-image' src='"+config.image+"' alt=''>";
      }

      var titleText = "<div><h1 class='titleText'>"+config.title+"</h1></div>";
      var startTime = "<h2 class='startTime'><img src='/resources/img/icons/clock.svg' class='fa-icon'>"+_this.formatTime(config.startDateTime)+"</h2>";
      var startDate = "<h2 class='date'><img src='/resources/img/icons/calendar.svg' class='fa-icon'>"+_this.formatDate(config.startDateTime)+"</h2>";
      var location = "<h3 class='location'><img src='/resources/img/icons/marker.svg' class='fa-icon'>"+config.place+"</h3>";
      var description = "<div class='description animate'>"+config.description+"</div>";
      var directions = "<a onclick='cardFactory.openDirections(event, this)'><img src='/resources/img/icons/map-directions.svg' class='directions'></a>";

      //Save value for lat & long in order to use when onClick() is called
      card.setAttribute('data-lat', config.lat);
      card.setAttribute('data-long', config.long);

      card.style.backgroundColor = config.color;
      card.innerHTML += headerImg + titleText + startTime + startDate + location + description + directions;

      return card;
    }

    /*
    * This function is a wrapper function arround the newCard function. It will
    * attach neccessary classes (in order to get the correct style from css) and
    * It will attach click listeners to axpand and collapse card.
    * @see(@link{ _this.newCard})
    * @param {object} config - the activity information
    * @return {HTML-node} - Activity card represented as a HTML-node
    */
    _this.newActivityCard = function(config) {
      var card = _this.newCard(config);
      card.innerHTML += "<div class='indicator-arrow-container'><img src='/resources/img/icons/angle-down.svg' class='indicator-arrow' alt='Image of an arrow pointing down in order to indicate that cards are clickable'></div>";

      card.classList.add('mo-card', 'mo-card-activity', 'no-select');
      card.addEventListener("click", function(e){
        this.classList.toggle('mo-card-expanded');
      }, false);

      return card;
    };

    /*
    * This function is a wrapper function arround the newCard function. It will
    * attach neccessary classes (in order to get the correct style from css)
    * @see(@link{ _this.newCard})
    * @param {object} config - the activity information
    * @return {HTML-node} - Highlight card represented as a HTML-node
    */
    _this.newStaticCard = function(config) {
      var card = _this.newCard(config);
      var wrapper = document.createElement('div');
      var headline = document.createElement('h2');
      card.id = "spotlight-card";
      wrapper.id = "next-activity-card-wrapper"
      headline.id = "spotlight-card-headline"
      headline.innerHTML = "Next activity:";
      card.classList.add('mo-card', 'mo-card-spotlight');
      wrapper.appendChild(headline);
      wrapper.appendChild(card);

      return wrapper;
    };

    /*
    * This is called whenever the directions icon is pressed. It redirects the
    * user to google maps with directions from their localtion to the activity
    * @param {event} e - the click event
    * @param {HTML-node} node - the element that was clicked
    */
    _this.openDirections = function(e, node) {
      e.stopPropagation(); //Prevent the activity card to collapse
      window.open("https://www.google.com/maps/dir/Current+Location/'"+node.parentElement.getAttribute('data-lat')+","+node.parentElement.getAttribute('data-long')+"'",'_blank');
    }

    //Helper methods

    /* Returns a string representing the time on format: 00:00 */
    _this.formatTime = function(date) {
      var time = ""+date.getHours()+":";
      if (date.getMinutes() < 10) {
        return time+"0"+date.getMinutes();
      }
      return time+date.getMinutes();
    }

    /* Returns a string representing the date on format 0/0 */
    _this.formatDate = function(date) {
      return ""+date.getDate()+"/"+(date.getMonth() + 1);
    }
  }
}());
