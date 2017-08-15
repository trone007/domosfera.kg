<?php $prefix = explode('/', $_SERVER['REQUEST_URI'])[1]; ?>
<?php $prefix = $prefix != 'new' && $prefix != 'other-new' ? '': $prefix; ?>
<?php switch($prefix): ?>
<?php case 'new': $view->extend('::smallBase.html.php'); break;?>
<?php case 'other-new': $view->extend('::largeBase.html.php'); break;?>
<?php default : $view->extend('::empty.html.php'); break;?>
<?php endswitch;?>
<?php $view['slots']->start('meta')?>
<link rel="stylesheet" href="/styles.css">
<script src="/js/filter.js"></script>
<script src="/js/pagerService.js"></script>
<script src="/js/underscore-min.js"></script>
<script src="/js/angular.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.7/angular-route.js"></script>

<?php $view['slots']->stop()?>
<script>

    hashCode = function(s){
        return s.split("").reduce(function(a,b){a=((a<<5)-a)+b.charCodeAt(0);return a&a},0);
    };

    angular
        .module('wallpaper', ['ngRoute'])
        .config(['$locationProvider', function($locationProvider) {
            $locationProvider.hashPrefix('!').html5Mode({
                enabled: true,
                requireBase: false
            });
        }])
        .factory('PagerService', PagerService)
        .controller('mainCtrl', function($scope, PagerService, $http, $location) {
            $scope.wallpapers={
            };

            $scope.toFavorite = toFavorite;

            $scope.vendor = "";
            $scope.priceType = 1;
            $scope.sortingMob = "";

            $scope.colors = [];
            $scope.pictures = [];
            $scope.style = [];
            $scope.sizes = [];
            $scope.countries = [];
            $scope.type = [];
            $scope.basis = [];

            $scope.searchProperties = [];
            $scope.searchProperties['hot'] = true;
            $scope.searchProperties['halyava'] = false;
            $scope.searchProperties['new'] = false;

            $scope.orderType = [];
            $scope.orderType['price'] = 'DESC';
            $scope.orderType['speed'] = 'ASC';
            $scope.orderType['successfull'] = 'ASC';

            $scope.orderBy = {
//                'column': 'price',
//                'type': $scope.orderType['price']
            };

            $scope.pager = {};

            $scope.setPage = setPage;

            $scope.checkFavorite = function(vendor) {
                var vendors = localStorage.getItem('vendors').split(","),
                    result = false;
                $.each(vendors, function (e, val) {
                   if(val == vendor) {
                       result = true;
                   }
                });
                return result;
            }

            $scope.redirectTo = function(url) {
                window.open('/new/wallpaper/'+url, '_blank');
            };

            $scope.updatePrice = function () {
                $http({
                    method: 'POST',
                    url: '/get-price',
                    data:
                    'query=' + JSON.stringify($scope.jsonQ)
                    ,
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }).then(function successCallback(response) {

                    var data = response.data;

//                    $scope.maxPrice = parseFloat(data.max_price);
//                    $scope.minPrice = parseFloat(data.min_price);
//                    $scope.maxPriceM = parseFloat(data.max_m_price);
//                    $scope.minPriceM = parseFloat(data.min_m_price);
//                    $scope.changePriceType($scope.priceType);
                }, function errorCallback(response){

                });
            };

            $scope.getWallpapers = function() {
                $scope.updatePrice();
                $http({
                    method: 'POST',
                    url: '/get-wallpapers',
                    data:
                    'query=' + JSON.stringify($scope.jsonQ)
                    ,
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }).then(function successCallback(response) {
                    var data = response.data;
                    $scope.wallpapers = data;
                    initController();
                }, function errorCallback(response){

                });
            };

            $scope.changePriceType = function(v) {
                var maxPrice = 0,
                    minPrice = 0;

                var $filterListContainer = $('.filter__categories__list');

                var $filterBoardContainer = $('.filter__categories__selected');
                var $filterBoard = $('.filter__categories__selected__board');

                var filterBy = 'color'; //if you change default active category in mark-up, don't forget to change it here
                var $clearAll = $('.filter__categories__selected__clear');

                var $filterSidebarOpen = $('.filter-sidebar__handler--open');

                if(typeof $( "#slider-range" ).attr("class") !== 'undefined') {
                    if($( "#slider-range" ).attr("class").indexOf('slider') > -1) {
                        $( "#slider-range" ).slider( "destroy" );
                    }
                }

                $scope.priceType = v;

                switch(v) {
                    case 0:
                        maxPrice = $scope.maxPrice;
                        minPrice = $scope.minPrice;
                        break;
                    case 1:
                        maxPrice = $scope.maxPriceM;
                        minPrice = $scope.minPriceM;
                        break;

                }
                var e = $('.filter-option[data-filter="price"]');

                if(e) {
                    $(e).remove();
                    if(!$filterBoard.html()) {
                        $filterBoardContainer.removeClass('filled');
                        $clearAll.removeClass('is-visible');
                        $filterSidebarOpen.removeClass('active');
                    }
                }

                $( "#slider-range" ).slider({
                    range: true,
                    min: minPrice,
                    max: maxPrice,
                    values: [ 0, maxPrice ],
                    slide: function( event, ui ) {
                        var priceItemText = ui.values[0] + ' – ' + ui.values[1] + ' сом';
                        $filterBoardContainer.addClass('filled');
                        $clearAll.addClass('is-visible');
                        if($filterBoard.children('li[data-filter="price"]').length) {
                            $filterBoard.children('li[data-filter="price"]').text(priceItemText)
                        } else {
                            $el = $('<li class="filter-option filter-option--on-board" data-filter="price">' + priceItemText + '</li>').appendTo($filterBoard);
                            $el.on('click', function() {
                                $(this).remove();
                                $("#slider-range").slider('values', [minPrice, maxPrice]);
                                if(!$filterBoard.html()) {
                                    $filterBoardContainer.removeClass('filled');
                                    $clearAll.removeClass('is-visible');
                                    $filterSidebarOpen.removeClass('active');
                                }
                            });
                            $filterSidebarOpen.addClass('active');
                            $filterListContainer.find('[data-filtercategory=' + filterBy + ']').addClass('selected');
                        }

                    },

                    change: function(event, ui) {
                        $scope.filterChange();
                        $('.size-slider__min').text( ui.values[0] + " м" );
                        $('.size-slider__max').text( ui.values[1] + " м" );

                        $('.price-slider__min').html($( "#slider-range" ).slider( "values", 0 ));
                        $('.price-slider__max').html($( "#slider-range" ).slider( "values", 1 ));
                    }
                });

                $('.price-slider__min').html($( "#slider-range" ).slider( "values", 0 ));
                $('.price-slider__max').html($( "#slider-range" ).slider( "values", 1 ));

                if(typeof $scope.jsonQ !== 'undefined') {
                    $scope.wallappers = [];
                    $scope.filterChange(false);
                }
            };

            $scope.vendorSearch = function(key = false) {
                if(!key || key == 13) {
                    if ($('#vendor').val().length > 0) {
                        $scope.vendor = $('#vendor').val();
                    } else {
                        $scope.vendor = '';
                    }
                    $scope.filterChange();

                    var $filterListContainer = $('.filter__categories__list');

                    var $filterBoardContainer = $('.filter__categories__selected');
                    var $filterBoard = $('.filter__categories__selected__board');
                    var $clearAll = $('.filter__categories__selected__clear');
                    var $filterSidebarOpen = $('.filter-sidebar__handler--open');


                    $filterBoardContainer.addClass('filled');
                    $clearAll.addClass('is-visible');

                    if($filterBoard.children('li[data-filter="vendor"]').length) {
                        $filterBoard.children('li[data-filter="vendor"]').text($scope.vendor)
                    } else {
                       var $el = $('<li class="filter-option filter-option--on-board" data-filter="vendor">' + $scope.vendor + '</li>').appendTo($filterBoard);
                        $el.on('click', function() {
                            $(this).remove();
                            if(!$filterBoard.html()) {
                                $filterBoardContainer.removeClass('filled');
                                $clearAll.removeClass('is-visible');
                                $filterSidebarOpen.removeClass('active');
                                $scope.vendor = '';
                                $scope.filterChange();

                            }
                        });
                        $filterSidebarOpen.addClass('active');
                        $filterListContainer.find('[data-filtercategory="vendor"]').addClass('selected');
//                        $scope.vendor = '';
                    }

                }
            };

            $scope.filterChange = function($showFilter = true) {
                var i = 0;
                $scope.colors = [];
                $scope.pictures = [];
                $scope.style = [];
                $scope.sizes = [];
                $scope.countries = [];
                $scope.type = [];
                $scope.basis = [];
                $scope.rooms = [];

                $('.filter__categories__selected__board').children().each(function(e,v) {
                    var filter = $(v).attr('filter'),
                        value = $(v).attr('filter-data');
                    switch(filter) {
                        case 'color':
                            $scope.colors.push(value);
                            break;
                        case 'picture':
                            $scope.pictures.push(value);
                            break;
                        case 'style':
                            $scope.style.push(value);
                            break;
                        case 'room':
                            $scope.rooms.push(value);
                            break;
                        case 'type':
                            $scope.type.push(value);
                            break;
                        case 'basis':
                            $scope.basis.push(value);
                            break;
                        case 'size':
                            $scope.sizes.push(value);
                            break;
                        case 'country':
                            $scope.countries.push(value);
                            break;
                    }
                });

                var hash = hashCode(JSON.stringify($scope.jsonQ));
                $scope.setJsonQ();


                if($scope.hash != hash){
                    if($showFilter) {
                        $('.filter__categories__selected').addClass('filled');
                        $('.filter__categories__selected__board').addClass('filled');
                        $('.filter__categories__selected__clear').addClass('is-visible');
                    }

                    $scope.hash = hash;
                    $scope.getWallpapers();
                }

            };

            $scope.clearFilters = function() {
                $scope.colors = [];
                $scope.pictures = [];
                $scope.style = [];
                $scope.sizes = [];
                $scope.countries = [];
                $scope.type = [];
                $scope.basis = [];
                $scope.vendor = "";


                $scope.jsonQ.priceStart = $scope.minPrice;
                $scope.jsonQ.priceFinish = $scope.maxPrice;

                $('.search__property').removeClass('active');

                $scope.hash = hashCode(JSON.stringify($scope.jsonQ));

                $scope.filterChange();

                $scope.changePriceType($scope.priceType);

                $('.filter__categories__selected__clear').removeClass('is-visible');
            };

            $scope.sort = function($event, column) {
                if($scope.orderType[column] == 'DESC') {
                    $scope.orderType[column] = 'ASC';
                } else {
                    $scope.orderType[column] = 'DESC'
                }

                $scope.searchProperties['hot'] = false;
                $scope.searchProperties['halyava'] = false;
                $scope.searchProperties['new'] = false;

                $('.col-md-4 > .switcher__item').removeClass('active');

                $($event.currentTarget).addClass('active');

                $scope.orderBy = {
                    'column': column,
                    'type': $scope.orderType[column]
                };

                $scope.filterChange(false);
            }

            $scope.searchPropertiesFn = function($event, column) {
                $scope.searchProperties['hot'] = false;
                $scope.searchProperties['halyava'] = false;
                $scope.searchProperties['new'] = false;

                $scope.orderType['price'] = 'DESC';
                $scope.orderType['speed'] = 'ASC';
                $scope.orderType['successfull'] = 'ASC';

                $('.col-md-4 > .switcher__item').removeClass('active');
                $($event.currentTarget).addClass('active');

                if(!$scope.searchProperties[column]) {
                    $scope.searchProperties[column] = true;
                }

                $scope.filterChange(false);
            };

            $scope.searchPropertiesFnMob = function() {
                $scope.searchProperties['hot'] = false;
                $scope.searchProperties['halyava'] = false;
                $scope.searchProperties['new'] = false;

                $scope.orderType['price'] = 'DESC';
                $scope.orderType['speed'] = 'ASC';
                $scope.orderType['successfull'] = 'ASC';

                if(!$scope.searchProperties[$scope.sortingMob]) {
                    $scope.searchProperties[$scope.sortingMob] = true;
                }

                $scope.filterChange(false);
            };



            $scope.setJsonQ = function() {
                var startValue = $('#slider-range').slider('values', 0);
                var finishValue = $('#slider-range').slider('values', 1);
                $scope.jsonQ = {
                    'priceStart': startValue,
                    'priceFinish': finishValue,
                    'priceType': $scope.priceType,
                    'vendor': $scope.vendor,
                    'orderBy': $scope.orderBy,
                    'colors': $scope.colors,
                    'sizes': $scope.sizes,
                    'countries': $scope.countries,
                    'pictures': $scope.pictures,
                    'style': $scope.style,
                    'type': $scope.type,
                    'basis': $scope.basis,
                    'rooms': $scope.rooms,
                    'halyava': $scope.searchProperties['halyava'],
                    'hot': $scope.searchProperties['hot'],
                    'new': $scope.searchProperties['new']
                };

                $scope.hash = hashCode(JSON.stringify($scope.jsonQ));
            };

            $scope.minPrice = <?php echo $maxMin['min_price'] ?: 0;?>;
            $scope.maxPrice = <?php echo $maxMin['max_price'] ?: 0;?>;

            $scope.minPriceM = <?php echo $maxMin['min_m_price'] ?: 0;?>;
            $scope.maxPriceM = <?php echo $maxMin['max_m_price'] ?: 0;?>;

            $scope.user = '<?php echo $user;?>';

            $scope.changePriceType(0);
            var filter = $location.search().filter;
            var value = $location.search().value;
            if (typeof filter !== 'undefined') {
                var a = $($('.filter__categories__content__item[data-filtercategory="' + filter + '"]')
                    .children('.filter-option:contains("'+value+'")')[0]);
                setTimeout(function(){a.click();$('.filter__categories__list__item[data-filtercategory="' + filter + '"]').click()}, 1000);

            }

            $scope.setJsonQ();
            filtersActivate($scope);

            $scope.getWallpapers();


            function initController() {
                $scope.setPage(1);
            }

            function setPage(page) {
                if ((page != 1) && (page < 1 || page > $scope.pager.totalPages)) {
                    return;
                }
                // get pager object from service
                $scope.sortType     = 'price'; // значение сортировки по умолчанию
                $scope.sortReverse  = true;  // обратная сортировка

                $scope.pager = PagerService.GetPager($scope.wallpapers.length, page, 12);

                // get current page of items
                $scope.items = $scope.wallpapers.slice($scope.pager.startIndex, $scope.pager.endIndex);
            }
        })
