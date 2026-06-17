<?php
// Prevent direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function plugin_activation() {
    global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Include the upgrade library for dbDelta
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // Table names
        $table_name_org = $wpdb->prefix . 'organizations';
        $table_name_contact = $wpdb->prefix . 'contacts';
        $table_name_groups = $wpdb->prefix . 'groups';
        $table_name_group_taxonomy = $wpdb->prefix . 'group_taxonomy';
        $table_name_group_relationships = $wpdb->prefix . 'group_relationships';
        $table_name_options_general = $wpdb->prefix . 'general_options';
        $table_name_sender_profiles = $wpdb->prefix . 'sender_profiles';
        $table_name_section_options = $wpdb->prefix . 'section_options';

        // SQL to create groups table
        $sql3 = "CREATE TABLE $table_name_groups (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            group_name VARCHAR(255) NOT NULL,
            description TEXT,
            PRIMARY KEY (id)
        ) $charset_collate;";
        dbDelta($sql3);

        // Log any error from dbDelta
        if ($wpdb->last_error) {
            error_log('Error creating groups table: ' . $wpdb->last_error);
        }

        // SQL to create organizations table
        $sql1 = "CREATE TABLE $table_name_org (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            address TEXT NOT NULL,
            contact_email VARCHAR(255),
            group_id mediumint(9) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY group_id (group_id),
            FOREIGN KEY (group_id) REFERENCES $table_name_groups(id) ON DELETE SET NULL
        ) $charset_collate;";
        dbDelta($sql1);

        // Log any error from dbDelta
        if ($wpdb->last_error) {
            error_log('Error creating organizations table: ' . $wpdb->last_error);
        }

        // SQL to create contacts table
        $sql2 = "CREATE TABLE $table_name_contact (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(255),
            organization_id mediumint(9) DEFAULT NULL,
            PRIMARY KEY (id),
            KEY organization_id (organization_id),
            FOREIGN KEY (organization_id) REFERENCES $table_name_org(id) ON DELETE CASCADE
        ) $charset_collate;";
        dbDelta($sql2);

        // Log any error from dbDelta
        if ($wpdb->last_error) {
            error_log('Error creating contacts table: ' . $wpdb->last_error);
        }

        // SQL to create group_taxonomy table
        $sql4 = "CREATE TABLE $table_name_group_taxonomy (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            groups_id mediumint(9) NOT NULL,
            group_type VARCHAR(255) NOT NULL,
            PRIMARY KEY (id),
            KEY groups_id (groups_id),
            FOREIGN KEY (groups_id) REFERENCES $table_name_groups(id) ON DELETE CASCADE
        ) $charset_collate;";
        dbDelta($sql4);

        // Log any error from dbDelta
        if ($wpdb->last_error) {
            error_log('Error creating group_taxonomy table: ' . $wpdb->last_error);
        }

        // SQL to create group_relationships table
        $sql5 = "CREATE TABLE $table_name_group_relationships (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            object_id mediumint(9) NOT NULL,
            group_taxonomy_id mediumint(9) NOT NULL,
            PRIMARY KEY (id),
            KEY group_taxonomy_id (group_taxonomy_id),
            FOREIGN KEY (group_taxonomy_id) REFERENCES $table_name_group_taxonomy(id) ON DELETE CASCADE
        ) $charset_collate;";
        dbDelta($sql5);

        // SQL to create general_options table
        $sql6 = "CREATE TABLE $table_name_options_general (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            post_type VARCHAR(255) NOT NULL,
            cat_id VARCHAR(255) NOT NULL,
            active mediumint(9) NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";
        dbDelta($sql6);

        $sql8 = "CREATE TABLE $table_name_section_options (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            section_type VARCHAR(255) NOT NULL,
            section_name VARCHAR(255) NOT NULL,
            section_content TEXT NOT NULL, -- Use TEXT for larger HTML content
            PRIMARY KEY (id)
        ) $charset_collate;";
        dbDelta($sql8);

        $sql7 = "CREATE TABLE $table_name_sender_profiles (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            profile_name VARCHAR(255) NOT NULL,
            sender_name VARCHAR(255) NOT NULL,
            email_address VARCHAR(255) NOT NULL,
            featured_img_pos VARCHAR(255) NOT NULL,
            font_color VARCHAR(255) NOT NULL,
            link_color VARCHAR(255) NOT NULL,
            font_opt VARCHAR(255) NOT NULL,
            header_id mediumint(9) DEFAULT NULL,
            about_id mediumint(9) DEFAULT NULL,
            footer_id mediumint(9) DEFAULT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";
        dbDelta($sql7);

        

        // Log any error from dbDelta
        if ($wpdb->last_error) {
            error_log('Error creating group_relationships table: ' . $wpdb->last_error);
        }
}
?>