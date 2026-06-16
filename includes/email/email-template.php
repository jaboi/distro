<?php
// Ensure this file is being accessed through WordPress
if (!defined('ABSPATH')) {
    exit;
}

function get_email_template($post) {
    // Get the values from the settings
    $header_image_url = get_option('email_header_image', '');
    $header_content = get_option('custom_email_header', '');
    $footer_image_url = get_option('email_footer_image', '');
    $footer_content = get_option('custom_email_footer', '');
    $post_content = apply_filters('the_content', $post->post_content); // Apply content filters

    ob_start(); // Start output buffering
    ?>
    <div style="font-family: Arial, sans-serif; color: #333;">
        <?php if ($header_image_url): ?>
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="<?php echo esc_url($header_image_url); ?>" alt="Header Image" style="max-width: 100%; height: auto;">
        </div>
        <?php endif; ?>

        <?php if ($header_content): ?>
        <div id="custom_header_content">
            <?php echo wp_kses_post($header_content); ?>
        </div>
        <?php endif; ?>

        <div style="margin: 0 20px;">
            <h1 style="text-align: center;"><?php echo esc_html($post->post_title); ?></h1>
            <div style="margin: 20px 0;">
                <?php echo wp_kses_post($post_content); ?>
            </div>
        </div>

        <?php if ($footer_image_url): ?>
        <div style="text-align: center; margin-top: 20px;">
            <img src="<?php echo esc_url($footer_image_url); ?>" alt="Footer Image" style="max-width: 100%; height: auto;">
        </div>
        <?php endif; ?>

        <?php if ($footer_content): ?>
        <div id="custom_footer_content">
            <?php echo wp_kses_post($footer_content); ?>
        </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean(); // Get the content of the buffer and return it
}
?>