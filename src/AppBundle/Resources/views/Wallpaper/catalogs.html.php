<?php $prefix = explode('/', $_SERVER['REQUEST_URI'])[1]; ?>
<?php $prefix = $prefix != 'new' && $prefix != 'other-new' ? '': $prefix; ?>
<?php switch($prefix): ?>
<?php case 'new': $view->extend('::smallBase.html.php'); break;?>
<?php case 'other-new': $view->extend('::largeBase.html.php'); break;?>
<?php default : $view->extend('::smallBase.html.php'); break;?>
<?php endswitch;?>
<?php $view['slots']->start('meta')?>
<link rel="stylesheet" href="/styles.css">
<?php $view['slots']->stop()?>
<?php $view['slots']->start('scripts')?>

<script src="/js/filter.js"></script>
<script src="/js/angular.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.7/angular-route.js"></script>
<script>
    (function(){
        var vendors = localStorage.getItem('vendors');
        if(vendors !== null) {
            vendors = vendors.split(",");
            $.each(vendors, function(e, val) {
                $('.list__item__info__like').each(function(e, el) {
                    if($(el).parent().find('.list__item__info__code').html() == val) {
                        $(el).addClass('list__item__info__like--active');
                    }
                });
            });
        }
    }());

    $(document).ready(function() {
        $('.list__item__info__like').click(function(){
            if($(this).hasClass('list__item__info__like--active')) {
                $(this).removeClass('list__item__info__like--active');
            } else {
                $(this).addClass('list__item__info__like--active');
            }
        });
    });
</script>

<script>

    hashCode = function(s){
        return s.split("").reduce(function(a,b){a=((a<<5)-a)+b.charCodeAt(0);return a&a},0);
    };

    var app = angular
        .module('wallpaper', ['ngRoute'])
        .filter('escape', function() {
            return window.encodeURIComponent;
        })
        .controller('mainCtrl', function($scope, $http) {
            $scope.wallpapers={
            };

            $scope.toFavorite = toFavorite;
            $scope.ITEMS_PER_PAGE = 3;

            $scope.vendor = "";
            $scope.priceType = 1;
            $scope.sortingMob = "";

            $scope.colors = [];
            $scope.pictures = [];
            $scope.textures = [];
            $scope.sizes = [];
            $scope.countries = [];
            $scope.glitter = "";

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

                    $scope.maxPrice = parseFloat(data.max_price);
                    $scope.minPrice = parseFloat(data.min_price);
                    $scope.maxPriceM = parseFloat(data.max_m_price);
                    $scope.minPriceM = parseFloat(data.min_m_price);
//                    $scope.changePriceType($scope.priceType);
                }, function errorCallback(response){

                });
            };

            $scope.getWallpapers = function() {
                $scope.updatePrice();
                $http({
                    method: 'POST',
                    url: '/get-filtered-collections',
                    data:
                    'query=' + JSON.stringify($scope.jsonQ)
                    ,
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }).then(function successCallback(response) {
                    var data = response.data;
                    $scope.wallpapers = data.catalogs;
                    $scope.totalCount = data.count;
                    $scope.collectionsCount = Object.keys($scope.wallpapers).length;
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
            };

            $scope.vendorSearch = function(key = false) {
                if(!key || key == 13) {
                    if ($('#vendor').val().length > 0) {
                        $scope.vendor = $('#vendor').val();
                    } else {
                        $scope.vendor = '';
                    }

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
                            }
                        });
                        $filterSidebarOpen.addClass('active');
                        $filterListContainer.find('[data-filtercategory="vendor"]').addClass('selected');
                        $scope.vendor = '';
                    }

                    $scope.filterChange();
                }
            };

            $scope.filterChange = function($showFilter = true) {
                var i = 0;
                $scope.colors = [];
                $scope.pictures = [];
                $scope.textures = [];
                $scope.sizes = [];
                $scope.countries = [];
                $scope.glitter = "";

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
                        case 'texture':
                            $scope.textures.push(value);
                            break;
                        case 'glitter':
                            $scope.glitter = value;
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
                $scope.textures = [];
                $scope.sizes = [];
                $scope.countries = [];
                $scope.glitter = "";
                $scope.vendor = "";


                $scope.jsonQ.priceStart = $scope.minPrice;
                $scope.jsonQ.priceFinish = $scope.maxPrice;

                $scope.searchProperties = [];
//                $scope.searchProperties['hot'] = false;
                $scope.searchProperties['halyava'] = false;
                $scope.searchProperties['new'] = false;
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
            };

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
                    'textures': $scope.textures,
                    'glitter': $scope.glitter,
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

            $scope.changePriceType(1);

            $scope.setJsonQ();
            filtersActivate($scope);

            $scope.getWallpapers();


            function initController() {
                $scope.offset = 0;
                $scope.page = 1;
            }

            function setPage(page) {

            }

            $scope.showMore = function() {
                if($scope.collectionsCount - ($scope.page-1) * $scope.ITEMS_PER_PAGE > $scope.ITEMS_PER_PAGE) {
                    $scope.page++;

                    $scope.offset = ($scope.page - 1) * $scope.ITEMS_PER_PAGE;
                }
            }
        }).filter('slice', function () {
            return function(items, start, count) {
                if(typeof items !== 'undefined') {
                    var i = 0;
                    var result = {};
                    $.each(items, function(item, value) {
                        i++;
                        if(i <= start) {
                            return true;
                        }

                        result[item] = value;

                        if(i == start + count) {
                            return false;
                        }
                    });
                    return result;
                }
                return {};
            };
        });
