<?php
// Ensure this file is being accessed through WordPress
if (!defined('ABSPATH')) {
    exit;
}

function frontend_button($content) {
    global $post, $wpdb;

        // Get the saved post types and categories from the database
        $table_name_options_general = $wpdb->prefix . 'general_options';
        $saved_data = $wpdb->get_results("SELECT * FROM $table_name_options_general WHERE active = 1");

        $saved_post_types = [];
        $saved_categories = [];

        // Populate arrays with saved post types and categories
        foreach ($saved_data as $row) {
            $saved_post_types[] = $row->post_type;
            $saved_categories[] = $row->cat_id;
        }

        // Check if the current post type is one of the saved ones
        if (in_array($post->post_type, $saved_post_types)) {
            // Get post terms (categories) for the post
            $post_categories = wp_get_post_terms($post->ID, get_object_taxonomies($post->post_type, 'names'), ['fields' => 'ids']);

            // Check if the post has any of the saved categories
            if (array_intersect($post_categories, $saved_categories)) {
                // Add the button

                if (is_user_logged_in() && current_user_can('administrator')) {
                    
                    $button = '<button class="btn btn-primary send-options" data-post_id="' . esc_attr($post->ID) . '">Send Press Release</button>';
                    $content .= '<div class="send-to-orgs-container">' . $button . '</div>';
                }
            
            }
        }


        return $content;

        if (is_user_logged_in() && current_user_can('administrator')) {
            display_send_modal();
        }
}

// Hook the function to the filter
add_filter('the_content', 'frontend_button');

// Display the modal HTML in the footer
function display_send_modal() {
    global $post; 
    // Ensure the post object exists
    if (isset($post->ID)) {
        $post_id = esc_attr($post->ID); // Get and sanitize the post ID
        $post_content = apply_filters('the_content', $post->post_content); // 
        $post_title = get_the_title($post_id); // Get the post title
    } else {
        $post_id = ''; // Fallback in case there's no post ID available
    }
    echo '<div id="sendModal" class="modal" style="display:none;">';
    echo '  <div class="modal-content">';
    echo '  <span class="close">&times;</span>';
    echo '  <h2><span class="display_post_title"></span> Press Release Email</h2>';
    echo '<form id="sendPressReleaseForm" method="post">'; // form start
    echo '<input type="hidden" name="cc_emails" id="ccEmails" value="" />';
    echo '<div class="step_1">';
    echo '  <p>Select who you want to send this press release to (further customizations may be made in the next step):</p>';
    
    echo '<input type="hidden" name="post_id" id="post_id" value="" />';
    echo '<input type="hidden" name="post_title" id="post_title" value="'.$post_title.'">';
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
    echo '  <button type="button" class="btn btn-success form_next button button-primary">Next</button>';
    
    echo '</div>'; // end step_1
    echo '<div class="step_2">';
    echo '  <h3>Select a email profile</h3>';
    global $wpdb;
    // Define your table name for sender profiles
    $table_name_sender_profiles = $wpdb->prefix . 'sender_profiles';
    // Query the database to retrieve all sender profiles
    $query = "SELECT * FROM $table_name_sender_profiles";
    $sender_profiles = $wpdb->get_results($query);

    echo '<input type="hidden" id="profile_id" name="profile_id" value="">';
    echo '<input type="hidden" id="profile_email" name="profile_email" value="">';
    // Check if there are any profiles
    if (!empty($sender_profiles)) {
        echo '<div id="profile_list" border="1">';
        // Loop through each profile and display the data
        foreach ($sender_profiles as $profile) {

            echo '<div class="profile_item" data-profile_id="'.esc_html($profile->id).'" data-profile_email="'.esc_html($profile->email_address).'" data-profile_template_header="'. esc_html($profile->header_id).'" data-profile_template_about="'.esc_html($profile->about_id).'" data-profile_template_footer="'.esc_html($profile->footer_id).'">';

            echo '<label for="select-profile-'.esc_html($profile->id).'"> <input type="radio" name="select-profile" id="select-profile-'.esc_html($profile->id).'"> ' . esc_html($profile->profile_name) . '</label>';
            echo '<span>' . esc_html($profile->email_address) . '</span>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        // If no profiles are found
        echo '<p>No sender profiles found.</p>';
    }
    // Submit button
    echo '  <br>';
    echo '  <button type="button" class="btn form_back button button-primary">Back</button>';
    echo '  <button type="button" class="btn btn-success form_next button button-primary" id="preview_email">Next</button>';
    echo '  <button type="submit" id="confirmSend" class="button button-primary">Test Send</button>';
    echo '</div>'; // end step_2
    echo '<div class="step_3" style="display:none;">';
    echo '  <h3>Email Preview</h3>';
    echo '  <div id="emailPreview" style="border:1px solid #ddd; padding:15px; margin-bottom:20px;">';
    echo '      <div id="emailHeaderPreview"></div>';  // Email header preview
    echo '      <div id="emailContentPreview"><h1>' . $post_title .'</h1>'. $post_content . '</div>';  // Email body/content preview
    echo '      <div id="emailAboutPreview"></div>';  
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
}
add_action('wp_footer', 'display_send_modal');

// Enqueue styles for front-end use
function enqueue_organization_contacts_manager_assets() {
    
    /*wp_localize_script(
        'send_to_sel_contacts', 
        'send_to_sel_contacts_params', 
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('send_to_sel_contacts_nonce'),
        )
    );*/

    

    wp_enqueue_script(
        'send_to_sel_contacts', 
        plugins_url('../assets/js/send-press-release.js', __FILE__),
        array('jquery'), 
        '0.3', 
        true
    );

    // Enqueue CSS file
    wp_enqueue_style(
        'organization-contacts-manager-styles',  // Handle for the stylesheet
        plugins_url('../assets/css/styles.css', __FILE__), // Path to the CSS file
        array(),  // Dependencies (none in this case)
        '0.45.0',  // Version of the stylesheet
        'all'  // Media type (e.g., 'all', 'screen', 'print')
    );

    //error_log(plugins_url('../assets/js/frontend-modal.js', __FILE__));
    wp_enqueue_script(
        'organization-contacts-manager-js', // Handle for the JS file
        plugins_url('../assets/js/frontend-modal.js', __FILE__), // Path to the JS file
        array('jquery'),  // Dependencies (only jQuery in this case)
        time(),  // Version of the JS file
        true  // Load in the footer (true = yes)
    );

    wp_localize_script(
        'organization-contacts-manager-js', // The handle of your script
        'ajax_params', // The name of the JavaScript object
        array('ajaxurl' => admin_url('admin-ajax.php')), // Pass ajaxurl from WordPress
    );

    wp_localize_script(
        'organization-contacts-manager-js', // Use the same handle for localization
        'send_to_sel_contacts_params', // The JavaScript object name
        array(
            'ajax_url' => admin_url('admin-ajax.php'), // Pass the admin-ajax URL
            'nonce'    => wp_create_nonce('send_to_sel_contacts_nonce'),
        )
    );
}

// Hook the function to the wp_enqueue_scripts action
add_action('wp_enqueue_scripts', 'enqueue_organization_contacts_manager_assets');
?>