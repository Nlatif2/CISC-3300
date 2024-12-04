$(document).ready(function() {
    $('#load-more').click(function() {
        let offset = $('.card').length;
        $.ajax({
            url: 'api/load_more_events.php',
            method: 'GET',
            data: { offset: offset },
            success: function(response) {
                $('#event-list').append(response);
            }
        });
        
    });
});