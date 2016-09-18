$(function(){
	$('#audioListTbl').tablesorter();
	$('a[data-audiosrc]').click(function(){
        popUpMsg('<p class="text-center"><audio controls><source src="'+$(this).data('audiosrc')+'" type="audio/mpeg">Your browser does not support the audio element.</audio></p>');
    });
});