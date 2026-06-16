jQuery(document).ready(function($) {
    $('#organization-select').select2({
        placeholder: 'Select an Organization',
        allowClear: true,
        width: 'resolve',
        
    });

    function formatSelection(item) {
        if (!item.id) {
            return item.text; // Return the default text for the item
        }
        // Get the corresponding option element
        var optionElement = $('#contact_email').find('option[value="' + item.id + '"]');
        
        // Extract data-contact_id attribute
        var contactId = optionElement.data('contact_id');

        // Custom HTML inside the selected item with data-contact_id as href
        return $('<span class="select2-selection__choice__display"><a target="_blank" href="' + contactId + '">' + item.text + '</a></span>');
    }
    function formatResult(item) {
        // Get the selected values
        var selectedValues = $('#contact_email').val();

        // Hide option if it's already selected
        if (selectedValues && selectedValues.includes(item.id)) {
            return $('<span class="hide-option"></span>'); // Hides the item
        }
        
        return item.text; // Return the default text if not selected
    }

    $('#contact_email').select2({
        placeholder: 'Select one or more contacts',
        allowClear: true,
        width: '100%',
        multiple: "multiple",
        templateSelection: formatSelection,
        templateResult: formatResult
    });
    $('#edit_contact_info, #view_contact_email').select2({
        placeholder: 'Select Contact',
        allowClear: true,
        width: 'resolve'
    });

    $('#organization_id').select2({
        placeholder: 'Select Organization',
        allowClear: true,
        width: 'resolve'
    });
    $('#dashboardorg_group').select2({
        placeholder: 'Group',
        allowClear: true,
        width: 'resolve'
    });
    $('#organization_id').on('change', function() {
        var selectedValue = $(this).val();
        $(this).parent().find('a.dashboard-search').attr('data-selected_org', selectedValue);
        
    });
    $('#dashboardorg_group').on('change', function() {
        var selectedValue = $(this).val();
        $(this).parent().find('a.dashboard-search').attr('data-selected_org_grp', selectedValue);
    });
    $('#dashboardorg_group, #organization_id').on('change', function() {
        var getFirstLink = $(this).parent().find('a.dashboard-search').attr('data-btn_link');
        var getSelected_org = $(this).parent().find('a.dashboard-search').attr('data-selected_org');
        var getSelected_org_grp = $(this).parent().find('a.dashboard-search').attr('data-selected_org_grp');
        $(this).parent().find('a.dashboard-search').attr('href', getFirstLink+"&view_org_id="+getSelected_org+"&org_group_id="+getSelected_org_grp);
    });

    $('#view_contact_email').on('change', function() {
        var selectedValue = $(this).val();
        $(this).parent().find('a.dashboard-search').attr('data-selected_contact', selectedValue);
        
    });
    $('#contact_groups').on('change', function() {
        var selectedValue = $(this).val();
        $(this).parent().find('a.dashboard-search').attr('data-selected_contact_grp', selectedValue);
    });
    $('#view_contact_email, #contact_groups').on('change', function() {
        var getFirstLink = $(this).parent().find('a.dashboard-search').attr('data-btn_link');
        var getSelected_org = $(this).parent().find('a.dashboard-search').attr('data-selected_contact');
        var getSelected_org_grp = $(this).parent().find('a.dashboard-search').attr('data-selected_contact_grp');
        $(this).parent().find('a.dashboard-search').attr('href', getFirstLink+"&view_contact_id="+getSelected_org+"&contact_group_id="+getSelected_org_grp);
    });






    $('#contact_groups').select2({
        placeholder: 'Group',
        allowClear: true,
        width: 'resolve'
    });
});