</script>
<div ng-app="wallpaper" ng-controller="mainCtrl" >
    <div class="head row">
        <h2 class="head__title title-1 col-md-6">Каталог обоев</h2>
        <div class="head__switcher switcher col-md-6">
            <?php if($prefix == 'new'):?>
                <a href="#" class="switcher__item active">список</a>
                /
                <a href="javascript:window.location.href='/<?php echo $prefix != '' ? $prefix . '/' : ''?>collections'" class="switcher__item">коллекции</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="mobile-menu closed" id="mobile-menu">
        <div class="mobile-menu__container">
            <div class="mobile-menu__head clearfix">
                <div class="select-container">
                    <select name="citySelector" id="citySelector">
                        <option value="bsk">Бишкек</option>
                        <option value="krb">Кара-Балта</option>
                        <option value="osh">Ош</option>
                        <option value="tls">Талас</option>
                        <option value="tmk">Токмак</option>
                        <option value="kzh">Казахстан</option>
                        <option value="oth">Другие</option>
                    </select>
                </div>
                <div class="mobile-menu__handler mobile-menu__handler--close" data-sidebar-close="mobile-menu"><span>menu close</span></div>
            </div>
            <ul class="mobile-menu__list">
                <li><a href="#">Обои</a></li>
                <li><a href="#">Кафель</a></li>
                <li><a href="#">Освещение</a></li>
                <li><a href="#">Текстиль</a></li>
                <li><a href="#">Лепнина</a></li>
                <li><a href="#">Напольные покрытия</a></li>
                <li><a href="#">Ковры</a></li>
                <li><a href="#">Мебель</a></li>
            </ul>
            <ul class="mobile-menu__small-list">
                <li><a href="#">мастера</a></li>
                <li><a href="#">дизайнеры</a></li>
            </ul>
            <ul class="mobile-menu__small-list">
                <li><a href="#">избранное</a></li>
                <li><a href="#">вход в личный кабинет</a></li>
            </ul>
        </div>
        <div class="blackout mobile-menu__handler--close"></div>
    </div>

    <!--<div class="head row">-->
    <!--    <h2 class="head__title title-1 col-md-6">Каталог обоев</h2>-->
    <!--    <div class="head__switcher switcher col-md-6">-->
    <!--        <a href="/catalog-list.html" class="switcher__item active">списком</a>-->
    <!--        <a href="/compare-list.html" class="switcher__item">в сравнении</a>-->
    <!--        <a href="/collection-list.html" class="switcher__item">коллекциями</a>-->
    <!--    </div>-->
    <!--</div>-->
    <div class="filter row">
        <div class="filter__categories col-md-7 col-lg-8 closed" id="filter-sidebar">
            <div class="blackout filter-sidebar__handler--close"></div>
            <div class="filter__categories__list">
                <div class="filter__categories__head clearfix">
                    <div class="filter__categories__head__title">Фильтры</div>
                    <div class="filter__categories__selected__clear">Сбросить</div>
                </div>
                <?php if($nom == 'Обои'):?>
                <div class="filter__categories__list__item active" data-filtercategory="color">Цвет</div>
                <div class="filter__categories__list__item" data-filtercategory="style">Стиль</div>
                <div class="filter__categories__list__item" data-filtercategory="pattern">Рисунок</div>
                <div class="filter__categories__list__item" data-filtercategory="vendor">Артикул</div>
