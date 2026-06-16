<?php
// Ensure that this file is being accessed through WordPress.
if (!defined('ABSPATH')) {
    exit;
}
add_action('wp_ajax_get_section_content', 'get_section_content');
add_action('wp_ajax_nopriv_get_section_content', 'get_section_content');

function get_section_content() {
    global $wpdb;

    // Get the IDs sent from the AJAX request
    $header_id = isset($_POST['header_id']) ? intval($_POST['header_id']) : 0;
    $about_id = isset($_POST['about_id']) ? intval($_POST['about_id']) : 0;
    $footer_id = isset($_POST['footer_id']) ? intval($_POST['footer_id']) : 0;

    $table_name_section_options = $wpdb->prefix . 'section_options';

    // Retrieve section content and name for header, about, and footer
    $header = $wpdb->get_row($wpdb->prepare("SELECT section_name, section_content FROM $table_name_section_options WHERE id = %d", $header_id));
    $about = $wpdb->get_row($wpdb->prepare("SELECT section_name, section_content FROM $table_name_section_options WHERE id = %d", $about_id));
    $footer = $wpdb->get_row($wpdb->prepare("SELECT section_name, section_content FROM $table_name_section_options WHERE id = %d", $footer_id));

    if ($header && $about && $footer) {
        wp_send_json_success(array(
            'header' => $header,
            'about'  => $about,
            'footer' => $footer,
        ));
    } else {
        wp_send_json_error('Could not retrieve section data.');
    }

    // Stop execution
    wp_die();
}

