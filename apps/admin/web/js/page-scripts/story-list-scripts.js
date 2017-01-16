/***********************************START: Bootstrap Functions***********************************/
$(function(){
    // Filter Button Functionality
    $('[data-sorttype="story_size"] > .btn, [data-sorttype="priority"] > .btn, [data-sorttype="story_date"] > .btn, [data-sorttype="visibility"] > .btn').click(function(){
        if($(this).hasClass("active")){
            $(this).removeClass("btn-info active");
            $(this).addClass("btn-default");
            $(this).blur();
        } else {
            $(this).addClass("btn-info").siblings().removeClass("btn-info");
            $(this).removeClass("btn-default").siblings().addClass("btn-default");
            $(this).addClass("active").siblings().removeClass("active");
        }
    });

    $('[name="reset"]').click(function(){
        $('button[data-sorttype]').removeClass("btn-info active");
        $('button[data-sorttype]').addClass("btn-default");
    });

    $('[name="refresh"]').click(function(){
        refreshStoryList();
    });
    // End Filter Button Functionality

    // Start Utilities
    loadListUtilities();
    loadImageAssetUtilities();
    loadAudioAssetUtilities();
    loadStoryTagAssetUtilities();
    loadVideoAssetUtilities();
});
/***********************************END: Bootstrap Functions***********************************/

/***********************************START: Story List Functions***********************************/
function refreshStoryList(){
    statusApp.showPleaseWait();
    var filters = {};
    $('button[data-sorttype]').each(function(){
        if($(this).hasClass("active")){
            filters[$(this).data('sorttype')] = $(this).data('sortorder');
        }
    });
    $.post('/storylist', {filters: filters}, function(data){
        $('#story_list').html(data);
    }).done(function(){
        loadListUtilities();
        loadImageAssetUtilities();
        loadAudioAssetUtilities();
        loadStoryTagAssetUtilities();
        loadVideoAssetUtilities();
        statusApp.hidePleaseWait();
    });
}

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
    values['story_size'] = $('[data-story="'+story_num+'"] [name="story_size"]').val();
    values['show_desc'] = $('[data-story="'+story_num+'"] [name^="show_desc"]:checked').val();
    $.post('/storylist/save', {story_values: values}, function(data){
        statusApp.hidePleaseWait();
        data = $.parseJSON(data);
     
        // report validation errors
        reportFormErrors(data.errors);
    
        if(data.save_success){
            // if save is success, give user feedback
            $('a[href="#collapse-'+story_num+'"]').html(values['title']);
            $('#collapse-'+story_num).removeClass('in');
            refreshStoryList();
            popUpMsg("The story has been updated!");
        }
    });   
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

                        // report validation errors
                        reportFormErrors(data.errors);
                        
                        if(data.save_success){
                            // if delete is success, give user feedback
                            $('.panel[data-story="'+story_num+'"]').remove();
                            popUpMsg("The story has been deleted.");
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
    $.post('/storylist/visible', {story_values: values}, function(data){
        statusApp.hidePleaseWait();
        data = $.parseJSON(data);
        // report validation errors
        reportFormErrors(data.errors);

        if(data.save_success){
            // if save is success, give user feedback
            refreshStoryList();
        }
    });
}

/**
 * updates the blog story priority
 */
function updatePriority(story_values){
    statusApp.showPleaseWait();
    $.post('/storylist/priority', {story_values: story_values}, function(data){
        data = $.parseJSON(data);
     
        statusApp.hidePleaseWait();
        // report validation errors
        reportFormErrors(data.errors);
    });
}

/**
 * loads the story lists utility functions
 */
