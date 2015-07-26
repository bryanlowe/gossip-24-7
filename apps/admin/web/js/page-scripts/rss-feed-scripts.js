$(function() {
    $('[name="rssBtn"]').click(function(){
        statusApp.showPleaseWait();
    	if($('[name="rss_select"]').val() != ""){
    		$.post('/rss/load', {url: $('[name="rss_select"]').val()}, function(data){$('#story_list').html(data);});
    	} else if($('[name="rss_url"]').val() != ""){  
    		$.post('/rss/load', {url: $('[name="rss_url"]').val()}, function(data){$('#story_list').html(data);});
    	} else {
            $.post('/rss/load', {url: 'N/A'}, function(data){$('#story_list').html(data);});
        }
        statusApp.hidePleaseWait();
    });

    $('[name="rss_select"]').change(function(){
    	if($(this).val() != ""){
    		$('[name="rss_url"]').val('');
    	}
    });

    $('[name="rss_url"]').blur(function(){
    	if($(this).val() != ""){
    		$('[name="rss_select"]').val('');
    	}
    });
});

/**
 * Saves the RSS Feed based on the data-story attribute
 */
function saveFeed(story_num){
    statusApp.showPleaseWait();
    var values = {};
    values['story_id'] = 0;
    values['title'] = $('[data-story="'+story_num+'"] [name="title"]').val();
    values['link'] = $('[data-story="'+story_num+'"] [name="link"]').val();
    values['description'] = $('[data-story="'+story_num+'"] [name="description"]').val();
    values['story_date'] = $('[data-story="'+story_num+'"] [name="story_date"]').val();
    values['story_type'] = $('[data-story="'+story_num+'"] [name="story_type"]').val();
    values['visible'] = 0;
    $.post('/rss/save', {story_values: values}, function(data){
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
            $('[data-story="'+story_num+'"] [name="title"], [data-story="'+story_num+'"] [name="description"]').prop('readonly', true);
            $('[data-story="'+story_num+'"] [name="story_type"]').prop('disabled', true);
            $('[data-story="'+story_num+'"] [type="button"]').replaceWith('<button class="btn btn-success">SAVED!</button>');
            popUpMsg("RSS entry has been saved!");
        } else {
            // unsuccessful save feedback
            popUpMsg("There was a problem with saving this entry. Please try again later.");
        }
    });
    statusApp.hidePleaseWait();
}