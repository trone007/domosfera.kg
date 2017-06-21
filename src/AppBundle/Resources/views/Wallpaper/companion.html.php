<?php $view->extend('::shaping.html.php') ?>
<?php $view['slots']->start('meta')?>
<link rel="stylesheet" href="/collection.css">
<link rel="stylesheet" href="/notebook-create.css">
<link rel="stylesheet" href="/css/switcher.css">

<?php $view['slots']->stop()?>

<?php $view['slots']->start('scripts')?>
<script src="/js/pagerService.js"></script>
<script src="/js/sticky-kit.js"></script>
<script src="/js/base64.js"></script>
<script src="/js/underscore-min.js"></script>
<script src="/js/angular.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-filter/0.5.14/angular-filter.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.7/angular-route.js"></script>
<script>
    var initScopeVariables = function($scope) {

        $scope.country = "";

        $scope.manufacturer = "";
        $scope.manufacturers = [];

        $scope.collection = "";
        $scope.collections = [];

        $scope.catalog = "";
        $scope.catalogs = [];

        $scope.wallpapers = [];
        $scope.complects = {
            'complects': [],
            'roots': []
        };
        $scope.checkedWallpapers=[];

        $scope.viewMode = true;

        $scope.countersFilter = {
            'country': null,
            'manufacturer': null,
            'catalog': null
        };

        $scope.counters = {
            'total': null,
            'country': null,
            'manufacturer': null,
            'catalog': null,
            'collection': null
        }

        $scope.vendors = {
            'root' : null,
            'node' : []
        };

        $scope.root = {};

    }
    angular
        .module('wallpaper', ['ngRoute', 'angular.filter'])
        .factory('PagerService', PagerService)
        .filter("optionalFilter",function(){
            return function(items, rootVendor) {
                var filtered = [];
                angular.forEach(items, function (value, key) {
                    if (value.rootVendor == rootVendor) {
                        this.push(value);
                    }
                }, filtered);
                return filtered;
            }
        })
        .controller('mainCtrl', function($scope, PagerService, $http) {
            initScopeVariables($scope);

            $scope.changeViewMode = function() {
                if($('.onoffswitch-inner').css('margin-left') == '0px') {
                    $scope.viewMode = false;
                    $('#single-vendor').hide()
                    $scope.checkedWallpapers = [];
                    $scope.wallpaper = [];
                } else {
                    $scope.viewMode = true;
                }

                if($scope.catalog)  {
                    $scope.changeCatalog();
                }
            };

            $scope.recalcCounters = function() {
                $scope.countersFilter.country = Base64.encode($scope.country);
                $scope.countersFilter.manufacturer = Base64.encode($scope.manufacturer);
                $scope.countersFilter.catalog = Base64.encode($scope.catalog);
                $scope.countersFilter.collection = Base64.encode($scope.collection);

                $http({
                    method: 'POST',
                    url: '/get-counts',
                    data:
                    'query=' + encodeURIComponent(JSON.stringify($scope.countersFilter)),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }).then(function successCallback(response) {
                    var data = response.data;

                    $scope.counters.total = data.total;
                    $scope.counters.country = data.country;
                    $scope.counters.manufacturer = data.manufacturer;
                    $scope.counters.catalog = data.catalog;
                    $scope.counters.collection = data.collection;

                }, function errorCallback(response){

                })
            };

            $scope.triggerChangeComplect = function (root) {
                $.each($scope.catalogs, function(event, value) {
                    if(value.label == root.catalog) {
                        $scope.catalog = value.label;
                        $scope.root = root;

                        $('.onoffswitch-inner').click();
                        $scope.changeViewMode();
                        return false;
                    }
                });
            };

            $scope.checkFn = function($event, vendor, notMain = false) {
                if($($event.target).get(0).tagName == 'A') {
                    return false;
                }

                if(!notMain) {
                    if ($($event.currentTarget).hasClass('bordered-div')) {
                        $($event.currentTarget).removeClass('bordered-div');
                    } else {
                        $($event.currentTarget).addClass('bordered-div');
                    }
                } else {
                    $('.bordered-div').each(function(event, element){
                        if(vendor == JSON.parse($(this).attr('vendor')).vendorCode) {
                            $(this).removeClass('bordered-div');
                        }
                    });
                }

                $scope.vendors = {
                    'root' : null,
                    'node' : []
                };

                $scope.checkedWallpapers = [];

                $scope.vendors.root = $scope.wallpaper.vendorCode;

                $('.bordered-div').each(function(event, element){
                    var el = JSON.parse($(element).attr('vendor'));
                    $scope.vendors.node.push(el.vendorCode);

                    $scope.checkedWallpapers.push({
                        'vendor': el.vendorCode,
                        'picture': el.picture,
                        'texture': el.texture,
                        'notebook': el.notebook,
                        'image' : el.image
                    });
                });
                if($scope.checkedWallpapers.length > 0) {
                    $('#single-vendor').show();
                }
                $('.sticky-spacer').height(0);
            };

            $scope.changeCountry = function() {
                $('#manufacturer-id').hide();
                $('#catalog').hide();
                $('#collection').hide();
                $('#single-vendor').hide();

                $scope.manufacturer = "";
                $scope.collection = "";
                $scope.catalog = "";
                $scope.wallpapers = [];
                $scope.wallpaper = [];
                $scope.complects = {
                    'complects': [],
                    'roots': []
                };

                $http({
                    method: 'POST',
                    url: '/get-manufacturer',
                    data:
                    'query=' + encodeURIComponent(JSON.stringify({country: Base64.encode($scope.country)})),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }).then(function successCallback(response) {
                    var data = response.data;
                    var i = 0;
                    $scope.manufacturers = [];

                    $.each(data, function(e, value) {
                        $scope.manufacturers.push({
                            'id': i++,
                            'label': value.name,
                            'color': value.not_handled == 0 ? 'darkgreen' : 'darkred'
                        })
                    });

                    $('#manufacturer-id').show();
                    $scope.recalcCounters();
                }, function errorCallback(response){

                })
            };

            $scope.changeManufacturer= function() {
                $('#collection').hide();
                $('#single-vendor').hide();

                $scope.collection = "";
                $scope.catalog = "";
                $scope.collections = [];
                $scope.wallpapers = [];
                $scope.wallpaper = [];
                $scope.complects = {
                    'complects': [],
                    'roots': []
                };

                $scope.checkedWallpapers = [];

                $http({
                    method: 'POST',
                    url: '/get-catalog',
                    data:
                    'query=' + encodeURIComponent(JSON.stringify({manufacturer: Base64.encode($scope.manufacturer)})),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }).then(function successCallback(response) {
                    var data = response.data;
                    var i = 0;
                    $scope.catalogs = [];

                    $.each(data, function(e, value) {
                        $scope.catalogs.push({
                            'id': i++,
                            'label': value.name,
                            'color': value.not_handled == 0 ? 'darkgreen' : 'darkred'
                        })
                    });

                    $('#catalog').show();
                    $scope.recalcCounters();
                }, function errorCallback(response){

                })
            };

            $scope.changeCatalog = function() {
                $('#collection').hide();
                $('#single-vendor').hide();
                $scope.collection = "";
                $scope.wallpapers = [];
                $scope.wallpaper = [];
                $scope.complects = {
                    'complects': [],
                    'roots': []
                };
                $scope.checkedWallpapers = [];

                $http({
                    method: 'POST',
                    url: '/get-vendors',
                    data:
                    'query=' + encodeURIComponent(JSON.stringify({catalog: Base64.encode($scope.catalog)})),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }).then(function successCallback(response) {
                    var data = response.data;
                    $scope.wallpapers = response.data.vendors;
                    $scope.complects.complects = response.data.complects.complects;
                    $scope.complects.roots = response.data.complects.roots;

                    if(Object.keys($scope.root).length != 0) {
                        $scope.wallpaper = $scope.root;
                        var position = 0;

                        $.each($scope.wallpapers, function(event, element) {
                            if(element.vendorCode==$scope.root.vendorCode) {
                                $scope.wallpapers.splice(position, 1);
                                $scope.root = {};
                                return false;
                            }

                            position++;
                        })
                    } else {
                        $scope.wallpaper =  $scope.wallpapers[0];
                        $scope.wallpapers.splice(0,1);
                    }

                    $scope.vendors = {
                        'root' : null,
                        'node' : []
                    };

                    $('.vendor-items').removeClass('bordered-div');

                    $.each($scope.complects.complects, function(event, complect) {
                        $.each(complect, function(event, el) {
                            if(el == null) return true;
                            var rootVendor = el.rootVendor;

                            if($scope.wallpaper.vendorCode == rootVendor) {
                                changeCheckedWallpapers(el)
                            }
                        })
                    });

                    if(!$scope.viewMode) {
                        $('#single-vendor').show();
                    }
                    setTimeout($scope.updateComplect, 100);

                    $scope.recalcCounters();
                    $scope.$applyAsync();
                }, function errorCallback(response){
                })
            };

            $scope.changeCollection= function() {
                $('#single-vendor').hide();

                $scope.wallpapers = [];
                $scope.wallpaper = [];
                $scope.complects = {
                    'complects': [],
                    'roots': []
                };
                $scope.checkedWallpapers = [];

                $http({
                    method: 'POST',
                    url: '/get-vendors',
                    data:
                    'query=' + encodeURIComponent(JSON.stringify({
                        collection: Base64.encode($scope.collection),
                        catalog: Base64.encode($scope.catalog)
                    })),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }).then(function successCallback(response) {
                    var data = response.data;
                    $scope.wallpapers = response.data.vendors;
                    $scope.complects.complects = response.data.complects.complects;
                    $scope.complects.roots = response.data.complects.roots;

                    if(Object.keys($scope.root).length != 0) {
                        $scope.wallpaper = $scope.root;
                        var position = 0;

                        $.each($scope.wallpapers, function(event, element) {
                            if(element.vendorCode==$scope.root.vendorCode) {
                                $scope.wallpapers.splice(position, 1);
                                $scope.root = {};
                                return false;
                            }

                            position++;
                        })
                    } else {
                        $scope.wallpaper =  $scope.wallpapers[0];
                        $scope.wallpapers.splice(0,1);
                    }

                    $scope.vendors = {
                        'root' : null,
                        'node' : []
                    };

                    $('.vendor-items').removeClass('bordered-div');

                    $.each($scope.complects.complects, function(event, complect) {
                        $.each(complect, function(event, el) {
                            var rootVendor = el.rootVendor;

                            if($scope.wallpaper.vendorCode == rootVendor) {
                                changeCheckedWallpapers(el)
                            }
                        })
                    });

                    if(!$scope.viewMode) {
                        $('#single-vendor').show();
                    }
                    setTimeout($scope.updateComplect, 100);

                    $scope.recalcCounters();
                    $scope.$applyAsync();
                }, function errorCallback(response){
                })
            };

            $scope.saveComplect = function() {
                $scope.vendors.root = $scope.wallpaper.vendorCode;
                $scope.checkedWallpapers = [];

                $http({
                    method: 'POST',
                    url: '/save-complect',
                    data:
                    'query=' +encodeURIComponent(JSON.stringify({
                        catalog: $scope.catalog,
                        vendors: $scope.vendors
                    })),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }).then(function successCallback(response) {
                    var data = response.data;
                    $scope.complects.complects = data.complects;
                    $scope.complects.roots = data.roots;

                    $('.bordered-div').removeClass('bordered-div');
                    $scope.wallpaper.complectCode = 'selected';

                    $scope.wallpapers.push($scope.wallpaper);
                    $tmp = $scope.wallpapers[0];

                    $scope.wallpapers.splice(0,1);
                    $scope.wallpaper = $tmp;

                    $scope.vendors = {
                        'root' : null,
                        'node' : []
                    };

                    $('.vendor-items').removeClass('bordered-div');

                    $.each($scope.complects, function(event, complect) {
                        $.each(complect, function(event, el) {
                            var rootVendor = el.rootVendor;

                            if($scope.wallpaper.vendorCode == rootVendor) {
                                changeCheckedWallpapers(el);
                            }
                        })
                    });

                    $scope.recalcCounters();
                    $scope.$applyAsync();
                }, function errorCallback(response){

                })
            };

            var changeCheckedWallpapers = function (vendor) {
                $('.vendor-items').each(function(ev, element) {
                    var node = typeof $(element).attr('vendor') !== 'undefined' ? JSON.parse($(element).attr('vendor')) : '';

                    if(node.vendorCode == vendor.vendorCode) {
                        $(element).addClass('bordered-div');
                    }
                });

                $scope.vendors.root = vendor.rootVendor;
                $scope.vendors.node.push(vendor.vendorCode);

                $scope.checkedWallpapers.push({
                    'vendor': vendor.vendorCode,
                    'picture': vendor.picture,
                    'texture': vendor.texture,
                    'notebook': vendor.notebook,
                    'image' : vendor.image
                });

                $(window).trigger('resize');
                $scope.$applyAsync();
            };

            $scope.updateComplect = function() {

                $scope.vendors = {
                    'root' : null,
                    'node' : []
                };

                $('.vendor-items').removeClass('bordered-div');

                $.each($scope.complects, function(event, complect) {
                    $.each(complect, function(event, el) {
                        var rootVendor = el.rootVendor;

                        if($scope.wallpaper.vendorCode == rootVendor) {
                            changeCheckedWallpapers(el)
                        }
                    })
                });
            };

            $scope.recalcCounters();

            $scope.checkVendors = function(key) {
                $('.vendor-items').removeClass('bordered-div');
            };
        })
