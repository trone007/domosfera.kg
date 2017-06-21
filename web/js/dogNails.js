$(window).on('load', function () {

	$('body').append('<div class="back-to-top"></div>');


	$(window).scroll(function() {
		if ( $(window).scrollTop() > 300 ) {
			$('.back-to-top').fadeIn('fast');
		} else {
			$('.back-to-top').fadeOut('fast');
		}
	});

	$('.back-to-top').click(function() {
		$('html, body').animate({
			scrollTop: 0
		}, 500);
	});

	$('.js-like').on('click', function(){
		if($(this).hasClass('js-like--false')) {
			$(this).removeClass('js-like--false').addClass('js-like--true').text('Убрать из избранного');
		} else {
			$(this).removeClass('js-like--true').addClass('js-like--false').text('В избранное');
		}
	})

});