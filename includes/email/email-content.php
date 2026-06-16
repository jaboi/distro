<?php
/*if (!defined('ABSPATH')) {
    exit;
}*/
global $wpdb;
// Get the selected profile ID from POST
$profile_id = isset($_POST['profile_id']) ? intval($_POST['profile_id']) : 0;

// Retrieve the email address of the selected profile from the sender_profiles table
$table_name_sender_profiles = $wpdb->prefix . 'sender_profiles';
$profile_email = $wpdb->get_var($wpdb->prepare("SELECT email_address FROM $table_name_sender_profiles WHERE id = %d", $profile_id));
$profile_name = $wpdb->get_var($wpdb->prepare("SELECT profile_name FROM $table_name_sender_profiles WHERE id = %d", $profile_id));
$sender_name = $wpdb->get_var($wpdb->prepare("SELECT sender_name FROM $table_name_sender_profiles WHERE id = %d", $profile_id));
$img_pos = $wpdb->get_var($wpdb->prepare("SELECT featured_img_pos FROM $table_name_sender_profiles WHERE id = %d", $profile_id));
$font_color = $wpdb->get_var($wpdb->prepare("SELECT font_color FROM $table_name_sender_profiles WHERE id = %d", $profile_id));
$link_color = $wpdb->get_var($wpdb->prepare("SELECT link_color FROM $table_name_sender_profiles WHERE id = %d", $profile_id));
$font_opt = $wpdb->get_var($wpdb->prepare("SELECT font_opt FROM $table_name_sender_profiles WHERE id = %d", $profile_id));

// Prepare fallback fonts for each case
$font_family = '';
switch ($font_opt) {
    case 'Arial':
        $font_family = 'Arial, sans-serif';
        break;
    case 'Courier New':
        $font_family = '"Courier New", Courier, monospace';
        break;
    case 'Georgia':
        $font_family = 'Georgia, serif';
        break;
    case 'Helvetica':
        $font_family = 'Helvetica, Arial, sans-serif';
        break;
    case 'Tahoma':
        $font_family = 'Tahoma, Geneva, sans-serif';
        break;
    case 'Times New Roman':
        $font_family = '"Times New Roman", Times, serif';
        break;
    case 'Trebuchet MS':
        $font_family = '"Trebuchet MS", Helvetica, sans-serif';
        break;
    case 'Verdana':
        $font_family = 'Verdana, Geneva, sans-serif';
        break;
    default:
        // If no font is selected or the option is invalid, use a default font
        $font_family = 'Arial, sans-serif';
        break;
}


// Apply inline styles for font, link, and text color
$display_link_color = 'style="color:'.$link_color.';text-decoration: none;"';
$display_text_color = "color:".$font_color." !important;";
$text_font = 'style="font-family:'.$font_opt.';"';

if (!$profile_email) {
    wp_send_json_error('Invalid sender profile selected.');
    return;
}

// Get the selected profile's section IDs
$header_id = isset($_POST['emailHeader']) ? intval($_POST['emailHeader']) : 0;
$about_id = isset($_POST['emailAbout']) ? intval($_POST['emailAbout']) : 0;
$footer_id = isset($_POST['emailFooter']) ? intval($_POST['emailFooter']) : 0;

// Ensure the IDs are valid
/*if ($header_id <= 0 || $about_id <= 0 || $footer_id <= 0) {
    wp_send_json_error('Invalid profile section IDs');
 return;
}*/

// Query the section_options table to get the content for each section
$table_name_section_options = $wpdb->prefix . 'section_options';
$header_content = $wpdb->get_var($wpdb->prepare("SELECT section_content FROM $table_name_section_options WHERE id = %d", $header_id));
$about_content = $wpdb->get_var($wpdb->prepare("SELECT section_content FROM $table_name_section_options WHERE id = %d", $about_id));
$footer_content = $wpdb->get_var($wpdb->prepare("SELECT section_content FROM $table_name_section_options WHERE id = %d", $footer_id));

// Post ID for the press release (this would usually come from your form)
$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

// Ensure the post ID is valid
if ($post_id <= 0) {
    wp_send_json_error('Invalid post ID');
    return;
}

$post_title = get_the_title($post_id);
// Retrieve the post content (assuming the press release content is stored in the post)
$post_content = get_post_field('post_content', $post_id);
// Get the permalink (post URL)
$post_link = get_permalink($post_id);

$original_content = $post_content;
//$domain = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']; 
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$domain = $scheme . '://' . $_SERVER['HTTP_HOST'];

