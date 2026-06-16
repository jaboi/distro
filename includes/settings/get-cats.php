<?php
// Ensure this file is being accessed through WordPress
if (!defined('ABSPATH')) {
    exit;
}

// Hook for handling the AJAX request
add_action('wp_ajax_ocm_get_categories', 'ocm_get_categories');

function ocm_get_categories() {
    // Verify nonce for security
    check_ajax_referer('ocm-ajax-nonce', 'nonce');

    $post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : '';

    if (!$post_type) {
        wp_send_json_error('Invalid post type.');
    }

    // Get taxonomies for the selected post type
    $taxonomies = get_object_taxonomies($post_type, 'objects');

    // Check if the 'category' taxonomy exists for this post type
    if (isset($taxonomies['category'])) {
        $categories = get_terms(array(
            'taxonomy' => 'category',
            'hide_empty' => false, // Set to true if you want to hide empty categories
        ));

        if (!empty($categories)) {
            $options = '<select name="post_categories[]" id="post_categories">';
            $options .= '<option value="">' . __('Select a category', 'textdomain') . '</option>';
            foreach ($categories as $category) {
                $options .= '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
            }
            $options .= '</select>';
            wp_send_json_success($options);
        }
    }

    wp_send_json_error('No categories found for this post type.');
}

?>