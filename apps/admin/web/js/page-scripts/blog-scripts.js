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
    values['title'] = $('[name="title"]').val();
    values['link'] = 'N/A';
    values['description'] = $('[name="description"]').val();
    values['story_type'] = $('[name="story_type"]').val();
    values['visible'] = 0;
    $.post('/blog/save', {story_values: values}, function(data){
        data = $.parseJSON(data);
        // report validation errors
        reportFormErrors(data.errors);
        if(data.save_success){
            // if save is success, give user feedback
            $('[name="title"], [name="description"]').val('');
            $('[name="story_type"]')[0].selectedIndex = 0;
            popUpMsg("A new story has been saved!");
        }
    });
    statusApp.hidePleaseWait();
}