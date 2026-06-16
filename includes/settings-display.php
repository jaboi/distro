<?php
// Ensure this file is being accessed through WordPress
if (!defined('ABSPATH')) {
    exit;
}
/*function display_sender_profiles() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'sender_profiles';
    //$results = $wpdb->get_results("SELECT profile_name, sender_name, email_address FROM $table_name", ARRAY_A);
    $results = $wpdb->get_results("SELECT id, profile_name, sender_name, email_address FROM $table_name", ARRAY_A);

    if ($results) {
        echo '<h2>' . __('Sender Profiles', 'textdomain') . '</h2>';
        echo '<div class="email_profiles">';
     
        foreach ($results as $row) {
            echo '<div class="email_profile">';
            echo '<div class="email_profile_name">' . esc_html($row['profile_name']) . '<div><span class="dashicons dashicons-edit edit_profile_btn" data-id="' . esc_attr($row['id']) . '"></span><span class="dashicons dashicons-trash delete_profile_btn" data-id="' . esc_attr($row['id']) . '"></span><span class="open_this_profile dashicons dashicons-arrow-down-alt2"></span></div></div>';
            echo '<div class="email_profile_details"><p>' . esc_html($row['sender_name']) . '</p>';
            echo '<p>' . esc_html($row['email_address']) . '</p></div>';
            echo '<div class="email_profile_content"><form>';
            echo '<input type="hidden" value="' . esc_attr($row['id']) . '">';
            echo '<b>Header</b>';
            echo '<b>About</b>';
            echo '<b>Footer</b>';
            echo '</form></div>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>' . __('No sender profiles found.', 'textdomain') . '</p>';
    }
}*/

require_once plugin_dir_path(__FILE__) . 'settings/display_sender_profiles.php';

require_once plugin_dir_path(__FILE__) . 'settings/update_sender_profile_ajax.php';

add_action('wp_ajax_delete_sender_profile', 'delete_sender_profile');
function delete_sender_profile() {
    if (!isset($_POST['profile_id'])) {
        wp_send_json_error(array('message' => 'Profile ID is missing.'));
        wp_die();
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'sender_profiles';
    $profile_id = intval($_POST['profile_id']);

    // Delete the profile from the database
    $deleted = $wpdb->delete($table_name, array('id' => $profile_id), array('%d'));

    if ($deleted) {
        wp_send_json_success();
    } else {
        wp_send_json_error(array('message' => 'Failed to delete profile.'));
    }

    wp_die();
}

add_action('wp_ajax_edit_sender_profile', 'edit_sender_profile');
function edit_sender_profile() {
    if (!isset($_POST['profile_id'])) {
        wp_send_json_error(array('message' => 'Profile ID is missing.'));
        wp_die();
    }

    //global $wpdb;
    //$table_name = $wpdb->prefix . 'sender_profiles';
    $profile_id = intval($_POST['profile_id']);
    $profile_name = sanitize_text_field($_POST['profile_name']);
    $sender_name = sanitize_text_field($_POST['sender_name']);
    $sender_email = sanitize_email($_POST['sender_email']);

    // New fields
    $featured_img_pos = isset($_POST['featured_img_pos']) ? sanitize_text_field($_POST['featured_img_pos']) : '';
    $font_color = isset($_POST['font_color']) ? sanitize_hex_color($_POST['font_color']) : ''; // Sanitize hex color
    $link_color = isset($_POST['link_color']) ? sanitize_hex_color($_POST['link_color']) : ''; // Sanitize hex color
    $font = isset($_POST['font_opt']) ? sanitize_text_field($_POST['font_opt']) : '';

    // Validate required fields
    if (empty($profile_name) || empty($sender_name) || empty($sender_email)) {
        wp_send_json_error(array('message' => 'Required fields are missing.'));
        wp_die();
    }

    // Access global $wpdb and define table name
    global $wpdb;
    $table_name = $wpdb->prefix . 'sender_profiles';

    // Update the profile in the database
    $updated = $wpdb->update($table_name, array(
        'profile_name' => $profile_name,
        'sender_name' => $sender_name,
        'email_address' => $sender_email,
        'featured_img_pos' => $featured_img_pos, // Save new fields
        'font_color' => $font_color,
        'link_color' => $link_color,
        'font_opt' => $font,
    ), 
    array('id' => $profile_id), 
    array('%s', '%s', '%s', '%s', '%s', '%s', '%s'), 
    array('%d'));

    if ($updated !== false) {
        wp_send_json_success();
    } else {
        wp_send_json_error(array('message' => 'Failed to update profile.'));
    }

    wp_die();
}

// save hedaer, about, footer
require_once plugin_dir_path(__FILE__) . 'settings/save_section_options.php';


add_action('wp_ajax_edit_existing_section', 'edit_existing_section');
function edit_existing_section() {
    // Verify nonce for security
    if (!isset($_POST['edit_existing_section_nonce']) || !wp_verify_nonce($_POST['edit_existing_section_nonce'], 'edit_existing_section_nonce_action')) {
        wp_send_json_error(array('message' => 'Invalid security check.'));
        wp_die();
    }

    // Check required fields
    if (!isset($_POST['id']) || !isset($_POST['section_type']) || !isset($_POST['section_content']) || !isset($_POST['section_name'])) {
        wp_send_json_error(array('message' => 'Missing required fields.'));
        wp_die();
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'section_options';

    // Sanitize inputs
    $section_id = intval($_POST['id']);
    $section_type = sanitize_text_field($_POST['section_type']);
    $section_name = sanitize_text_field($_POST['section_name']);
    $section_content = wp_kses_post($_POST['section_content']);

    // Update the existing entry
    $updated = $wpdb->update(
        $table_name,
        array(
            'section_name' => $section_name,
            'section_content' => $section_content,
        ),
        array('id' => $section_id),
        array('%s', '%s'),
        array('%d')
    );

    if ($updated !== false) {
        wp_send_json_success(array('message' => 'Section updated successfully.'));
    } else {
        wp_send_json_error(array('message' => 'Failed to update the content. Error: ' . $wpdb->last_error));
    }

    wp_die();
}

/*function display_section_options($section_type) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'section_options';

    // Fetch entries for the specific section type
    $results = $wpdb->get_results($wpdb->prepare("SELECT section_content FROM $table_name WHERE section_type = %s", $section_type), ARRAY_A);

    // Render a section table
    if (!empty($results)) {
        echo '<h3>' . esc_html(ucfirst($section_type)) . ' Sections</h3>';
        echo '<table class="widefat fixed striped">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>' . __('Section Content', 'textdomain') . '</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($results as $row) {
            echo '<tr>';
            echo '<td>' . wp_kses_post($row['section_content']) . '</td>'; // Allow safe HTML
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>' . __('No ' . strtolower($section_type) . ' sections found.', 'textdomain') . '</p>';
    }
}*/

function display_section_options($section_type) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'section_options';

    // Fetch entries for the specific section type
    $results = $wpdb->get_results($wpdb->prepare("SELECT id, section_name, section_type, section_content FROM $table_name WHERE section_type = %s", $section_type), ARRAY_A);

    // Render a section table
    if (!empty($results)) {
        echo '<h3>' . esc_html(ucfirst($section_type)) . ' Sections</h3>';
        echo '<table class="widefat fixed striped">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>' . __('Section Content', 'textdomain') . '</th>';
        echo '<th>' . __('Actions', 'textdomain') . '</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        foreach ($results as $row) {
            echo '<tr>';
            echo '<td>' . wp_kses_post($row['section_name']) . '</td>';
            echo '<td>';
            echo '<button class="view_section_btn" data-content="' . esc_attr($row['section_content']) . '">' . __('View', 'textdomain') . '</button> ';
            echo '<button class="edit_section_btn" data-section_type="' . esc_attr($row['section_type']) . '" data-id="' . esc_attr($row['id']) . '">' . __('Edit', 'textdomain') . '</button>';
            echo '<button class="delete-section-btn" data-id="' . esc_attr($row['id']) . '">' . __('Delete', 'textdomain') . '</button>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>' . __('No ' . strtolower($section_type) . ' sections found.', 'textdomain') . '</p>';
    }
    // Add the modal container to the bottom of the page (initially hidden)
    echo '
    <div id="viewSectionModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>' . __('Section Content', 'textdomain') . '</h3>
            <div id="modalSectionContent"></div>
        </div>
    </div>';

    // Include JavaScript for the modal functionality
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // When the "View" button is clicked
            $('.view_section_btn').on('click', function() {
                var sectionContent = $(this).data('content');
                
                // Set the section content inside the modal
                $(this).parents('.data_card-list').find('#modalSectionContent').html(sectionContent);

                // Show the modal
                $(this).parents('.data_card-list').find('#viewSectionModal').show();
            });

            // When the "close" span is clicked, hide the modal

            $('.modal .close').on('click', function() {
                if ($(this).parents('#viewSectionModal').is(':visible')) {
                    // uncomment this later
                    $(this).parents('#viewSectionModal').hide();
                }
            });

            // Hide the modal if clicked outside of the modal content
            $(window).on('click', function(event) {
                if ($(event.target).is('#viewSectionModal')) {
                    $('#viewSectionModal').hide();
                }
            });
        });
    </script>
    <style type="text/css">
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
        }

        .modal .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .modal .close:hover,
        .modal .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
    <?php
}
// AJAX handler for deleting a section option
add_action('wp_ajax_delete_section_option', 'delete_section_option');
function delete_section_option() {
    global $wpdb;

    // Check for permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permission denied.');
        wp_die();
    }

    // Check if the ID is provided and valid
    if (!isset($_POST['section_id'])) {
        wp_send_json_error('Missing section ID.');
        wp_die();
    }

    $section_id = intval($_POST['section_id']);
    $table_name = $wpdb->prefix . 'section_options';

    // Delete the section option
    $deleted = $wpdb->delete($table_name, array('id' => $section_id), array('%d'));

    if ($deleted) {
        wp_send_json_success('Section deleted.');
    } else {
        wp_send_json_error('Failed to delete section.');
    }
    wp_die();
}
// AJAX handler for editing a section option
add_action('wp_ajax_edit_section_option', 'edit_section_option');
function edit_section_option() {
    global $wpdb;

    // Check for permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permission denied.');
        wp_die();
    }

    // Validate the data
    if (!isset($_POST['section_id']) || !isset($_POST['section_content'])) {
        wp_send_json_error('Missing required data.');
        wp_die();
    }

    $section_id = intval($_POST['section_id']);
    $section_content = sanitize_text_field($_POST['section_content']);
    $table_name = $wpdb->prefix . 'section_options';

    // Update the section option
    $updated = $wpdb->update(
        $table_name,
        array('section_content' => $section_content),
        array('id' => $section_id),
        array('%s'),
        array('%d')
    );

    if ($updated !== false) {
        wp_send_json_success('Section updated.');
    } else {
        wp_send_json_error('Failed to update section.');
    }
    wp_die();
}


