<?php
function display_sender_profiles() {
    global $wpdb;
    $sender_profiles_table = $wpdb->prefix . 'sender_profiles';
    $section_options_table = $wpdb->prefix . 'section_options';

    // Fetch sender profiles
    $results = $wpdb->get_results("SELECT * FROM $sender_profiles_table", ARRAY_A);

    // Fetch sender profiles
    //$results = $wpdb->get_results("SELECT id, profile_name, sender_name, email_address FROM $sender_profiles_table", ARRAY_A);

    if ($results) {
        echo '<h2>' . __('Sender Profiles', 'textdomain') . '</h2>';
        echo '<div class="email_profiles">';

        foreach ($results as $row) {
            $profile_id = esc_attr($row['id']);
            $saved_header_id = esc_attr($row['header_id']);
            $saved_about_id = esc_attr($row['about_id']);
            $saved_footer_id = esc_attr($row['footer_id']);

            // Fetch section options for Header, About, and Footer
            $header_options = $wpdb->get_results($wpdb->prepare("SELECT id, section_content, section_name FROM $section_options_table WHERE section_type = %s", 'header'), ARRAY_A);
            $about_options = $wpdb->get_results($wpdb->prepare("SELECT id, section_content, section_name FROM $section_options_table WHERE section_type = %s", 'about'), ARRAY_A);
            $footer_options = $wpdb->get_results($wpdb->prepare("SELECT id, section_content, section_name FROM $section_options_table WHERE section_type = %s", 'footer'), ARRAY_A);

            // Display the profile with edit and delete options
            echo '<div class="email_profile">';
            echo '<div class="email_profile_name">' . esc_html($row['profile_name']) . '<div><span class="dashicons dashicons-edit edit_profile_btn" data-id="' . esc_attr($row['id']) . '"></span><span class="dashicons dashicons-trash delete_profile_btn" data-id="' . esc_attr($row['id']) . '"></span><span class="open_this_profile dashicons dashicons-arrow-down-alt2"></span></div></div>';
            
            // Display the profile details (sender name and email)
            echo '<div class="email_profile_details"><p>' . esc_html($row['sender_name']) . '</p>';
            echo '<p>' . esc_html($row['email_address']) . '</p></div>';

            // Form with Header, About, and Footer sections
            echo '<div class="email_profile_content">';
            echo '<div>Featured Image: <br><span style="margin-left:10px;">';
            if ( $row['featured_img_pos'] === "display-top" ){
                echo "Display At Top";
            }
            if ( $row['featured_img_pos'] === "display-below-headline" ){
                echo "Display Below Headline";
            }
            if ( $row['featured_img_pos'] === "email-attach" ){
                echo "Attach To Email";
            }
            if ( $row['featured_img_pos'] === "ignore" ){
                echo "Ignore";
            }
            echo '</span></div>';
            //echo '<span>'.esc_html($row['featured_img_pos']).'</span>';
            echo '<div>Font Color: <br><span style="display:inline-block; width:20px; height:20px; margin-left:10px; background-color:' . esc_attr($row['font_color']) . ';"></span>';
            echo esc_html($row['font_color']).'</div>';

            echo '<div>Link Color: <br><span style="display:inline-block; width:20px; height:20px; margin-left:10px; background-color:' . esc_attr($row['link_color']) . ';"></span>';
            echo esc_html($row['link_color']).'</div>';
            echo '<div>Font: <br><span style="margin-left:10px;">'.esc_html($row['font_opt']).'</span></div>';
            echo '<form method="post" name="profile_option_' . esc_html($row['id']) . '" id="profile_option_' . esc_html($row['id']) . '" action="'.esc_url( admin_url('admin-post.php') ).'">';

            wp_nonce_field('update_profile_action', 'update_profile_nonce');

            echo '<input type="hidden" name="action" value="update_sender_profile_ajax">';
            //echo '<input type="hidden" name="action" value="update_sender_profile_sections">';
            echo '<input type="hidden" name="update_profile_id" value="' . esc_attr($row['id']) . '">';

            // Header section radio buttons (use id as value)
            echo '<label data-sec_name="header_' . esc_attr($row['id']) . '"><b>Header</b></label>';
            /*if (!empty($header_options)) {
                foreach ($header_options as $header) {
                    $is_checked = $saved_header_id == $header['id'] ? 'checked' : '';
                    echo '<div>';
                    echo '<input type="radio" id="header_option_' .esc_attr($row['id']) ."_". esc_attr($header['id']) . '" id="header_content" value="' . esc_attr($header['id']) . '" name="header_content" value="' . esc_attr($header['id']) . '" ' . $is_checked . '>';
                    echo '<label for="header_option_' .esc_attr($row['id']) ."_". esc_attr($header['id']) . '">' . esc_html($header['section_name']) . '</label>';
                    echo '</div>';
                }
            } else {
                echo '<p>' . __('No header options found.', 'textdomain') . '</p>';
            }*/
            echo '<br>';

            // About section radio buttons (use id as value)
            echo '<label data-sec_name="about_' . esc_attr($row['id']) . '"><b>About</b></label>';
            /*if (!empty($about_options)) {
                foreach ($about_options as $about) {
                    $is_checked = $saved_about_id == $about['id'] ? 'checked' : '';
                    echo '<div>';
                    echo '<input type="radio" id="about_option_'  .esc_attr($row['id']) ."_". esc_attr($about['id']) . '" id="about_content" value="' . esc_attr($about['id']) . '" name="about_content" value="' . esc_attr($about['id']) . '"' . $is_checked . '>';
                    echo '<label for="about_option_'  .esc_attr($row['id']) ."_". esc_attr($about['id']) . '">' . esc_html($about['section_name']) . '</label>';
                    echo '</div>';
                }
            } else {
                echo '<p>' . __('No about options found.', 'textdomain') . '</p>';
            }*/
            echo '<br>';

            // Footer section radio buttons (use id as value)
            echo '<label data-sec_name="footer_' . esc_attr($row['id']) . '"><b>Footer</b></label>';
            /*if (!empty($footer_options)) {
                foreach ($footer_options as $footer) {
                    $is_checked = $saved_footer_id == $footer['id'] ? 'checked' : '';
                    echo '<div>';
                    echo '<input type="radio" id="footer_option_'  .esc_attr($row['id']) ."_". esc_attr($footer['id']) . '" id="footer_content" value="' . esc_attr($footer['id']) . '" name="footer_content" value="' . esc_attr($footer['id']) . '"' . $is_checked . '>';
                    echo '<label for="footer_option_'  .esc_attr($row['id']) ."_". esc_attr($footer['id']) . '">' . esc_html($footer['section_name']) . '</label>';
                    echo '</div>';
                }
            } else {
                echo '<p>' . __('No footer options found.', 'textdomain') . '</p>';
            }*/
            echo '<br>';

            // Submit button for updating the selected content
            echo '<input type="submit" class="btn btn-primary" value="Save Content">';
            echo '</form></div>';

            echo '</div>'; // End of profile
        }
        echo '</div>';
    } else {
        echo '<p>' . __('No sender profiles found.', 'textdomain') . '</p>';
    }
}
?>