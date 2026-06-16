<?php
// Ensure this file is being accessed through WordPress
if (!defined('ABSPATH')) {
    exit;
}

function ocm_enqueue_admin_scripts() {
    wp_enqueue_script('jquery');

    wp_enqueue_script(
        'send_to_sel_contacts', 
        plugins_url('../assets/js/send-press-release.js', __FILE__),
        array('jquery'), 
        '0.2', 
        true
    );
    wp_localize_script(
        'send_to_sel_contacts', 
        'send_to_sel_contacts_params', 
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('send_to_sel_contacts_nonce'),
        )
    );

    /*wp_enqueue_script(
        'ocm-gen-set-cat-script',
        plugins_url('../assets/js/gen-set-cat-script.js', __FILE__),
        //plugin_dir_url(__FILE__) . '../assets/js/gen-set-cat-script.js',
        array('jquery'),
        null,
        true
    );

    wp_localize_script(
        'ocm-gen-set-cat-script',
        'ocmAjax',
        array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('ocm-ajax-nonce')
        )
    );*/


    // Enqueue custom admin AJAX script
    wp_enqueue_script(
        'custom-admin-ajax',
        plugins_url('../assets/js/custom-admin-ajax.js', __FILE__),
        array('jquery'),
        '0.8.0',
        true
    );
    wp_localize_script(
        'custom-admin-ajax',
        'ajax_object',
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'send_to_contacts_nonce' => wp_create_nonce('send_to_contacts_nonce'),
            'send_to_orgs_nonce' => wp_create_nonce('send_to_orgs_nonce'),
        )
    );

    // Enqueue custom AJAX handling script (myplugin-ajax.js)
    wp_enqueue_script(
        'myplugin-ajax',
        plugin_dir_url(__FILE__) . '../assets/js/myplugin-ajax.js',
        array('jquery'),
        '0.5',
        true
    );
    wp_localize_script(
        'myplugin-ajax',
        'myplugin_ajax_object',
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'send_to_contacts_nonce' => wp_create_nonce('send_to_contacts_nonce'),
        )
    );

    wp_enqueue_style(
        'organization-contacts-manager-styles',
        plugins_url('../assets/css/styles.css', __FILE__),
        array(),
        '0.79.0',
        'all'
    );

    wp_enqueue_script(
        'organization-contacts-manager-js',
        plugins_url('../assets/js/main.js', __FILE__),
        array('jquery', 'datatables-js'),
        '0.69.0',
        true
    );

    wp_enqueue_script(
        'custom-select2-init', 
        plugins_url('../assets/js/custom-select2.js', __FILE__), 
        array('jquery', 'select2-js'), 
        '0.22.0', 
        true
    );

    if (isset($_GET['success']) && $_GET['success'] == 1) {
        wp_localize_script('sweetalert-custom-js', 'ocmSuccessMessage', 'Organization saved successfully!');
    }

    wp_enqueue_script(
        'ocm-custom-js', 
        plugins_url('../assets/js/sweetalert-custom.js', __FILE__), 
        array('jquery', 'sweetalert2-js'), 
        '0.26.0', 
        true
    );

    wp_enqueue_script(
        'hugerte-js', 
        'https://cdn.jsdelivr.net/npm/hugerte@1.0.2/hugerte.min.js', 
        array('jquery'), 
        '1.0', 
        true);
        
    wp_enqueue_style(
        'font-awesome-4.7.0',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css',
        array(), // No dependencies
        '4.7.0' // Version number
    );
    

    //add_action('admin_enqueue_scripts', 'ocm_enqueue_admin_scripts');
    //function ocm_enqueue_admin_scripts() {
    /*wp_enqueue_script(
        'ocm-gen-set-cat-script',
        plugin_dir_url(__FILE__) . '../assets/js/gen-set-cat-script.js',
        array('jquery'),
        '0.2.0',
        true
    );

    wp_localize_script(
        'ocm-gen-set-cat-script',
        'ocmAjax',
        array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('ocm-ajax-nonce')
        )
    );*/
    //}

    // Check if it's the page where your form is located (use your admin page slug)
    //if ($hook_suffix == 'settings_page') {
        // Enqueue the WordPress color picker script and style
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        
        // Enqueue your custom script to initialize the color picker
        wp_enqueue_script(
            'color-picker', 
            plugin_dir_url(__FILE__) . '../assets/js/color-picker-init.js', 
            array('wp-color-picker'), 
            '0.0.1', 
            true
        );
    //}

    
}
add_action('admin_enqueue_scripts', 'ocm_enqueue_admin_scripts');

?>