/*$new_content = preg_replace_callback(
    '#<a([^>]+)href=["\']([^"\']+)["\']#',
    function ($matches) use ($link_color, $domain) {
        $href = $matches[2];

        // If the href is relative (starts with '/'), prepend the domain
        if (strpos($href, '/') === 0) {
            $href = $domain . $href;
        }

        // Return the updated <a> tag with the style applied
        return '<a' . $matches[1] . ' href="' . $href . '" style="color:' . $link_color . ' !important;"';
    },
    $original_content
);*/
$new_content = preg_replace_callback(
    '#<a([^>]+)href=["\']([^"\']+)["\']#',
    function ($matches) use ($link_color, $domain) {
        $href = $matches[2];

        // If the href is relative (starts with '/'), prepend the domain
        if (strpos($href, '/') === 0) {
            $href = $domain . $href;
        }

        // Return the updated <a> tag with the style applied
        return '<a' . $matches[1] . ' href="' . $href . '" style="color:' . $link_color . ' !important;"';
    },
    $original_content
);

// Automatically add <p> tags to paragraphs using wpautop()
$wrapped_content = wpautop($original_content);

// Define the text color and font family
$display_text_color = 'color:' . $font_color . ';';
$text_font = 'font-family:' . $font_opt . ';';

// Apply text color and font family to all tags except <a>
$new_content = preg_replace_callback(
    // Match opening HTML tags (excluding <a>) with or without attributes
    '#<((?!a\b)[a-z0-9]+)([^>]*)>#i', 
    function ($matches) use ($display_text_color, $text_font) {
        // Check if the tag does not already have a color or font-family style
        if (strpos($matches[2], 'color') === false || strpos($matches[2], 'font-family') === false) {
            // If no style attribute is present, add both color and font-family
            if (strpos($matches[2], 'style=') === false) {
                return '<' . $matches[1] . $matches[2] . ' style="' . $display_text_color . ' ' . $text_font . '">';
            } else {
                // Append the color and font-family to the existing style attribute
                $updated_style = $matches[2];
                if (strpos($matches[2], 'color') === false) {
                    $updated_style = preg_replace('/style=["\']([^"\']*)["\']/', 'style="$1 ' . $display_text_color . '"', $matches[0]);
                }
                if (strpos($matches[2], 'font-family') === false) {
                    $updated_style = preg_replace('/style=["\']([^"\']*)["\']/', 'style="$1 ' . $text_font . '"', $updated_style);
                }
                return $updated_style;
            }
        }
        // If both color and font-family exist, return the tag unchanged
        return $matches[0];
    },
    $wrapped_content
);

$featured_img_url = get_the_post_thumbnail_url($post_id, 'full');
//$featured_image_url = wp_get_attachment_url($featured_image_id); // Get the URL
$featured_img = $featured_img_url ? '<img src="' . esc_url($featured_img_url) . '" alt="' . esc_attr($post_title) . '" style="max-width:100%; height:auto;"/>' : '';

if ( $img_pos === "display-top" ) {
    $emailBody = $featured_img . 
             "<h1 style='font-family:".$font_family.";'><a href='" . esc_url($post_link) . "' " . $display_link_color . ">" . esc_html($post_title) . "</a></h1>" . 
             "\n\n" . "" . wp_kses_post($new_content) . "";
}
if ( $img_pos === "display-below-headline" ) {
    $emailBody = "<h1 style='font-family:".$font_family.";'><a href='" . esc_url($post_link) . "' " . $display_link_color . ">" . esc_html($post_title) . "</a></h1>" . 
             "\n\n" . $featured_img . 
             "\n\n" . "" . wp_kses_post($new_content) . "";
}
if ( $img_pos === "ignore" ) {
    $emailBody ="<h1 style='font-family:".$font_family.";'><a href='" . esc_url($post_link) . "' " . $display_link_color . ">" . esc_html($post_title) . "</a></h1>" . 
             "\n\n" . "" . wp_kses_post($new_content) . "";
}
if ($img_pos === "email-attach") {
    // Define recipient and subject
    $email = $profile_email; // Retrieved earlier
    $email_subject = 'Press Release: ' . $post_title;

    // Get the featured image URL
    $featured_image_id = get_post_thumbnail_id($post_id);
    $featured_image_url = wp_get_attachment_url($featured_image_id);

    if ($featured_image_url) {
        // Get upload directory
        $upload_dir = wp_upload_dir();
        $image_name = basename($featured_image_url);
        $image_path = $upload_dir['path'] . '/' . $image_name;

        // Download image using wp_remote_get (safer than @copy)
        $response = wp_remote_get($featured_image_url, array('timeout' => 15));
        if (!is_wp_error($response)) {
            $image_data = wp_remote_retrieve_body($response);
            if (!empty($image_data)) {
                file_put_contents($image_path, $image_data);

                // Prepare the email content
                $emailBody = "<h1 style='font-family:{$font_family};'>
                                <a href='" . esc_url($post_link) . "' $display_link_color>" . esc_html($post_title) . "</a>
                              </h1>" .
                             wp_kses_post($new_content);

                $headers = array('Content-Type: text/html; charset=UTF-8');
                $attachments = array($image_path);

                $sent = wp_mail($email, $email_subject, $emailBody, $headers, $attachments);

                if ($sent) {
                    error_log('Email sent with attachment.');
                } else {
                    error_log('Failed to send email.');
                }

                // Delete temp image
                unlink($image_path);
            } else {
                error_log('Failed to retrieve image data.');
            }
        } else {
            error_log('Image fetch error: ' . $response->get_error_message());
        }
    } else {
        error_log('No featured image found.');

        // Fallback email (no attachment)
        $emailBody = "<h1 style='font-family:{$font_family};'>
                        <a href='" . esc_url($post_link) . "' $display_link_color>" . esc_html($post_title) . "</a>
                      </h1>" .
                     wp_kses_post($new_content);

        $headers = array('Content-Type: text/html; charset=UTF-8');

        $sent = wp_mail($email, $email_subject, $emailBody, $headers);

        if ($sent) {
            error_log('✅ Email sent with attachment.');
        } else {
            error_log('❌ wp_mail failed. Check server mail settings or headers.');
            error_log('wp_mail error: ' . print_r(error_get_last(), true));
        }

    }
}

