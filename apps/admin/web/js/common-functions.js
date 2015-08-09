/**
 * Status bar
 */
var statusApp;
statusApp = statusApp || (function () {
    var pleaseWaitDiv = $('#pleaseWaitDialog');
    return {
        showPleaseWait: function() {
            pleaseWaitDiv.modal('show');
        },
        hidePleaseWait: function() {
            pleaseWaitDiv.modal('hide');
        },

    };
})();

/**
 * All impromptu properties are stored here.
 */
var popUpProperties = {
	classes: {
				box: '',
				fade: '',
				prompt: '',
				close: '',
				title: 'lead',
				message: '',
				buttons: '',
				button: 'pure-button',
				defaultButton: 'pure-button-primary'
			 }
}

/**
 * Performs pop up messages using impromptu
 */
function popUpMsg(msg){
	$.prompt(msg, popUpProperties);
}

/**
 * Reports form validation errors
 */
function reportFormErrors(errors){
	var errorMsg = '';
    for(err in errors){
        for(var i = 0; i < errors[err].length; i++){
            errorMsg += '<font color="red">'+errors[err][i]+'</font><br />';
        }
    }
    if(errorMsg != ''){
        popUpMsg("There were some errors in validation. Please fix and try again.<br /><strong>Errors:</strong><br />"+errorMsg);
    }
}