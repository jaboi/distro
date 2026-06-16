<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
// Hook to create the CSV upload handler
add_action('admin_post_upload_csv', 'handle_csv_upload');

// Function to handle CSV file upload
function handle_csv_upload() {
    // Check if the user has permissions to upload files
    if (!current_user_can('manage_options')) {
        wp_redirect(admin_url('admin.php?page=contact_page&error=You do not have permission to upload files.'));
        exit();
    }

    if (isset($_POST['upload_csv']) && !empty($_FILES['csv_file']['name'])) {
        $file = $_FILES['csv_file'];

        // Check for errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            wp_redirect(admin_url('admin.php?page=contact_page&error=File upload error: ' . $file['error']));
            exit();
        }

        // Check file type
        $file_type = wp_check_filetype(basename($file['name']));
        if ($file_type['ext'] !== 'csv') {
            wp_redirect(admin_url('admin.php?page=contact_page&error=Please upload a valid CSV file.'));
            exit();
        }

        // Read the CSV file
        $csv_data = array_map('str_getcsv', file($file['tmp_name']));

        // Prepare to insert data into the database
        global $wpdb;
        $table_name_contact = $wpdb->prefix . 'contacts'; // Adjust to your actual table name
        $inserted_rows = 0; // Counter for inserted rows

        // Validate CSV data and insert into database
        foreach ($csv_data as $row) {
            if (count($row) < 3) {
                continue; // Skip rows that do not have enough columns
            }

            $name = sanitize_text_field($row[0]);
            $email = sanitize_email($row[1]);
            $phone = sanitize_text_field($row[2]);
            //$organization_id = isset($row[3]) ? intval($row[3]) : null; // Assuming organization_id is optional

            // Insert data into the database
            $result = $wpdb->insert($table_name_contact, array(
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                //'organization_id' => $organization_id // Include organization_id in the insert
            ));

            if ($result !== false) {
                $inserted_rows++; // Increment the counter if the insert was successful
            } else {
                error_log('Failed to insert row: ' . print_r($row, true)); // Log error for debugging
            }
        }

        // Redirect with success message including the number of inserted rows
        wp_redirect(admin_url('admin.php?page=contact_page&success=1&inserted=' . $inserted_rows));
        exit();
    } else {
        wp_redirect(admin_url('admin.php?page=contact_page&error=No file uploaded.'));
        exit();
    }
}
?>