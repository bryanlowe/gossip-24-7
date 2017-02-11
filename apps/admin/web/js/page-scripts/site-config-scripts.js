$(function() {
    $('[name="maintenance-off"]').click(function(){
       toggleMaintenancePage(0);
    });

    $('[name="maintenance-on"]').click(function(){
       toggleMaintenancePage(1);
    });

    $('[name="site_mode"]').change(function(){
    	if(parseInt($(this).val(), 10) === 0){
    		$('[name="maintenance-off"], [name="maintenance-on"]').prop('disabled', true);
    	} else {
    		$('[name="maintenance-off"], [name="maintenance-on"]').prop('disabled', false);
    	}
    });
});

function toggleMaintenancePage(state){
	if(parseInt($('[name="site_mode"]').val(), 10) > 0){
		statusApp.showPleaseWait();
	    var values = {};
	    values['site_config_id'] = $('[name="site_mode"]').val();
	    values['maintenance_mode'] = state;
	    $.post('/site/updatemaintenance', {maintenance_toggle: values}, function(data){
	        statusApp.hidePleaseWait();
	        data = $.parseJSON(data);
	     
	        // report validation errors
	        reportFormErrors(data.errors);
	    
	        if(data.save_success){
	            // if save is success, give user feedback
	            popUpMsg("The maintenance page has been "+(state ? 'activated' : 'deactivated')+" for the "+$('[name="site_mode"] option:selected').text()+" site");
	        }
	    });
	}
}