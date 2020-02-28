$(document).ready(function() {
    // Selecting the two elements
    var $locationSelect = $('.js-article-form-location'); // This is the actual select element
    var $specificLocationTarget = $('.js-specific-location-target'); // This represents the div that is around that field in _form.html.twig

    $locationSelect.on('change', function(e) { // When the location select changes
        $.ajax({
            url: $locationSelect.data('specific-location-url'), // We make the ajax call by reading the data specific data location url attribute
            data: {
                location: $locationSelect.val() // The location key in the data option will cause that to be set as a query parameter
            },
            success: function (html) {
                if (!html) { // if the response is empty that means that we have selected an option that should not have a specific location name drop down
                    $specificLocationTarget.find('select').remove(); // So we look inside the specific location target for the select, and remove it to make sure it doesn't submit with the form
                    $specificLocationTarget.addClass('d-none'); // On the wrapper div in _form.html.twig, we also need to add a bootstrap class called 'd-none' which stands for display none which will hide the entire element, including the label
                    return;
                }
                // Replace the current field and show
                $specificLocationTarget // if there is some html returned, we do the opposite of the code above.
                    .html(html) // We replace the entire html of the target with the new html
                    .removeClass('d-none') // and remove the display none class so that it is not hidden on the page
            }
        });
    });
});