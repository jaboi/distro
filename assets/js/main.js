jQuery(document).ready(function($) {
    // Initialize DataTables for the organizations table
    $('#organizations-table').DataTable();

    // Initialize DataTables for the contacts table
    $('#contacts-table, #org_contact_list, #group_list').DataTable();

    $('#press-release-table').DataTable();

    if ( $('.press_release_tabs').hasClass('editing_on') ){

    } else {
        var orgTable = $('#contacts-table, #organizations-table, #group_list').DataTable();
        var rowCount = orgTable.rows().count();
        $('.press_release_tabs a > span').text(rowCount);
        console.log(rowCount);
    }
    
    
    $('#organization-select, #view_contact_email').on('change', function() {
        var selectedId = $(this).val();
        if (selectedId) {
            //alert('Selected Organization ID: ' + selectedId);
        }
        var getATag = $(this).parent('.dashboard-opt-content').find('a.btn-small').attr('href');
        var newEditLink = getATag + selectedId;
        $(this).parent('.dashboard-opt-content').find('a.btn-small').attr('href', newEditLink);
        //alert(newEditLink);

    });

    $('#contact_email').on('change', function() {
        var selectedId = $(this).val();
        var getEmailContactId = $(this).find(':selected').attr('data-contact_id');
        if (selectedId) {
            //alert('Selected Organization ID: ' + getEmailContactId);
        }
        $(this).parent('.complex-form').find('span.edit_link a').attr('href', "");
        var getATag = $(this).parent('.complex-form').find('span.edit_link').attr('data-link');
        var newEditLink = getATag+""+getEmailContactId;
        $(this).parent('.complex-form').find('span.edit_link a').attr('href', newEditLink);
        //alert(newEditLink);

    });

    $('#contact_email').on('load', function() {
        var selectedId = $(this).val();
        var getEmailContactId = $(this).find(':selected').attr('data-contact_id');
        if (selectedId) {
            //alert('Selected Organization ID: ' + getEmailContactId);
        }
        $(this).parent('.complex-form').find('span.edit_link a').attr('href', "");
        var getATag = $(this).parent('.complex-form').find('span.edit_link').attr('data-link');
        var newEditLink = getATag+""+getEmailContactId;
        $(this).parent('.complex-form').find('span.edit_link a').attr('href', newEditLink);
        //alert(newEditLink);

    });

    // Get the modal element
    var modal = $('#sendModal');

    // Get the close button
    var span = $('.close');

    // When the user clicks on "SEND" button, show the modal
    $('.send-options').click(function() {
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
        $('#emailContentPreview').empty();
        $('.step_1').show();
        $('.step_2').hide();
        $('.step_3').hide();
        $('.send_opts label:first-child input').prop('checked', true);
        $('.send_opts label:not(:first-child) input').prop('checked', false);
        $('#profile_list input[type="radio"]').prop('checked', false);
        $('#oug_group_select option:selected').remove();
    });

    // When the user clicks anywhere outside of the modal, close it
    $(window).click(function(event) {
        if ($(event.target).is(modal)) {
            modal.hide();
        }
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

    // Handle the form submission
    /*$('#sendPressReleaseForm').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize(); // Gather form data

        $.ajax({
            url: ajaxurl, // WordPress AJAX URL
            type: 'POST',
            data: {
                action: 'send_press_release', // WordPress action hook
                form_data: formData // Data from the form
            },
            success: function(response) {
                alert('Press release has been sent!');
                $('#sendModal').hide(); // Close the modal
            },
            error: function(error) {
                console.error('Error:', error);
                alert('There was an error sending the press release.');
            }
        });
    });*/

    $('#oug_group_select').change(function() {

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



    /*$('#contact_group_opt').change(function() {
        var selectedValue = $(this).val();
        console.log(selectedValue);

        $('div.contact-group').hide();
        $('div.contact-group[data-group_contact_id="'+selectedValue+'"]').show();

    });*/

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
        //alert("preview email");
        // Fetch TinyMCE content for email header and footer
        //let emailHeader = (tinymce.get('emailHeader') !== null) ? tinymce.get('emailHeader').getContent() : '';
        //let emailFooter = (tinymce.get('emailFooter') !== null) ? tinymce.get('emailFooter').getContent() : '';

        // Get the post content (use the correct variable for the post ID)
        //let postID = $('#post_id').val();
        //let postContent = window['postContent_' + postID];  //

        // Display the content in the email preview
        //$('#emailHeaderPreview').html(emailHeader);
        //$('#emailFooterPreview').html(emailFooter);

        // Add the actual email content (this can be dynamic content from your form or placeholder text)
        //let emailContent = '<p>This is the body of the press release email.</p>';
        //$('#emailContentPreview').html(emailContent);

        //console.log(postContent);

        // Display the post content in the email preview (sanitize as needed)
        //$('#emailContentPreview').html(postContent);

        // Move to step 3 (Email Preview)
        //$step2.hide();
        //$step3.show();
    });

    //jQuery(document).ready(function() {
        /*tinymce.init({
            selector: 'textarea#emailHeader, textarea#emailFooter', // Apply TinyMCE to both emailHeader and emailFooter
            menubar: false,
            toolbar: 'bold italic underline | alignleft aligncenter alignright | bullist numlist | forecolor backcolor | formatselect | image',
            plugins: 'lists link textcolor colorpicker image', // Removed 'export' plugin
            formats: {
                bold: { inline: 'strong', styles: { 'font-weight': 'bold' } },
                italic: { inline: 'em', styles: { 'font-style': 'italic' } },
                underline: { inline: 'span', styles: { 'text-decoration': 'underline' } },
            },
            style_formats: [
                { title: 'Bold text', inline: 'strong', styles: { 'font-weight': 'bold' } },
                { title: 'Italic text', inline: 'em', styles: { 'font-style': 'italic' } },
                { title: 'Underline text', inline: 'span', styles: { 'text-decoration': 'underline' } },
                { title: 'Image Left', selector: 'img', styles: { 'float': 'left', 'margin': '0 10px 0 0' } },
                { title: 'Image Right', selector: 'img', styles: { 'float': 'right', 'margin': '0 0 10px 10px' } },
                { title: 'Full Width Image', selector: 'img', styles: { 'width': '100%' } },
            ],
            image_advtab: true, // Allows advanced image settings like adding styles
            inline_styles: true, // Forces inline CSS instead of classes
            extended_valid_elements: 'img[class|src|border|alt|title|width|height|style]', // Allow inline styles on images
            image_caption: true,

            // Ensure absolute URLs for images
            relative_urls: false,  // Disable relative URLs
            remove_script_host: false,  // Include the hostname in URLs
            document_base_url: '<?php echo get_site_url(); ?>',  // Set your site's base URL

            // Disable wrapping <p> tag around inline elements like images
            //forced_root_block: '', // Disable forcing <p> tag on inline elements
            //valid_elements: '*[*]', // Allow all valid elements and attributes

            // Force inline styles on image insertion
            setup: function(editor) {
                editor.on('ExecCommand', function(e) {
                    if (e.command === 'mceInsertContent') {
                        editor.$('img').each(function() {
                            var $img = $(this);
                            // Apply inline styles if missing
                            if (!$img.attr('style')) {
                                $img.css({
                                    'width': $img.attr('width') ? $img.attr('width') + 'px' : '',
                                    'height': $img.attr('height') ? $img.attr('height') + 'px' : '',
                                    'float': $img.css('float') || ''
                                });
                            }
                        });
                    }
                });
            }
        });*/
    //});

    $(document).on('click', '.press_release_tabs a', function(e) {
        e.preventDefault();

        var getTab = $(this).attr('href').replace('#','');
        //getTab.replace('#','');
        console.log(getTab);

        $('.press_release_tabs a').removeClass("active_tab");
        $(this).addClass("active_tab");
        
        $('.data_card-list').hide();

        $('div#'+getTab+'').show();
        $('div#'+getTab+'').addClass("active_tab");

        $('div.data-card_title').hide();

        $('div.data-card_title[data-tab_name="'+getTab+'"]').show();
        $('div.data-card_title[data-tab_name="'+getTab+'"]').addClass("active_tab");

        
        

        //alert("next");
        //$(this).parent().hide();
        //$(this).parent().next().show();
    });

    $(document).on('click', '#select_pt_category div[data-pt_cat] label[for="select_all"]', function(e) {
        //alert("select all");
        $(this).toggleClass("all_selected");
        if ( $(this).hasClass("all_selected")) {
            //alert("selected all");
            $(this).parent().find('input').not(this).prop('checked',true);
        } else {
            // alert("not all");
            $(this).parent().find('input').not(this).prop('checked',false);
        }
    });

    /*$('#select_pt_category div[data-pt_cat]').each(function() {
        let $categoryDiv = $(this); // The specific div[data-pt_cat]
        
        // Check if all checkboxes except .select_all are checked on page load
        let totalCheckboxes = $categoryDiv.find('input[type="checkbox"]').not('.select_all').length;
        let checkedCheckboxes = $categoryDiv.find('input[type="checkbox"]:checked').not('.select_all').length;

        // If all checkboxes are checked, also check select_all on load
        $categoryDiv.find('.select_all').prop('checked', totalCheckboxes === checkedCheckboxes);

        // Event when select_all is clicked, check/uncheck all other checkboxes
        $categoryDiv.find('.select_all').on('change', function() {
            let isChecked = $(this).is(':checked');
            $categoryDiv.find('input[type="checkbox"]').not('.select_all').prop('checked', isChecked);
        });
    });*/

    $('#select_pt_category div[data-pt_cat]').each(function() {
        let $categoryDiv = $(this); // The specific div[data-pt_cat]
        
        // Function to update select_all based on the status of individual checkboxes
        function updateSelectAll() {
            let totalCheckboxes = $categoryDiv.find('input[type="checkbox"]').not('.select_all').length;
            let checkedCheckboxes = $categoryDiv.find('input[type="checkbox"]:checked').not('.select_all').length;

            // If all checkboxes except select_all are checked, check the select_all
            $categoryDiv.find('.select_all').prop('checked', totalCheckboxes === checkedCheckboxes);
        }

        // Check status on page load
        updateSelectAll();

        // When any individual checkbox (except select_all) is clicked, update select_all status
        $categoryDiv.find('input[type="checkbox"]').not('.select_all').on('change', function() {
            updateSelectAll();
        });

        // When select_all is clicked, check/uncheck all other checkboxes
        $categoryDiv.find('.select_all').on('change', function() {
            let isChecked = $(this).is(':checked');
            $categoryDiv.find('input[type="checkbox"]').not('.select_all').prop('checked', isChecked);
        });
    });




    /*$('#sendPressReleaseForm').on('submit', function(e) {
        e.preventDefault();

        // Prepare form data
        var formData = $(this).serializeArray();
        formData.push({
            name: 'nonce',
            value: send_to_contacts_params.nonce // Use the nonce passed via wp_localize_script
        });

        // You can add post_id or other additional data if needed, e.g.:
        formData.push({
            name: 'post_id',
            value: $('#post_id').val() // Assuming post_id is in a hidden input field
        });

        // AJAX request to send emails
        $.ajax({
            type: 'POST',
            url: send_to_contacts_params.ajax_url, // WP's AJAX URL
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
            }
        });
    });*/




    

    /*$.ajax({
        url: ajaxurl, // WordPress AJAX URL
        type: 'POST',
        data: {
            action: 'send_press_release',
            send_option: sendOption,
            contacts: selectedContacts
        },
        success: function(response) {
            alert(response.message); // Show success message
            modal.hide(); // Hide the modal after successful submission
        },
        error: function() {
            alert('An error occurred while sending the press release.');
        }
    });*/


});
