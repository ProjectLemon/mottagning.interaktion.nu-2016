/**
* File:     fonts.js
* Author:   Linus Lagerhjelm
* Last      Modified: 2016-05-17
* Purpose:  This file is used to download external fonts to the site. This is
*           done through JavaScript on order to be able to load it after all
*           the page's content has loaded. And also to allow thos resource to
*           be downloaded using it's own, separate thread.
*/

WebFontConfig = {
  google: { families: [ 'Quicksand:400,300:latin' ] }
};
(function() {
  var wf = document.createElement('script');
  wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
  wf.type = 'text/javascript';
  wf.async = 'true';
  var s = document.getElementsByTagName('script')[0];
  s.parentNode.insertBefore(wf, s);
})();
