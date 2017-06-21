(function () {
	var $roomSwitcherNav = $('.section-carousel__room-list__item');
	var $roomSwitcherOverlays = $('.wallpaper-carousel__overlay');
	var $carouselItems = $('.wallpaper-carousel__item');
	var $wallpaperCarousel = $('.wallpaper-carousel');

	var $leftArrow = $('.section-carousel__arrow--left') || null;
	var $rightArrow = $('.section-carousel__arrow--right') || null;

	var carousel = new Sly('.wallpaper-carousel__frame', {
		horizontal: true,
		itemNav: 'centered',
		startAt: 0,
		smart: true,
		speed: 300,

		activateOn: 'click',
	  	cycleBy: 'items',
    	cycleInterval: 1000,
    	touchDragging: true,

    	prev:     $leftArrow,
    	next:     $rightArrow,
	}).init();

	$roomSwitcherNav.on('click', function () {
		$roomSwitcherNav.removeClass('active');
		$roomSwitcherOverlays.removeClass('active');
		$(this).addClass('active');
		$roomSwitcherOverlays.parent().find('[data-room=' + $(this).data('room') + ']').addClass('active');
	});

	$carouselItems.on('click', function() {
		if($(window).innerWidth() > 992) {
			$carouselItems.removeClass('selected');
			$(this).addClass('selected');
			carousel.pause();
		}
	});

	$wallpaperCarousel.on('mouseenter', function() {
		$wallpaperCarousel.addClass('hovered');
	});

	$wallpaperCarousel.on('mouseleave', function() {
		$wallpaperCarousel.removeClass('hovered');
		$carouselItems.removeClass('selected');
		carousel.resume();
	});


}());