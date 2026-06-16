<?php
// Ensure that this file is being accessed through WordPress.
if (!defined('ABSPATH')) {
    exit;
}
//ob_start();
function pr_modal_option() {
echo '<div id="sendModal" class="modal" style="display:none;">';
echo '    <div class="modal-content">';
echo '        <span class="close">&times;</span>';
echo '        <h2>Create Press Release Email</h2>';
echo '        <p>Select who you want to send this press release to (further customizations may be made in the next step):</p>';
echo '        <form id="sendPressReleaseForm">';
echo '            <input type="hidden" name="post_release_id" id="postReleaseId" value="">';
echo '            <label>';
echo '                <input type="radio" name="send_option" value="allOrganization" checked>';
echo '                Send to all organizations and their contacts';
echo '            </label>';
echo '            <br>';
echo '            <div id="organizationContactList" style="display:block;padding-left: 20px;">';
// You can call the function here to display organization contact checkboxes if needed
display_organization_contact_checkboxes();
echo '            </div>';
echo '            <br>';

echo '            <label>';
echo '                <input type="radio" name="send_option" value="OrgGroups">';
echo '                Send to an organizations group and their contacts';
echo '            </label>';
echo '            <br>';
echo '            <div id="organizationGroupContactList" style="display:block;padding-left: 20px;">';
// You can call the function here to display organization group contact checkboxes if needed
display_organization_group_contact_checkboxes();
echo '            </div>';

echo '            <label>';
echo '                <input type="radio" name="send_option" value="selected">';
echo '                Send to a contacts group';
echo '            </label>';
echo '            <br>';

echo '            <div id="groupContactList" style="display:block;padding-left: 20px;">';
// You can call the function here to display contact group checkboxes if needed
display_contact_group_checkboxes();
echo '            </div>';
echo '            <br>';
echo '            <button type="submit" id="confirmSend" class="button button-primary">Send</button>';
echo '        </form>';
echo '    </div>';
echo '</div>';
// End buffering and send output
//ob_end_flush();
}
?>