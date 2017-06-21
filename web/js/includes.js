var content;
$.get('html_includes/header_new.html', function(data) {
	content = data;
	$(".main-container").prepend(content); 
});