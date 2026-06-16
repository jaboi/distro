/*jQuery(document).ready(function($) {
    $('#sendPressReleaseForm').on('submit', function(e) {
        e.preventDefault();
        alert("send press");

        // Prepare form data
        var formData = $(this).serializeArray();
        formData.push({
            name: 'nonce',
            value: send_to_sel_contacts_params.nonce // Ensure this matches the nonce used in PHP
        });
        formData.push({
            name: 'post_id',
            value: $('#post_id').val() // Ensure this field exists
        });
        formData.push({
            name: 'action',
            value: 'send_to_sel_contacts' // This must match the action hooked in PHP
        });

        // Log formData and AJAX URL
        console.log('Form data:', formData);
        console.log('AJAX URL:', send_to_sel_contacts_params.ajax_url);

        // AJAX request
        $.ajax({
            url: send_to_sel_contacts_params.ajax_url,
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Success: ' + response.data);
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred: ' + error);
                console.log('XHR:', xhr);
                console.log('Status:', status);
                console.log('Error:', error);
            }
        });
    });
});*/

jQuery(document).ready(function($) {
    $('#sendPressReleaseForm').on('submit', function(e) {
        e.preventDefault();
        alert("send press");

        // Prepare form data
        var formData = $(this).serializeArray();

        // Capture the selected profile data
        var selectedProfile = $('input[type="radio"]:checked').closest('.profile_item');
        if (selectedProfile.length === 0) {
            alert('Please select a profile first.');
            return;
        }

        var headerId = selectedProfile.data('profile_template_header');
        var aboutId = selectedProfile.data('profile_template_about');
        var footerId = selectedProfile.data('profile_template_footer');
        console.log(headerId + " - " + aboutId + " - " + footerId);

        // Add profile section IDs to formData
        formData.push({
            name: 'emailHeader',
            value: headerId
        });
        formData.push({
            name: 'emailAbout',
            value: aboutId
        });
        formData.push({
            name: 'emailFooter',
            value: footerId
        });

        // Add nonce and action data
        formData.push({
            name: 'nonce',
            value: send_to_sel_contacts_params.nonce // Ensure this matches the nonce used in PHP
        });
        formData.push({
            name: 'post_id',
            value: $('#post_id').val() // Ensure this field exists and is populated
        });
        formData.push({
            name: 'cc_emails',
            value: $('#ccEmails').val() // Ensure this field exists and is populated
        });
        formData.push({
            name: 'action',
            value: 'send_to_sel_contacts' // This must match the action hooked in PHP
        });

        // Log formData and AJAX URL
        console.log('Form data:', formData);
        console.log('AJAX URL:', send_to_sel_contacts_params.ajax_url);

        // AJAX request
        $.ajax({
            url: send_to_sel_contacts_params.ajax_url,
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Success: ' + response.data);
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred: ' + error);
                console.log('XHR:', xhr);
                console.log('Status:', status);
                console.log('Error:', error);
            }
        });
    });
});

