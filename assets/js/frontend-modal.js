jQuery(document).ready(function($) {
    // Get the modal element
    //alert("show modal");
    var modal = $('#sendModal');

    // Get the close button
    var span = $('.close');

    // When the user clicks on "SEND" button, show the modal
    $(document).on('click', '.send-options', function() {
    //$('.send-options').click(function() {
        //alert("test");
        modal.show();
        var GetReleasePostId = $(this).attr('data-post_id');
        $('form#sendPressReleaseForm').find('input[name="post_release_id"]').val(GetReleasePostId);
        $('form#sendPressReleaseForm').find('input[name="post_id"]').val(GetReleasePostId);

        var getPostTitle = $(this).parents('tr').attr('data-post_title');
        $('#sendModal h2 > span').text(getPostTitle);
    });

    // When the user clicks on close (x), close the modal
    span.click(function() {
        modal.hide();
    });

    // When the user clicks anywhere outside of the modal, close it
    $(window).click(function(event) {
        if ($(event.target).is(modal)) {
            modal.hide();
        }
    });

    $(document).on('change', '#oug_group_select', function() {
    //$('#oug_group_select').change(function() {    
        var selectedValue = $(this).val();
        console.log(selectedValue);

        // Hide all groups
        $('div.group').hide();
        
        // Show the selected group
        var selectedGroup = $('div.group[data-group_id="'+selectedValue+'"]');
        selectedGroup.show();

        // Check all checkboxes within the selected group
        selectedGroup.find('input[type="checkbox"]').prop('checked', true);

        // Uncheck all checkboxes in groups that are not the selected one
        $('div.group').not(selectedGroup).find('input[type="checkbox"]').prop('checked', false);
    });

    $(document).on('change', '#contact_group_opt', function() {
    //$('#contact_group_opt').change(function() {
        
        var selectedValue = $(this).val();
        console.log(selectedValue);

        // Hide all contact groups
        $('div.contact-group').hide();

        // Show the selected contact group
        var selectedGroup = $('div.contact-group[data-group_contact_id="'+selectedValue+'"]');
        selectedGroup.show();

        // Check all checkboxes within the selected contact group
        selectedGroup.find('input[type="checkbox"]').prop('checked', true);

        // Uncheck all checkboxes in contact groups that are not selected
        $('div.contact-group').not(selectedGroup).find('input[type="checkbox"]').prop('checked', false);

    });


    $(document).on('click', '.form_next', function() {
        //alert("next");
        $(this).parent().hide();
        $(this).parent().next().show();
    });

    $(document).on('click', '.form_back', function() {
        //alert("back");
        $(this).parent().hide();
        $(this).parent().prev().show();
    });
    
    $(document).on('click', '#preview_email', function() {
        alert("preview email");
        // Fetch TinyMCE content for email header and footer
        //let emailHeader = (tinymce.get('emailHeader') !== null) ? tinymce.get('emailHeader').getContent() : '';
        //let emailFooter = (tinymce.get('emailFooter') !== null) ? tinymce.get('emailFooter').getContent() : '';

        // Get the post content (use the correct variable for the post ID)
        let postID = $('#post_id').val();
        //let postContent = window['postContent_' + postID];  //
        //let emailContent = '<p>This is the body of the press release email.</p>';
        //let postTitle = window['postTitle_' + postID];

        // Display the post content in the email preview (sanitize as needed)
        //$('#emailContentPreview').html(postContent);

        $('form#sendPressReleaseForm #emailContentPreview .send-to-orgs-container').remove();

        // Move to step 3 (Email Preview)
        //$step2.hide();
        //$step3.show();
        //let emailFinalContent = '<h1>' + postTitle + '</h1>' + '<div>' + postContent + '</div>';

            //console.log(postContent);
            // Display the post content in the email preview (sanitize as needed)
            //$('#emailContentPreview').html(emailFinalContent);


            // Get the selected radio button's profile data
            //alert("test new preview");
            var selectedProfile = $('input[type="radio"]:checked').closest('.profile_item');
            
            if (selectedProfile.length === 0) {
                alert('Please select a profile first.');
                return;
            }

            console.log(selectedProfile);
            
            var profileId = selectedProfile.data('profile_id');
            var headerId = selectedProfile.data('profile_template_header');
            var aboutId = selectedProfile.data('profile_template_about');
            var footerId = selectedProfile.data('profile_template_footer');
            
            console.log(profileId + " - " +headerId + " - " + aboutId +" - " + footerId);

            // Send an AJAX request to get the section options data
            $.ajax({
                url: ajax_params.ajaxurl, // AJAX URL provided by WordPress
                type: 'POST',
                data: {
                    action: 'get_section_content', // Custom action hook
                    header_id: headerId,
                    about_id: aboutId,
                    footer_id: footerId
                },
                success: function(response) {
                    // Process the response and show the sections
                    if (response.success) {
                        // Display the retrieved section data on the page
                        $('#emailHeaderPreview').html(response.data.header.section_content);
                        $('#emailAboutPreview').html(response.data.about.section_content);
                        $('#emailFooterPreview').html(response.data.footer.section_content);
                    } else {
                        //alert('Failed to retrieve section content.');
                    }
                },
                error: function() {
                    alert('Error occurred while processing the request.');
                }
            });
    });

    $('input[name="select-profile"]').on('change', function() {
        // Get the selected radio button
        var selectedProfileItem = $(this).closest('.profile_item');

        // Get the profile ID from the data attribute
        var selectedProfileId = selectedProfileItem.data('profile_id');
        var selectedProfileEmail = selectedProfileItem.data('profile_email');

        // Output the profile ID (for debugging)
        console.log('Selected Profile ID: ' + selectedProfileId);
        console.log('Selected Profile Email: ' + selectedProfileEmail);

        $('#profile_id').val(selectedProfileId);
        $('#profile_email').val(selectedProfileEmail);

        // Now you can use the selectedProfileId to do anything you need, like updating a form or sending AJAX requests.
    });



    // Toggle the contact list based on selected option
    $(document).on('change', 'input[name="send_option"]', function() {
    //$('input[name="send_option"]').change(function() {
        if ($(this).val() == 'allOrganization') {
            $('#organizationContactList').show(); // Show contact list
            $('#sending_opt').val("send_to_all_contact");
            $('.contact_lists_opts:not(#organizationContactList)').find('input[type="checkbox"]').prop('checked', false);
        } else {
            $('#organizationContactList').hide(); // Hide contact list
        }

        if ($(this).val() == 'OrgGroups') {
            $('#organizationGroupContactList').show(); // Show contact list
            $('#sending_opt').val("send_to_org_group");
            $('.contact_lists_opts:not(#organizationGroupContactList)').find('input[type="checkbox"]').prop('checked', false);
        } else {
            $('#organizationGroupContactList').hide(); // Hide contact list
        }

        if ($(this).val() == 'ContactGroups') {
            $('#groupContactList').show(); // Show contact list
            $('#sending_opt').val("send_to_contact_group");
            $('.contact_lists_opts:not(#groupContactList)').find('input[type="checkbox"]').prop('checked', false);
        } else {
            $('#groupContactList').hide(); // Hide contact list
        }
    });
});
jQuery(document).ready(function($) {
    $('.form_next').click(function() {
        var selectedEmails = [];

        // Collect selected email addresses from checkboxes (adjust the selector based on your form structure)
        $('.contact_lists_opts input[type="checkbox"]:checked').each(function() {
            selectedEmails.push($(this).val());
        });

        // Join emails with commas and set it in the hidden input field
        $('#ccEmails').val(selectedEmails.join(','));
    });
});
jQuery(document).ready(function ($) {
    $('#downloadEmail').on('click', function (e) {
        e.preventDefault();

        // Capture content from TinyMCE editors
        var emailHeader = $('#emailHeaderPreview').html(); // Get HTML content
        var emailAbout = $('#emailAboutPreview').html(); // Get HTML content
        var emailFooter = $('#emailFooterPreview').html(); // Get HTML content
        var emailFrom = $('#profile_email').val();
        var emailCc = $('#ccEmails').val();
        var postId = $('#post_id').val();
        var postTitle = $('#post_title').val(); // Assuming you fill this title elsewhere

        // Gather email data
        var emailData = {
            post_id: postId,
            post_title: postTitle,
            email_from: emailFrom,
            email_cc: emailCc,
            email_header: emailHeader,
            email_about: emailAbout,
            email_footer: emailFooter,
            //email_header: emailHeader,
            //email_footer: emailFooter,
            action: 'download_eml' // AJAX action for handling the request in PHP
        };

        console.log(postTitle);
        console.log(postId);

        // Send AJAX request to download .eml
        $.ajax({
            url: send_to_sel_contacts_params.ajax_url, // WordPress AJAX URL
            type: 'POST',
            data: emailData,
            xhrFields: {
                responseType: 'blob'
            },
            success: function (data) {
                // Create a downloadable link and trigger the download
                var a = document.createElement('a');
                var url = window.URL.createObjectURL(data);
                a.href = url;
                a.download = 'PressRelease-' + postTitle + '.eml'; // Filename for the .eml
                document.body.append(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            },
            error: function (xhr, status, error) {
                console.log('Download failed: ', error);
            }
        });
    });
});

/*jQuery(document).ready(function($) {
    var mediaUploader;

    $('#your-add-media-button').on('click', function(e) {
        e.preventDefault();

        // If the media uploader instance already exists, reopen it
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        // Create a new media uploader instance
        mediaUploader = wp.media({
            title: 'Select Image',
            button: {
                text: 'Use this image'
            },
            multiple: false // Set to true if multiple files should be selected
        });

        // When a file is selected, run a callback
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#your-image-url-input').val(attachment.url); // For example, set the URL in a hidden input
        });

        // Open the uploader dialog
        mediaUploader.open();
    });
});*/