</script>

<?php $view['slots']->stop()?>

<div ng-app="wallpaper" ng-controller="mainCtrl" >
    <div class="head row">
        <h2 class="head__title title-1 col-md-6">Каталог обоев</h2>
        <div class="head__switcher switcher col-md-6">
            <a href="/<?php echo $prefix != '' ? $prefix . '/' : ''?>catalog" class="switcher__item">списком</a>
            /
            <a href="#" class="switcher__item active">коллекциями</a>
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
        <div class="filter__categories col-md-8 closed" id="filter-sidebar">
            <div class="blackout filter-sidebar__handler--close"></div>
            <div class="filter__categories__list">
                <div class="filter__categories__head clearfix">
                    <div class="filter__categories__head__title">Фильтры</div>
                    <div class="filter__categories__selected__clear">Сбросить</div>
                </div>
                <div class="filter__categories__list__item active" data-filtercategory="color">Цвет</div>
                <div class="filter__categories__list__item" data-filtercategory="pattern">Рисунок</div>
                <div class="filter__categories__list__item" data-filtercategory="vendor">Артикул</div>
                <div class="filter__categories__list__item" data-filtercategory="texture">Фактура</div>
                <div class="filter__categories__list__item" data-filtercategory="glitter">Глиттер</div>
                <div class="filter__categories__list__item" data-filtercategory="size">Ширина рулона</div>
                <div class="filter__categories__list__item" data-filtercategory="country">Страна</div>
                <div class="filter__categories__list__item" data-filtercategory="price">Цена</div>
                <div class="filter__categories__button filter-sidebar__handler--close">Готово</div>
            </div>
            <div class="filter__categories__content">
                <div class="filter__categories__head">
                    <div class="filter__categories__head__back">цвет</div>
                </div>
                <ul class="filter__categories__content__item active" data-filtercategory="color">
                    <li class="filter-option color color--white"></li>
                    <ol class="color_popup_wrap">
                        <div class="color_popup">
                            <div class="filter-item-wrap">
                                <ul class="filter__categories__content__item active" data-filtercategory="color">
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
                                    <li class="filter-option color color--peach" filter="color" filter-data='Персиковый' data-filter="color-peach">
                                        <span class="color-name">Персик</span>
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
                                    <li class="filter-option color color--yellow" filter="color" filter-data='Жёлтый' data-filter="color-yellow">
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
                                    <li class="filter-option color color--terracotta" filter="color" filter-data='Терракот' data-filter="color-terracotta">
                                        <span class="color-name">Терракот</span>
                                    </li>
                                    <li class="filter-option color color--brown" filter="color" filter-data='Коричневый' data-filter="color-brown">
                                        <span class="color-name">Коричневый</span>
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
                                    <li class="filter-option color color--pink" filter="color" filter-data='Розовый' data-filter="color-pink">
                                        <span class="color-name">Розовый</span>
                                    </li>
                                    <li class="filter-option color color--red" filter="color" filter-data='Красный' data-filter="color-red">
                                        <span class="color-name">Красный</span>
                                    </li>
                                    <li class="filter-option color color--wine--red" filter="color" filter-data='Бордо' data-filter="color-winered">
                                        <span class="color-name">Бордовый</span>
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
                                    <li class="filter-option color color--pistachio" filter="color" filter-data='Фисташковый' data-filter="color-pistachio">
                                        <span class="color-name">Фисташковый</span>
                                    </li>
                                    <li class="filter-option color color--green" filter="color" filter-data='Зелёный' data-filter="color-green">
                                        <span class="color-name">Зеленый</span>
                                    </li>
                                    <li class="filter-option color color--khaki" filter="color" filter-data='Хаки' data-filter="color-khaki">
                                        <span class="color-name">Хаки</span>
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
                                    <li class="filter-option color color--turquoise" filter="color" filter-data='Бирюзовый' data-filter="color-turquoise">
                                        <span class="color-name">Бирюзовый</span>
                                    </li>
                                    <li class="filter-option color color--blue" filter="color" filter-data='Голубой' data-filter="color-blue">
                                        <span class="color-name">Голубой</span>
                                    </li>
                                    <li class="filter-option color color--darkblue" filter="color" filter-data='Синий' data-filter="color-darkblue">
                                        <span class="color-name">Синий</span>
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
                                    <li class="filter-option color color--lilac" filter="color" filter-data='Сиреневый' data-filter="color-lilac">
                                        <span class="color-name">Сиреневый</span>
                                    </li>
                                    <li class="filter-option color color--purple" filter="color" filter-data='Фиолетовый' data-filter="color-purple">
                                        <span class="color-name">Фиолетовый</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </ol>
                </ul>
                <ul class="filter__categories__content__item" data-filtercategory="pattern">
                    <li class="filter-option" filter = "picture" filter-data = "Без рисунка"  data-filter="pattern-1">Без рисунка</li>
                    <li class="filter-option" filter="picture"  filter-data ='Геометрия' data-filter="pattern-2">Геометрия</li>
                    <li class="filter-option"  filter="picture" filter-data ='Полоса' data-filter="pattern-3">Полоса</li>
                    <li class="filter-option"  filter="picture" filter-data ='Дамаск' data-filter="pattern-4">Дамаск</li>
                    <li class="filter-option"  filter="picture" filter-data ='Цветы' data-filter="pattern-5">Цветы</li>
                    <li class="filter-option"  filter="picture" filter-data ='Сюжет' data-filter="pattern-6">Сюжет</li>
                    <li class="filter-option"  filter="picture" filter-data ='Узор' data-filter="pattern-7">Узор</li>
                </ul>
                <ul class="filter__categories__content__item" data-filtercategory="vendor" style="margin-bottom: 15px; ">
                    <div class="col-xs-6" >
                        <input type="text" class="form-control" id="vendor" ng-keyup="vendorSearch($event.keyCode)" ng-model="vendor"/>
                    </div>
                    <input type="button" class="btn btn-default" ng-click="vendorSearch()" value="Найти">
                </ul>
                <ul class="filter__categories__content__item" data-filtercategory="texture">
                    <li class="filter-option" filter="texture" filter-data="Дерево" data-filter="base-1">Дерево</li>
                    <li class="filter-option" filter="texture" filter-data="Камень, Мрамор" data-filter="base-2">Камень,Мрамор</li>
                    <li class="filter-option" filter="texture" filter-data="Камень, Прочее" data-filter="base-3">Камень,Прочее</li>
                    <li class="filter-option" filter="texture" filter-data="Камень, Штукатурка" data-filter="base-4">Камень,Штукатурка</li>
                    <li class="filter-option" filter="texture" filter-data="Кожа" data-filter="base-5">Кожа</li>
                    <li class="filter-option" filter="texture" filter-data="Металл" data-filter="base-6">Металл</li>
                    <li class="filter-option" filter="texture" filter-data="Текстиль Гладкий" data-filter="base-7">Текстиль Гладкий</li>
                    <li class="filter-option" filter="texture" filter-data="Текстиль Грубый" data-filter="base-8">Текстиль Грубый</li>
                    <li class="filter-option" filter="texture" filter-data="Текстиль Средний" data-filter="base-9">Текстиль Средний</li>
                </ul>
                <ul class="filter__categories__content__item" data-filtercategory="base">
                    <li class="filter-option" filter="base" filter-data="true" data-filter="base-1">с глиттером</li>
                    <li class="filter-option" filter="base" filter-data="false" data-filter="base-2">без глиттера</li>
                    <li class="filter-option" filter="base" filter-data="null" data-filter="base-3">не указанно</li>
                </ul>
                <ul class="filter__categories__content__item" data-filtercategory="glitter">
                    <li class="filter-option" filter="glitter" filter-data="true" data-filter="base-1">с глиттером</li>
                    <li class="filter-option" filter="glitter" filter-data="false" data-filter="base-2">без глиттера</li>
                    <li class="filter-option" filter="glitter" filter-data="null" data-filter="base-3">не указанно</li>
                </ul>
                <ul class="filter__categories__content__item" data-filtercategory="size">
                    <li class="filter-option" filter = "size" filter-data = "0.53"  data-filter="size-1">0.53 метра</li>
                    <li class="filter-option" filter="size"  filter-data ='0.7' data-filter="size-2">0.7 метра</li>
                    <li class="filter-option"  filter="size" filter-data ='1.06' data-filter="size-3">1.06 метра</li>
                </ul>
                <ul class="filter__categories__content__item" data-filtercategory="country">
                    <li class="filter-option" filter = "country" filter-data = "Россия"  data-filter="country-1">Россия</li>
                    <li class="filter-option" filter="country"  filter-data ='Украина' data-filter="country-2">Украина</li>
                    <li class="filter-option"  filter="country" filter-data ='Италия' data-filter="country-3">Италия</li>
                    <li class="filter-option"  filter="country" filter-data ='Бельгия' data-filter="country-4">Бельгия</li>
                    <li class="filter-option"  filter="country" filter-data ='США' data-filter="country-5">США</li>
                    <li class="filter-option"  filter="country" filter-data ='Китай' data-filter="country-6">Китай</li>
                    <li class="filter-option"  filter="country" filter-data ='Корея' data-filter="country-7">Корея</li>
                    <li class="filter-option"  filter="country" filter-data ='Германия' data-filter="country-8">Германия</li>
                    <li class="filter-option"  filter="country" filter-data ='Англия' data-filter="country-9">Англия</li>
                    <li class="filter-option"  filter="country" filter-data ='Голландия' data-filter="country-10">Голландия</li>
                </ul>
                <div class="filter__categories__content__item slider-container" data-filtercategory="price">
                    <div id="slider-range"></div>
                    <div class="price-slider__min"></div>
                    <div class="price-slider__max"></div>
                    <div class="radio">
                        <label><input type="radio" name="price-type" ng-model="priceType" ng-change="changePriceType(0)" ng-value="0" class="ng-pristine ng-untouched ng-valid ng-not-empty" value="0"> Рулон</label>
                        <label><input type="radio" name="price-type" ng-model="priceType" ng-change="changePriceType(1)" ng-value="1" checked="" class="ng-pristine ng-untouched ng-valid ng-not-empty" value="1"><i>m <sup><small>2</small></sup></i></label>
                    </div>
                </div>
                <div class="filter__categories__button filter-sidebar__handler--close">Сохранить</div>
            </div>
            <div class="filter__categories__selected">
                <div class="filter__categories__selected__board"></div>
                <div class="filter__categories__selected__clear">очистить всё</div>
            </div>
        </div>

        <div class="switcher col-md-4">
            <a href="#" class="switcher__item active" ng-click="searchPropertiesFn($event, 'hot')">ХИТЫ</a>
            <a href="#" class="switcher__item" ng-click="searchPropertiesFn($event, 'halyava')">РАСПРОДАЖА</a>
            <a href="#" class="switcher__item" ng-click="searchPropertiesFn($event, 'new')">НОВИНКИ</a>
            <a href="#" class="switcher__item" ng-click="sort($event, 'price')">ПО ЦЕНЕ</a>

            <div>
                Всего артикулов: {{totalCount}}
            </div>
        </div>
        <div class="select-container switcher--mobile">
            <select name="sorting" ng-blur="searchPropertiesFnMob()"

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
    </div>
    <section class="section-collection-list row" ng-repeat="(key, catalog) in wallpapers | slice: offset:ITEMS_PER_PAGE">
        <div class="col-sm-12 col-md-5 collection">
            <div class="collection__info">
                <h2 class="collection__info__name title-2">{{key}}</h2>
                <h3 class="collection__info__from">{{catalog.country}},{{catalog.manufacturer}}</h3>
            </div>
            <div class="collection__image"
                 style="background-image: url(/get-interior-image/{{ catalog.catalog | escape }});"></div>
        </div>
        <div class="col-sm-12 col-md-7"><div class="row list">
                <div class="col-sm-6 col-md-4" ng-repeat="vendor in catalog.vendors | limitTo: 5" >
                    <div style="cursor:pointer;" ng-class="{
                    'list__item--sale' : vendor.points == 1 || vendor.points == 2,
                    'list__item--new' : vendor.points == 7,
                    'list__item--hot' : vendor.points == 5 || vendor.points == 4
                }
            " class = "list__item list__item--aqua">
                        <div class="list__item__pattern"
                             style="background: url('/image?id={{vendor.image | escape }}&width=300&height=300') no-repeat"></div>
                        <div class="list__item__info">
                            <div class="list__item__info__code">{{vendor.vendorCode}}</div>
                            <div class="list__item__info__like"></div>
                            <a href="/new/wallpaper/{{vendor.vendorCode}}" target="_blank" class="list__item__info__title">
                                {{vendor.catalog}}
                            </a>
                            <div class="list__item__info__title">{{vendor.manufacturer}}</div>
                            <div class="list__item__info__size">{{vendor.size}} x {{vendor.height|number:0}} м</div>
                            <div class="list__item__info__price" ng-if="vendor.points != 1 && vendor.points != 2"
                                 ng-class="{'meter' : priceType,
                                'rul': !priceType}"
                            >{{(priceType ? vendor.m_price : vendor.price)| number:0}}</div>
                            <div ng-if="vendor.points == 1 || vendor.points == 2"

                                 ng-class="{'meter-sale' : priceType,
                             'rul-sale': !priceType}"

                                 class="list__item__info__price"
                                 data-old-price="{{(priceType ? vendor.m_old_price : vendor.priceOld)|  number:0}}">
                                {{(priceType ? vendor.m_price : vendor.price)| number:0}}
                            </div>
                            <div class="list__item__info__country">{{vendor.country}}
                                ({{(priceType ? vendor.m_count : vendor.count)|number:0}} | {{(priceType ? vendor.m_totalCount : vendor.totalCount)|number:0}})</div>
                        </div>
                    </div>
                </div>

                <a ng-hide="catalog.vendors.length < 5" href="/<?php echo $prefix != '' ? $prefix . '/' : ''?>collection-page/{{catalog.vendors[0].vendorCode}}"
                   class="col-sm-6 col-md-3 col-md-offset-1 list__item--next">
                    <span class="arrow-link">Вся коллекция</span>
                    <div class="list__item--next__count">{{catalog.vendors.length}}<small>видов</small></div>
                </a>
            </div>
        </div>
    </section>
    <a href="#" ng-hide="collectionsCount - (page-1) * ITEMS_PER_PAGE < ITEMS_PER_PAGE">
        <div class="show-more" ng-click="showMore()">
            <span class="show-more__phrase">Показать ещё</span>
            <p><span class="show-more__current">{{ ITEMS_PER_PAGE }}</span>
                из {{collectionsCount - (page-1) * ITEMS_PER_PAGE }}
            </p>
        </div>
    </a>
</div>