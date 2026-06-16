<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


// Function to display the CSV upload form
function contact_csv_upload() {
    ?>
    <div class="wrap">
        <h1>Upload CSV File</h1>

        <?php if (isset($_GET['success'])): ?>
            <div class="updated"><p>CSV file uploaded successfully!</p></div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="error"><p>Error: <?php echo esc_html($_GET['error']); ?></p></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="upload_csv"> <!-- Specify action -->
            <input type="file" name="csv_file" accept=".csv" required>
            <input type="submit" name="upload_csv" value="Upload CSV" class="button button-primary">
        </form>
    </div>
    <?php
}

?>