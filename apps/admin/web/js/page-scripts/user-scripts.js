$(function() {
    $('[name="saveBtn"]').click(function(){
       saveUser();
    });
    $('[name="loadBtn"]').click(function(){
       loadUser();
    });
    $('[name="newBtn"]').click(function(){
       $('[name="user_select"]')[0].selectedIndex = 0;
       loadUser();
    });
});

/**
 * Saves the User
 */
function saveUser(){
    statusApp.showPleaseWait();
    var values = {};
    values['user_id'] = $('[name="user_id"]').val();
    values['first_name'] = $('[name="first_name"]').val();
    values['last_name'] = $('[name="last_name"]').val();
    values['email'] = $('[name="email"]').val();
    values['username'] = $('[name="username"]').val();
    values['password'] = $('[name="password"]').val();
    values['active_status'] = $('[name="active_status"]:checked').val();
    $.post('/users/save', {user_values: values}, function(data){
        data = $.parseJSON(data);
        // report validation errors
        reportFormErrors(data.errors);
        if(data.save_success){
            // if save is success, give user feedback
            $('[name="first_name"], [name="last_name"], [name="email"], [name="username"], [name="password"]').val('');
            popUpMsg("The user has been saved!");
        }
    });
    statusApp.hidePleaseWait();
}

/**
 * Loads the User
 */
function loadUser(){
    statusApp.showPleaseWait();
    $.post('/users/load', {user_id: $('[name="user_select"]').val()}, function(data){
        data = $.parseJSON(data);
        if(data.load_success){
            // if load is success, give user feedback
            $('[name="user_id"]').val(data.user.user_id);
            $('[name="first_name"]').val(data.user.first_name);
            $('[name="last_name"]').val(data.user.last_name);
            $('[name="email"]').val(data.user.email);
            $('[name="username"]').val(data.user.username);
            if(data.user.active_status && !$('[name="active_status"][value="1"]').is(':checked')){
                $('[name="active_status"][value="1"]').click();
            } else if(!data.user.active_status && !$('[name="active_status"][value="1"]').is(':checked')){
                $('[name="active_status"][value="0"]').click();
            }
        } else {
            // if load is failure, give user feedback
            $('[name="user_id"]').val('');
            $('[name="first_name"]').val('');
            $('[name="last_name"]').val('');
            $('[name="email"]').val('');
            $('[name="username"]').val('');
            if(!$('[name="active_status"][value="1"]').is(':checked')){
                $('[name="active_status"][value="1"]').click();
            } 
        }
    });
    statusApp.hidePleaseWait();
}