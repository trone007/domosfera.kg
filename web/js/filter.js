var filtersActivate = function($scope){
	
    var $window = $(window);
    var $filterMain = $('.filter');

	var $filterListContainer = $('.filter__categories__list');
	var $filterList = $('.filter__categories__list__item');

	var $filterContentContainer = $('.filter__categories__content');
	var $filterContent = $('.filter__categories__content__item');
	var $filterContentItems = $filterContent.children('li[filter-data]');

	var $filterBoardContainer = $('.filter__categories__selected');
	var $filterBoard = $('.filter__categories__selected__board');

	var filterBy = 'color'; //if you change default active category in mark-up, don't forget to change it here
	var $clearAll = $('.filter__categories__selected__clear');

	var $filterSidebarOpen = $('.filter-sidebar__handler--open');
	var $filterCategoriesContentHide = $('.filter__categories__head__back');

    $window.scroll(function() {
        if ( $window.scrollTop() > 300 ) {
            $filterMain.addClass('fixed');
        } else {
            $filterMain.removeClass('fixed');
        }
    });

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
            $scope.filterChange(false);
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
            $scope.filterChange(false);
			return;
		}

        if($(this).attr('filter') == 'glitter') {
            $("li[filter='glitter']").each(function(v, el) {
                unactivateItem($(el));
            });
        }

		$filterBoardContainer.addClass('filled');
		$clearAll.addClass('is-visible');

		$el = $(this).clone().addClass('filter-option--on-board').appendTo($filterBoard);
		onBoardListener($el);
		$(this).addClass('active');

		//for mobile
		$filterSidebarOpen.addClass('active');
		$filterListContainer.find('[data-filtercategory=' + filterBy + ']').addClass('selected');

        $scope.filterChange(false);
	});

	$clearAll.on('click', function () {
		$filterBoard.html('');
		$filterContentItems.removeClass('active');
		$filterList.removeClass('selected');
        $scope.clearFilters(false);
        clearIfNothingChoosen();

    })

	//for mobile
	$filterCategoriesContentHide.on('click', function() {
		$filterContentContainer.removeClass('is-visible');
	});

};