<?php
echo '<div id="sendModal" class="modal" style="display:none;">';
    echo '  <div class="modal-content">';
    echo '  <span class="close">&times;</span>';
    echo '  <h2><span class="display_post_title"></span> Press Release Email frontend</h2>';
    echo '<form id="sendPressReleaseForm" method="post">'; // form start
    echo '<input type="hidden" name="cc_emails" id="ccEmails" value="" />';
    echo '<div class="step_1">';
    echo '  <p>Select who you want to send this press release to (further customizations may be made in the next step):</p>';
    
    echo '<input type="hidden" name="post_id" id="post_id" value="" />';
    echo '  <input type="hidden" name="post_release_id" id="postReleaseId" value="">';
    echo '  <input type="hidden" name="sending_opt" id="sending_opt" value="send_to_all_contact">';
    echo '  <div class="send_opts">';
    echo '      <label>';
    echo '          <input type="radio" name="send_option" value="allOrganization" checked>';
    echo '          Send to all organizations and their contacts';
    echo '      </label>';

    echo '      <label>';
    echo '          <input type="radio" name="send_option" value="OrgGroups">';
    echo '          Send to an organizations group and their contacts';
    echo '      </label>';

    echo '      <label>';
    echo '          <input type="radio" name="send_option" value="ContactGroups">';
    echo '          Send to a contacts group';
    echo '      </label>';
    echo '  </div>';

    echo '  <div class="contact_lists_opts" id="organizationContactList" style="display:block;padding-left: 20px;">';
                display_organization_contact_checkboxes();
    echo '  </div>';

    echo '  <div class="contact_lists_opts" id="organizationGroupContactList" style="display:none;padding-left: 20px;">';
                display_organization_group_contact_checkboxes();
    echo '  </div>';

    echo '  <div class="contact_lists_opts" id="groupContactList" style="display:none;padding-left: 20px;">';
                display_contact_group_checkboxes();
    echo '  </div>';
    echo '  <br>';

    //echo '  <button type="submit" id="confirmSend" class="button button-primary">Send</button>';
    echo '  <button type="button" class="form_next button button-primary">Next</button>';
    
    echo '</div>'; // end step_1
    echo '<div class="step_2">';
    echo '  <h3>Customize Email</h3>';
    echo '  <label for="emailHeader">' . __('Email Header', 'textdomain') . '</label>';
    wp_editor(
        '', // Default content (empty initially)
        'emailHeader', // ID and name of the field
        array(
            'textarea_name' => 'emailHeader', // Textarea name for form submission
            'media_buttons' => true,         // Disable media buttons if not needed
            'textarea_rows' => 8,
            'teeny'         => true,          // Use a simplified version of TinyMCE
            'quicktags'     => true          // Disable the Text view (if desired)
        )
    );
    echo '  <label for="emailFooter">' . __('Email Footer', 'textdomain') . '</label>';
    wp_editor(
        '', // Default content (empty initially)
        'emailFooter', // ID and name of the field
        array(
            'textarea_name' => 'emailFooter',
            'media_buttons' => true,
            'textarea_rows' => 8,
            'teeny'         => true,
            'quicktags'     => true
        )
    );

    // Submit button
    echo '  <br>';
    echo '  <button type="button" class="form_back button button-primary">Back</button>';
    echo '  <button type="button" class="form_next button button-primary" id="preview_email">Next</button>';
    echo '  <button type="submit" id="confirmSend" class="button button-primary">Test Send</button>';
    echo '</div>'; // end step_2
    echo '<div class="step_3" style="display:none;">';
    echo '  <h3>Email Preview</h3>';
    echo '  <div id="emailPreview" style="border:1px solid #ddd; padding:15px; margin-bottom:20px;">';
    echo '      <div id="emailHeaderPreview"></div>';  // Email header preview
    echo '      <div id="emailContentPreview">[Content will appear here]</div>';  // Email body/content preview
    echo '      <div id="emailFooterPreview"></div>';  // Email footer preview
    echo '  </div>';

    // Submit button
    //previewEmail
    echo '  <button type="button" class="form_back button button-primary">Back</button>';
    echo '  <button type="submit" class="button button-primary">Send Press Release</button>';
    echo '<button id="downloadEmail" type="button" class="button button-primary">Download Email</button>';
    echo '</div>'; // End of step 3
    echo ' </form>'; // form end
    echo '  </div>';
    echo '</div>';
?>
    <script>
    jQuery(document).ready(function($) {
        tinymce.init({
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
            var emailHeader = tinymce.get('emailHeader').getContent();
            var emailFooter = tinymce.get('emailFooter').getContent();
            var postId = $('#post_id').val();
            var postTitle = $('.display_post_title').text(); // Assuming you fill this title elsewhere

            // Gather email data
            var emailData = {
                post_id: postId,
                post_title: postTitle,
                email_header: emailHeader,
                email_footer: emailFooter,
                action: 'download_eml' // AJAX action for handling the request in PHP
            };

            // Send AJAX request to download .eml
            $.ajax({
                url: ajaxurl, // WordPress AJAX URL
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
    </script><?php
?>