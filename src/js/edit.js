
/* Reaload page with filled content when changing select */
var select = document.getElementById('select-list');
var selectorInput = document.getElementById('selector-input');

select.addEventListener('change', function(event) {
    var checkedElement = event.target;
    var value = checkedElement.value;
    var id = checkedElement.getAttribute('id');

    // Reload page with selected parameter
    if (id == 'select-new') {
        window.location.href = window.location.href.split('?')[0]
    } else {
        window.location.href = window.location.href.split('?')[0] + '?select=' + encodeURIComponent(value);
    }
});

/* Preview image before uploading */
var imageUpload = document.getElementById('image-upload');
var imageError = document.getElementById('form-image-error');

imageUpload.addEventListener('change', function changeActivity(event) {
    var file = event.target.files[0];
    if (file != null) {
        var imageUploadShow = document.getElementById('image-upload-show');
        var image = URL.createObjectURL(file);
    
        // Change source of image or add new
        if (imageUploadShow) {
            imageUploadShow.src = image;
            imageUploadShow.addEventListener('load', showCropOverlay); // extend function showCropOverlay for changing image event
        } else {
            imageUpload.parentNode.insertAdjacentHTML('beforebegin', '<img id="image-upload-show" src="'+image+'">');
            imageUploadShow = document.getElementById('image-upload-show');
            imageUploadShow.addEventListener('load', showCropOverlay);
        }
    
        if (imageUpload.files && imageUpload.files[0]) {
    
            // Show error if image is larger than 5mb
            if (imageUpload.files[0].size > 5*1024*1024) {
    
                // Add error message
                var imageToBigError = function() {
                    imageError.style.display = 'block';
                    imageUpload.value = '';
                    imageUploadShow.style.opacity = '.3';
    
                    // only trigger once
                    imageUploadShow.removeEventListener('load', imageToBigError);
                };
                // wait till image is shown
                imageUploadShow.addEventListener('load', imageToBigError);
    
            } else {
    
                // Remove error message
                imageError.style.display = 'none';
                imageUploadShow.style.opacity = '1';
            }
        }
    }
});


var form = document.getElementById('form');
var editForm = document.getElementById('edit-form');
var response = document.getElementById('response');
var deleteButton = document.getElementById('form-delete');
var request = new XMLHttpRequest();
var sending = false;

/* When starting a submit */
request.onreadystatechange = function() {
    if (request.readyState == 1) {
        sending = true;
        startLoading();
    }
}

form.noValidate = true;
errorTime = 6000; // 6 seconds

form.addEventListener('submit', function(event) {

    /* Add cross-browser support for required */
    if (!event.target.checkValidity()) { // if not valid

        // Show error message
        var formError = document.getElementById('form-error');
        formError.style.display = 'inline';
        // Remove error message after a time
        window.setTimeout(function(){ formError.style.display = 'none'; }, errorTime);

        // Mark all invalid inputs labels
        for (i=0; i<event.target.length; i++) {
            var input = event.target[i];
            if (!input.validity.valid) {
                input.parentNode.classList.add('input-error');
                // Use closure to capture correct input
                window.setTimeout((function(inputParent) {
                    return function() {
                        inputParent.classList.remove('input-error');
                    };
                })(input.parentNode), errorTime);
            }
        }

    /* Ajax form submit */
    } else if (!sending) {

        var formData = new FormData(event.target);
        formData.append('save', true);

        // When done
        request.onload = function(e) {
            if (request.status == 200) { // success
                response.innerHTML = request.responseText;
                response.classList.remove('error');

                imageUpload.value = '';
                imageUpload.required = false;

                var listPositionChanged = false;
                var selected = select.querySelector('input:checked');
                var date = document.getElementById('form-date');
                var time = document.getElementById('form-time');
                var datetime;
                var datetimeAttributeString = '';
                if (date && time) {
                    datetime = date.value+' '+time.value;
                    datetimeAttributeString = 'data-datetime="'+datetime+'"';
                }
                // new select:
                if (selected.id == 'select-new') {
                    var newListItem = document.createElement('li');
                    var selector = document.getElementById('selector-input');
                    var deleteButton = document.createElement('button');
                    deleteButton.setAttribute('id', 'form-delete');
                    deleteButton.setAttribute('type', 'button');
                    deleteButton.setAttribute('name', 'delete');
                    deleteButton.innerHTML = 'Radera';
                    deleteButton.addEventListener('click', deleteButtonListener);
                    editForm.insertBefore(deleteButton, editForm.firstChild);

                    newListItem.innerHTML = '<label>'+selector.value+'<input type="radio" name="'+select.getElementsByTagName('input')[0].name+'" value="'+selector.value+'" '+datetimeAttributeString+' required checked></label>';
                    select.appendChild(newListItem);


                    if (window.history && window.history.replaceState) {
                        window.history.replaceState({}, document.title, location.pathname+'?select='+encodeURIComponent(selector.value));
                    }

                    listPositionChanged = true;

                // changed select entry:
                } else if (selectorInput.value != selected.value) {
                    selected.value = selectorInput.value;
                    selected.parentElement.innerHTML = selectorInput.value + selected.outerHTML;

                    if (window.history && window.history.replaceState) {
                        window.history.replaceState({}, document.title, location.pathname+'?select='+encodeURIComponent(selectorInput.value));
                    }
                }
                // changed date or time
                if (selected.id != 'select-new' && selected.getAttribute('data-datetime') != datetime) {
                    selected.setAttribute('data-datetime', datetime);
                    listPositionChanged = true;
                }

                /* Sort activity list according to date and time */
                if (listPositionChanged && editForm.classList.contains('form-activity')) {
                    var activitiesCollection = select.getElementsByTagName('input');
                    var activities = Array.prototype.slice.call(activitiesCollection);
                    select.innerHTML = '<li><label>'+activities[0].value+activities[0].outerHTML+'</label></li>'; // create new form label

                    activities.sort(function(a, b) {
                        var aDate = new Date(a.getAttribute('data-datetime'));
                        var bDate = new Date(b.getAttribute('data-datetime'));
                        if (aDate < bDate) {
                            return -1;
                        } else if (aDate > bDate) {
                            return 1;
                        } else {
                            return 0;
                        }
                    });

                    var lastActivityDate = null;
                    for (var i = 1; i < activities.length; i++) { // 1 becauce create new form label already been added
                        var activity = activities[i];
                        var activityDate = activity.getAttribute('data-datetime').slice(0, -5);
                        if (activityDate != lastActivityDate || lastActivityDate == null) {
                            select.innerHTML += '<li><h3 class="select-seperator">'+activityDate+'</h3><hr></li>';
                        }
                        lastActivityDate = activityDate;

                        select.innerHTML += '<li><label>'+activity.value+activity.outerHTML+'</label></li>';
                    }
                }

            } else { // error
                response.innerHTML = request.responseText;
                response.classList.add('error');
            }

            stopLoading();
        };
        request.open('POST', 'save.php', true);
        request.send(formData);
        response.innerHTML = '';

    }

    event.preventDefault();
}, false);

