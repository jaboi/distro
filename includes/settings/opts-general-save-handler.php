<?php
// Ensure this file is being accessed through WordPress
if (!defined('ABSPATH')) {
    exit;
}

// Handle form submission
if (isset($_POST['save_general_settings'])) {
    if (!isset($_POST['general_settings_nonce']) || !wp_verify_nonce($_POST['general_settings_nonce'], 'save_general_settings')) {
        wp_die('Security check failed.');
    }
    if (!current_user_can('manage_options')) {
        wp_die('You do not have permission to change these settings.');
    }
    global $wpdb; // Use the WordPress database object

    // Table name (use the correct table name with your WP prefix)
    $table_name_options_general = $wpdb->prefix . 'general_options';

    // Get the selected post types (array of post types)
    $selected_post_types = isset($_POST['selected_custom_post_type_category']) ? array_map('sanitize_text_field', $_POST['selected_custom_post_type_category']) : array();

    // Get selected categories (array where keys are post types and values are arrays of categories)
    $categories_by_post_type = isset($_POST['categories']) ? $_POST['categories'] : array(); 

    // Ensure post types are selected
    if (!empty($selected_post_types)) {
        // 1. Delete ALL previous entries (this clears the table before new insertion)
        $wpdb->query("TRUNCATE TABLE $table_name_options_general");

        // 2. Loop through each selected post type and insert the associated categories
        foreach ($selected_post_types as $post_type) {
            if (isset($categories_by_post_type[$post_type])) {
                foreach ($categories_by_post_type[$post_type] as $category_id) {
                    // Insert each category for the corresponding post type
                    $wpdb->insert(
                        $table_name_options_general, // Your table name
                        array(
                            'post_type' => $post_type,
                            'cat_id'    => sanitize_text_field($category_id),
                            'active'    => 1 // Default active to 1
                        ),
                        array(
                            '%s', // post_type as string
                            '%s', // cat_id as string
                            '%d'  // active as integer
                        )
                    );
                }
            }
        }

        // Output success message with the saved post types
        echo '<div class="updated"><p>' . __('Settings saved for the selected post types and categories.', 'textdomain') . '</p></div>';
    } else {
        // Output error message if no post type was selected
        echo '<div class="error"><p>' . __('Please select at least one post type.', 'textdomain') . '</p></div>';
    }
}

?>