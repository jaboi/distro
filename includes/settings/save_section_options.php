<?php
add_action('wp_ajax_save_section_options', 'save_section_options');
function save_section_options() {
    // Verify nonce for security
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'save_section_nonce_action')) {
        wp_send_json_error(array('message' => 'Invalid security check.'));
        wp_die();
    }

    // Check if section_type and section_content are available
    if (!isset($_POST['section_type']) || !isset($_POST['section_content']) || !isset($_POST['section_name'])) {
        wp_send_json_error(array('message' => 'Missing required fields.'));
        wp_die();
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'section_options';

    // Sanitize inputs
    $section_id = sanitize_text_field($_POST['id']);
    $section_type = sanitize_text_field($_POST['section_type']);
    $section_name = sanitize_text_field($_POST['section_name']);
    $section_content = wp_kses_post($_POST['section_content']); // Allow safe HTML content

    // Check if the section_type already exists
    $existing_entry = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %s", $section_id));

    if ($existing_entry) {
        // Update the existing entry
        $updated = $wpdb->update(
            $table_name,
            array(
                'id' => $section_id,
            ),
            array('section_type' => $section_type),
            array('%s'),
            array('%s'),
            array('%s')
        );

        if ($updated !== false) {
            wp_send_json_success();
        } else {
            wp_send_json_error(array('message' => 'Failed to update the content.'));
        }
    } else {
        // Insert a new entry if it doesn't exist
        $inserted = $wpdb->insert(
            $table_name,
            array(
                'section_type' => $section_type,
                'section_name' => $section_name,
                'section_content' => $section_content,
            ),
            array('%s', '%s', '%s')
        );

        if ($inserted) {
            wp_send_json_success();
        } else {
            wp_send_json_error(array('message' => 'Failed to save the content.'));
        }
    }

    wp_die();
}
?>