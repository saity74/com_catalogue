jQuery(function($) {
    var options = {
        threshold: 0.7,
        searchClass: 'fuzzy-search',
        valueNames: [ 'checkbox-value' ],
        plugins: [ ListFuzzySearch() ]
    };

    $('.category-items').each(function(i, e) {
        new List(e.id, options);
    });

});