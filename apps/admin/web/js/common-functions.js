/**
 * Status bar
 */
var statusApp;
statusApp = statusApp || (function () {
    var pleaseWaitDiv = $('<div class="modal col-lg-4" id="pleaseWaitDialog" data-backdrop="static" data-keyboard="false"><div class="modal-header"><h1>Processing...</h1></div><div class="modal-body"><div class="progress progress-striped active"><div class="progress-bar" style="width: 100%;"></div></div></div></div>');
    return {
        showPleaseWait: function() {
            pleaseWaitDiv.modal();
        },
        hidePleaseWait: function () {
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