jQuery(document).ready(function($) {
    // Handle the preview button click
    $('#preview_email').on('click', function() {
        alert("test preview");
        var postId = $('#post_id').val();
        var emailHeader = tinymce.get('emailHeader').getContent();
        var emailFooter = tinymce.get('emailFooter').getContent();

        $.ajax({
            url: ajaxurl, // WordPress AJAX URL
            type: 'POST',
            data: {
                action: 'generate_email_preview',
                post_id: postId,
                email_header: emailHeader,
                email_footer: emailFooter
            },
            success: function(response) {
                // Update the preview div with the generated content
                $('#emailHeaderPreview').html(emailHeader);
                $('#emailContentPreview').html(response);
                $('#emailFooterPreview').html(emailFooter);
                
                // Show the preview step
                //$('.step_2').hide();
                //$('.step_3').show();
            },
            error: function() {
                alert('An error occurred while generating the email preview.');
            }
        });
    });
});