function loadListUtilities(){
    $('[data-story][name="saveBtn"]').click(function(){
        var story_num = $(this).data('story');
        saveStory(story_num);
    });

    $('[data-story][name="deleteBtn"]').click(function(){
        var story_num = $(this).data('story');
        deleteStory(story_num);
    });

    $('[data-story][name="priority"]').change(function(){
        var values = {};
        values['story_priority_id'] = $(this).data('storypid');
        values['story_id'] = $(this).data('story');
        values['priority'] = $(this).val();
        updatePriority(values);
    });

    $('[data-story][data-visible]').click(function(){
        var story_num = $(this).data('story');
        var state = $(this).data('visible');
        toggleVisibility(story_num, state);
    });

    $('#accordion').sortable({
        stop: function(e, ui) {
            var story_values = [];
            $.each($.map($(this).find('.ui-sortable-handle'), function(el) {
                var values = {};
                values['story_priority_id'] = $(el).data('storypid');
                values['priority'] = $(el).index();
                story_values.push(values);
            }));
            updatePriority(story_values);
        }
    });

    $('[id^="story-tabs-"]').tabs();
}
/***********************************END: Story List Functions***********************************/

/***********************************START: Image Tab Functions***********************************/
function refreshImageMedia(story_num){
    statusApp.showPleaseWait();
    var values = {};
    values['story_id'] = story_num;
    $.post('/image/assets', {story_values: values}, function(data){
        $('[data-storyid="'+story_num+'"].image_assets').html(data);
        $('#image-upload-form-'+story_num+' #storyimage-imagefile').val('');
        loadImageAssetUtilities();
    }).done(function(){
        statusApp.hidePleaseWait();
    });
}

function refreshImageListFunctions(){
    $('button[data-storyimageid].close').click(function(){
        removeImage($(this).data('storyimageid'));
    });
    $('.image-list').sortable({
        stop: function(e, ui) {
            var story_values = [];
            $.each($.map($(this).find('.ui-sortable-handle'), function(el) {
                var values = {};
                values['story_image_id'] = $(el).data('storyimageid');
                values['order'] = $(el).index();
                story_values.push(values);
            }));
            updateImageOrder(story_values);
        }
    });
    $('a[data-imagesrc]').click(function(){
        popUpMsg('<p class="text-center"><img src="'+$(this).data('imagesrc')+'" style="max-width: 200px;" /></p>');
    });
}

function updateImageOrder(story_values){
    statusApp.showPleaseWait();
    $.post('/image/order', {story_values: story_values}, function(data){
        data = $.parseJSON(data);
     
        statusApp.hidePleaseWait();
        // report validation errors
        reportFormErrors(data.errors);
    });
}

/**
 * remove images from the image tab
 */
function removeImage(story_image_num){
    statusApp.showPleaseWait();
    var values = {};
    values['story_image_id'] = story_image_num;
    $.post('/image/delete', {story_values: values}, function(data){
        data = $.parseJSON(data);
     
        statusApp.hidePleaseWait();
        // report validation errors
        reportFormErrors(data.errors);
    
        if(data.save_success){
            // if delete is success, give user feedback
            $('div.list-group-item[data-storyimageid="'+story_image_num+'"]').remove();
        }
    });
}

/**
 * loads the image assets utility functions
 */
function loadImageAssetUtilities(){
    var imagefiles = null;
    // Add events
    $('input[id="storyimage-imagefile"]').on('change', prepareUpload);

    // Grab the files and set them to our variable
    function prepareUpload(event){
      imagefiles = event.target.files;
    }
    $('form[id^="image-upload-form-"] input[type="button"].submit').click(function(){
        $('form[id="image-upload-form-'+$(this).data('storyid')+'"]').submit();
    });
    // process the form
    $('[id^="image-upload-form"]').submit(function(event) {
        // stop the form from submitting the normal way and refreshing the page
        event.preventDefault();
        event.stopImmediatePropagation();

        if(imagefiles != null){
            statusApp.showPleaseWait();
            var story_id = $(this).data('storyid');

            // Create a formdata object and add the files
            var data = new FormData();
            $.each(imagefiles, function(key, value)
            {
                data.append(key, value);
            });
            data.append('story_id', story_id);
            data.append('StoryImage[imageFile]', $(this).find('#storyimage-imagefile').val());

            // process the form
            $.ajax({
                url: '/image/upload',
                type: 'POST',
                data: data,
                dataType: 'json',
                processData: false, // Don't process the files
                contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                success: function(data, textStatus, jqXHR){  
                    statusApp.hidePleaseWait();

                    // report validation errors
                    reportFormErrors(data.errors);

                    //refresh media
                    if(data.save_success){
                        refreshImageMedia(story_id);
                    }
                }
            });
            imagefiles = null; // reset files
        }
    });
    refreshImageListFunctions();
} 
/***********************************END: Image Tab Functions***********************************/

