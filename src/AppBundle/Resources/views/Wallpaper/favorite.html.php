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
    }

    angular
        .module('wallpaper', ['ngRoute'])
        .factory('PagerService', PagerService)
        .controller('mainCtrl', function($scope, PagerService, $http) {
            $scope.wallpapers={
            };
            $scope.vendors = localStorage.getItem('vendors').split(",");

            $scope.redirectTo = function(url) {
                window.open('/new/wallpaper/'+url, '_blank');
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
    </div>

    <main class = "list row">
        <div class="col-sm-6 col-md-2" ng-repeat="row in wallpapers">
            <div ng-click="redirectTo(row.id)" style="cursor:pointer;" ng-class="{
                    'list__item--sale' : row.points == 5,
                    'list__item--new' : row.points == 7,
                    'list__item--hot' : row.points == 1
                }
            " class = "list__item list__item--beige" points = "{{parseInt(row.points)}}">
                <a href="/<?php echo $prefix != '' ? $prefix . '/' : ''?>wallpaper/{{row.id}}" target="_blank"  class="list__item__pattern"
                   style="background-image: url('/image?id={{row.image}}&width=231&height=174')"></a>
                <div class="list__item__info">
                    <div class="list__item__info__code">{{row.vendorCode}}</div>
                    <div class="list__item__info__code">{{row.manufacturer}}</div>
                    <a href="/<?php echo $prefix != '' ? $prefix . '/' : ''?>wallpaper/{{row.id}}" target="_blank" class="list__item__info__title" style="white-space: nowrap;
    overflow: hidden;">&nbsp; {{row.catalog}}</a>
                    <div class="list__item__info__size">{{row.height|number:0}} x {{row.size}}</div>
                    <div ng-if="row.points == 5" class="list__item__info__price" data-old-price="{{priceType ? row.m_old_price : row.priceOld}}">
                        {{(priceType ? row.m_price : row.price)| number:0}}
                    </div>
                    <div ng-if="row.points != 5" class="list__item__info__price" >
                        {{(priceType ? row.m_price : row.price)| number:0}}
                    </div>

                    <div class="list__item__info__country">{{row.country}} ({{(priceType ? row.m_count : row.count)|number:0}} | {{(priceType ? row.m_totalCount : row.totalCount)|number:0}})</div>
                </div>
            </div>
        </div>
    </main>
</div>


