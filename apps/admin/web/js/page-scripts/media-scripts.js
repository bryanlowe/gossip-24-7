$(function(){
    $('.list-group a[data-story]').click(function(){
        var story_num = $(this).data('story');
        // reset the graphic on all buttons
        $('.list-group a[data-story]').css('background-color', '#fff');
        $('.list-group a[data-story]').data('active', false);
        $('.list-group a[data-story] i.fa').removeClass('fa-check-circle-o text-success');
        $('.list-group a[data-story] i.fa').addClass('fa-circle-o text-danger');

        // update this button's graphic
        $(this).css('background-color', '#f5f5f5');
        $(this).data('active',true);
        $(this).find('i.fa').removeClass('fa-circle-o text-danger');
        $(this).find('i.fa').addClass('fa-check-circle-o text-success');
        refreshMedia(story_num);
    });
});

function refreshMedia(story_num){
    statusApp.showPleaseWait();
    var values = {};
    values['story_id'] = story_num;
    $.post('/media/assets', {story_values: values}, function(data){
        $('#media_assets').html(data);
    }).done(function(){
        statusApp.hidePleaseWait();
    });
}

function removeImage(story_image_num){
    statusApp.showPleaseWait();
    var values = {};
    values['story_image_id'] = story_image_num;
    $.post('/media/delete', {story_values: values}, function(data){
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

function updateImageOrder(story_values){
    statusApp.showPleaseWait();
    $.post('/media/order', {story_values: story_values}, function(data){
        data = $.parseJSON(data);
     
        statusApp.hidePleaseWait();
        // report validation errors
        reportFormErrors(data.errors);
    });
}

function loadAssetUtilities(){
    $('button.close').click(function(){
        removeImage($(this).data('storyimageid'));
    });
    var files;
    // Add events
    $('input[type="file"]').on('change', prepareUpload);

    // Grab the files and set them to our variable
    function prepareUpload(event){
      files = event.target.files;
    }
    // process the form
    $('#upload-form').submit(function(event) {
        statusApp.showPleaseWait();
        // stop the form from submitting the normal way and refreshing the page
        event.preventDefault();
        // Create a formdata object and add the files
        data = new FormData();
        $.each(files, function(key, value)
        {
            data.append(key, value);
        });
        data.append('story_id', $('#storyimage-story_id').val());
        data.append('StoryImage[imageFile]', $('#storyimage-imagefile').val());

        // process the form
        $.ajax({
            url: '/media/upload',
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
                    refreshMedia($('#storyimage-story_id').val());
                }
            }
        }); 
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