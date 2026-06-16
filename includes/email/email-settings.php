<?php
// Ensure this file is being accessed through WordPress
if (!defined('ABSPATH')) {
    exit;
}

function ocm_register_email_settings() {
    register_setting('email_settings_group', 'email_from_name');
    register_setting('email_settings_group', 'email_from_address');
    register_setting('email_settings_group', 'email_header_image');
    register_setting('email_settings_group', 'email_footer_image');
    register_setting('email_settings_group', 'custom_email_header');
    register_setting('email_settings_group', 'custom_email_header_2');
    register_setting('email_settings_group', 'custom_email_footer');

    register_setting('email_settings_group', 'selected_custom_post_type_category');

    /*add_settings_section(
        'email_settings_section',
        __('Customize Email Settings', 'textdomain'),
        null,
        'email-settings'
    );*/

    add_settings_field(
        'email_from_name',
        //__('Email From Name', 'textdomain'),
        'ocm_render_email_from_name_field',
        'email-settings',
        'email_settings_section'
    );

    add_settings_field(
        'email_from_address',
        //__('Email From Address', 'textdomain'),
        'ocm_render_email_from_address_field',
        'email-settings',
        'email_settings_section'
    );

    add_settings_field(
        'email_header_image',
        //__('Email Header Image URL', 'textdomain'),
        'ocm_render_email_header_image_field',
        'email-settings',
        'email_settings_section'
    );

    add_settings_field(
        'email_footer_image',
        //__('Email Footer Image URL', 'textdomain'),
        'ocm_render_email_footer_image_field',
        'email-settings',
        'email_settings_section'
    );

    add_settings_field(
        'custom_email_header',
        //__('Custom Email Header', 'textdomain'),
        'ocm_render_custom_email_header_field',
        'email-settings',
        'email_settings_section'
    );

    add_settings_field(
        'custom_email_header_2',
        //__('Custom Email Header 2', 'textdomain'),
        'ocm_render_custom_email_header_2_field',
        'email-settings',
        'email_settings_section'
    );
    
    add_settings_field(
        'custom_email_footer',
        //__('Custom Email Footer', 'textdomain'),
        'ocm_render_custom_email_footer_field',
        'email-settings',
        'email_settings_section'
    );

    add_settings_field(
        'selected_custom_post_type_category',
        __('Select Post Type & Category', 'textdomain'),
        'ocm_render_custom_post_type_category_select_field',
        'email-settings',
        'email_settings_section'
    );
}

// Hook the function to the admin_init action
add_action('admin_init', 'ocm_register_email_settings');

// Render input fields
function ocm_render_email_from_name_field() {
    /*$value = get_option('email_from_name', '');
    echo '<div class="test">';
    echo '<input type="text" name="email_from_name" value="' . esc_attr($value) . '" class="regular-text">';
    echo '</div>';*/
}

function ocm_render_email_from_address_field() {
    /*$value = get_option('email_from_address', '');
    echo '<input type="email" name="email_from_address" value="' . esc_attr($value) . '" class="regular-text">';*/
}

function ocm_render_email_header_image_field() {
    /*$value = get_option('email_header_image', '');
    ?>
    <input type="text" id="email_header_image" name="email_header_image" value="<?php echo esc_url($value); ?>" class="regular-text">
    <input type="button" id="upload_header_image_button" class="button" value="<?php _e('Select Image', 'textdomain'); ?>">
    <script>
        jQuery(document).ready(function($) {
            var custom_uploader;

            $('#upload_header_image_button').click(function(e) {
                e.preventDefault();

                // If the uploader object has already been created, reopen the dialog
                if (custom_uploader) {
                    custom_uploader.open();
                    return;
                }

                // Extend the wp.media object
                custom_uploader = wp.media.frames.file_frame = wp.media({
                    title: '<?php _e('Select Image', 'textdomain'); ?>',
                    button: {
                        text: '<?php _e('Use Image', 'textdomain'); ?>'
                    },
                    multiple: false
                });

                // When an image is selected in the media library, run a callback
                custom_uploader.on('select', function() {
                    // Get the selected attachment
                    var attachment = custom_uploader.state().get('selection').first().toJSON();

                    // Set the image URL in the input field
                    $('#email_header_image').val(attachment.url);
                });

                // Open the media library frame
                custom_uploader.open();
            });
        });
    </script>
    <?php*/
}


