$('document').ready(function() {
	if($(window).width() > 768) {
		$.each($('.list__item--next'), function() {
			$(this).outerHeight($(this).prev().outerHeight(true) - 50);
		});
	}

	var $slider = $('#carousel');
	var $sliderItems = $('.slider-item');
	var $prevSlideController = $('.slider-arrow--left');
	var $prevNeighborName = $prevSlideController.find('.neighbor-slide-name');
	var $nextSlideController = $('.slider-arrow--right');
	var $nextNeighborName = $nextSlideController.find('.neighbor-slide-name');

	var allSlidesLength;

	$slider.Peppermint({
		onSlideChange: slideChangeHandler
	});

	allSlidesLength = $slider.data('Peppermint').getSlidesNumber();

	function slideChangeHandler(currentSlide) {
		$prevNeighborName.text($sliderItems.eq(currentSlide - 1).data('slide-name'));
		$nextNeighborName.text($sliderItems.eq(currentSlide + 1).data('slide-name'));

		if(currentSlide === 0){
			$prevSlideController.addClass('hidden');
		} else if (currentSlide === allSlidesLength - 1) {
			$nextSlideController.addClass('hidden');
		} else {
			$prevSlideController.removeClass('hidden');
			$nextSlideController.removeClass('hidden');
		}
	}

	$prevSlideController.on('click', function (argument) {
		$slider.data('Peppermint').prev();
		
	});

	$nextSlideController.on('click', function (argument) {
		$slider.data('Peppermint').next();
	});
})