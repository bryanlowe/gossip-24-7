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