<!--                <div class="filter__categories__list__item" data-filtercategory="texture">Фактура</div>-->
<!--                <div class="filter__categories__list__item" data-filtercategory="type">Глиттер</div>-->
                <div class="filter__categories__list__item" data-filtercategory="size">Ширина рулона</div>
                <div class="filter__categories__list__item" data-filtercategory="type">Покрытие</div>
                <div class="filter__categories__list__item" data-filtercategory="basis">Основа</div>
                <?php  endif;?>
                <div class="filter__categories__list__item" data-filtercategory="price">Цена</div>

                <div class="filter__categories__list__item" data-filtercategory="country">Страна</div>
                <div class="filter__categories__list__item" data-filtercategory="room">Комната</div>
                <div class="filter__categories__button filter-sidebar__handler--close">Готово</div>
            </div>
            <div class="filter__categories__content">

                <?php if($nom == 'Обои'):?>
                <div class="filter__categories__head">
                    <div class="filter__categories__head__back">цвет</div>
                </div>
                <ul class="filter__categories__content__item active" data-filtercategory="color">
                    <li class="filter-option color color--white"></li>
                    <ol class="color_popup_wrap">
                        <div class="color_popup">
                            <div class="filter-item-wrap">
                                <ul class="filter__categories__content__item active" data-filtercategory="color">
                                    <li class="filter-option color color--light-grey" filter="color" filter-data='Светло-серый' data-filter="color-light-grey">
                                        <span class="color-name">Светло-серый</span>
                                    </li>
                                    <li class="filter-option color color--grey" filter="color" filter-data='Серый' data-filter="color-grey">
                                        <span class="color-name">Серый</span>
                                    </li>
                                    <li class="filter-option color color--white" filter="color" filter-data='Белый' data-filter="color-white">
                                        <span class="color-name">Белый</span>
                                    </li>
                                    <li class="filter-option color color--black" filter="color" filter-data='Черный' data-filter="color-black">
                                        <span class="color-name">Черный</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </ol>
                    <li class="filter-option color color--beige"></li>
                    <ol class="color_popup_wrap">
                        <div class="color_popup">
                            <div class="filter-item-wrap">
                                <ul class="filter__categories__content__item active" data-filtercategory="color">
                                    <li class="filter-option color color--milk" filter="color" filter-data='Молочный' data-filter="color-peach">
                                        <span class="color-name">Молочный</span>
                                    </li>
                                    <li class="filter-option color color--beige" filter="color" filter-data='Бежевый' data-filter="color-beige">
                                        <span class="color-name">Бежевый</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </ol>
                    <li class="filter-option color color--yellow"></li>
                    <ol class="color_popup_wrap">
                        <div class="color_popup">
                            <div class="filter-item-wrap">
                                <ul class="filter__categories__content__item active" data-filtercategory="color">
                                    <li class="filter-option color color--gold" filter="color" filter-data='Золотой' data-filter="color-gold">
                                        <span class="color-name">Золотой</span>
                                    </li>
                                    <li class="filter-option color color--yellow" filter="color" filter-data='Желтый' data-filter="color-yellow">
                                        <span class="color-name">Жёлтый</span>
                                    </li>
                                    <li class="filter-option color color--bronze" filter="color" filter-data='Бронзовый' data-filter="color-bronze">
                                        <span class="color-name">Бронзовый</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </ol>
                    <li class="filter-option color color--orange"></li>
                    <ol class="color_popup_wrap">
                        <div class="color_popup">
                            <div class="filter-item-wrap">
                                <ul class="filter__categories__content__item active" data-filtercategory="color">
                                    <li class="filter-option color color--orange" filter="color" filter-data='Оранжевый' data-filter="color-orange">
                                        <span class="color-name">Оранжевый</span>
                                    </li>
                                    <li class="filter-option color color--brown" filter="color" filter-data='Коричневый' data-filter="color-brown">
                                        <span class="color-name">Коричневый</span>
                                    </li>
                                    <li class="filter-option color color--deep-brown" filter="color" filter-data='Коричневый глубокий' data-filter="color-deep-brown">
                                        <span class="color-name">Коричневый глубокий</span>
                                    </li>
                                    <li class="filter-option color color--taup" filter="color" filter-data='Тауп' data-filter="color-taup">
                                        <span class="color-name">Тауп</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </ol>
                    <li class="filter-option color color--red"></li>
                    <ol class="color_popup_wrap">
                        <div class="color_popup">
                            <div class="filter-item-wrap">
                                <ul class="filter__categories__content__item active" data-filtercategory="color">
                                    <li class="filter-option color color--red" filter="color" filter-data='Красный' data-filter="color-red">
                                        <span class="color-name">Красный</span>
                                    </li>
                                    <li class="filter-option color color--light-pink" filter="color" filter-data='Розовый нежный' data-filter="color-light-pink">
                                        <span class="color-name">Розовый нежный</span>
                                    </li>
                                    <li class="filter-option color color--modern-pink" filter="color" filter-data=' Розовый современный' data-filter="color-modern-pink">
                                        <span class="color-name">Розовый современный</span>
                                    </li>
                                    <li class="filter-option color color--deep-red" filter="color" filter-data='Красный глубокий' data-filter="color-winered">
                                        <span class="color-name">Красный глубокий</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </ol>
                    <li class="filter-option color color--green"></li>
                    <ol class="color_popup_wrap">
                        <div class="color_popup">
                            <div class="filter-item-wrap">
                                <ul class="filter__categories__content__item active" data-filtercategory="color">
                                    <li class="filter-option color color--modern-green" filter="color" filter-data='Зеленый современный' data-filter="color-pistachio">
                                        <span class="color-name">Зеленый современный</span>
                                    </li>
                                    <li class="filter-option color color--light-green" filter="color" filter-data='Зеленый нежный' data-filter="color-green">
                                        <span class="color-name">Зеленый нежный</span>
                                    </li>
                                    <li class="filter-option color color--deep-green" filter="color" filter-data='Зеленый глубоки' data-filter="color-khaki">
                                        <span class="color-name">Зеленый глубокий</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </ol>
                    <li class="filter-option color color--blue"></li>
                    <ol class="color_popup_wrap">
                        <div class="color_popup">
                            <div class="filter-item-wrap">
                                <ul class="filter__categories__content__item active" data-filtercategory="color">
                                    <li class="filter-option color color--blue" filter="color" filter-data='Голубой' data-filter="color-blue">
                                        <span class="color-name">Голубой</span>
                                    </li>
                                    <li class="filter-option color color--turquoise" filter="color" filter-data='Бирюзовый' data-filter="color-turquoise">
                                        <span class="color-name">Бирюзовый</span>
                                    </li>
                                    <li class="filter-option color color--deep-blue" filter="color" filter-data='Синий глубокий' data-filter="color-darkblue">
                                        <span class="color-name">Синий глубокий</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </ol>
                    <li class="filter-option color color--lilac"></li>
                    <ol class="color_popup_wrap">
                        <div class="color_popup">
                            <div class="filter-item-wrap">
                                <ul class="filter__categories__content__item active" data-filtercategory="color">
                                    <li class="filter-option color color--modern-lilac" filter="color" filter-data='Сиреневый современный' data-filter="color-modern-lilac">
                                        <span class="color-name">Сиреневый современный</span>
                                    </li>
                                    <li class="filter-option color color--light-lilac" filter="color" filter-data='Сиреневый нежный' data-filter="color-light-lilac">
                                        <span class="color-name">Сиреневый современный</span>
                                    </li>
                                    <li class="filter-option color color--deep-purple" filter="color" filter-data='Фиолетовый глубокий' data-filter="color-deep-purple">
                                        <span class="color-name">Фиолетовый глубокий</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </ol>
                </ul>
                <ul class="filter__categories__content__item" data-filtercategory="pattern">
                    <?php $i = 0;?>
                    <?php foreach($pictures as $picture):?>
                        <?php if($picture[1] != null):?>
                        <li class="filter-option"  filter="picture" filter-data ='<?php echo $picture[1]?>' data-filter="pattern-<?php echo $i++?>"><?php echo $picture[1]?></li>
                        <?php else:?>
                            <li class="filter-option"  filter="picture" filter-data ='NULL' data-filter="pattern-<?php echo $i++?>">NULL</li>
                        <?php endif;?>
                    <?php endforeach;?>
                </ul>
                <ul class="filter__categories__content__item" data-filtercategory="basis">
                    <?php $i = 0;?>
                    <?php foreach($basises as $basis):?>
                        <?php if($basis[1] != null):?>
                        <li class="filter-option"  filter="basis" filter-data ='<?php echo $basis[1]?>' data-filter="basis-<?php echo $i++?>"><?php echo $basis[1]?></li>
                        <?php else:?>
                            <li class="filter-option"  filter="basis" filter-data ='NULL' data-filter="basis-<?php echo $i++?>">NULL</li>
                        <?php endif;?>
                    <?php endforeach;?>
                </ul>
                <ul class="filter__categories__content__item" data-filtercategory="type">
                    <?php $i = 0;?>
                    <?php foreach($types as $type):?>
                        <?php if($type[1] != null):?>
                        <li class="filter-option"  filter="type" filter-data ='<?php echo $type[1]?>' data-filter="type-<?php echo $i++?>"><?php echo $type[1]?></li>
                        <?php else:?>
                            <li class="filter-option"  filter="type" filter-data ='NULL' data-filter="type-<?php echo $i++?>">NULL</li>
                        <?php endif;?>
                    <?php endforeach;?>
                </ul>
                <ul class="filter__categories__content__item" data-filtercategory="vendor" style="margin-bottom: 15px; ">
                    <div class="col-xs-6" >
                        <input type="text" class="form-control" id="vendor" ng-keyup="vendorSearch($event.keyCode)" ng-model="vendor"/>
                    </div>
                    <input type="button" class="btn btn-default" ng-click="vendorSearch()" value="Найти">
                </ul>
                <ul class="filter__categories__content__item" data-filtercategory="style">
                    <?php $i = 0;?>
                    <?php foreach($styles as $style):?>
                        <?php if($style[1] != null):?>
                            <li class="filter-option"  filter="style" filter-data ='<?php echo $style[1]?>' data-filter="style-<?php echo $i++?>"><?php echo $style[1]?></li>
                        <?php else:?>
                            <li class="filter-option"  filter="style" filter-data ='NULL' data-filter="style-<?php echo $i++?>">NULL</li>
                        <?php endif;?>
                    <?php endforeach;?>
                </ul>
                <ul class="filter__categories__content__item" data-filtercategory="room">
                    <?php $i = 0;?>
                    <?php foreach($rooms as $room):?>
                        <?php if($room[1] != null):?>
                            <li class="filter-option"  filter="room" filter-data ='<?php echo $room[1]?>' data-filter="room-<?php echo $i++?>"><?php echo $room[1]?></li>
                        <?php else:?>
                            <li class="filter-option"  filter="room" filter-data ='NULL' data-filter="room-<?php echo $i++?>">NULL</li>
                        <?php endif;?>
                    <?php endforeach;?>
                </ul>
                <ul class="filter__categories__content__item" data-filtercategory="size">
                    <?php $i = 0;?>
                    <?php foreach($sizes as $size):?>
                        <?php if($size[1] != null):?>
                            <li class="filter-option"  filter="size" filter-data ='<?php echo $size[1]?>' data-filter="size-<?php echo $i++?>"><?php echo $size[1]?> метра</li>
                        <?php else:?>
                            <li class="filter-option"  filter="size" filter-data ='NULL' data-filter="size-<?php echo $i++?>">NULL</li>
                        <?php endif;?>
                    <?php endforeach;?>
                </ul>
                <?php endif; ?>
                <div class="filter__categories__content__item slider-container" data-filtercategory="price">
                    <div id="slider-range"></div>
                    <div class="price-slider__min"></div>
                    <div class="price-slider__max"></div>
                    <div class="radio">
                        <label><input type="radio" name="price-type" ng-model="priceType" ng-change="changePriceType(0)" ng-value="0" class="ng-pristine ng-untouched ng-valid ng-not-empty" value="0"> Рулон</label>
                        <label><input type="radio" name="price-type" ng-model="priceType" ng-change="changePriceType(1)" ng-value="1" checked="" class="ng-pristine ng-untouched ng-valid ng-not-empty" value="1"><i>m <sup><small>2</small></sup></i></label>
                    </div>
                </div>
                <ul class="filter__categories__content__item country-block" data-filtercategory="country">
                    <?php $i = 0;?>
                    <?php foreach($countries as $key => $country):?>
                        <li class="country-box">
                            <ol class="color_popup_wrap">
                                <div class="color_popup">
                                    <div class="filter-item-wrap">
                                        <ul class="filter__categories__content__item active" data-filtercategory="country">
                                            <?php foreach($country as $manufacturer):?>
                                                <li class="filter-option" filter="country" filter-data='<?php echo $manufacturer?>' data-filter="country-<?php echo $i++?>">
                                                    <?php echo $manufacturer?>
                                                </li>
                                            <?php endforeach;?>
                                        </ul>
                                    </div>
                                </div>
                            </ol>

                            <?php if($key != null):?>
                                <div class="filter-option country"  filter="country" filter-data ='<?php echo $key?>' data-filter="country-<?php echo $i++?>"><?php echo $key?></div>
                            <?php else:?>
                                <div class="filter-option country"  filter="country" filter-data ='NULL' data-filter="country-<?php echo $i++?>">NULL</div>
                            <?php endif;?>
                            <?php $j = 0;?>
                        </li>

                    <?php endforeach;?>
                </ul>
                <div class="filter__categories__button filter-sidebar__handler--close">Сохранить</div>
            </div>

        </div>

        <div class="switcher col-md-5 col-lg-4">
            <a href="#" class="switcher__item active" ng-click="searchPropertiesFn($event, 'hot')">ХИТЫ</a>
            <a href="#" class="switcher__item" ng-click="searchPropertiesFn($event, 'halyava')">РАСПРОДАЖА</a>
            <a href="#" class="switcher__item" ng-click="searchPropertiesFn($event, 'new')">НОВИНКИ</a>
            <a href="#" class="switcher__item" ng-click="sort($event, 'price')">ПО ЦЕНЕ</a>

            <div>
                Всего артикулов: {{wallpapers.length}}
            </div>
        </div>
        <div class="col-xs-12 desktop-only">
            <div class="filter__categories__selected">
                <div class="filter__categories__inner">
                    <div class="filter__categories__selected__board"></div>
                    <div class="filter__categories__selected__clear">очистить всё</div>
                </div>
            </div>
        </div>
        <div class="select-container switcher--mobile select-box" >
            <select name="sorting"
                    ng-blur="searchPropertiesFnMob()"
                    ng-focus="searchPropertiesFnMob()"
                    ng-model="sortingMob" id="sortingSwitcher">
                <option value="" disabled selected>СОРТИРОВАТЬ</option>
                <option value='hot'>ХИТЫ</option>
                <option value='halyava'>РАСПРОДАЖА</option>
                <option value='new'>НОВИКИ</option>
                <option value='price'>ПО ЦЕНЕ</option>
            </select>
        </div>
        <div class="filter-sidebar__handler filter-sidebar__handler--open">фильтр</div>
        <div class="articules-mobile">
            Всего артикулов: {{wallpapers.length}}
        </div>
    </div>
    <div class="text-center">
        <ul ng-if="pager.pages.length" class="pagination" style="cursor:pointer">
            <li ng-class="{hide:pager.currentPage < 5 }">
                <a ng-click="setPage(1)">Первая</a>
            </li>
            <li ng-repeat="page in pager.pages" ng-class="{active:pager.currentPage === page}">
                <a ng-click="setPage(page)">{{page}}</a>
            </li>
            <li ng-class="{hide:pager.currentPage > pager.totalPages - 3}">
                <a ng-click="setPage(pager.totalPages)">Последняя</a>
            </li>
        </ul>
    </div>

    <main class="list row" style ="margin: 0;">
        <div class="col-sm-6 col-md-3 col-lg-2" ng-repeat="row in items">
            <a href="/<?php echo $prefix != '' ? $prefix . '/' : ''?>wallpaper/{{row.vendorCode}}"
               target="_blank" style="cursor:pointer;" ng-class="{
                    'list__item--sale' : (row.points == 1 || row.points == 2) && (row.priceOld != row.price),
                    'list__item--new' : row.points == 7,
                    'list__item--hot' : row.points == 5 || row.points == 4
                }
            " class = "list__item list__item--aqua" points = "{{ row.points }}">
                    <div class="list__item__pattern" style="background-image: url('/image?id={{row.image}}&width=175&height=243')"></div>
                    <div class="list__item__info">
                        <div class="list__item__info__code">
                            {{row.vendorCode}}
                        </div>
                        <div class="list__item__info__title">
                            {{row.catalog}}&nbsp;
                        </div>
                        <div class="list__item__info__title">
                            {{row.manufacturer}}&nbsp;
                        </div>
                        <div class="list__item__info__size">{{row.size}} x {{row.height|number:0}} м</div>
                        <div ng-if="(row.points == 1 || row.points == 2)  && (row.priceOld != row.price)"

                             ng-class="{'meter-sale' : priceType,
                             'rul-sale': !priceType}"

                             class="list__item__info__price"
                             data-old-price="{{(priceType ? row.m_old_price : row.priceOld)|  number:0}}">
                            {{(priceType ? row.m_price : row.price)| number:0}}
                        </div>
                        <div ng-if="(row.points != 1 && row.points != 2)"
                             ng-class="{'meter' : priceType,
                             'rul': !priceType}"
                             class="list__item__info__price"
                        >
                            {{(priceType ? row.m_price : row.price)| number:0}}
                        </div>
                        <div ng-if="(row.points == 1 || row.points == 2)  && (row.priceOld == row.price)"
                             ng-class="{'meter' : priceType,
                             'rul': !priceType}"
                             class="list__item__info__price"
                        >
                            {{(priceType ? row.m_price : row.price)| number:0}}
                        </div>
                        <div class="list__item__info__country" >{{row.country}} <span ng-if="user">(
                            {{(priceType ? row.m_count : row.count)|number:0}} |
                                {{(priceType ? row.m_totalCount : row.totalCount) | number:0}})</span>
                        </div>
                    </div>
                </a>
        </div>
    </main>
</div>


