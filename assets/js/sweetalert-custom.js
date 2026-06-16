jQuery(document).ready(function($) {
    // Show success message if the organization was saved
    if (typeof ocmSuccessMessage !== 'undefined' && ocmSuccessMessage) {
        Swal.fire({
            title: 'Success!',
            text: ocmSuccessMessage,
            icon: 'success',
            confirmButtonText: 'OK'
        });
    }

    $('form#save_org').on('submit', function(event) {
        //event.preventDefault(); // Prevent form submission
        //alert("test");
        
        // Capture form values
        var orgName = $('#organization_name').val();
        var orgAddress = $('#organization_address').val();
        var orgGroup = $('#org_group option:selected').text();
        
        // Build the HTML content dynamically
        var htmlContent = '<strong>Organization Name:</strong> ' + orgName + '<br>';
        
        // Add address only if it's not empty
        if (orgAddress) {
            htmlContent += '<strong>Address 123:</strong> ' + orgAddress + '<br>';
        }
        
        // Add group only if it's not empty
        if (orgGroup) {
            htmlContent += '<strong>Group:</strong> ' + orgGroup;
        }
        
        Swal.fire({
            title: 'Saving ' + orgName,
            html: htmlContent,
            icon: 'warning',
            showCancelButton: false,
            showCloseButton: false,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        }).then((result) => {
            if (result.isConfirmed) {
                // If confirmed, submit the form
                //$('#save_org')[0].submit();
            }
        });
    });

    
    $('form#save_contact').on('submit', function(event) {
        //event.preventDefault(); // Prevent form submission
        //alert("test");
        // Capture form values
        var name = $('input[name="name"]').val();
        var email = $('input[name="email"]').val();
        var phone = $('input[name="phone"]').val();
        var org = $('#organization_id option:selected').text();
        var group = $('#contact_group option:selected').text();
        
        // Build the HTML content dynamically
        var htmlContent = '<strong>Name:</strong> ' + name + '<br>' +
                         '<strong>Email:</strong> ' + email + '<br>';
        
        // Add phone only if it's not empty
        if (phone) {
            htmlContent += '<strong>Phone:</strong> ' + phone + '<br>';
        }
        
        // Add organization (assuming you always want to show this)
        htmlContent += '<strong>Organization:</strong> ' + org + '<br>';
        
        // Add group only if it's not empty
        if (group) {
            htmlContent += '<strong>Group:</strong> ' + group;
        }

        Swal.fire({
            title: 'Saving ' + name,
            html: htmlContent,
            icon: 'warning',
            showCancelButton: false,
            showCloseButton: false,
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        }).then((result) => {
            if (result.isConfirmed) {
                // If confirmed, submit the form
                //$('form#save_contact')[0].submit();
            }
        });
    });

    $('form#save_group').on('submit', function(event) {
        //event.preventDefault(); // Prevent the form from submitting immediately
    
        // Capture form values
        var groupName = $('#group_name').val();
        var description = $('#description').val();
        var groupType = $('#group_type option:selected').text();
    
        // Build the HTML content dynamically
        var htmlContent = '<strong>Group Name:</strong> ' + groupName;
        
        // Add description only if it's not empty
        if (description) {
            htmlContent += '<br><strong>Description:</strong> ' + description;
        }
        
        // Add type only if it's not empty
        if (groupType) {
            htmlContent += '<br><strong>Type:</strong> ' + groupType;
        }
    
        // Show SweetAlert2 confirmation dialog
        Swal.fire({
            title: 'Saving ' + groupName,
            html: htmlContent,
            icon: 'warning',
            showCancelButton: false,
            showCloseButton: false,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.timer) {
                // Timer expired logic
                //$(this).off('submit').submit();
                //console.log("I was closed by the timer");
            }
        });
    });

});
