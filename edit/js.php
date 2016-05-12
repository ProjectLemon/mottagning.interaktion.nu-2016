<script>
/* Reaload page with filled content when changing activity */
var select = document.getElementById('edit-select');
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
imageUpload.addEventListener('change', function changeActivity(event) {
    var imageUploadShow = document.getElementById('image-upload-show');
    var image = URL.createObjectURL(event.target.files[0])
    if (imageUploadShow) {
        imageUploadShow.src = image;
    } else {
        imageUpload.parentNode.insertAdjacentHTML('beforebegin', '<img id="image-upload-show" src="'+image+'">Ersätt ');
    }
});

/* Add cross-browser support for required */
var form = document.getElementById('edit-form');
form.noValidate = true;
errorTime = 4000 // 4 seconds
form.addEventListener('submit', function(event) {
    if (!event.target.checkValidity()) {
        event.preventDefault();
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
    }
}, false);

/* Make input readonly through code to not collide with require attribute  */
var readonly = document.getElementsByClassName('readonly');
var preventDefault = function(event) { event.preventDefault(); };
for (var i = readonly.length-1; i >= 0; i--) {
    readonly[i].addEventListener('mousedown', preventDefault);
    readonly[i].addEventListener('keydown', preventDefault);
}
</script>