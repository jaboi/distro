jQuery(document).ready(function($) {
    $('#selected_custom_post_type').change(function() {
        alert("Post type selected"); // Debugging

        var selectedPostType = $(this).val();
        console.log("Selected post type:", selectedPostType); // Debugging
        console.log(ocmAjax.nonce); 
        console.log(ocmAjax.ajaxurl);

        $.ajax({

            url: ocmAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'ocm_get_categories',
                post_type: selectedPostType,
                nonce: ocmAjax.nonce
            },

            success: function(response) {
                console.log(response); // Debugging: Check the response

                if (response.success) {
                    $('#post_type_category').html(response.data);
                    console.log("Categories loaded successfully");
                } else {
                    alert('No categories found for this post type.');
                    console.log("No categories found");
                }
            },
            error: function(xhr, status, error) {
                console.log("AJAX error:", status, error); // Debugging: For AJAX failure
            }
        });
    });
});
