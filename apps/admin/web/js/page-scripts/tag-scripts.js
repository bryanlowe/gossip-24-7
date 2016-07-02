$(function() {
    $('[name="saveBtn"]').click(function(){
       saveStoryTag();
    });
    $('[id="story-tags"]').tabs();
    $('[href="#edit-story-tag-fragment"').click(function(){
        refreshEditTab();
    });
    tagListUtilities();
});

/**
 * Tag Utilities assets set
 */
function tagListUtilities(){
    $('.list-group a[data-tag]').click(function(){
        var story_tag_num = $(this).data('tag');
        // reset the graphic on all buttons
        $('.list-group a[data-tag]').css('background-color', '#fff');
        $('.list-group a[data-tag]').data('active', false);
        $('.list-group a[data-tag] i.fa').removeClass('fa-check-circle-o text-success');
        $('.list-group a[data-tag] i.fa').addClass('fa-circle-o text-danger');

        // update this button's graphic
        $(this).css('background-color', '#f5f5f5');
        $(this).data('active',true);
        $(this).find('i.fa').removeClass('fa-circle-o text-danger');
        $(this).find('i.fa').addClass('fa-check-circle-o text-success');
        refreshEditTagForm(story_tag_num);
    });
    $('[name="editBtn"]').click(function(){
       editStoryTag();
    });
    $('[name="deleteBtn"]').click(function(){
       deleteStoryTag();
    });
}

/**
 * Load new edit tag list tab
 */
function refreshEditTab(){
    statusApp.showPleaseWait();
    var values = {};
    $.post('/celebritytags/taglist', function(data){
        $('#edit-story-tag-fragment').html(data);
        tagListUtilities();
    }).done(function(){
        statusApp.hidePleaseWait();
    });
}

/**
 * Load new edit tag form
 */
function refreshEditTagForm(story_tag_num){
    statusApp.showPleaseWait();
    var values = {};
    values['story_tag_id'] = story_tag_num;
    $.post('/celebritytags/load', {story_values: values}, function(data){
        data = $.parseJSON(data);
        $('[name="edit_story_tag_id"]').val(data.story_tag_id);
        $('[name="edit_tag_name"]').val(data.tag_name);
        $('[name="edit_description"]').val(data.description);
        $('[name="edit_source_link"]').val(data.source_link);
    }).done(function(){
        statusApp.hidePleaseWait();
    });
}

/**
 * Saves the celebrity tag
 */
function saveStoryTag(){
    statusApp.showPleaseWait();
    var values = {};
    values['tag_name'] = $('[name="tag_name"]').val();
    values['source_link'] = $('[name="source_link"]').val();
    values['description'] = $('[name="description"]').val();
    $.post('/celebritytags/save', {story_values: values}, function(data){
        data = $.parseJSON(data);
        // report validation errors
        reportFormErrors(data.errors);
        if(data.save_success){
            // if save is success, give user feedback
            $('[name="tag_name"], [name="description"], [name="source_link"]').val('');
            popUpMsg("A new tag has been saved!");
        }
    });
    statusApp.hidePleaseWait();
}

/**
 * Edit the celebrity tag
 */
function editStoryTag(){
    if(Number($('[name="edit_story_tag_id"]').val()) > 0){
        statusApp.showPleaseWait();
        var values = {};
        values['story_tag_id'] = $('[name="edit_story_tag_id"]').val();
        values['tag_name'] = $('[name="edit_tag_name"]').val();
        values['source_link'] = $('[name="edit_source_link"]').val();
        values['description'] = $('[name="edit_description"]').val();
        $.post('/celebritytags/save', {story_values: values}, function(data){
            data = $.parseJSON(data);
            // report validation errors
            reportFormErrors(data.errors);
            if(data.save_success){
                // if save is success, give user feedback
                refreshEditTab();
                popUpMsg("The story tag has been edited!");
            }
        });
        statusApp.hidePleaseWait();
    } else {
        popUpMsg("A story tag needs to be selected before it can be edited.");
    }
}

/**
 * Delete the celebrity tag
 */
function deleteStoryTag(){
    if(Number($('[name="edit_story_tag_id"]').val()) > 0){
        var values = {};
        values['story_tag_id'] = $('[name="edit_story_tag_id"]').val();
        $.prompt("<p>Are you sure you want to delete this entry?</p>", {
            title: "Are you sure?",
            buttons: { "Yes": true, "No": false },
            classes: {
                button: 'pure-button',
                defaultButton: 'pure-button-primary'
            },
            submit: function(e,v,m,f){ 
                if(v){
                    $.post('/celebritytags/delete', {story_values: values}, function(data){
                        data = $.parseJSON(data);

                        // report validation errors
                        reportFormErrors(data.errors);
                        
                        if(data.save_success){
                            // if delete is success, give user feedback
                            refreshEditTab();
                            popUpMsg("The story tag has been deleted.");
                        } 
                    });
                } 
                $.prompt.close();
            }
        });
    } else {
        popUpMsg("A story tag needs to be selected before it can be deleted.");
    }
}