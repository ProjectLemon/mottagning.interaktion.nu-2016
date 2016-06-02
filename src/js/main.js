
/**
* File:     main.js
* Author:   Linus Lagerhjelm
* Last      Modified: 2016-05-17
* Purpose:  This file handles functionality that is re-occuring on every page
*           on the site. Such as highlighting active page in menu and keeping
*           the page title up do date with the current year.
*/

//Call "main" function
(function main() {
  initializeMenu();
  setPageTitle();
}());

//Functions
/* Adds the active class to the menu item the user is currently visisting */
function initializeMenu() {
  var subpages = ["contact", "info"];
  var address = window.location.href;
  for (var i = 0; i < subpages.length; i++) {
    if (address.search(subpages[i]) !== -1) {
      var active = document.getElementById(subpages[i]);
      var activeCollapsed = document.getElementById(subpages[i]+"-collapsed");
      activeCollapsed.classList.add("active");
      active.classList.add("active");
      return;
    }
  }
  var active = document.getElementById("schedule");
  var activeCollapsed = document.getElementById("schedule-collapsed");
  active.classList.add("active");
  activeCollapsed.classList.add("active");
}

/* Appends the current year to page title */
function setPageTitle() {
  document.title += " "+new Date().getFullYear();
}

/* Add last() function to all arrays without appearing in enumerations */
Object.defineProperty(Array.prototype, 'last', {
    enumerable: false,
    value: function last() {
      return this[this.length-1];
    }
});
