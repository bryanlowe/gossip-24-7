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