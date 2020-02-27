$(document).ready(function() { // Making sure the DOM is fully loaded
    $('.js-user-autocomplete').each(function() { // selecting all elements with a js-user-autocomplete class, and loop over them using ".each" function
        var autocompleteUrl = $(this).data('autocomplete-url'); // Setting the data URL which holds all the JSON data this autocompleter will use to a variable

        $(this).autocomplete({hint: false}, [  // "this" refers to the class we defined above. We are calling autocomplete on it and passing ({hint: false} because the docs said to
            {
                source: function (query, cb) { // This function gets called when we are typing in the text field the class is defined in and pass whatever we have entered in to the textbox so far as the query argument.
                                                // Our job is to determine which results match this query text and pass those back by calling the cb function
                    $.ajax({ // Using jQuery to make the AJAX call to the URL variable we set above
                        url: autocompleteUrl+'?query='+query
                    }).then(function(data) {
                        cb(data.users)
                    })
                },
                displayKey: 'email',
                debounce: 500
            }
        ])
    })
});