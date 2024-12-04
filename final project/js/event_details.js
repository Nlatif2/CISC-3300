$(document).ready(function() {
    $('#rsvp-btn').click(function() {
        $.ajax({
            url: 'api/rsvp.php',
            method: 'POST',
            data: { event_id: <?php echo $event_id; ?> },
            success: function(response) {
                alert('RSVP successful!');
                $('#rsvp-btn').replaceWith('<button class="btn btn-success mt-3" disabled>You\'ve RSVP\'d</button>');
            },
            error: function(xhr) {
                alert('Error RSVPing: ' + xhr.responseText);
            }
        });
    });
});