function ocm_render_email_footer_image_field() {
    /*$image_url = get_option('email_footer_image', '');
    ?>
    <input type="text" id="email_footer_image" name="email_footer_image" value="<?php echo esc_url($image_url); ?>" style="width: 75%;" />
    <input type="button" id="upload_image_button" class="button" value="<?php _e('Select Image', 'textdomain'); ?>" />
    <script>
        jQuery(document).ready(function($) {
            var custom_uploader;

            $('#upload_image_button').click(function(e) {
                e.preventDefault();

                if (custom_uploader) {
                    custom_uploader.open();
                    return;
                }

                custom_uploader = wp.media.frames.file_frame = wp.media({
                    title: '<?php _e('Select Image', 'textdomain'); ?>',
                    button: {
                        text: '<?php _e('Use Image', 'textdomain'); ?>'
                    },
                    multiple: false
                });

                custom_uploader.on('select', function() {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    $('#email_footer_image').val(attachment.url);
                });

                custom_uploader.open();
            });
        });
    </script>
    <?php*/
}

function ocm_render_custom_email_header_field() {
    //$header_content = get_option('custom_email_header', '');
    //wp_editor($header_content, 'custom_email_header', array('textarea_name' => 'custom_email_header'));
}

function ocm_render_custom_email_header_2_field() {
    //$header_content_2 = get_option('custom_email_header_2', '');
    //wp_editor($header_content_2, 'custom_email_header_2', array('textarea_name' => 'custom_email_header_2'));
}

function ocm_render_custom_email_footer_field() {
    //$footer_content = get_option('custom_email_footer', '');
    //wp_editor($footer_content, 'custom_email_footer', array('textarea_name' => 'custom_email_footer'));
}
function ocm_render_custom_post_type_category_select_field() {
    // Fetch built-in and custom post types (public ones)
    $args = array(
        'public' => true // Fetch both custom and built-in post types
    );
    $post_types = get_post_types($args, 'objects');

    // Get the saved option value
    $selected_post_type_category = get_option('selected_custom_post_type_category', '');

    // Create the select dropdown
    echo '<select name="selected_custom_post_type_category" id="selected_custom_post_type_category">';
    echo '<option value="">' . __('Select a post type or category', 'textdomain') . '</option>';

    // Loop through each post type
    foreach ($post_types as $post_type) {
        // Add the post type as an option
        echo '<option value="' . esc_attr($post_type->name) . '" ' . selected($selected_post_type_category, $post_type->name, false) . '>';
        echo esc_html($post_type->labels->name);
        echo '</option>';

        // Get categories (terms) for this post type, if it has any
        $taxonomies = get_object_taxonomies($post_type->name, 'objects');

        // Check if the post type supports categories (or other taxonomies)
        if (isset($taxonomies['category'])) {
            // Fetch all categories under this post type
            $categories = get_terms(array(
                'taxonomy'   => 'category',
                'hide_empty' => false // Show all categories, even if empty
            ));

            // Loop through categories and add them as sub-options
            foreach ($categories as $category) {
                // Indent categories for better UX
                echo '<option value="' . esc_attr($post_type->name . '|' . $category->term_id) . '" ' . selected($selected_post_type_category, $post_type->name . '|' . $category->term_id, false) . '>';
                echo '&nbsp;&nbsp;&nbsp;&nbsp; - ' . esc_html($category->name);
                echo '</option>';
            }
        }
    }

    echo '</select>';
}



?>