</script>


<?php $view['slots']->stop()?>

<?php $view['slots']->start('component')?>
<!--<tr ng-hide="collections == ''">-->
<!--    <td class="col-md-3">-->
<!--        <div>Коллекция</div>-->
<!--    </td>-->
<!--    <td class="col-md-6">-->
<!--        <select class="form-control"-->
<!--                ng-change="changeCollection()"-->
<!--                ng-model="collection"-->
<!--                style="display:none;"-->
<!--                id="collection"-->
<!--        >-->
<!--            <option value=""></option>-->
<!--            <option ng-repeat="item in collections" style="color:{{item.color}}" value="{{item.label}}">{{item.label}}</option>-->
<!--        </select>-->
<!--    </td>-->
<!--    <td class="col-md-3">-->
<!--        <span style="color:red">{{counters.collection[0].not_handled}}</span>-->
<!--        <span style="color:green">{{counters.collection[0].handled}}</span>-->
<!--    </td>-->
<!--</tr>-->

<?php $view['slots']->stop()?>

<?php $view['slots']->start('single_vendor')?>
<div id='single-vendor' style="display: none;  border:none;">
    <div class="list__item list__item--beige main-item">
        <div class="list__item__info" style="padding: 5px 0px 15px; width:55%; float:left;">
            <div class="list__item__info__code">{{wallpaper.vendorCode}}</div>
            <div class="list__item__info__code">{{wallpaper.texture}}</div>
            <div class="list__item__info__code">{{wallpaper.picture}}</div>
            <div class="list__item__info__code">{{wallpaper.notebook}} &nbsp;</div>
            <div class="list__item__info__code" style="padding-bottom: 10px">
                <a style="width: 100%" href="http://www.gallery.kg/image/кыргызстан/{{wallpaper.image}}" target="_blank">
                    Изображение
                </a>
            </div>
            <div class="list__item__info__code">
                <button class="btn btn-success" style="width: 100%" ng-click="saveComplect()">Сохранить</button>
            </div>
        </div>
        <div class="list__item__pattern"
             style="background-image: url('http://www.gallery.kg/image/кыргызстан/{{wallpaper.image}}/174/231'); background-repeat: no-repeat; width: 45%;  height:120px; float:left"></div>
    </div>
    <div class="col-sm-12 col-md-12 col-lg-12 vendor-children">
        <div ng-repeat="row in checkedWallpapers">
            <div class="list__item--beige vendor-items"
                 ng-click="checkFn($event, row.vendor, true)"
                 style="cursor:pointer;" vendor="">
                <div class="list__item__info" style="padding: 5px 0px 15px; width:55%; float:left">
                    <div class="list__item__info__code">{{row.vendor}}</div>
                    <div class="list__item__info__code">{{row.texture}}</div>
                    <div class="list__item__info__code">{{row.picture}}</div>
                    <div class="list__item__info__code">{{row.notebook}} &nbsp;</div>
                    <div class="list__item__info__code">
                        <a style="width: 100%" href="http://www.gallery.kg/image/кыргызстан/{{row.image}}"target="_blank">
                            Изображение
                        </a>
                    </div>
                </div>
                <div  class="list__item__pattern"
                      style="background-image: url('http://www.gallery.kg/image/кыргызстан/{{row.image}}/174/231'); background-repeat: no-repeat; width: 45%;  height:120px; float:left"></div>
            </div>
        </div>
    </div>
