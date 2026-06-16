jQuery(document).ready(function($) {
    // You can now access send_to_contacts_params.ajax_url and send_to_contacts_params.nonce

    // Example of an AJAX request using the localized parameters
    /*$.ajax({
        type: 'POST',
        url: send_to_contacts_params.ajax_url, // Access the localized ajax_url
        data: {
            action: 'send_to_contacts_action', // The action name defined in your PHP handler
            nonce: send_to_contacts_params.nonce, // Access the localized nonce
            // other data you want to send
        },
        success: function(response) {
            console.log(response);
        },
        error: function(error) {
            console.error(error);
        }
    });*/

    $('#sendPressReleaseForm').submit(function(e) {
        e.preventDefault(); // Prevent form from submitting normally

        // Get the selected contacts
        var selectedContacts = [];
        $('input[name="contacts[]"]:checked').each(function() {
            selectedContacts.push($(this).val());
        });

        // Get post_release_id and sending_opt values
        var postReleaseId = $('#postReleaseId').val();
        var sendingOpt = $('#sending_opt').val();

        // Check if contacts are selected
        if (selectedContacts.length === 0) {
            alert("Please select at least one contact.");
            return false;
        }

        // Send the data via AJAX
        $.ajax({
            url: '../js/press-release/send-to-contacts-handler.php', // Your PHP handler
            type: 'POST',
            data: {
                post_release_id: postReleaseId,
                sending_opt: sendingOpt,
                contacts: selectedContacts
            },
            success: function(response) {
                // Show the server's response (can be success message, etc.)
                alert(response);
            },
            error: function(xhr, status, error) {
                // Handle error
                console.error(xhr.responseText);
                alert('An error occurred while sending the release.');
            }
        });
    });
});