function render_settings_page() {
    ?>

    <div class="wrap">
        <h1><?php //_e('Email Settings', 'textdomain'); ?></h1>
        <div class="press_release_tabs"> 
            <a href="#general_settings" class="btn <?php 
            if ( !isset($_GET['section'])) { echo "no-section "; } // opening setting page
            if ( !isset($_GET['section']) && isset($_GET['profile_id']) ) { echo "no-section-editing-profile "; } // opening setting page
            if ( isset($_GET['section']) && $_GET['section'] === "general" ) { echo "general-selected "; } // selecting general tab
            if ( (!isset($_GET['section']) || $_GET['section'] != "profile") && !isset($_GET['profile_id']) ){ echo "active_tab"; }?>">
                General Settings
            </a>

            <a href="#profile_settings" class="btn <?php if ( (isset($_GET['profile_id']) ) || (isset($_GET['section']) && $_GET['section'] === "profile")  ){ echo "active_tab"; }?>"> 
                Sender Profiles 
            </a>

            <!-- <a href="#header_settings" class="btn <?php if ( isset($_GET['section']) && $_GET['section'] === "header" ){ echo "active_tab"; }?>"> 
                Header Settings 
            </a> -->

            <!-- <a href="#about_settings" class="btn <?php if ( isset($_GET['section']) && $_GET['section'] === "about" ){ echo "active_tab"; }?>"> 
                About Sections 
            </a> -->

            <!-- <a href="#footer_settings" class="btn <?php if ( isset($_GET['section']) && $_GET['section'] === "footer" ){ echo "active_tab"; }?>"> 
                Footer Settings 
            </a> -->
        </div>

            <div>
                <?php
                settings_fields('email_settings_group');
                ?>
            </div>
            <div class="data_card-list form-group <?php if ( !isset($_GET['section'])) { echo "no-section "; } // opening setting page
            if ( !isset($_GET['section']) && isset($_GET['profile_id']) ) { echo "no-section-editing-profile "; } // opening setting page
            if ( isset($_GET['section']) && $_GET['section'] === "general" ) { echo "general-selected "; } // selecting general tab
            if ( (!isset($_GET['section']) || $_GET['section'] != "profile") && !isset($_GET['profile_id']) ){ echo "active_tab_content"; }?>" id="general_settings">
                <div>
                    
                    <?php
                    require_once plugin_dir_path(__FILE__) . 'settings/opts-general-save-handler.php';
                    ?>
                    <form class="simple_form wide_form" id="general_settings_form" method="post">
                        <?php
                        global $wpdb;
                        // Fetch built-in and custom post types (public ones)
                        $args = array(
                            'public' => true // Fetch both custom and built-in post types
                        );
                        $post_types = get_post_types($args, 'objects');

                        // Exclude 'attachment' post type (media items)
                        unset($post_types['attachment']);

                        // Fetch saved post_type and categories from the database
                        $table_name_options_general = $wpdb->prefix . 'general_options';
                        $saved_data = $wpdb->get_results("SELECT * FROM $table_name_options_general WHERE active = 1");

                        // Initialize empty arrays for post_type and categories
                        $default_post_type = 'post';
                        $saved_post_type = $default_post_type; // Default to 'post' if no saved data
                        //$saved_post_type = '';
                        // Initialize variables
                        $saved_post_types = array();
                        $saved_categories = array();

                        // If there's saved data, store the post_type and categories
                        if (!empty($saved_data)) {
                            foreach ($saved_data as $row) {
                                $saved_post_types[] = $row->post_type; // Store post types in an array
                                $saved_categories[] = $row->cat_id;    // Store category IDs in an array
                            }
                        }

                        echo '<h2>Where are press releases located?</h2>';
                        // Loop through each post type and mark the saved post type as selected
                        echo '<div class="pt_opts">';
                        echo '<div id="select_pt">';
                        echo '</div>';

                        // Now render categories (terms) checkboxes for each post type
                        echo '<div id="select_pt_category">';
                        ?>
                        <!-- <div class="pt_cat_list">
                            <div>more divs</div>
                        </div> -->
                        <?php
                        foreach ($post_types as $post_type) {
                            // Check if the post type was previously saved
                            $selected = in_array($post_type->name, $saved_post_types) ? 'selected' : '';
                            $checked = in_array($post_type->name, $saved_post_types) ? 'checked' : '';

                            // Get taxonomies for each post type
                            $taxonomies = get_object_taxonomies($post_type->name, 'objects');
                            $has_category = false;

                            // Container for the post type and its categories
                            
                            echo '<div class="pt_cat_list ' . $selected . '" data-pt_cat_slug="' . esc_attr($post_type->name) . '">';
                            
                            // Show the post type name
                            //echo '<h3>' . esc_html($post_type->labels->name) . '</h3>';
                            
                            echo '<label for="' . esc_attr($post_type->name) . '">';
                            // Hidden checkbox for the post type (to keep track of the selected post types)
                            echo '<input type="checkbox" name="selected_custom_post_type_category[]" id="' . esc_attr($post_type->name) . '" value="' . esc_attr($post_type->name) . '" ' . $checked . '>';
                            echo esc_html($post_type->labels->name);
                            echo '</label>';

                            // Display the categories related to this post type
                            echo '<div data-pt_cat="' . esc_attr($post_type->name) . '" ' . ($saved_post_type === $post_type->name || ($saved_post_type === $default_post_type && $post_type->name === $default_post_type) ? '' : 'post_type_cat_disabled') . '>';
                            ?>
                            
                            <?php
                            echo '<b class="text-center">Categories</b>';
                            foreach ($taxonomies as $taxonomy) {

                                if ($taxonomy->hierarchical) {
                                    $terms = get_terms(array(
                                        'taxonomy'   => $taxonomy->name,
                                        'hide_empty' => false
                                    ));

                                    if (!empty($terms)) {
                                        $has_category = true;

                                        // Loop through terms and add them as checkboxes for each category
                                        foreach ($terms as $term) {
                                            $checked = in_array($term->term_id, $saved_categories) ? 'checked' : ''; // Check if this category was previously saved
                                            $cat_saved = in_array($term->term_id, $saved_categories) ? 'db_cat_saved' : ''; // Indicate if it's saved
                                            ?>
                                            <label for="<?php echo esc_attr($term->term_id); ?>" style="order: 1;">
                                                <input data-cat_saved="<?php echo $cat_saved; ?>" type="checkbox" name="categories[<?php echo esc_attr($post_type->name); ?>][]" value="<?php echo esc_attr($term->term_id);?>" id="<?php echo esc_attr($term->term_id); ?>" <?php echo $checked; ?>><?php echo esc_html($term->name);?>
                                            </label>
                                            <?php
                                        }
                                    }
                                }
                            }

                            // If no categories are available
                            if (!$has_category) {
                                echo __('No categories', 'textdomain');
                            }
                            if ($has_category) {
                                echo '<label for="select_all" style="order: 0;"><input type="checkbox" id="select_all" class="select_all"> Select All</label>';
                            }

                            echo '</div>'; // Close categories container
                            echo '</div>'; // Close post type container
                            
                        }

                        echo '</div>';

                        echo '</div>';
                        ?>
                        <br>
                        <?php wp_nonce_field('save_general_settings', 'general_settings_nonce'); ?>
                        <input type="submit" class="btn btn-primary" value="Save General Settigns">
                    </form>
                </div> 
            </div>
            

            <div class="data_card-list form-group <?php if ( (isset($_GET['profile_id']) ) || (isset($_GET['section']) && $_GET['section'] === "profile")){ echo "active_tab_content"; }?>" id="profile_settings">
                 <?php
                    require_once plugin_dir_path(__FILE__) . 'settings/email-profile-save-handler.php';
                ?>
                <?php
                if (isset($_GET['profile_id'])) {
                    global $wpdb;
                    $profile_id = intval($_GET['profile_id']);
                    $table_name = $wpdb->prefix . 'sender_profiles';
                    $profile = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $profile_id", ARRAY_A);

                    // Check if the profile exists and fetch the saved values
                    //$saved_featured_img_pos = isset($profile['featured_img_pos']) ? $profile['featured_img_pos'] : 'display-top';
                    $saved_featured_img_pos = esc_attr($profile['featured_img_pos']);
                    $saved_font = isset($profile['font_opt']) ? $profile['font_opt'] : 'Arial';

                    echo $saved_featured_img_pos;

                    if ($profile) {
                        ?>
                        <form id="edit_profile_form" class="simple_form wide_form" style="display: flex;align-content: center;">
                            <h2>Edit <?php echo esc_attr($profile['profile_name']); ?></h2>
                            <input type="hidden" name="profile_id" value="<?php echo esc_attr($profile['id']); ?>">
                            <div>
                                <label for="profile_name"><?php _e('Profile Name', 'textdomain'); ?></label>
                                <input type="text" name="profile_name" id="profile_name" value="<?php echo esc_attr($profile['profile_name']); ?>" class="regular-text">
                            </div>
                            <br>
                            <div>
                                <label for="sender_name"><?php _e('Sender Name', 'textdomain'); ?></label>
                                <input type="text" name="sender_name" id="sender_name" value="<?php echo esc_attr($profile['sender_name']); ?>" class="regular-text">
                            </div>
                            <br>
                            <div>
                                <label for="sender_email"><?php _e('Sender Email Address', 'textdomain'); ?></label>
                                <input type="text" name="sender_email" id="sender_email" value="<?php echo esc_attr($profile['email_address']); ?>" class="regular-text">
                            </div>
                            <br>
                            <div>
                                <label>Featured Image Preference:</label><br>
                                 <label>
                                    <input type="radio" name="featured_img_pos" value="display-top" <?php if ($saved_featured_img_pos === "display-top" ) { echo "checked"; }?>/> 
                                    Display At Top
                                </label>

                                <label>
                                    <input type="radio" name="featured_img_pos" value="display-below-headline" <?php if ($saved_featured_img_pos === "display-below-headline" ) { echo "checked"; }?>/> 
                                    Display Below Headline
                                </label>

                                <label>
                                    <input type="radio" name="featured_img_pos" value="email-attach" <?php if ($saved_featured_img_pos === "email-attach" ) { echo "checked"; }?>/> 
                                    Attach To Email
                                </label>

                                <label>
                                    <input type="radio" name="featured_img_pos" value="ignore" <?php if ($saved_featured_img_pos === "ignore" ) { echo "checked"; }?>/> Ignore
                                </label>
                            </div>
                            <br>
                            <div>
                                <label>Font Color</label><br>
                                <input type="text" name="font_color" id="font_color" value="<?php echo esc_attr($profile['font_color']); ?>" class="regular-text">
                            </div>
                            <br>
                            <div>
                                <label>Link Color</label><br>
                                <input type="text" name="link_color" id="link_color" value="<?php echo esc_attr($profile['link_color']); ?>" class="regular-text">
                            </div>
                            <br>
                            <div>
                                <label>Font</label>
                                <select name="font_opt" id="font">
                                    <option value="Arial" <?php selected($saved_font, 'Arial'); ?>>Arial</option>
                                    <option value="Courier New" <?php selected($saved_font, 'Courier New'); ?>>Courier New</option>
                                    <option value="Georgia" <?php selected($saved_font, 'Georgia'); ?>>Georgia</option>
                                    <option value="Helvetica" <?php selected($saved_font, 'Helvetica'); ?>>Helvetica</option>
                                    <option value="Tahoma" <?php selected($saved_font, 'Tahoma'); ?>>Tahoma</option>
                                    <option value="Times New Roman" <?php selected($saved_font, 'Times New Roman'); ?>>Times New Roman</option>
                                    <option value="Trebuchet MS" <?php selected($saved_font, 'Trebuchet MS'); ?>>Trebuchet MS</option>
                                    <option value="Verdana" <?php selected($saved_font, 'Verdana'); ?>>Verdana</option>
                                </select>
                            </div>
                            <br>
                            <div class="btn-container">
                                <a href="<?php echo esc_url(admin_url('admin.php?page=settings_page&section=profile')); ?>" class="btn btn-danger text-center">Cancel</a>
                                <button class="btn btn-primary" type="submit"><?php _e('Update', 'textdomain'); ?></button>
                            </div>
                        </form>

                        <script>
                        jQuery(document).ready(function($) {
                            $('#edit_profile_form').on('submit', function(e) {
                                e.preventDefault();

                                var data = {
                                    action: 'edit_sender_profile',
                                    profile_id  : $('input[name="profile_id"]').val(),
                                    profile_name: $('input[name="profile_name"]').val(),
                                    sender_name : $('input[name="sender_name"]').val(),
                                    sender_email: $('input[name="sender_email"]').val(),
                                    //featured_img_pos: $('input[name="featured_img_pos"]').val(),
                                    featured_img_pos: $('input[name="featured_img_pos"]:checked').val(),
                                    font_color  : $('input[name="font_color"]').val(),
                                    link_color  : $('input[name="link_color"]').val(),
                                    font_opt    : $('select[name="font_opt"]').val(),
                                };

                                $.ajax({
                                    url: ajaxurl,
                                    type: 'POST',
                                    data: data,
                                    success: function(response) {
                                        if (response.success) {
                                            alert('Profile updated successfully.');
                                            window.location.href = '/wp-admin/admin.php?page=settings_page&section=profile'; // Redirect to the list page
                                        } else {
                                            alert('Failed to update profile: ' + response.data.message);
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.log('AJAX Error: ', error);
                                    }
                                });
                            });
                        });
                        </script>
                        <?php
                    } else {
                        echo '<p>' . __('Profile not found.', 'textdomain') . '</p>';
                    }
                }
                if (!isset($_GET['profile_id'])) {
                ?>
                <form class="simple_form wide_form" id="email_settings_form" method="post" style="display: flex;align-content: center;">
                    <h2>Create New Profile</h2>
                    <div>
                        <label for="profile_name"><?php _e('New Profile Name', 'textdomain'); ?></label>
                        <input type="text" name="profile_name" id="profile_name" value="" class="regular-text">
                    </div>
                    <br>
                    <div>
                        <label for="sender_name"><?php _e('New Sender Name', 'textdomain'); ?></label>
                        <input type="text" name="sender_name" id="sender_name" value="" class="regular-text">
                    </div>
                    <br>
                    <div>
                        <label for="sender_email"><?php _e('New Sender Email Address', 'textdomain'); ?></label>
                        <input type="email" name="sender_email" id="sender_email" value="" class="regular-text">
                    </div>
                    <br>
                    <div>
                        <label>Featured Image Preference:</label><br>
                        <label><input type="radio" name="featured_img_pos" value="display-top" checked />Display At Top</label>
                        <label><input type="radio" name="featured_img_pos" value="display-below-headline" />Display Below Headline </label>
                        <label><input type="radio" name="featured_img_pos" value="email-attach" />Attach To Email  </label>
                        <label><input type="radio" name="featured_img_pos" value="ignore" />Ignore </label>
                    </div>
                    <br>
                    <div>
                        <label>Font Color</label><br>
                        <input type="text" name="font_color" id="font_color" value="#000000" class="regular-text">
                    </div>
                    <br>
                    <div>
                        <label>Link Color</label><br>
                        <input type="text" name="link_color" id="link_color" value="#1e73be" class="regular-text">
                    </div>
                    <br>
                    <div>
                        <label>Font</label>
                        <select name="font_opt" id="font">
                            <option value="Arial">Arial</option>
                            <option value="Courier New">Courier New</option>
                            <option value="Georgia">Georgia</option>
                            <option value="Helvetica">Helvetica</option>
                            <option value="Tahoma">Tahoma</option>
                            <option value="Times New Roman">Times New Roman</option>
                            <option value="Trebuchet MS">Trebuchet MS</option>
                            <option value="Verdana">Verdana</option>
                        </select>
                    </div>
                    <br>
                    <?php wp_nonce_field('save_email_settings', 'email_settings_nonce'); ?>
                    <div>
                        <input type="submit" name="save_email_settings" value="<?php _e('Save Settings', 'textdomain'); ?>" class="btn btn-primary">
                    </div>
                </form>
                <?php } ?>
                <?php display_sender_profiles(); ?>

                <!-- Add a div for feedback message -->
                <div id="ajax-response"></div>  

                <script type="text/javascript">
                    jQuery(document).ready(function($) {
                        $('.delete_profile_btn').on('click', function() {
                            var profileId = $(this).data('id');
                            if (confirm('Are you sure you want to delete this profile?')) {
                                $.ajax({
                                    url: ajaxurl,
                                    type: 'POST',
                                    data: {
                                        action: 'delete_sender_profile',
                                        profile_id: profileId
                                    },
                                    success: function(response) {
                                        if (response.success) {
                                            alert('Profile deleted successfully.');
                                            location.reload(); // Reload the page to reflect the changes
                                        } else {
                                            alert('Failed to delete profile: ' + response.data.message);
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.log('AJAX Error: ', error);
                                    }
                                });
                            }
                        });

                        $('.edit_profile_btn').on('click', function() {
                            var profileId = $(this).data('id');
                            // You can open a modal or populate an edit form with AJAX here
                            // For simplicity, we can reload the page to pass the profile ID to a form for editing
                            window.location.href = '/wp-admin/admin.php?page=settings_page&profile_id=' + profileId;
                        });
                    });
/*jQuery(document).ready(function($) {
    $('#email_settings_form').on('submit', function(e) {
        e.preventDefault();

        var data = {
            action: 'save_email_settings',
            profile_name: $('input[name="profile_name"]').val(),
            sender_name: $('input[name="sender_name"]').val(),
            sender_email: $('input[name="sender_email"]').val(),
            email_settings_nonce: $('input[name="email_settings_nonce"]').val(),
        };

        // Clear any previous messages
        $('#ajax-response').html('');

        $.ajax({
            url: '<?php echo admin_url("admin-ajax.php"); ?>', // WordPress AJAX URL
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    console.log("good");
                    $('#ajax-response').html('<div class="updated">' + response.data.message + '</div>');
                } else {
                    console.log("bad");
                    $('#ajax-response').html('<div class="error">' + response.data.message + '</div>');
                }
            },
            error: function(xhr, status, error) {
                console.log("super bad");
                $('#ajax-response').html('<div class="error">An error occurred: ' + error + '</div>');
            }
        });
    });
});*/
</script>

            </div>

            <div class="data_card-list form-group <?php if ( isset($_GET['section']) && $_GET['section'] === "header" ){ echo "active_tab_content"; }?>" id="header_settings">
                <div>
                    
                    <?php
                    if ( isset($_GET['section_id']) && ( isset($_GET['section']) && $_GET['section'] === "header") ) {
                    //if (isset($_GET['section_id'])) {
                        global $wpdb;
                        $section_id = intval($_GET['section_id']);
                        $table_name = $wpdb->prefix . 'section_options';
                        $section = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $section_id", ARRAY_A);

                        if ($section) { ?>
                            <form id="update_header_custom_content" name="update_header_custom_content" class="simple_form wide_form" method="post">
                                <?php wp_nonce_field('edit_existing_section_nonce_action', 'edit_existing_section_nonce'); ?>
                                <input type="hidden" name="id" value="<?php echo $section['id']; ?>">
                                <input type="hidden" name="section_type" value="header">
                                
                                <div>
                                    <label for="header_section_name">Header Section Name</label>
                                    <input type="text" id="header_section_name" name="section_name" value="<?php echo esc_attr($section['section_name']); ?>">
                                </div>
                                <br>
                                <div>
                                    <label for="custom_email_header">Header Section Content</label>
                                    <?php
                                    $header_content = $section['section_content'];
                                    wp_editor($header_content, 'custom_email_header', array('textarea_name' => 'section_content'));
                                    ?>
                                </div>
                                <br>
                                <div class="btn-container">
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=settings_page&section=header')); ?>" class="btn btn-danger text-center">Cancel</a>
                                    <input type="submit" name="submit" class="btn btn-primary" value="Update <?php echo esc_attr($section['section_name']); ?>">
                                </div>
                            </form>
                        <?php
                        }
                    } else { ?>
                        <form id="header_custom_content" name="header_custom_content" class="simple_form wide_form" method="post">
                            <?php wp_nonce_field('save_section_nonce_action', 'save_section_nonce'); ?>
                            <input type="hidden" name="section_type" value="header">
                            
                            <div>
                                <label for="header_section_name">Header Section Name</label>
                                <input type="text" id="header_section_name" name="section_name" value="">
                            </div>
                            <br>
                            <div>

                                <label for="custom_email_header">Header Section Content</label>

                                <?php $header_content = esc_html($section['section_content']); ?>
                                <textarea id="custom_email_header" name="section_content"><?php echo $header_content; ?></textarea>
                               

                                <?php
                                //$header_content = get_option('custom_email_header', '');
                                //wp_editor($header_content, 'custom_email_header', array('textarea_name' => 'section_content'));
                                ?>
                            </div>
                            <br>
                            <input type="submit" name="submit" class="btn btn-primary" value="Save Header">
                        </form>
                    <?php } ?>


                </div>
                <?php
                // Fetch and display the footer sections below the form
                display_section_options('header');
                ?>
            </div>

            <div class="data_card-list form-group <?php if ( isset($_GET['section']) && $_GET['section'] === "about" ){ echo "active_tab_content"; }?>" id="about_settings">
                <div>
                    <?php
                    if ( isset($_GET['section_id']) && ( isset($_GET['section']) && $_GET['section'] === "about") ) {
                    //if (isset($_GET['section_id'])) {
                        global $wpdb;
                        $section_id = intval($_GET['section_id']);
                        $table_name = $wpdb->prefix . 'section_options';
                        $section = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $section_id", ARRAY_A);

                        if ($section) { ?>
                            <form id="update_about_custom_content" name="update_about_custom_content" class="simple_form wide_form" method="post">
                                <?php wp_nonce_field('edit_existing_section_nonce_action', 'edit_existing_section_nonce'); ?>
                                <input type="hidden" name="id" value="<?php echo $section['id']; ?>">
                                <input type="hidden" name="section_type" value="about">
                                
                                <div>
                                    <label for="header_section_name">About Section Name</label>
                                    <input type="text" id="header_section_name" name="section_name" value="<?php echo esc_attr($section['section_name']); ?>">
                                </div>
                                <br>
                                <div>
                                    <label for="custom_about_header">About Section Content</label>
                                    <?php
                                    $header_content = $section['section_content'];
                                    wp_editor($header_content, 'custom_about_header', array('textarea_name' => 'section_content'));
                                    ?>
                                </div>
                                <br>
                                <div class="btn-container">
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=settings_page&section=about')); ?>" class="btn btn-danger text-center">Cancel</a>
                                    <input type="submit" name="submit" class="btn btn-primary" value="Update <?php echo esc_attr($section['section_name']); ?>">
                                </div>
                            </form>
                        <?php
                        }
                    } else { ?>
                    <form id="about_custom_content" name="about_custom_content" class="simple_form wide_form" method="post">
                        <?php wp_nonce_field('save_section_nonce_action', 'save_section_nonce'); ?>
                        <input type="hidden" name="section_type" value="about">
                        <div>
                            <label for="about_section_name">About Section Name</label>
                            <input type="text" id="about_section_name" name="section_name" value="">
                        </div>
                        <br>
                        <div>
                            <label for="custom_about_header">About Section Content</label>
                            <?php
                            $about_content = get_option('custom_about_header', '');
                            wp_editor($about_content, 'custom_about_header', array('textarea_name' => 'custom_about_header'));
                            ?>
                        </div>
                        
                        <br>
                        <input type="submit" name="submit" class="btn btn-primary" value="Save About">
                    </form>
                    <?php } ?>
                </div>
                <?php
                // Fetch and display the footer sections below the form
                display_section_options('about');
                ?>
            </div>

            

            <div class="data_card-list form-group <?php if ( isset($_GET['section']) && $_GET['section'] === "footer" ){ echo "active_tab_content"; }?>" id="footer_settings">
                <div>
                    <?php
                    if ( isset($_GET['section_id']) && ( isset($_GET['section']) && $_GET['section'] === "footer") ) {
                        global $wpdb;
                        $section_id = intval($_GET['section_id']);
                        $table_name = $wpdb->prefix . 'section_options';
                        $section = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $section_id", ARRAY_A);

                        if ($section) { ?>
                            <form id="update_footer_custom_content" name="update_footer_custom_content" class="simple_form wide_form" method="post">
                                <?php wp_nonce_field('edit_existing_section_nonce_action', 'edit_existing_section_nonce'); ?>
                                <input type="hidden" name="id" value="<?php echo $section['id']; ?>">
                                <input type="hidden" name="section_type" value="footer">
                                
                                <div>
                                    <label for="header_section_name">Footer Section Name</label>
                                    <input type="text" id="header_section_name" name="section_name" value="<?php echo esc_attr($section['section_name']); ?>">
                                </div>
                                <br>
                                <div>
                                    <label for="custom_email_footer">Footer Section Content</label>
                                    <?php
                                    //$footer_content = esc_html($section['section_content']);
                                    $footer_content = $section['section_content'];
                                    wp_editor($footer_content, 'custom_email_footer', array('textarea_name' => 'section_content'));
                                    ?>
                                    <!-- <textarea name="custom_email_header" id="custom_email_header"><?php echo $header_content; ?></textarea> -->
                                </div>
                                <br>
                                <div class="btn-container">
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=settings_page&section=footer')); ?>" class="btn btn-danger text-center">Cancel</a>
                                    <input type="submit" name="submit" class="btn btn-primary" value="Update <?php echo esc_attr($section['section_name']); ?>">
                                </div>
                                
                            </form>
                        <?php
                        }
                    } else { ?>
                    <form id="footer_custom_content" name="footer_custom_content" class="simple_form wide_form" method="post">
                        <?php wp_nonce_field('save_section_nonce_action', 'save_section_nonce'); ?>
                        <input type="hidden" name="section_type" value="footer">
                        <div>
                            <label for="footer_section_name">Update Footer Name</label>
                            <input type="text" id="footer_section_name" name="section_name" value="">
                        </div>
                        <br>
                        <div>
                            <label for="custom_email_footer">Update Footer Content</label>
                            <?php
                            //$footer_content = get_option('custom_email_footer', '');
                            //wp_editor($footer_content, '', array('textarea_name' => 'custom_email_footer'));
                            ?>
                            <textarea name="custom_email_footer" id="custom_email_footer"></textarea>
                        </div>
                        <br>
                        <input type="submit" name="submit" class="btn btn-primary" value="Save Footer">
                    </form>
                    <?php } ?>
                </div>
                <?php
                    // Fetch and display the footer sections below the form
                    display_section_options('footer');
                ?>
            </div>


            <div>
                <?php
                do_settings_sections('email-settings');
                ?>
            </div>
            <div>
                <?php
                //submit_button();
                ?>
            </div>
        <!-- </form> -->

        <script>
            /*jQuery(document).ready(function($) {
                // Function to handle form submission
                function submitCustomContentForm(formId, sectionType, editorId) {
                    $(formId).on('submit', function(e) {
                        e.preventDefault();

                        // Extract TinyMCE content
                        var section_content = tinyMCE.get(editorId).getContent(); // Get content from TinyMCE editor

                        // Get section_name dynamically from the form
                        var section_name = $(this).find('input[name="section_name"]').val(); 

                        var data = {
                            action: 'save_section_options',  // AJAX action name
                            section_type: sectionType,
                            section_name: section_name, // Get the section name from input
                            section_content: section_content,
                            security: $(this).find('input[name="save_section_nonce"]').val()  // Nonce for security
                        };

                        $.ajax({
                            url: ajaxurl,  // WordPress admin-ajax.php URL
                            type: 'POST',
                            data: data,
                            success: function(response) {
                                if (response.success) {
                                    alert('Section content saved successfully!');
                                } else {
                                    alert('Failed to save section content: ' + response.data.message);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.log('AJAX Error: ', error); // Log error details
                                console.log(xhr.responseText); // Log server response
                            }
                        });
                    });
                }

                // Bind AJAX form submission for each section
                submitCustomContentForm('#header_custom_content', 'header', 'custom_email_header');
                submitCustomContentForm('#about_custom_content', 'about', 'custom_about_header');
                submitCustomContentForm('#footer_custom_content', 'footer', 'custom_email_footer');
            });*/
            jQuery(document).ready(function($) {
                $(document).on('click', '.press_release_tabs a', function(e) {
                    e.preventDefault();

                    var getTab = $(this).attr('href').replace('#','');
                    //getTab.replace('#','');
                    //console.log(getTab);

                    var newUrl = getTab.replace('_settings','');

                    var newTabUrl = '/wp-admin/admin.php?page=settings_page&section=' + newUrl;
                    //console.log(newTabUrl);
                    //window.location.href = '/wp-admin/admin.php?page=settings_page&section='+ newTabUrl;
                    history.pushState(null, '', newTabUrl); 
            });


    // Function to handle form submission
                var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
                function submitCustomContentForm(formId, sectionType, editorId) {
                    $(formId).on('submit', function(e) {
                        e.preventDefault();

                        // Extract TinyMCE content
                        var section_content = hugerte.get(editorId).getContent(); // Get content from TinyMCE editor

                        // Get section_name dynamically from the form
                        var section_name = $(this).find('input[name="section_name"]').val(); 
                        var sectionName = $(this).find('input[name="section_type"]').val();

                        

                        var data = {
                            action: 'save_section_options',  // AJAX action name
                            section_type: sectionType,
                            section_name: section_name, // Get the section name from input
                            section_content: section_content,
                            security: $(this).find('input[name="save_section_nonce"]').val()  // Nonce for security
                        };

                        console.log($(this).find('input[name="save_section_nonce"]').val());

                        $.ajax({
                            url: ajaxurl,  // WordPress admin-ajax.php URL
                            type: 'POST',
                            data: data,
                            success: function(response) {
                                if (response.success) {
                                    alert('Section content saved successfully!');
                                    window.location.href = '/wp-admin/admin.php?page=settings_page&section='+ sectionName;
                                } else {
                                    alert('Failed to save section content: ' + response.data.message);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.log('AJAX Error: ', error); // Log error details
                                console.log(xhr.responseText); // Log server response
                            }
                        });
                    });
                }

                // Bind AJAX form submission for each section
                submitCustomContentForm('#header_custom_content', 'header', 'custom_email_header');
                submitCustomContentForm('#about_custom_content', 'about', 'custom_about_header');
                submitCustomContentForm('#footer_custom_content', 'footer', 'custom_email_footer');

                //function submitEditSectionForm(formId, editorId) {
                $('#update_header_custom_content, #update_about_custom_content, #update_footer_custom_content').on('submit', function(e) {
                    e.preventDefault();
                    var sectionType = $(this).find('input[name="section_type"]').val();

                    // Make sure the hugeRTE content is updated in the textarea
                    //var editorContent = hugeRTE.getContent('custom_email_header');  // Assuming hugeRTE has a `getContent` function
                     // Set the content into the textarea
                    //var editorContent = hugeRTE.get('custom_email_header').getContent();
                    //$('#custom_email_header').val(editorContent); 

                    /*if (hugeRTE.get('custom_email_header')) {
                        var editorContent = hugeRTE.get('custom_email_header').getContent();
                        $('#custom_email_header').val(editorContent);
                        console.log(editorContent);
                    } else {
                        console.error('Editor not found or not initialized for ID: custom_email_header');
                    }*/

                    var editorId;
                    if (sectionType === 'header') {
                    editorId = 'custom_email_header';
                    } else if (sectionType === 'about') {
                    editorId = 'custom_about_header';
                    } else if (sectionType === 'footer') {
                    editorId = 'custom_email_footer';
                    }

                    console.log(editorId);

                    // Ensure we are grabbing the content from the editor, not the <textarea>
                    if (hugeRTE.get(editorId)) {
                    var editorContent = hugeRTE.get(editorId).getContent(); // Get updated content from the hugeRTE editor
                    console.log('Editor content:', editorContent); // For debugging, see if the updated content is printed

                    // Manually sync the content with the textarea
                    $(this).find('textarea[name="section_content"]').val(editorContent);  // Set editor content into textarea
                    } else {
                    console.error('Editor not found or not initialized for ID: ' + editorId);
                    return;
                    }

                    // Serialize the form data
                    var formData = $(this).serialize();
                    console.log(formData);

                    $.ajax({
                        url: ajaxurl,  // WordPress admin-ajax.php URL
                        type: 'POST',
                        data: formData + '&action=edit_existing_section',  // Adding action to form data
                        success: function(response) {
                            if (response.success) {
                                alert('Section updated successfully!');
                                window.location.href = '/wp-admin/admin.php?page=settings_page&section='+ sectionType;
                            } else {
                                alert('Failed to update section: ' + response.data.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log('AJAX Error: ', error);  // Log error details
                            console.log(xhr.responseText);  // Log server response
                        }
                    });
                });
                //}
            });


            // update profile sections
            jQuery(document).ready(function($) {
                // Capture form submission via AJAX
                $('.email_profile_content form').on('submit', function(e) {
                    e.preventDefault(); // Prevent default form submission
                    
                    // Prepare form data
                    var form = $(this);
                    var formData = form.serialize(); // Serialize the form data
                    
                    // Show loading indicator (optional)
                    form.find('input[type="submit"]').val('Saving...').prop('disabled', true);

                    // Make AJAX request
                    $.ajax({
                        type: 'POST',
                        url: ajaxurl, // WordPress's AJAX handler URL
                        data: formData, // Form data
                        success: function(response) {
                            // Check if the update was successful
                            if (response.success) {
                                alert('Profile updated successfully!');
                            } else {
                                alert('Failed to update profile: ' + response.data.message);
                            }
                            // Reset submit button state
                            form.find('input[type="submit"]').val('Save Content').prop('disabled', false);
                        },
                        error: function(xhr, status, error) {
                            alert('An error occurred: ' + error);
                            // Reset submit button state
                            form.find('input[type="submit"]').val('Save Content').prop('disabled', false);
                        }
                    });
                });
            });

            jQuery(document).ready(function ($) {
                // Delete Section
                $('.delete-section-btn').on('click', function (e) {
                    e.preventDefault();
                    var sectionId = $(this).data('id');

                    if (confirm('Are you sure you want to delete this section?')) {
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'delete_section_option',
                                section_id: sectionId
                            },
                            success: function (response) {
                                if (response.success) {
                                    alert('Section deleted successfully.');
                                    location.reload(); // Reload the page after successful deletion
                                } else {
                                    alert(response.data);
                                }
                            }
                        });
                    }
                });

                $('.edit_section_btn').on('click', function() {
                    var sectionId = $(this).data('id');
                    var sectionName = $(this).data('section_type');
                    window.location.href = '/wp-admin/admin.php?page=settings_page&section_id=' + sectionId + '&section='+ sectionName;

                    <?php //$getSectionName = $_GET['section']; ?>
                });

                // Edit Section (this could involve showing a modal for content editing)
                /*$('.edit-section-btn').on('click', function (e) {
                    e.preventDefault();
                    var sectionId = $(this).data('id');
                    var sectionContent = prompt('Enter new section content:');

                    if (sectionContent !== null && sectionContent.trim() !== '') {
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'edit_section_option',
                                section_id: sectionId,
                                section_content: sectionContent
                            },
                            success: function (response) {
                                if (response.success) {
                                    alert('Section updated successfully.');
                                    location.reload(); // Reload the page after successful update
                                } else {
                                    alert(response.data);
                                }
                            }
                        });
                    }
                });*/
            });



jQuery(document).ready(function($) {
    hugerte.init({
        selector: '#custom_email_header, #custom_about_header, #custom_email_footer', // Apply TinyMCE to both emailHeader and emailFooter
        menubar: true,
        //toolbar: 'bold italic underline | alignleft aligncenter alignright | bullist numlist | forecolor backcolor | formatselect | image',
        //plugins: 'lists link textcolor colorpicker image',
        //plugins: 'accordion advlist anchor autolink autoresize autosave charmap code codesample directionality emoticons fullscreen help image insertdatetime link lists media nonbreaking pagebreak preview quickbars save searchreplace table template visualblocks visualchars wordcount',

        plugins: 'accordion advlist anchor autolink code codesample directionality emoticons fullscreen help image insertdatetime link lists media nonbreaking pagebreak preview quickbars save searchreplace table template visualblocks visualchars wordcount',

        //a_plugin_option: true,
        //a_configuration_option: 400,
        //height: 300
        // To make TinyMCE leaner, only include the plugins you need
            //plugins: '  code media table autolink emoticons image link lists help',

            // Define the toolbar
            // https://www.tiny.cloud/docs/tinymce/latest/toolbar-configuration-options/#basic-toolbar-options
            toolbar: 'undo redo | aidialog aishortcuts | styles | bold italic underline forecolor | link image emoticons | align | mergetags inserttemplate | spellcheckdialog a11ycheck | code removeformat | markdown | bullist numlist | formatselect ',

            // Make the toolbar sticky so it's visible as users scroll through the email
            // https://www.tiny.cloud/docs/tinymce/latest/menus-configuration-options/#toolbar_sticky
            toolbar_sticky: true,

            // Toggle the menubar off to get a leaner visual experience
            // https://www.tiny.cloud/docs/tinymce/latest/menus-configuration-options/
            menubar: false,

            // Set editor height
            height: 500,

            // Enable Multi-Root Editing by setting editable_root to false. This makes the
            // entire contents of the editor non-editable by default
            // https://www.tiny.cloud/docs/tinymce/latest/content-behavior-options/#editable_root
            editable_root: true,

            // Specify which class to use to identify the regions that are editable
            // https://www.tiny.cloud/docs/tinymce/latest/content-behavior-options/#editable_class
            //editable_class: 'tiny-editable',

            // Disable the element path in the status bar to avoid user confusion while
            // navigating the multiple editable regions inside the editor
            elementpath: true,

            // Disable the default "dotted line" visual aid for table borders, since this
            // email is a series of tables inside tables and the visual aid would be
            // distracting
            visual: false,

            // In emails we don't use targets for links so we hide the
            // target drop down in the link dialog
            // https://www.tiny.cloud/docs/tinymce/latest/link/#link_target_list
            link_target_list: true,
        
    });
});


        jQuery(document).ready(function($) {


            $('.open_this_profile').on('click', function(){
                // alert("profile");
                $(this).parents('.email_profile').toggleClass("open_profile");
            });
            
            $('.pt_cat_list > label input[type="checkbox"]').on('change', function(){
            //$('#select_pt input[type="checkbox"]').on('change', function(){
                
            //$('#selected_custom_post_type_category').on('change', function(){
                var getPostValue = $(this).val();
                console.log(getPostValue);

                //$('#select_pt_category div[data-pt_cat]').hide();
                //$('#select_pt_category div.pt_cat_list').removeClass("selected");
                
                //$('#select_pt_category div[data-pt_cat] input:not([data-cat_saved="db_cat_saved"])').prop('checked', false);
                //$('#select_pt_category div[data-pt_cat="'+getPostValue+'"]').show();
                if(this.checked) {
                    $('div.pt_cat_list[data-pt_cat_slug="'+getPostValue+'"] > input[value="'+getPostValue+'"]').prop('checked', true); 
                    $('#select_pt_category div.pt_cat_list[data-pt_cat_slug="'+getPostValue+'"]').addClass("selected");
                } else {
                    $('div.pt_cat_list[data-pt_cat_slug="'+getPostValue+'"] > input[value="'+getPostValue+'"]').prop('checked', false);
                    $('div[data-pt_cat="'+getPostValue+'"] label input[type="checkbox"]').prop('checked', false); 

                    
                    $('#select_pt_category div.pt_cat_list[data-pt_cat_slug="'+getPostValue+'"]').removeClass("selected");
                }
            });

            
        });
            </script>

    </div>
    <?php
}

?>