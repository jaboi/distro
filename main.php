<?php
/*
Plugin Name: Organization and Contacts Manager
Description: A plugin to manage organizations and their contacts.
Version: 0.50.27
Author: KeyToClick
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// run sql code when plugin is activated
require_once plugin_dir_path( __FILE__ ) . 'includes/activation-handler.php';

require_once plugin_dir_path( __FILE__ ) . 'includes/admin-menu.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/admin-style-script.php';

require_once plugin_dir_path( __FILE__ ) . 'vendor/vendor.php';

// Register the activation hook
register_activation_hook(__FILE__, 'plugin_activation');

//require_once plugin_dir_path(__FILE__) . 'lib/CssInliner.php';
// Use the namespace provided by Emogrifier
//use Pelago\Emogrifier\CssInliner;

require_once plugin_dir_path( __FILE__ ) . 'includes/dashboard-display.php';


// add new organization form
require_once plugin_dir_path(__FILE__) . 'includes/organization/organization-display.php';
// add new organization form
require_once plugin_dir_path(__FILE__) . 'includes/organization/organization-add-form.php';
// edit organization form
require_once plugin_dir_path(__FILE__) . 'includes/organization/organization-edit-form.php';
// add organization handler file
require_once plugin_dir_path( __FILE__ ) . 'includes/organization/organization-save-handler.php';
// edit organization handler file
require_once plugin_dir_path( __FILE__ ) . 'includes/organization/organization-edit-handler.php';
// delete organization handler file
require_once plugin_dir_path( __FILE__ ) . 'includes/organization/organization-delete-handler.php';

// display contact page
require_once plugin_dir_path(__FILE__) . 'includes/contact/contact-display.php';
// add contact form
require_once plugin_dir_path(__FILE__) . 'includes/contact/contact-add-form.php';
// add contact handler file
require_once plugin_dir_path(__FILE__) . 'includes/contact/contact-save-handler.php';
// edit contact handler file
require_once plugin_dir_path(__FILE__) . 'includes/contact/contact-edit-handler.php';
// delete contact handler file
require_once plugin_dir_path(__FILE__) . 'includes/contact/contact-delete-handler.php';

require_once plugin_dir_path(__FILE__) . 'includes/contact/contact-csv-upload-handler.php';

// display groups and edit group form
include_once plugin_dir_path(__FILE__) . 'includes/group/group-display.php';
// add group handler file
require_once plugin_dir_path(__FILE__) . 'includes/group/group-save-handler.php';
// edit group handler file
require_once plugin_dir_path(__FILE__) . 'includes/group/group-edit-handler.php';
// delete group handler file
require_once plugin_dir_path(__FILE__) . 'includes/group/group-delete-handler.php';
// add group form
require_once plugin_dir_path(__FILE__) . 'includes/group/group-add-form.php';


require_once plugin_dir_path( __FILE__ ) . 'includes/press-release-display.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/press-release/email-preview-handler.php';
//require_once plugin_dir_path( __FILE__ ) . 'includes/frontend-send-btn.php';

require_once plugin_dir_path(__FILE__) . 'includes/settings-display.php';

require_once plugin_dir_path(__FILE__) . 'includes/email/email-settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/email/email-template.php';
require_once plugin_dir_path(__FILE__) . 'includes/email/email-download.php';
require_once plugin_dir_path(__FILE__) . 'includes/email/send-to-contacts.php';
require_once plugin_dir_path(__FILE__) . 'includes/email/send-to-orgs.php';

//require_once plugin_dir_path(__FILE__) . 'includes/frontend-send-btn.php';
//require_once plugin_dir_path(__FILE__) . 'includes/settings/opts-general-save-handler.php';

//require_once(plugin_dir_path(__FILE__) . 'lib/PHPMailer/Exception.php');
//require_once(plugin_dir_path(__FILE__) . 'lib/PHPMailer/PHPMailer.php');
//require_once(plugin_dir_path(__FILE__) . 'lib/PHPMailer/SMTP.php');

function ocm_migrate_sender_profiles_nullable() {
    if (get_option('ocm_migration_sender_profiles_nullable')) {
        return;
    }
    global $wpdb;
    $table = $wpdb->prefix . 'sender_profiles';
    $wpdb->query("ALTER TABLE $table
        MODIFY COLUMN header_id mediumint(9) DEFAULT NULL,
        MODIFY COLUMN about_id mediumint(9) DEFAULT NULL,
        MODIFY COLUMN footer_id mediumint(9) DEFAULT NULL");
    // Drop FK constraints if they exist
    $constraints = $wpdb->get_results("
        SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = '{$table}'
          AND CONSTRAINT_TYPE = 'FOREIGN KEY'
    ");
    foreach ($constraints as $constraint) {
        $wpdb->query("ALTER TABLE $table DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
    }
    update_option('ocm_migration_sender_profiles_nullable', true);
}
add_action('admin_init', 'ocm_migrate_sender_profiles_nullable');

class Organization_Contacts_Manager {

}

new Organization_Contacts_Manager();
?>