</div>
<?php $view['slots']->stop()?>
<div class="content">
    <div ng-if="viewMode == false" >
        <div class = "list row">
            <div ng-repeat="(key, value) in wallpapers | groupBy: 'pictureMain'">
                <div class="col-sm-12">
                    {{key}}<br/>
                </div>
                <div ng-repeat="row in value">
                    <div class="col-sm-2">
                        <div ng-if="row.complectCode == null">
                            <div class="list__item list__item--beige vendor-items"
                                 ng-click="checkFn($event, row.vendorCode)"
                                 style="cursor:pointer; border: 2px solid darkred !important;" vendor="{{row}}" image="{{row.image}}">
                                <div class="list__item__info">
                                    <div class="list__item__pattern"
                                         style="background-image: url('http://www.gallery.kg/image/кыргызстан/{{row.image}}/174/231')"></div>
                                    <div class="list__item__info__code" style="cursor:pointer;">{{row.vendorCode}}</div>
                                    <div class="list__item__info__code" style="cursor:pointer;">{{row.texture}}</div>
                                    <div class="list__item__info__code" style="cursor:pointer;">{{row.picture}}</div>
                                    <div class="list__item__info__code" style="cursor:pointer;">{{row.notebook}} &nbsp;</div>
                                    <div class="list__item__info__code" style="padding-bottom: 10px">
                                        <a style="width: 100%" href="http://www.gallery.kg/image/кыргызстан/{{row.image}}" target="_blank">
                                            Изображение
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div ng-if="row.complectCode != null">
                            <div class="list__item list__item--beige vendor-items"
                                 ng-click="checkFn($event, row.vendorCode)"
                                 style="cursor:pointer; border: 2px solid darkgreen !important;" vendor="{{row}}" image="{{row.image}}">
                                <div class="list__item__info">
                                    <div class="list__item__pattern"
                                         style="background-image: url('http://www.gallery.kg/image/кыргызстан/{{row.image}}/174/231')"></div>
                                    <div class="list__item__info__code" style="cursor:pointer;" >{{row.vendorCode}}</div>
                                    <div class="list__item__info__code" style="cursor:pointer;" >{{row.texture}}</div>
                                    <div class="list__item__info__code" style="cursor:pointer;" >{{row.picture}}</div>
                                    <div class="list__item__info__code" style="cursor:pointer;" >{{row.notebook}}  &nbsp;</div>
                                    <div class="list__item__info__code" style="padding-bottom: 10px">
                                        <a style="width: 100%" href="http://www.gallery.kg/image/кыргызстан/{{row.image}}" target="_blank">
                                            Изображение
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div ng-if="viewMode == true">
        <div class = "container" >
            <div ng-repeat="row in complects.roots" class="complect-catalog-block section-product">
                <div style="display: table;">
                    <div style="display: table-row;">
                        <div class="complect-catalog">
                            <div class="list__item list__item--beige vendor-items"
                                 style="border: 2px solid darkseagreen">
                                <div class="list__item__info">
                                    <div class="list__item__pattern"
                                         style="background-image: url('http://www.gallery.kg/image/кыргызстан/{{row.image}}/174/231')"></div>
                                    <div class="list__item__info__code" style="cursor:pointer;">{{row.vendorCode}}</div>
                                    <div class="list__item__info__code" style="cursor:pointer;">{{row.texture}}</div>
                                    <div class="list__item__info__code" style="cursor:pointer;">{{row.picture}}</div>
                                    <div class="list__item__info__code" style="cursor:pointer;">{{row.notebook}} &nbsp;</div>
                                    <div class="list__item__info__code" style="padding-bottom: 10px">
                                        <a style="width: 100%" href="http://www.gallery.kg/image/кыргызстан/{{row.image}}" target="_blank">
                                            Изображение
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="complect-catalog" ng-repeat="item in complects.complects | toArray:false  | optionalFilter: row.vendorCode">
                            <div class="list__item list__item--beige vendor-items">
                                <div class="list__item__pattern"
                                     style="background-image: url('http://www.gallery.kg/image/кыргызстан/{{item.image}}/174/231')"></div>
                                <div class="list__item__info__code" style="cursor:pointer;">{{item.vendorCode}}</div>
                                <div class="list__item__info__code" style="cursor:pointer;">{{item.texture}}</div>
                                <div class="list__item__info__code" style="cursor:pointer;">{{item.picture}}</div>
                                <div class="list__item__info__code" style="cursor:pointer;">{{item.notebook}} &nbsp;</div>
                                <div class="list__item__info__code" style="padding-bottom: 10px">
                                    <a style="width: 100%" href="http://www.gallery.kg/image/кыргызстан/{{row.image}}" target="_blank">
                                        Изображение
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="display: table-row; ">
                        <div class="product__info product__info--edit" style="width:100px; margin: 0 auto;" ng-click="triggerChangeComplect(row)" >Редактировать</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

<script>
</script>

