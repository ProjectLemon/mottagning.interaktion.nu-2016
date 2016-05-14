<script>
/* Reaload page with filled content when changing select */
var select = document.getElementById('edit-select');
var selectorInput = document.getElementById('selector-input');

select.addEventListener('change', function() {
    var value = select.options[select.selectedIndex].value;
    var id = select.options[select.selectedIndex].getAttribute('id');
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
    var imageUploadShow = document.getElementById('image-upload-show');
    var image = URL.createObjectURL(event.target.files[0])
    if (imageUploadShow) {
        imageUploadShow.src = image;
    } else {
        imageUpload.parentNode.insertAdjacentHTML('beforebegin', '<img id="image-upload-show" src="'+image+'">');
        var imageUploadShow = document.getElementById('image-upload-show');
    }
    
    if (imageUpload.files && imageUpload.files[0]) {
        if (imageUpload.files[0].size > 5*1024*1024) { // 5mb
            imageError.style.display = 'block';
            imageUpload.value = '';
            imageUploadShow.style.opacity = '.3';
        } else {
            imageError.style.display = 'none';
            imageUploadShow.style.opacity = '1';
        }
    }
});


var form = document.getElementById('edit-form');
var response = document.getElementById('response');
var deleteButton = document.getElementById('form-delete');
var request = new XMLHttpRequest();
var sending = false;

form.noValidate = true;
errorTime = 4000 // 4 seconds
form.addEventListener('submit', function(event) {
    
    /* Add cross-browser support for required */
    if (!event.target.checkValidity()) {
        var formError = document.getElementById('form-error');
        formError.style.display = 'inline';
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
        request.onreadystatechange = function() {
            if (request.readyState == 1) {
                sending = true;
                startLoading();
            }
        }
        request.onload = function(e) {
            if (request.status == 200) { // success
                response.innerHTML = request.responseText;
                response.classList.remove('error');
                
                // new select:
                if (select.selectedIndex == 0) {
                    var option = document.createElement('option');
                    var selector = document.getElementById('selector-input');
                    option.text = selector.value;
                    select.add(option);
                    option.selected = true;
                    
                    deleteButton.disabled = false;
                    
                    if (window.history && window.history.replaceState) {
                        window.history.replaceState({}, document.title, location.pathname+'?select='+selector.value);
                    }
                    
                // changed select entry:
                } else if (selectorInput.value != select.value) {
                    var option = select.options[select.selectedIndex];
                    option.value = selectorInput.value;
                    option.text = selectorInput.value;
                    
                    if (window.history && window.history.replaceState) {
                        window.history.replaceState({}, document.title, location.pathname+'?select='+selectorInput.value);
                    }
                }
                
            } else { // error
                response.innerHTML = request.responseText;
                response.classList.add('error');
            }
            
            stopLoading();
        };
        request.open('POST', 'save.php');
        request.send(formData);
        response.innerHTML = '';
        
    }
    
    event.preventDefault();
}, false);

/* Ajax delete submit */
// Delete button has no actual submit attribute, as it should not be triggered with enter key
deleteButton.addEventListener('click', function() {
    request.onload = function(e) {
        if (request.status == 200) {
            response.innerHTML = request.responseText;
            // reaload and remove paramaters:
            window.setTimeout(function() { location.href = location.protocol + '//' + location.host + location.pathname; }, 1200);
            
        } else {
            response.innerHTML = request.responseText;
        }
        stopLoading();
    }
    request.open('POST', 'save.php');
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.send(deleteButton.name+'='+deleteButton.innerHTML+'&'+select.name+'='+encodeURIComponent(select.value));
});

var loadingBar = document.createElement('DIV');
loadingBar.classList.add('loading-bar');
form.appendChild(loadingBar);
function startLoading() {
    loadingBar.style.width = '80%';
}
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
        }, 20);
    }, 1000);
}

/* Make input readonly through code to not collide with require attribute  */
var readonly = document.getElementsByClassName('readonly');
var preventDefault = function(event) { event.preventDefault(); };
for (var i = readonly.length-1; i >= 0; i--) {
    readonly[i].addEventListener('mousedown', preventDefault);
    readonly[i].addEventListener('keydown', preventDefault);
}




</script>