/* Ajax delete submit */
// Delete button has no actual submit attribute, as it should not be triggered with enter key
function deleteButtonListener() {
    // When done
    request.onload = function(e) {
        if (request.status == 200) {
            response.innerHTML = request.responseText;
            editForm.style.background = 'transparent';
            editForm.style.boxShadow = 'none';
            // reaload and remove paramaters:
            window.setTimeout(function() {location.href = location.protocol + '//' + location.host + location.pathname; }, 800);

        } else {
            response.innerHTML = request.responseText;
        }
        stopLoading();
    }
    var selected = select.querySelector('input:checked');
    request.open('POST', 'save.php', true);
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.send(event.target.name+'='+event.target.innerHTML+'&'+selected.name+'='+encodeURIComponent(selected.value));
}
if (deleteButton) {deleteButton.addEventListener('click', deleteButtonListener);}

/* Loading bar for server requests */
var loadingBar = document.createElement('DIV');
loadingBar.classList.add('loading-bar');
editForm.appendChild(loadingBar);

/**
 * Load progress bar width, not fully
 */
function startLoading() {
    loadingBar.style.width = '80%';
}
/**
 * Load progress bar width fully, and add appropiet class states
 * Set sending variable to false when done
 */
function stopLoading() {
    loadingBar.classList.add('done');
    loadingBar.style.width = '100%';

    window.setTimeout(function() {
        loadingBar.classList.remove('done');
        loadingBar.classList.add('retract');
        loadingBar.style.width = '';

        window.setTimeout(function() {
            loadingBar.classList.remove('retract');
            sending = false;
        }, 30);
    }, 1000);
}

/**
 * Delete all forms
 */
var deleteAllButton = document.getElementById('form-delete-all');
var confirmText = 'mottagning';
var confirmResponse = false;
var deleteConfirm = function(event) {

    var inputText = event.target.querySelector('input[name="confirm"]');
    if (inputText.value == confirmText) {

      request.onload = function(e) {
          if (request.status == 200) {
              response.innerHTML = request.responseText;
              editForm.style.background = 'transparent';
              editForm.style.boxShadow = 'none';
              // reaload and remove paramaters:
              window.setTimeout(function() {location.href = location.protocol + '//' + location.host + location.pathname; }, 800);

          }
      }
      request.open('POST', 'save.php', true);
      request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
      request.send('delete-all=true');

    } else if (confirmResponse == false) {
        var responseP = document.createElement('p');
        responseP.innerHTML = 'Skriv in ordet "'+confirmText+'"!';
        event.target.appendChild(responseP)
        confirmResponse = true;
    }

    event.preventDefault();
};

deleteAllButton.addEventListener('click', function() {
    var overlay = document.createElement('div');
    overlay.setAttribute('class', 'confirm-overlay');
    overlay.addEventListener('click', closeConfirm);

    var closeButton = document.createElement('img');
    closeButton.src = '/resources/img/icons/close.svg';
    closeButton.setAttribute('class', 'close');

    var formContentName = deleteAllButton.innerHTML.split(' ').pop();
    var popup = document.createElement('form');
    popup.setAttribute('class', 'confirm-delete-all');
    popup.innerHTML =
        '<h1>Bekräfta</h1>' +
        '<p>Detta kommer radera ALLA '+formContentName+'. <br>Skriv in ordet "'+confirmText+'" nedan för att bekräfta</p>' +
        '<input type="text" name="confirm">' +
        '<input type="submit" name="delete-all" value="Radera">';

    var closeConfirm = function(event) {
        if (event.target === overlay || event.target === closeButton) {
            overlay.parentNode.removeChild(overlay);
        }
        event.stopPropagation();
    };

    closeButton.addEventListener('click', closeConfirm);
    overlay.addEventListener('click', closeConfirm);
    popup.addEventListener('submit', deleteConfirm);

    popup.appendChild(closeButton);
    overlay.appendChild(popup);

    document.body.appendChild(overlay);
});


/* Make input readonly through code to not collide with require attribute  */
var readonly = document.getElementsByClassName('readonly');
var preventDefault = function(event) { event.preventDefault(); };
for (var i = readonly.length-1; i >= 0; i--) {
    readonly[i].addEventListener('mousedown', preventDefault);
    readonly[i].addEventListener('keydown', preventDefault);
}
