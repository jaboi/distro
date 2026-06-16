jQuery(document).ready(function($) {

    $('.send-to-contacts-front').on('click', function(e) {
        alert("sending");
        e.preventDefault();

        var action = $(this).hasClass('send-to-contacts-front') ? 'send_to_contacts' : 'send_to_orgs';
        var post_id = $(this).data('post-id');
        var nonce = action === 'send_to_contacts' ? ajax_object.send_to_contacts_nonce : ajax_object.send_to_orgs_nonce;

        $.ajax({
            url: ajax_object.ajax_url,
            method: 'POST',
            data: {
                action: action,
                post_id: post_id,
                nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('Success: ' + response.data);

                    // Update the status column
                    $('#status-' + post_id).text(response.data);
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An unexpected error occurred.');
            }
        });
    });
});