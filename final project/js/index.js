$(document).ready(function() {
    $('#event-carousel').carousel({
        interval: 10000 
    });

    $('.category-btn').click(function() {
        $(this).toggleClass('active');
        filterEvents();
    });

    function filterEvents() {
        var activeCategories = $('.category-btn.active').map(function() {
            return $(this).data('category');
        }).get();

        console.log('Active categories:', activeCategories);

        $('.event-tile').each(function() {
            var eventCategory = $(this).data('category');
            console.log('Event category:', eventCategory);

            if (activeCategories.length === 0 || activeCategories.includes(eventCategory)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }
    
    $('.event-tile').each(function() {
        console.log('Initial category check:', $(this).data('category'));
    });
});