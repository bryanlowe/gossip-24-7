$(function(){
    $('[data-story][name="saveBtn"]').click(function(){
        var story_num = $(this).data('story');
        saveStory(story_num);
    });

    $('[data-story][name="deleteBtn"]').click(function(){
        var story_num = $(this).data('story');
        deleteStory(story_num);
    });

    $('[data-story][name="priority"]').change(function(){
        if(!isNaN($(this).val()) && $(this).val() != ""){
            var values = {};
            values['story_priority_id'] = $(this).data('storypid');
            values['story_id'] = $(this).data('story');
            values['priority'] = $(this).val();
            updatePriority(values);
        } else {
            popUpMsg("A number is needed for priority, please try again.");
        }
    });

    $('[data-story][data-visible]').click(function(){
        var story_num = $(this).data('story');
        var state = $(this).data('visible');
        toggleVisibility(story_num, state);
    });
});

/**
 * Saves the blog story
 */
function saveStory(story_num){
    statusApp.showPleaseWait();
    var values = {};
    values['story_id'] = story_num;
    values['title'] = $('[data-story="'+story_num+'"] [name="title"]').val();
    values['link'] = $('[data-story="'+story_num+'"] [name="link"]').val();
    values['description'] = $('[data-story="'+story_num+'"] [name="description"]').val();
    values['story_date'] = $('[data-story="'+story_num+'"] [name="date"]').val();
    values['story_type'] = $('[data-story="'+story_num+'"] [name="story_type"]').val();
    $.post('/storylist/save', {story_values: values}, function(data){
        data = $.parseJSON(data);
        if(data.errors){
            // report validation errors
            var errorMsg = '';
            for(err in data.errors){
                for(var i = 0; i < data.errors[err].length; i++){
                    errorMsg += '<font color="red">'+data.errors[err][i]+'</font><br />';
                }
            }
            popUpMsg("There were some errors in validation. Please fix and try again. Errors: <br />"+errorMsg);
        } else if(data.save_success && data.save_success > 0){
            // if save is success, give user feedback
            $('a[href="#collapse-'+story_num+'"]').html(values['title']);
            $('#collapse-'+story_num).removeClass('in');
            popUpMsg("The story has been updated!");
        } else {
            // unsuccessful save feedback
            popUpMsg("There was a problem with saving this entry. Please try again later.");
        }
    });
    statusApp.hidePleaseWait();
}

/**
 * Deletes the story from the site
 */
function deleteStory(story_num){
    statusApp.showPleaseWait();
    var values = {};
    values['story_id'] = story_num;
    if(story_num){
        $.prompt("<p>Are you sure you want to delete this entry?</p>", {
            title: "Are you sure?",
            buttons: { "Yes": true, "No": false },
            classes: {
                button: 'pure-button',
                defaultButton: 'pure-button-primary'
            },
            submit: function(e,v,m,f){ 
                if(v){
                    $.post('/storylist/delete', {story_values: values}, function(data){
                        data = $.parseJSON(data);
                        if(data.errors){
                            // report validation errors
                            var errorMsg = '';
                            for(err in data.errors){
                                for(var i = 0; i < data.errors[err].length; i++){
                                    errorMsg += '<font color="red">'+data.errors[err][i]+'</font><br />';
                                }
                            }
                            popUpMsg("There were some errors in validation. Please fix and try again. Errors: <br />"+errorMsg);
                        } else if(data.save_success && data.save_success > 0){
                            // if delete is success, give user feedback
                            $('.panel[data-story="'+story_num+'"]').remove();
                            popUpMsg("The story has been deleted.");
                        } else {
                            // unsuccessful delete feedback
                            popUpMsg("There was a problem with deleting this entry. Please try again later.");
                        }
                    });
                } 
                $.prompt.close();
            }
        });
    }
    statusApp.hidePleaseWait();
}

/**
 * toggles the blog story visibility
 */
function toggleVisibility(story_num, state){
    statusApp.showPleaseWait();
    var values = {};
    values['story_id'] = story_num;
    values['visible'] = (state) ? 0 : 1;
    $.post('/storylist/togglevisible', {story_values: values}, function(data){
        data = $.parseJSON(data);
        if(data.errors){
            // report validation errors
            var errorMsg = '';
            for(err in data.errors){
                for(var i = 0; i < data.errors[err].length; i++){
                    errorMsg += '<font color="red">'+data.errors[err][i]+'</font><br />';
                }
            }
            popUpMsg("There were some errors in validation. Please fix and try again. Errors: <br />"+errorMsg);
        } else if(data.save_success && data.save_success > 0){
            // if save is success, give user feedback
            if(state){
                $('[data-story="'+story_num+'"][data-visible]').html('<i class="fa fa-eye-slash fa-fw"></i>');
            } else {
                $('[data-story="'+story_num+'"][data-visible]').html('<i class="fa fa-eye fa-fw"></i>');
            }
            $('[data-story="'+story_num+'"][data-visible]').data('visible', (state ? 0 : 1));
        } else {
            // unsuccessful save feedback
            popUpMsg("There was a problem with saving this entry. Please try again later.");
        }
    });
    statusApp.hidePleaseWait();
}

/**
 * updates the blog story priority
 */
function updatePriority(story){
    statusApp.showPleaseWait();
    $.post('/storylist/priority', {story_values: story}, function(data){
        data = $.parseJSON(data);
        if(data.errors){
            // report validation errors
            var errorMsg = '';
            for(err in data.errors){
                for(var i = 0; i < data.errors[err].length; i++){
                    errorMsg += '<font color="red">'+data.errors[err][i]+'</font><br />';
                }
            }
            popUpMsg("There were some errors in validation. Please fix and try again. Errors: <br />"+errorMsg);
        } else if(data.save_success && data.save_success == 0){
            // unsuccessful save feedback
            popUpMsg("There was a problem with updating the priority of this entry. Please try again later.");
        }
    });
    statusApp.hidePleaseWait();
}