/***********************************START: Story Tag Tab Functions***********************************/
/**
 * loads the story tag assets utility functions
 */
function loadStoryTagAssetUtilities(){
    $('button[data-storytaglistid].close').click(function(){
        removeStoryTag($(this).data('storytaglistid'));
    });
    $('[data-newstorytagid]').click(function(){
        var values = {};
        values['story_id'] = $(this).data('storyid');
        values['story_tag_id'] = $(this).data('newstorytagid');
        $.post('/storylist/addtag', {story_values: values}, function(data){
            data = $.parseJSON(data);
            // report validation errors
            reportFormErrors(data.errors);
            if(data.save_success){
                // if save is success, give user feedback
                reloadStoryTagList(values['story_id']);
            }
        });
    });
}
/**
 * Loads the story tag list for one story
 */
function reloadStoryTagList(story_id){
    statusApp.showPleaseWait();
    var values = {};
    values['story_id'] = story_id;
    $.post('/storylist/reloadtags', {story_values: values}, function(data){
        $('[id^="edit-tags-fragment-'+story_id+'"]').html(data);
        loadStoryTagAssetUtilities();
    }).done(function(){
        statusApp.hidePleaseWait();
    });
}
/**
 * Removes a story tag from a story
 */
function removeStoryTag(story_tag_list_id){
    if(Number(story_tag_list_id) > 0){
        var values = {};
        values['story_tag_list_id'] = story_tag_list_id;
        $.post('/storylist/removetag', {story_values: values}, function(data){
            data = $.parseJSON(data);
            // report validation errors
            reportFormErrors(data.errors);
            if(data.save_success){
                // if save is success, give user feedback
                reloadStoryTagList(data.story_id);
            }
        });
    }
}
/***********************************END: Story Tag Tab Functions***********************************/

/***********************************START: Audio Tab Functions***********************************/
function refreshAudioMedia(story_num){
    statusApp.showPleaseWait();
    var values = {};
    values['story_id'] = story_num;
    $.post('/audio/assets', {story_values: values}, function(data){
        $('[data-storyid="'+story_num+'"].audio_assets').html(data);
        loadAudioAssetUtilities();
    }).done(function(){
        statusApp.hidePleaseWait();
    });
}

function refreshAudioFunctions(){
    $('button[data-storyaudioid].close').click(function(){
        removeAudio($(this).data('storyaudioid'), $(this).data('storyid'));
    });
    $('a[data-audiosrc]').click(function(){
        popUpMsg('<p class="text-center"><audio controls><source src="'+$(this).data('audiosrc')+'" type="audio/mpeg">Your browser does not support the audio element.</audio></p>');
    });
}

/**
 * remove audio from the audio tab
 */
function removeAudio(story_audio_num, story_num){
    statusApp.showPleaseWait();
    var values = {};
    values['story_audio_id'] = story_audio_num;
    $.post('/audio/delete', {story_values: values}, function(data){
        data = $.parseJSON(data);
     
        statusApp.hidePleaseWait();
        // report validation errors
        reportFormErrors(data.errors);
    
        if(data.save_success){
            // if delete is success, give user feedback
            refreshAudioMedia(story_num);
        }
    });
}

