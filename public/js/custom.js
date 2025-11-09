$(document).on('click', 'a[data-ajax-popup="true"], button[data-ajax-popup="true"], div[data-ajax-popup="true"]', function () {

    var data = {};
    var title1 = $(this).data("title");

    var title2 = $(this).data("bs-original-title");
    var title3 = $(this).data("original-title");
    var title = (title1 != undefined) ? title1 : title2;
    var title=(title != undefined) ? title : title3;

    $('.modal-dialog').removeClass('modal-xl');
    var size = ($(this).data('size') == '') ? 'md' : $(this).data('size');

    var url = $(this).data('url');
    $("#commonModal .modal-title").html(title);
    $("#commonModal .modal-dialog").addClass('modal-' + size);
  
    $.ajax({
        url: url,
        data: data,
        success: function (data) {
            $('#commonModal .body').html(data);
                         $("#commonModal").css('display','flex');

            // daterange_set();
            
           
           bindFormSubmit();

        },
        error: function (data) {
            data = data.responseJSON;
            show_toastr('Error', data.error, 'error')
        }
    });

});

$(document).on('click', 'a[data-ajax-popup="trues"], button[data-ajax-popup="trues"], div[data-ajax-popup="trues"]', function () {

    var data = {};
    var title1 = $(this).data("title");

    var title2 = $(this).data("bs-original-title");
    var title3 = $(this).data("original-title");
    var title = (title1 != undefined) ? title1 : title2;
    var title=(title != undefined) ? title : title3;

    $('.modal-dialog').removeClass('modal-xl');
    var size = ($(this).data('size') == '') ? 'md' : $(this).data('size');

    var url = $(this).data('url');
    $("#commonModal .modal-title").html(title);
    $("#commonModal .modal-dialog").addClass('modal-' + size);

    $.ajax({
        url: url,
        data: data,
        success: function (data) {
            if(sessionStorage.getItem('listId')){
            $('#commonModal .body').html(data);

             $("#commonModal").css('display','flex');
            // daterange_set();
            
           bindFormSubmit();
           
        }

        },
        error: function (data) {
            data = data.responseJSON;
            show_toastr('Error', data.error, 'error')
        }
    });

});


function updateQueryParam(url, key, value) {
    var urlParts = url.split('?');
    if (urlParts.length >= 2) {
        var prefix = encodeURIComponent(key) + '=';
        var queryParams = urlParts[1].split('&');

        for (var i = 0; i < queryParams.length; i++) {
            if (queryParams[i].indexOf(prefix) === 0) {
                // If parameter already exists, update its value
                queryParams[i] = prefix + encodeURIComponent(value);
                return urlParts[0] + '?' + queryParams.join('&');
            }
        }
        // If parameter doesn't exist, append it
        return url + '&' + key + '=' + encodeURIComponent(value);
    } else {
        // If there are no query parameters, add the parameter
        return url + '?' + key + '=' + encodeURIComponent(value);
    }
}


function bindFormSubmit() {


function hideModel(){
    $("#commonModal").css('display','none');
}
        // Trigger form submission via AJAX
      
$('form').on('submit', function (event) {
    if ($(this).attr('method').toUpperCase() == 'POST') {
        event.preventDefault();

        var form = $(this);
        var formData = new FormData(this);
        var loaderTimeout;

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                // Delay showing the loader
                loaderTimeout = setTimeout(function () {
                    $('#preloader').css('display', 'flex').hide().fadeIn();
                }, 1000);

                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
            },
            success: function (response) {
                console.log(1)
                clearTimeout(loaderTimeout);
                $('#preloader').fadeOut();

                if (response.status === 200) {
                    succeesAlert(response); // Your custom success handler
                }
            },
            error: function (xhr) {
                clearTimeout(loaderTimeout);
                $('#preloader').fadeOut();

                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    console.log(errors)

                    $.each(errors, function (key, value) {
                        var input = $('[name="' + key + '"]');
                        input.addClass('is-invalid');
                        input.after('<div class="invalid-feedback">' + value[0] + '</div>');
                    });

                    $.each(errors, function (key, messages) {
                        const parts = key.split('.');
                        const field = parts[0];
                        const index = parts[1];

                        const inputs = $('[name="' + field + '[]"]');
                        const $input = inputs.eq(index);

                        if ($input.length > 0) {
                            $input.addClass('is-invalid');

                            if (!$input.next('.invalid-feedback').length) {
                                $input.after('<div class="invalid-feedback">' + messages[0] + '</div>');
                            }
                        }
                    });
                } else {
                    warningAlert(xhr.responseJSON);
                }
            }
        });
    }
});



 function succeesAlert(response){
  Swal.fire({
                title: 'Success!',
                text: response.message, 
                icon: 'success',
                 showCancelButton: false,
                customClass: {
          confirmButton: 'btn btn-primary waves-effect waves-light'
        },
        buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = response.redirect_url; // Redirect on OK click
                }
            });
 }

function warningAlert(response) {
    let errorText = response.message;

    if (response.errors && Array.isArray(response.errors)) {
        errorText += '<br><br><ul style="text-align: left;">';
        response.errors.forEach(function(error) {
            errorText += `<li>${error}</li>`;
        });
        errorText += '</ul>';
    }

    Swal.fire({
        title: 'Warning!',
        html: errorText, // Use `html` instead of `text` to support formatting
        icon: 'warning',
        showCancelButton: false,
        customClass: {
            confirmButton: 'btn btn-primary waves-effect waves-light'
        },
        buttonsStyling: false,
    });
}

    }


   