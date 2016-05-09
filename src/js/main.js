
//Call "main" function
initializeMenu();

//Functions
function initializeMenu() {
  var subpages = ["contact", "info"];
  var address = window.location.href;
  for (var i = 0; i < subpages.length; i++) {
    if (address.search(subpages[i]) !== -1) {
      var active = document.getElementById(subpages[i]);
      active.classList.add("active");
      return;
    }
  }
  var active = document.getElementById("schedule");
  active.classList.add("active");
}

function showOverlay(message) {
  var overlayBackground = document.createElement('div');
  var messageContainer = document.createElement('div');
  overlayBackground.style.cssText = "height:100%; width: 100%; position:fixed; top:0;right:0; z-index:100000000000000;background-color: rgba(0,0,0,.4);";
  messageContainer.style.cssText = "position: absolute; margin:auto; top:50%; left:50%; transform: translate(-50%, -50%); width:300px; padding: 20px; background-color: #EDE4DA; text-align: center; color: black;";
  messageContainer.innerHTML = message;
  overlayBackground.id = "Site overlay";
  overlayBackground.appendChild(messageContainer);
  document.body.appendChild(overlayBackground);
}