/**
 * loads the audio assets utility functions
 */
function loadAudioAssetUtilities(){
    var audiofiles = null;
    // Add events
    $('input[id="storyaudio-audiofile"]').on('change', prepareAudioUpload);

    // Grab the files and set them to our variable
    function prepareAudioUpload(event){
        audiofiles = event.target.files;
    }
    $('form[id^="audio-upload-form-"] input[type="button"].submit').click(function(){
        $('form[id="audio-upload-form-'+$(this).data('storyid')+'"]').submit();
    });
    // process the form
    $('[id^="audio-upload-form"]').submit(function(event) {
        // stop the form from submitting the normal way and refreshing the page
        event.preventDefault();
        event.stopImmediatePropagation();

        if(audiofiles != null){
            statusApp.showPleaseWait();
            var story_id = $(this).data('storyid');

            // Create a formdata object and add the files
            var data = new FormData();
            $.each(audiofiles, function(key, value)
            {
                data.append(key, value);
            });
            data.append('story_id', story_id);
            data.append('StoryAudio[audioFile]', $(this).find('#storyaudio-audiofile').val());

            // process the form
            $.ajax({
                url: '/audio/upload',
                type: 'POST',
                data: data,
                dataType: 'json',
                processData: false, // Don't process the files
                contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                success: function(data, textStatus, jqXHR){  
                    statusApp.hidePleaseWait();

                    // report validation errors
                    reportFormErrors(data.errors);

                    //refresh media
                    if(data.save_success){
                        refreshAudioMedia(story_id);
                    }
                }
            });
            audiofiles = null; // reset files
        }
    });
    refreshAudioFunctions();
} 
/***********************************END: Audio Tab Functions***********************************/

/***********************************START: Video Tab Functions***********************************/
function refreshVideoMedia(story_num){
    statusApp.showPleaseWait();
    var values = {};
    values['story_id'] = story_num;
    $.post('/video/assets', {story_values: values}, function(data){
        $('[id="edit-video-fragment-'+story_num+'"]').html(data);
        loadVideoAssetUtilities();
    }).done(function(){
        statusApp.hidePleaseWait();
    });
}

function loadVideoAssetUtilities(){
    $('input[name="saveVideoBtn"]').click(function(){
        addVideo($(this).data('storyid'));
    });
    $('input[name="deleteVideoBtn"]').click(function(){
        removeVideo($(this).data('storyvideoid'), $(this).data('storyid'));
    });
}

/**
 * Saves the story video HTML
 */
function addVideo(story_num){
    statusApp.showPleaseWait();
    var values = {};
    values['story_id'] = story_num;
    values['video_title'] = $('[id="edit-video-fragment-'+story_num+'"] input[name="video_title"]').val();
    values['video_html'] = $('[id="edit-video-fragment-'+story_num+'"] textarea[name="video_html"]').val();
    $.post('/storylist/addvideo', {story_values: values}, function(data){
        statusApp.hidePleaseWait();
        data = $.parseJSON(data);
     
        // report validation errors
        reportFormErrors(data.errors);
    
        if(data.save_success){
            // if save is success, give user feedback
            refreshVideoMedia(story_num);
            popUpMsg("The video has been updated!");
        }
    });   
}

/**
 * remove video from the video tab
 */
function removeVideo(story_video_num, story_num){
    statusApp.showPleaseWait();
    var values = {};
    values['story_video_id'] = story_video_num;
    values['story_id'] = story_num;
    $.post('/storylist/removevideo', {story_values: values}, function(data){
        statusApp.hidePleaseWait();
        data = $.parseJSON(data);
     
        // report validation errors
        reportFormErrors(data.errors);
    
        if(data.save_success){
            // if delete is success, give user feedback
            refreshVideoMedia(story_num);
            popUpMsg("The video HTML has been deleted");
        }
    });
}
/***********************************END: Video Tab Functions***********************************/