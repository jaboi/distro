/*jQuery(document).ready(function($) {
    $('.send-to-contacts, .send-to-orgs').on('click', function(e) {
        e.preventDefault();

        var action = $(this).hasClass('send-to-contacts') ? 'send_to_contacts' : 'send_to_orgs';
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
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An unexpected error occurred.');
            }
        });
    });
});*/
/*jQuery(document).ready(function($) {
    $('.send-to-contacts, .send-to-orgs').on('click', function(e) {
        e.preventDefault();

        var action = $(this).hasClass('send-to-contacts') ? 'send_to_contacts' : 'send_to_orgs';
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
                    // Update the status column in the table
                    var statusCell = $('#post-status-' + post_id); // Assuming you have a cell with id="post-status-[post_id]"
                    statusCell.text('Sent'); // Update the status text

                    // Optionally, update the actions to disable them or change the button text
                    var actionButton = $('.send-to-contacts[data-post-id="' + post_id + '"], .send-to-orgs[data-post-id="' + post_id + '"]');
                    actionButton.prop('disabled', true).text('Sent'); // Disable and change text to 'Sent'

                    alert('Success: ' + response.data);
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An unexpected error occurred.');
            }
        });
    });
});*/

jQuery(document).ready(function($) {
    $('.send-to-contacts, .send-to-orgs').on('click', function(e) {
        e.preventDefault();

        var action = $(this).hasClass('send-to-contacts') ? 'send_to_contacts' : 'send_to_orgs';
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


