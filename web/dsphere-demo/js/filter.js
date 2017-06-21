$(document).ready(function(){

	var $window = $(window);
	var $filterMain = $('.filter');

	var $filterListContainer = $('.filter__categories__list');
	var $filterList = $('.filter__categories__list__item');

	var $filterContentContainer = $('.filter__categories__content');
	var $filterContent = $('.filter__categories__content__item');
	var $filterContentItems = $filterContent.children('li');

	var $filterBoardContainer = $('.filter__categories__selected');
	var $filterBoard = $('.filter__categories__selected__board');

	var filterBy = 'color'; //if you change default active category in mark-up, don't forget to change it here
	var $clearAll = $('.filter__categories__selected__clear');

	var $filterSidebarOpen = $('.filter-sidebar__handler--open');
	var $filterCategoriesContentHide = $('.filter__categories__head__back');

	$filterList.on('click', function() {
		$filterList.removeClass('active');
		$(this).addClass('active');

		filterBy = ($(this).data('filtercategory'));

		$filterContent.removeClass('active');
		$filterContentContainer.find('[data-filtercategory=' + filterBy + ']').addClass('active');
		$filterContentContainer.find('.filter__categories__head__back').html($(this).html());

		$filterContentContainer.addClass('is-visible');
	});

	function onBoardListener($el) {
		$el.on('click', function() {
			$(this).remove();
			$filterContent.find('[data-filter=' + $(this).data('filter') + ']').removeClass('active');
			clearIfNothingChoosen();
		});
	}
	function clearIfNothingChoosen() {
		if(!$filterBoard.html()) {
			$filterBoardContainer.removeClass('filled');
			$clearAll.removeClass('is-visible');
			$filterSidebarOpen.removeClass('active');
		}
	}
	function unactivateItem($el) {
		$el.removeClass('active');
		$filterBoardContainer.find('[data-filter=' + $el.data('filter') + ']').remove();
		clearIfNothingChoosen();
		if(!isAnythingInCategorySelected()){
			$filterListContainer.find('[data-filtercategory=' + filterBy + ']').removeClass('selected');
		}
	}
	function isAnythingInCategorySelected() {
		$filterContentContainer.find('[data-filtercategory=' + filterBy + ']').children().hasClass('active');
	}

	$filterContentItems.on('click', function() {
		if ($(this).hasClass('active')) {
			unactivateItem($(this));
			return;
		}
		$filterBoardContainer.addClass('filled');
		$clearAll.addClass('is-visible');

		$el = $(this).clone().addClass('filter-option--on-board').appendTo($filterBoard);
		onBoardListener($el);
		$(this).addClass('active');

		//for mobile
		$filterSidebarOpen.addClass('active');
		$filterListContainer.find('[data-filtercategory=' + filterBy + ']').addClass('selected');
	});

	$clearAll.on('click', function () {
		$filterBoard.html('');
		$filterContentItems.removeClass('active');
		$filterList.removeClass('selected');
		$(".price-slider").slider('values', [100, 5000]);
		$(".size-slider").slider('values', [10, 1000]);
		clearIfNothingChoosen();
	})

	//for mobile
	$filterCategoriesContentHide.on('click', function() {
		$filterContentContainer.removeClass('is-visible');
	});

	$(function() {
	    $(".price-slider").slider({
	      range: true,
	      min: 100,
	      max: 5000,
	      values: [ 100, 5000 ],
	      slide: function(event, ui) {
	      	var priceItemText = ui.values[0] + ' – ' + ui.values[1] + ' сом';

	      	$filterBoardContainer.addClass('filled');
			$clearAll.addClass('is-visible');

			if($filterBoard.children('li[data-filter="price"]').length) {
				$filterBoard.children('li[data-filter="price"]').text(priceItemText)
			} else {
				$el = $('<li class="filter-option filter-option--on-board" data-filter="price">' + priceItemText + '</li>').appendTo($filterBoard);
				$el.on('click', function() {
					$(this).remove();
					$(".price-slider").slider('values', [100, 5000]);
					clearIfNothingChoosen();
				});
				$filterSidebarOpen.addClass('active');
				$filterListContainer.find('[data-filtercategory=' + filterBy + ']').addClass('selected');
			}
	      },
	      change: function(event, ui){
			$('.price-slider__min').text( ui.values[0] + " сом" );
	      	$('.price-slider__max').text( ui.values[1] + " сом" );
	      }
	    });
	});

	$(function() {
	    $(".size-slider").slider({
	      range: true,
	      min: 10,
	      max: 1000,
	      values: [ 10, 1000 ],
	      slide: function(event, ui) {
	      	var sizeItemText = ui.values[0] + ' – ' + ui.values[1] + ' м';

	      	$filterBoardContainer.addClass('filled');
			$clearAll.addClass('is-visible');

			if($filterBoard.children('li[data-filter="size"]').length) {
				$filterBoard.children('li[data-filter="size"]').text(sizeItemText)
			} else {
				$el = $('<li class="filter-option filter-option--on-board" data-filter="size">' + sizeItemText + '</li>').appendTo($filterBoard);
				$el.on('click', function() {
					$(this).remove();
					$(".size-slider").slider('values', [10, 1000]);
					clearIfNothingChoosen();
				});
				$filterSidebarOpen.addClass('active');
				$filterListContainer.find('[data-filtercategory=' + filterBy + ']').addClass('selected');
			}
	      },
	      change: function(event, ui){
			$('.size-slider__min').text( ui.values[0] + " м" );
	      	$('.size-slider__max').text( ui.values[1] + " м" );
	      }
	    });
	});

});