function render_press_release_page() {
    /*// Check for status in URL
    $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';

    echo '<script type="text/javascript">';
    if ($status === 'success') {
        echo 'alert("' . __('Emails sent successfully.', 'textdomain') . '");';
    } elseif ($status === 'error') {
        echo 'alert("' . __('An error occurred while sending emails.', 'textdomain') . '");';
    }
    echo '</script>';

    // Rest of the method...
    // Query for all published posts
    $args = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => -1 // Get all posts
    );
    $posts = get_posts($args);*/

    global $wpdb;

    // Check for status in URL
    $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';

    echo '<script type="text/javascript">';
    if ($status === 'success') {
        echo 'alert("' . __('Emails sent successfully.', 'textdomain') . '");';
    } elseif ($status === 'error') {
        echo 'alert("' . __('An error occurred while sending emails.', 'textdomain') . '");';
    }
    echo '</script>';

    // Fetch post types and categories from the database
    $table_name_options_general = $wpdb->prefix . 'general_options';
    $saved_data = $wpdb->get_results("SELECT * FROM $table_name_options_general WHERE active = 1");

    // Initialize arrays for post types and categories
    $post_types = array();
    $categories_by_post_type = array();

    // Populate post types and categories
    foreach ($saved_data as $row) {
        // Add post type to the array
        $post_types[] = $row->post_type;
        
        // Create an array for categories associated with the post type
        if (!isset($categories_by_post_type[$row->post_type])) {
            $categories_by_post_type[$row->post_type] = array();
        }
        // Add category id to the associated post type
        $categories_by_post_type[$row->post_type][] = $row->cat_id;
    }

    // Ensure we only use unique post types
    $post_types = array_unique($post_types);

    // Create the query arguments for posts
    $args = array(
        'post_type' => $post_types, // Use the array of saved post types (custom and default)
        'post_status' => 'publish',
        'posts_per_page' => -1 // Get all posts
    );

    // If categories exist, modify the query
    if (!empty($categories_by_post_type)) {
        $tax_query = array('relation' => 'OR'); // Initialize tax_query for multiple categories
        
        foreach ($categories_by_post_type as $post_type => $category_ids) {
            if (!empty($category_ids)) {
                // Get the associated taxonomy name for each post type
                $taxonomies = get_object_taxonomies($post_type, 'names');

                // We assume 'category' taxonomy exists, or use the first available taxonomy
                $taxonomy_name = in_array('category', $taxonomies) ? 'category' : reset($taxonomies);

                // Create a tax query for each post type and its categories
                $tax_query[] = array(
                    'taxonomy' => $taxonomy_name,
                    'field'    => 'term_id',
                    'terms'    => $category_ids,
                    'operator' => 'IN',
                );
            }
        }

        $args['tax_query'] = $tax_query; // Add the tax query to the args
    }

    // Query for all published posts based on the defined arguments
    $posts = get_posts($args);

    if ($posts) {
        echo '<div class="wrap">';
        echo '<div class="data_card-list active_tab_content">';
        echo '<div class="card-header card-header-primary">';
        echo '<div><h4 class="card-title ">Press Release</h4>';
        echo '<p class="card-category">Send to organizations and contacts</p></div>';
        //echo '</div>';
        echo '</div>';

        echo '<table id="press-release-table" class="wp-list-table fixed striped">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>' . __('Title', 'textdomain') . '</th>';
        echo '<th>' . __('Date', 'textdomain') . '</th>';
        echo '<th>' . __('Status', 'textdomain') . '</th>';
        echo '<th>' . __('Actions', 'textdomain') . '</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($posts as $post) {
            ?>
            <?php
            $status = get_post_meta($post->ID, '_press_release_status', true);
            $status_display = $status ? esc_html($status) : __('Not Sent', 'textdomain');

            echo '<tr data-post_title="' . esc_html($post->post_title) . '">';
            echo '<td>' . esc_html($post->post_title) . '</td>';
            echo '<td>' . esc_html(get_the_date('', $post)) . '</td>';
            //echo '<td id="status-'.$post->ID.'">' . get_post_meta($post->ID, '_email_status', true) . '</td>';
            echo '<td>' . $status_display . '</td>';
            echo '<td>';
            echo '<a href="' . esc_url(get_edit_post_link($post->ID)) . '">' . __('Edit', 'textdomain') . '</a> | ';
            echo '<a href="' . esc_url(get_permalink($post->ID)) . '" target="_blank">' . __('View', 'textdomain') . '</a> | ';
            ?>
            <button class="send-options" data-post_id="<?php echo esc_html($post->ID) ?>" >SEND</button>
            <!-- <a href="#" class="button send-to-contacts" data-post-id="<?php //echo $post->ID; ?>">Send Press Release</a> -->
            <?php

            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody></table></div></div>';
    } else {
        echo '<p>' . __('No published posts found.', 'textdomain') . '</p>';
    }

    ?>
    <div id="email-preview-modal" style="display:none;">
        <div id="email-preview-content"></div>
    </div>

    
    <?php
    //echo '</div>';

    // AJAX handler to generate the email preview with inline CSS
    /*add_action('wp_ajax_generate_email_preview', 'generate_email_preview_callback');

    function generate_email_preview_callback() {
        // Ensure the Emogrifier library is available
        if (!class_exists('Pelago\Emogrifier\CssInliner')) {
            require_once plugin_dir_path(__FILE__) . '../lib/CssInliner.php';
        }

        // Get the post ID, header, and footer from the AJAX request
        $post_id = intval($_POST['post_id']);
        $email_header = wp_kses_post($_POST['email_header']);
        $email_footer = wp_kses_post($_POST['email_footer']);
        
        // Fetch and process the email content
        $post_content = get_post($post_id)->post_content;
        $full_content = $email_header . $post_content . $email_footer;

        // Apply inline styles
        $cssInliner = new Pelago\Emogrifier\CssInliner();
        $html_with_inline_css = $cssInliner->inlineCss($full_content);

        // Output the result
        echo $html_with_inline_css;
        
        wp_die(); // WordPress specific: always die in functions for AJAX
    }*/


    
    echo '<div id="sendModal" class="modal" style="display:none;">';
    echo '  <div class="modal-content">';
    echo '  <span class="close">&times;</span>';
    echo '  <h2><span class="display_post_title"></span> Press Release Email</h2>';
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
    echo '  <h3>Select a email profile</h3>';
    // Define your table name for sender profiles
    $table_name_sender_profiles = $wpdb->prefix . 'sender_profiles';
    // Query the database to retrieve all sender profiles
    $query = "SELECT * FROM $table_name_sender_profiles";
    $sender_profiles = $wpdb->get_results($query);

    echo '<input type="hidden" id="profile_id" name="profile_id" value="">';
    echo '<input type="hidden" id="profile_email" name="profile_email" value="">';
    echo '<input type="hidden" id="profile_sender" name="profile_sender" value="">';
    echo '<input type="hidden" id="img_pos" name="img_pos" value="">';
    echo '<input type="hidden" id="btn_action" name="btn_action" value="emlDownload">';
    // Check if there are any profiles
    if (!empty($sender_profiles)) {
        echo '<div id="profile_list" border="1">';
        // Loop through each profile and display the data
        foreach ($sender_profiles as $profile) {

            echo '<div class="profile_item" data-profile_id="'.esc_html($profile->id).'" data-img_pos="'.esc_html($profile->featured_img_pos).'" data-profile_email="'.esc_html($profile->email_address).'" data-profile_sender="'.esc_html($profile->sender_name).'" data-profile_template_header="'. esc_html($profile->header_id).'" data-profile_template_about="'.esc_html($profile->about_id).'" data-profile_template_footer="'.esc_html($profile->footer_id).'">';

            echo '<label for="select-profile-'.esc_html($profile->id).'"> <input type="radio" name="select-profile" id="select-profile-'.esc_html($profile->id).'"> ' . esc_html($profile->profile_name) . '</label>';
            echo '<span>' . esc_html($profile->email_address) . '</span>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        // If no profiles are found
        echo '<p>No sender profiles found.</p>';
    }
    //echo '<div id="section_preview"></div>';

    // Submit button
    echo '  <br>';
    echo '  <button type="button" class="form_back button button-primary">Back</button>';
    echo '  <button type="button" class="form_next button button-primary" id="preview_email">Next</button>';
    echo '  <button type="submit" id="confirmSend" class="button button-primary">Test Send</button>';
    echo '<button id="email-preview-btn" type="button">Preview Email</button>';
    echo '</div>'; // end step_2
    echo '<div class="step_3" style="display:none;">';
    echo '  <h3>Email Preview</h3>';
    echo '  <div id="emailPreview" style="border:1px solid #ddd; padding:15px; margin-bottom:20px;">';
    //echo '      <div id="emailHeaderPreview"></div>';  // Email header preview
    echo '      <div id="emailContentPreview">[Content will appear here]</div>';  // Email body/content preview
    
    //echo '      <div id="emailAboutPreview"></div>';  // Email footer preview
    //echo '      <div id="emailFooterPreview"></div>';  // Email footer preview
    echo '  </div>';

    // Submit button
    //previewEmail
    echo '  <button type="button" class="form_back button button-primary">Back</button>';
    echo '  <button type="submit" class="button button-primary">Send Press Release</button>';
    echo '<button id="downloadEmail" type="button" class="button button-primary">Download Email</button>';
    echo '</div>'; // End of step 3
    echo ' </form>'; // form end
    echo '  </div>';
    echo '</div>';?>
    <script>
    jQuery(document).ready(function($) {
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
        
        // When the "Next" button is clicked
        $(document).on('click', '#preview_email', function() {
            var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
        //$('#preview_email').on('click', function() {
            var btn_action = $('#btn_action').val();
            var ccEmails = $('#ccEmails').val();
            //var profileId = $('#profile_id').val();
            var emailFrom = $('#profile_email').val();
            var emailSender = $('#profile_sender').val();
            var senderFrom = $('#profile_sender').val();
            //var postId = $('#post_id').val();
            var postTitle = $('.display_post_title').text();

            var profileId = $('#profile_id').val();
            var postId = $('#post_id').val();
            var imgPos = $('#img_pos').val();
            var btnAction = 'previewEmail';
            var selectedProfile = $('input[type="radio"]:checked').closest('.profile_item');
            
            if (selectedProfile.length === 0) {
                alert('Please select a profile first.');
                return;
            }

            console.log(selectedProfile);
            
            var profileId = selectedProfile.data('profile_id');
            $.ajax({
                url: ajaxurl, // WordPress AJAX URL
                type: 'POST',
                data: {
                    action: 'generate_email_preview', // Custom WordPress AJAX action
                    profile_id: profileId,
                    post_id: postId,
                    img_pos: imgPos,
                    btn_action: btnAction,
                },
                success: function(response) {
                    if (response.success) {
                        // Display the email body content in the preview div
                        $('#emailContentPreview').html(response.data.emailBody);
                    } else {
                        alert('Failed to generate the email content.');
                    }
                },
                error: function() {
                    alert('Error occurred while generating the email preview.');
                }
            });
        });

        $('input[name="select-profile"]').on('change', function() {
            // Get the selected radio button
            var selectedProfileItem = $(this).closest('.profile_item');

            // Get the profile ID from the data attribute
            var selectedProfileId = selectedProfileItem.data('profile_id');
            var selectedProfileEmail = selectedProfileItem.data('profile_email');
            var selectedProfilePos = selectedProfileItem.data('img_pos');
            var selectedProfileSender = selectedProfileItem.data('profile_sender');


            // Output the profile ID (for debugging)
            //console.log('Selected Profile ID: ' + selectedProfileId);
            //console.log('Selected Profile Email: ' + selectedProfileEmail);

            $('#profile_id').val(selectedProfileId);
            $('#profile_email').val(selectedProfileEmail);
            $('#img_pos').val(selectedProfilePos);
            $('#profile_sender').val(selectedProfileSender);

            // Now you can use the selectedProfileId to do anything you need, like updating a form or sending AJAX requests.
        });

    });
    
    jQuery(document).ready(function($) {
            $('#email-preview-btn').on('click', function() {
                // Collect form data like in the form submission
                var data = {
                    action: 'preview_email_content',
                    nonce: '<?php echo wp_create_nonce('send_to_sel_contacts_nonce'); ?>',
                    contacts: $('input[name="contacts[]"]').val(),
                    profile_id: $('select[name="profile_id"]').val(),
                    post_id: $('input[name="post_id"]').val(),
                    emailHeader: $('input[name="emailHeader"]').val(),
                    emailAbout: $('input[name="emailAbout"]').val(),
                    emailFooter: $('input[name="emailFooter"]').val(),
                    img_pos: $('input[name="img_pos"]:checked').val()
                };

                // AJAX request to generate email preview
                $.post(ajaxurl, data, function(response) {
                    if (response.success) {
                        // Display the email preview in a modal
                        $('#email-preview-content').html(response.data.emailBody);
                        $('#email-preview-modal').show();
                    } else {
                        alert('Error: ' + response.data);
                    }
                });
            });

            // Close modal functionality
            $('#email-preview-modal').on('click', function() {
                $(this).hide();
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
            //var emailHeader = $('#emailHeaderPreview').html(); // Get HTML content
            //var emailAbout = $('#emailAboutPreview').html(); // Get HTML content
            //var emailFooter = $('#emailFooterPreview').html(); // Get HTML content
            var btn_action = $('#btn_action').val();
            var ccEmails = $('#ccEmails').val();
            var profileId = $('#profile_id').val();

            var emailFrom = $('#profile_email').val();
            var emailSender = $('#profile_sender').val();
            var senderFrom = $('#profile_sender').val();
            var postId = $('#post_id').val();
            var postTitle = $('.display_post_title').text(); // Assuming you fill this title elsewhere

            // Gather email data
            var emailData = {
                post_id: postId,
                post_title: postTitle,
                email_from: emailFrom,
                cc_emails : ccEmails,
                profile_id : profileId,
                btn_action : btn_action,
                profile_sender : emailSender,
                //email_header: emailHeader,
                //email_about: emailAbout,
                //email_footer: emailFooter,
                action: 'download_eml' // AJAX action for handling the request in PHP
            };

            // Send AJAX request to download .eml
            $.ajax({
                url: ajaxurl, // WordPress AJAX URL
                type: 'POST',
                data: emailData,
                dataType: 'binary', 
                xhrFields: {
                    responseType: 'blob'
                },
                //processData: false, // Prevent jQuery from processing the data
                //contentType: false, // Prevent jQuery from setting the content-type header
                success: function (data) {
                    console.log("Success response: ", data);
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
                    console.log('Download failed: ', status, error);
    console.log('XHR object:', xhr);
    console.log('Response Text:', xhr.responseText); 
                },
                complete: function(xhr, status) {
                    console.log("Request completed with status: ", status);
                    console.log("XHR object in complete callback:", xhr);
                }
            });
        });
    });
    </script>
    <?php

    // Include your JavaScript file
    //echo '<script src="' . plugin_dir_url(__FILE__) . '../assets/js/preview-email.js"></script>';
}
require_once plugin_dir_path(__FILE__) . 'press-release/display_organization_contact_checkboxes.php';
require_once plugin_dir_path(__FILE__) . 'press-release/display_organization_group_contact_checkboxes.php';
require_once plugin_dir_path(__FILE__) . 'press-release/display_contact_group_checkboxes.php';
//require_once plugin_dir_path(__FILE__) . 'press-release/press-release-modal.php';

?>