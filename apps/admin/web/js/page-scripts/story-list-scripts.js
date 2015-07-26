$(function(){
    $('[data-story][name="saveBtn"]').click(function(){
        var story_num = $(this).data('story');
        saveStory(story_num);
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
            for(err in data.errors){
                for(var i = 0; i < data.errors[err].length; i++){
                    $('<font color="red">'+data.errors[err][i]+'</font><br />').insertBefore('[data-story="'+story_num+'"] [name="'+err+'"]');
                }
            }
            popUpMsg("There were some errors in validation. Please fix and try again.");
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
 * Saves the blog story
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
            for(err in data.errors){
                for(var i = 0; i < data.errors[err].length; i++){
                    $('<font color="red">'+data.errors[err][i]+'</font><br />').insertBefore('[data-story="'+story_num+'"] [name="'+err+'"]');
                }
            }
            popUpMsg("There were some errors in validation. Please fix and try again.");
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