/*if ($img_pos === "email-attach") {
    // Get the featured image URL
    $featured_image_id = get_post_thumbnail_id($post_id); // Get the featured image ID
    $featured_image_url = wp_get_attachment_url($featured_image_id); // Get the URL

    if ($featured_image_url) {
        // Get WordPress upload directory
        $upload_dir = wp_upload_dir(); 
        $image_name = basename($featured_image_url); // Get the image file name
        $image_path = $upload_dir['path'] . '/' . $image_name; // Local path to save image

        // Try to copy/download the image from the URL to the local server
        if (@copy($featured_image_url, $image_path)) {
            error_log("Image successfully copied to: " . $image_path);

            // Log the path of the file that will be attached to the email
            error_log("File to be attached: " . $image_path);

            // Prepare the email body (without embedding the image in the body)
            //$emailBody = $header_content . "\n\n" . 
            //             "<h1><a href='" . esc_url($post_link) . "' " . $display_link_color . ">" . esc_html($post_title) . "</a></h1>" . 
            //             "\n\n" . "<div " . $display_text_color . ">" . wp_kses_post($post_content) . "</div>" . 
            //             "\n\n" . wp_kses_post($about_content) . "\n\n" . wp_kses_post($footer_content);
            
            $emailBody ="<h1 style='font-family:".$font_family.";'><a href='" . esc_url($post_link) . "' " . $display_link_color . ">" . esc_html($post_title) . "</a></h1>" . 
             "\n\n" . "" . wp_kses_post($new_content) . "";

            // Prepare email headers
            $headers = array('Content-Type: text/html; charset=UTF-8');

            // Prepare attachments
            $attachments = array($image_path); // Attach the downloaded image

            // Log the attachments array for debugging
            error_log("Attachments: " . print_r($attachments, true));

            // Send the email with attachment
            $sent = wp_mail($email, $email_subject, $emailBody, $headers, $attachments);

            // Check if the email was sent successfully
            if ($sent) {
                error_log('Email sent with attachment.');
            } else {
                error_log('Failed to send email.');
            }

            // Optional: Remove the temporarily downloaded image
            unlink($image_path); // Delete the local file after sending the email
        } else {
            error_log('Failed to copy the image from ' . $featured_image_url . ' to ' . $image_path);
        }
    } else {
        error_log('No featured image found.');
        // Fallback: Send the email without an attachment
        
        $emailBody ="<h1 style='font-family:".$font_family.";'><a href='" . esc_url($post_link) . "' " . $display_link_color . ">" . esc_html($post_title) . "</a></h1>" . 
             "\n\n" . "" . wp_kses_post($new_content) . "";
                     //"\n\n" . wp_kses_post($about_content) . "\n\n" . wp_kses_post($footer_content);

        // Send the email without attachments
        $sent = wp_mail($email, $email_subject, $emailBody, $headers);

        if ($sent) {
            error_log('Email sent without attachment.');
        } else {
            error_log('Failed to send email without attachment.');
        }
    }
}*/

/*if (isset($_POST['btn_action']) == "emlDownload" ){
    echo $emailBody; 
}*/
    
?>