<?php $prefix = explode('/', $_SERVER['REQUEST_URI'])[1]; ?>
<?php $prefix = $prefix != 'new' && $prefix != 'other-new' ? '': $prefix; ?>
<?php switch($prefix): ?>
<?php case 'new': $view->extend('::smallBase.html.php'); break;?>
    <?php case 'other-new': $view->extend('::largeBase.html.php'); break;?>
    <?php default : $view->extend('::base.html.php'); break;?>
    <?php endswitch;?>
<?php $view['slots']->start('meta')?>
<link rel="stylesheet" href="/styles.css">

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
        .factory('PagerService', PagerService)
        .controller('mainCtrl', function($scope, PagerService, $http) {
            $scope.wallpapers={
            };
            $scope.vendors = localStorage.getItem('vendors') != null ? localStorage.getItem('vendors').split(",") : [];

            $scope.redirectTo = function(url) {
                window.open('/new/wallpaper/'+url, '_blank');
            };

            $scope.clear = function () {
                localStorage.clear();
                window.location.reload(true);
            };

            $scope.getWallpapers = function() {
                $http({
                    method: 'POST',
                    url: '/get-favorites',
                    data:
                    'query=' + JSON.stringify({'vendors': $scope.vendors})
                    ,
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }).then(function successCallback(response) {
                    var data = response.data;
                    $scope.wallpapers = data;
                }, function errorCallback(response){

                })
            };
            $scope.getWallpapers();
        })
</script>
<div ng-app="wallpaper" ng-controller="mainCtrl" >
    <div class="head row">
        <h2 class="head__title title-1 col-md-6">Избранные</h2>
        <div class="col-md-6 text-right"><button class="btn btn-default" ng-click="clear()">Очистить</div>
    </div>

    <main class = "list row">
        <div class="col-sm-6 col-md-2" ng-repeat="row in wallpapers">
            <div ng-click="redirectTo(row.id)" style="cursor:pointer;" ng-class="{
                    'list__item--sale' : row.points == 5,
                    'list__item--new' : row.points == 7,
                    'list__item--hot' : row.points == 1
                }
            " class = "list__item list__item--aqua" points = "{{parseInt(row.points)}}">
                <a href="/<?php echo $prefix != '' ? $prefix . '/' : ''?>wallpaper/{{row.id}}" target="_blank"  class="list__item__pattern"
                   style="background-image: url('/image?id={{row.image}}&width=231&height=174')"></a>
                <div class="list__item__info">
                    <div class="list__item__info__code">{{row.vendorCode}}</div>
                    <div class="list__item__info__title">{{row.manufacturer}}</div>
                    <div class="list__item__info__title" style="white-space: nowrap;
    overflow: hidden;">&nbsp; {{row.catalog}}</div>
                    <div class="list__item__info__size">{{row.height|number:0}} x {{row.size}}</div>

                    <div ng-if="(row.points == 1 || row.points == 2)  && (row.priceOld != row.price)"


                         class="list__item__info__price"
                         data-old-price="{{ row.priceOld |  number:0}}">
                        {{ row.price)| number:0}}
                    </div>
                    <div ng-if="(row.points != 1 && row.points != 2)"
                         ng-class="{'meter' : priceType,
                             'rul': !priceType}"
                         class="list__item__info__price"
                    >
                        {{  row.price| number:0}}
                    </div>
                    <div ng-if="(row.points == 1 || row.points == 2)  && (row.priceOld == row.price)"
                         ng-class="{'meter' : priceType,
                             'rul': !priceType}"
                         class="list__item__info__price"
                    >
                        {{ row.price | number:0}}
                    </div>
                    <div class="list__item__info__country" >{{row.country}}
                    </div>
                </div>
            </div>

            <div class="list__item__info__qr">
                <img src="http://qrcoder.ru/code/?http://gallery.kg/wallpaper/{{row.vendorCode()}}&3&0">
            </div>
        </div>
    </main>
</div>


