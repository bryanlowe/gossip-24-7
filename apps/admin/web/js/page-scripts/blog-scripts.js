$(function() {
    $('[name="saveBtn"]').click(function(){
       saveStory();
    });
});

/**
 * Saves the blog story
 */
function saveStory(){
    statusApp.showPleaseWait();
    var values = {};
    values['story_id'] = 0;
    values['title'] = $('[name="title"]').val();
    values['link'] = 'N/A';
    values['description'] = $('[name="description"]').val();
    values['story_date'] = '';
    values['story_type'] = $('[name="story_type"]').val();
    values['visible'] = 0;
    $.post('/blog/save', {story_values: values}, function(data){
        data = $.parseJSON(data);
        if(data.errors){
            // report validation errors
            for(err in data.errors){
                for(var i = 0; i < data.errors[err].length; i++){
                    $('<font color="red">'+data.errors[err][i]+'</font><br />').insertBefore('[name="'+err+'"]');
                }
            }
            popUpMsg("There were some errors in validation. Please fix and try again.");
        } else if(data.save_success && data.save_success > 0){
            // if save is success, give user feedback
            $('[name="title"], [name="description"]').val('');
            $('[name="story_type"]')[0].selectedIndex = 0;
            popUpMsg("A new story has been saved!");
        } else {
            // unsuccessful save feedback
            popUpMsg("There was a problem with saving this entry. Please try again later.");
        }
    });
    statusApp.hidePleaseWait();
}