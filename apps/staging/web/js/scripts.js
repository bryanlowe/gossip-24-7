$(function(){
	$('a[data-audiocontainer]').click(function(){
		var containerId = $(this).data('audiocontainer');
		$(containerId).toggle();
	});
});