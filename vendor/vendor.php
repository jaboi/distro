<?php
// Ensure this file is being accessed through WordPress
if (!defined('ABSPATH')) {
    exit;
}

function ocm_enqueue_admin_styles() {

    // Enqueue jQuery (if not already included)
    wp_enqueue_script('jquery');
    
    // Enqueue DataTables CSS
    wp_enqueue_style(
        'datatables-css',
        'https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css',
        array(),
        '1.13.5'
    );

    // Enqueue DataTables JS
    wp_enqueue_script(
        'datatables-js',
        'https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js',
        array('jquery'),
        '1.13.5',
        true
    );

    // Enqueue Select2 CSS
    wp_enqueue_style(
        'select2-css', 
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');

    // Enqueue Select2 JS
    wp_enqueue_script(
        'select2-js', 
        'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', 
        array('jquery'), 
        null, 
        true
    );

    // Enqueue SweetAlert2 CSS
    wp_enqueue_style(
        'sweetalert2-css', 
        'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css', 
        array(), 
        '11.0.0'
    );

    // Enqueue SweetAlert2 JS
    wp_enqueue_script('sweetalert2-js', 
        'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js',
        array('jquery'), 
        '11.0.0', 
        true
    );

    
}

// Hook into WordPress to enqueue the styles and scripts
add_action('admin_enqueue_scripts', 'ocm_enqueue_admin_styles');
?>