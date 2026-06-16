<?php
// Ensure that this file is being accessed through WordPress.
if (!defined('ABSPATH')) {
    exit;
}
function media_manager_admin_menu() {
    add_menu_page(
        __( 'Media Directory', 'textdomain' ),
        __( 'Media Directory', 'textdomain' ),
        'manage_options',
        'media_manager',
        'render_manager_page',
        'dashicons-admin-generic',
        6
    );
    add_submenu_page(
        'media_manager',
        __( 'Press Release', 'textdomain' ),
        __( 'Press Release', 'textdomain' ),
        'manage_options',
        'press_release',
        'render_press_release_page'
    );

    add_submenu_page(
        'media_manager',
        __( 'Organizations', 'textdomain' ),
        __( 'Organizations', 'textdomain' ),
        'manage_options',
        'organization_page',
        'render_organizations_page'
    );

    add_submenu_page(
        'media_manager',
        __( 'Contacts', 'textdomain' ),
        __( 'Contacts', 'textdomain' ),
        'manage_options',
        'contact_page',
        'render_contacts_page'
    );

    add_submenu_page(
        'media_manager',
        __( 'Groups', 'textdomain' ),
        __( 'Groups', 'textdomain' ),
        'manage_options',
        'group_page',
        'render_group_page'
    );

    add_submenu_page(
        'media_manager',
        __( 'Settings', 'textdomain' ),
        __( 'Settings', 'textdomain' ),
        'manage_options',
        'settings_page',
        'render_settings_page' 
    );

    /*add_submenu_page(
        'media_manager',
        __( 'Add New Organization', 'textdomain' ),
        __( 'Add New Organization', 'textdomain' ),
        'manage_options',
        'add_new_organization',
        'render_add_new_organization_page'
    );*/

    /*add_submenu_page(
        'media_manager',
        __( 'Add New Contact', 'textdomain' ),
        __( 'Add New Contact', 'textdomain' ),
        'manage_options',
        'add_new_contact',
        'render_add_new_contact_page'
    );*/

    /*add_submenu_page(
        'media_manager',
        __( 'Add New Group', 'textdomain' ),
        __( 'Add New Group', 'textdomain' ),
        'manage_options',
        'add_group_page',
        'render_add_group_page'
    );*/

    /*add_submenu_page(
        null, // No parent menu, making this a hidden page
        __( 'View Contacts', 'textdomain' ), // Page title
        __( 'View Contacts', 'textdomain' ), // Menu title (won't be shown)
        'manage_options', // Capability required to access this page
        'view_contacts_page', // Menu slug
        array($this, 'view_contacts_page') // Callback function to display the page
    );*/
}

// Hook into WordPress to add the admin menu
add_action('admin_menu', 'media_manager_admin_menu');

?>