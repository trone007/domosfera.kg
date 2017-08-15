$(window).on('load', function () {

	function highlightMenuItem() {
		var urlContains = function (string) {
            return window.location.href.indexOf(encodeURIComponent(string)) > -1;
        }
        var isOnPhotoWallpapersPage = urlContains("Фотообои"),
        	isOnTilePage =  urlContains("Кафель"),
			isOnMouldingPage =  urlContains("Лепнина"),
			isOnCatalogPage = urlContains('catalog') && !(isOnTilePage || isOnMouldingPage || isOnPhotoWallpapersPage) ;

        if (isOnCatalogPage) {$('#page-catalog-link').addClass('active')}
        else if (isOnPhotoWallpapersPage) {$('#page-wallpapers-link').addClass('active')}
        else if (isOnMouldingPage) {$('#page-moulding-link').addClass('active')}
        else if (isOnTilePage) {$('#page-tile-link').addClass('active')}
	};
	highlightMenuItem();
	$('.mobile-menu__list').on('click', highlightMenuItem());

	// disable button on catalg page
    var isOnCatalogPage = window.location.href.indexOf("catalog") > -1;
	if(!isOnCatalogPage) {

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
    }

	$('.js-like').on('click', function(){
		if($(this).hasClass('js-like--false')) {
			$(this).removeClass('js-like--false').addClass('js-like--true').text('Убрать из избранного');
		} else {
			$(this).removeClass('js-like--true').addClass('js-like--false').text('В избранное');
		}
	})

});