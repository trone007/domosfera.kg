<?php $view->extend('::shaping.html.php') ?>
<?php $view['slots']->start('meta')?>
<link rel="stylesheet" href="/collection.css">
<link rel="stylesheet" href="/notebook-create.css">
<link rel="stylesheet" href="/css/switcher.css">

<?php $view['slots']->stop()?>

<?php $view['slots']->start('scripts')?>
<script src="/js/pagerService.js"></script>
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
        $scope.complects = {};
        $scope.checkedWallpapers=[];

        $scope.viewMode = true;
        $scope.showName = false;

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
        .controller('mainCtrl', function($scope, PagerService, $http) {
            initScopeVariables($scope);

            $scope.changeViewMode = function() {
                if($('.onoffswitch-inner').css('margin-left') == '0px') {
                    $scope.viewMode = false;
                } else {
                    $scope.viewMode = true;
                }
                $scope.changeCatalog();
            };


            $scope.recalcCounters = function() {
                $scope.countersFilter.country = Base64.encode($scope.country);
                $scope.countersFilter.manufacturer = Base64.encode($scope.manufacturer);
                $scope.countersFilter.catalog = Base64.encode($scope.catalog);
                $scope.countersFilter.collection = Base64.encode($scope.collection);

                $http({
                    method: 'POST',
                    url: '/notebook/get-counts',
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
                $scope.vendors.node = [];
                $scope.checkedWallpapers = [];
                $scope.viewMode = false;
                $scope.checkedName = root.split("/")[root.split("/").length - 1];
                $scope.shortName = root.split("/").slice(0, root.split("/").length - 1).join("/");

                $('#single-vendor').hide();
                $('#single-vendor').show();

                $scope.notebookChecked = root;

                $('div[collection="' + root + '"]').hide();

                $.each($scope.complects, function(event, complect) {
                    $.each(complect, function(ev, el){
                        if(el.complectCode == root) {
                            $scope.showName = true;
                            $scope.property = el.property;
                            $scope.propertySecond = el.propertySecond;
                            changeCheckedWallpapers(el);
                        }
                    });
                });
            };

            $scope.unCheckFn = function($event, vendor) {
                if($scope.viewMode) {
                    return true;
                }

                $.each($scope.wallpapers, function(ev, el) {
                    if(el.vendorCode == vendor) {
                        el.handled = 0;
                        var pos = 0;
                        $.each($scope.vendors.node, function (event, element) {
                            if(element == vendor) {
                                $scope.vendors.node.splice(pos, 1);
                                return false;
                            }
                            pos++;
                        });
                        return false;
                    }
                });

                var position = 0;

                $.each($scope.checkedWallpapers, function(ev, el) {
                    if(el.vendorCode == vendor) {

                        $scope.checkedWallpapers.splice(position, 1);
                        return false;
                    }
                    position ++;
                });

            };

            $scope.checkFn = function($event, vendor, notMain = false) {
                if($($event.target).get(0).tagName == 'A') {
                    return false;
                }

                if($scope.viewMode) {
                    return false;
                }

                $scope.unCheckFn($event, vendor);

                var position = 0;
                $scope.vendors.node = [];
                $scope.showName = true;

                $.each($scope.wallpapers, function(ev, el) {
                    if(el.vendorCode == vendor) {
                        el.handled = 1;

                        $scope.checkedWallpapers.push({
                            'vendorCode': el.vendorCode,
                            'picture': el.picture,
                            'texture': el.texture,
                            'style': el.style,
                            'notebook': el.notebook,
                            'catalog': el.catalog,
                            'image' : el.image
                        });

                        $.each($scope.checkedWallpapers, function (event, element) {
                            $scope.vendors.node.push(element.vendorCode);
                        });

                        return false;
                    }

                    position++;
                });

                $('#single-vendor').show();

                $scope.$applyAsync();
            };

            $scope.changeCountry = function() {
                $('#manufacturer-id').hide();
                $('#catalog').hide();
                $('#collection').hide();
                $('#single-vendor').hide();

                $scope.manufacturer = "";
                $scope.notebook = "";
                $scope.catalog = "";
                $scope.wallpapers = [];
                $scope.complects = {};

                $http({
                    method: 'POST',
                    url: '/notebook/get-manufacturer',
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
                $scope.complects = {};

                $scope.checkedWallpapers = [];

                $http({
                    method: 'POST',
                    url: '/notebook/get-catalog',
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
                    $('.sticky-spacer').height(0);
                    $scope.recalcCounters();
                }, function errorCallback(response){

                })
            };

            $scope.changeCatalog = function() {
                if($scope.checkedWallpapers.length == 0 || $scope.catalog != $scope.checkedWallpapers[0].catalog) {
                    $scope.notebook = "";
                    $('#notebook').hide();
                    $('#single-vendor').hide();
                    $scope.checkedWallpapers = [];
                }

                $scope.wallpapers = [];
                $scope.complects = {};

                $http({
                    method: 'POST',
                    url: $scope.viewMode ? '/notebook/get-notebooks' : '/notebook/get-vendors',
                    data:
                    'query=' + encodeURIComponent(JSON.stringify({catalog: Base64.encode($scope.catalog)})),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }).then(function successCallback(response) {
                    var data = response.data;
                    var i = 0;
                    $scope.notebooks = [];

                    switch($scope.viewMode) {
                        case true:
                                $scope.complects = data.vendors.complects.complects;
                                $.each(data.notebooks, function(e, value) {
                                    $scope.notebooks.push({
                                        'id': i++,
                                        'label': value.name,
                                        'color': value.not_handled == 0 ? 'darkgreen' : 'darkred'
                                    });
                                });
                                $('#notebook').show();

                                $scope.wallpapers = data.vendors.vendors;

                                $scope.recalcCounters();
                            break;
                        case false:
                            $scope.complects = data.complects.complects;

                            $scope.wallpapers = data.vendors;

                            $scope.recalcCounters();
                            $scope.$applyAsync();

                            break;
                    }
                }, function errorCallback(response){
                })
            };

            $scope.changeNotebook= function() {
                $('#single-vendor').hide();

                $scope.wallpapers = [];
                $scope.complects = {};
                $scope.checkedWallpapers = [];

                $http({
                    method: 'POST',
                    url: '/notebook/get-notebook-vendors',
                    data:
                    'query=' + encodeURIComponent(JSON.stringify({
                        notebook: Base64.encode($scope.notebook),
                        catalog: Base64.encode($scope.catalog)
                    })),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }).then(function successCallback(response) {
                    var data = response.data;
                    $scope.wallpapers = data.vendors;

                    $scope.vendors = {
                        'node' : []
                    };

                    $('.vendor-items').removeClass('bordered-div');

                    $.each($scope.wallpapers, function(event, el) {
                        changeCheckedWallpapers(el);
                    });

                    $('#single-vendor').show();
                    $scope.recalcCounters();
                    $scope.$applyAsync();
                }, function errorCallback(response){
                })
            };

            $scope.saveComplect = function() {
                $scope.checkedWallpapers = [];
                $http({
                    method: 'POST',
                    url: '/notebook/save',
                    data:
                    'query=' +encodeURIComponent(JSON.stringify({
                        notebook: $scope.notebook ? $scope.notebook : $scope.notebookChecked,
                        newName: $scope.checkedName,
                        property: $scope.property,
                        propertySecond: $scope.propertySecond,
                        catalog: $scope.catalog,
                        manufacturer: $scope.manufacturer,
                        vendors: $scope.vendors
                    })),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }).then(function successCallback(response) {
                    var data = response.data;
                    $('.bordered-div').removeClass('bordered-div');

                    $scope.wallpapers = [];
                    $scope.wallpapers = data.complects.vendors;

                    $scope.complects = data.complects.complects.complects;
                    $('div[collection="' + $scope.collectionChecked + '"]').show();

                    $scope.notebookChecked = "";
                    $scope.checkedName = "";
                    $scope.shortName = "";
                    $scope.notebookChecked = "";
                    $scope.showName = false;
                    $scope.recalcCounters();
                    $scope.$applyAsync();

                    $('#single-vendor').hide();

                }, function errorCallback(response){
                    alert("Ошибка при сохранении, возможно имя уже занято");
                });
            };

            var changeCheckedWallpapers = function (vendor) {
                $scope.vendors.node.push(vendor.vendorCode);

                $scope.checkedWallpapers.push({
                    'vendorCode': vendor.vendorCode,
                    'property': vendor.property,
                    'propertySecond': vendor.propertySecond,
                    'texture': vendor.texture,
                    'notebook': vendor.notebook,
                    'catalog': vendor.catalog,
                    'image' : vendor.image
                });

                $scope.$applyAsync();
            };

            $scope.updateComplect = function() {

                $scope.vendors = {
                    'node' : []
                };

                $('.vendor-items').removeClass('bordered-div');

                $.each($scope.complects, function(event, complect) {
                    $.each(complect, function(event, el) {
                        var rootVendor = el.complectCode;

                        if(rootVendor != null && $scope.wallpaper.complectCode == rootVendor) {
                            changeCheckedWallpapers(el);
                        }
                    });
                });
            };

            $scope.recalcCounters();

            $scope.checkVendors = function(key) {
                $('.vendor-items').removeClass('bordered-div');
            };

            $scope.changeName = function () {

                if($scope.notebookChecked == $scope.shortName + "/" + $scope.checkedName || $scope.checkedName == '') {
                    $scope.showBtn = false;
                } else {
                    $scope.showBtn = true;
                }
            }

            $scope.changeNotebookType = function () {
                $scope.showBtn = true;
            }
        })
</script>

<?php $view['slots']->stop()?>
<?php $view['slots']->start('component')?>
<tr ng-if="viewMode" ng-hide="collections == ''">
    <td class="col-md-3">
        <div>Тетрадь</div>
    </td>
    <td class="col-md-6">
        <select class="form-control"
                ng-change="changeNotebook()"
                ng-model="$parent.notebook"
                style="display:none;"
                id="collection"
        >
            <option value=""></option>
            <option ng-repeat="item in notebooks" style="color:{{item.color}}" value="{{item.label}}">{{item.label}}</option>
        </select>
    </td>
    <td class="col-md-3">
        <span style="color:red">{{counters.collection[0].not_handled}}</span>
        <span style="color:green">{{counters.collection[0].handled}}</span>
    </td>
</tr>

<?php $view['slots']->stop()?>

<?php $view['slots']->start('single_vendor')?>
<div id='single-vendor' style="display: none;  border:none;">
    <div ng-hide="viewMode">
        <div class="list__item list__item--beige">
            <button class="btn btn-success" style="width: 100%;" ng-click="saveComplect()">Сохранить</button>
        </div>
        <div class="list__item list__item--beige" style="margin-bottom: 40px" ng-hide="!showName">
            <div class="col-md-6 text-right">
                {{shortName}}/
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" style="width: 100%;" ng-model="checkedName" ng-change="changeName()"/>
            </div>
        </div>
        <div class="list__item list__item--beige" style="margin-bottom: 40px" ng-hide="!showName">
            <select class="form-control col-md-6 " ng-model="property" ng-change="changeNotebookType()">
                <option value="Классические">Классические</option>
                <option value="Растительные">Растительные</option>
                <option value="Лофт">Лофт</option>
                <option value="Геометрия">Геометрия</option>
                <option value="Полоса">Полоса</option>
                <option value="Детские">Детские</option>
                <option value="Однотонные">Однотонные</option>
                <option value="Сюжет">Сюжет</option>
                <option value="Прочее">Прочее</option>
            </select>
            <select class="form-control col-md-6 " ng-model="propertySecond" ng-change="changeNotebookType()">
                <option value="Классические">Классические</option>
                <option value="Растительные">Растительные</option>
                <option value="Лофт">Лофт</option>
                <option value="Геометрия">Геометрия</option>
                <option value="Полоса">Полоса</option>
                <option value="Детские">Детские</option>
                <option value="Однотонные">Однотонные</option>
                <option value="Сюжет">Сюжет</option>
                <option value="Прочее">Прочее</option>
            </select>
        </div>
    </div>
    <div class="col-sm-12 col-md-12 col-lg-12 vendor-children">
        <div ng-repeat="row in checkedWallpapers" style="margin-bottom:5px;">
            <div class="list__item--beige"
                 ng-click="unCheckFn($event, row.vendorCode)"
                 style="cursor:pointer;" vendor="">
                <div class="list__item__info" style="padding: 5px 0px 15px; width:55%; float:left">
                    <div class="list__item__info__code">{{row.vendorCode}}  &nbsp;</div>
                    <div class="list__item__info__code">{{row.style}} &nbsp;</div>
                    <div class="list__item__info__code">{{row.texture}}  &nbsp;</div>
                    <div class="list__item__info__code">{{row.property}}  &nbsp;</div>
                    <div class="list__item__info__code">{{row.propertySecond}}  &nbsp;</div>
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
        <div class="row">
            <div class="col-sm-4 col-lg-2 col-md-3 col-xs-12" ng-repeat="row in wallpapers" ng-if="!row.handled">
                <div class="list__item list__item--beige vendor-items"
                     ng-click="checkFn($event, row.vendorCode)"
                     ng-class="{'collection-element--disabled': row.handled}"
                     vendor="{{row}}"
                     image="{{row.image}}"
                >
                    <div class="list__item__info">
                        <div class="list__item__pattern"
                             style="background-image: url('http://www.gallery.kg/image/кыргызстан/{{row.image}}/174/231')"></div>
                        <div class="list__item__info__code" style="cursor:pointer;">{{row.vendorCode}} &nbsp;</div>
                        <div class="list__item__info__code" style="cursor:pointer;">{{row.style}} &nbsp;</div>
                        <div class="list__item__info__code" style="cursor:pointer;">{{row.texture}} &nbsp;</div>
                        <div class="list__item__info__code">{{row.property}}  &nbsp;</div>
                        <div class="list__item__info__code">{{row.propertySecond}}  &nbsp;</div>
                        <div class="list__item__info__code" style="cursor:pointer;">{{row.notebook}} &nbsp;</div>
                        <div class="list__item__info__code" style="">
                            <a style="width: 100%" href="http://www.gallery.kg/image/кыргызстан/{{row.image}}" target="_blank">
                                Изображение
                            </a>
                        </div>
                    </div>
                </div>
                <br/>
            </div>
        </div>
        <div class="container-fluid">
            <div ng-repeat="(key, value) in complects" class="complect-catalog-block section-product group col-lg-auto col-xs-auto col-sm-auto col-md-auto"  style="padding-right: 15px"  collection="{{key}}">
                <div class="row">
                    <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">{{key}}</div>
                </div>

                <div class="row data" >
                    <div ng-class="
                            {'col-lg-2 col-md-3 col-sm-12': value.length > 4,
                              'col-lg-4 col-md-4 col-sm-12 ': value.length == 4,
                              'col-lg-4 col-md-4 col-sm-12': value.length == 3,
                              'col-lg-12 col-md-12 col-sm-12': value.length == 1,
                              'col-lg-6 col-md-6 col-sm-12': value.length == 2
                               }
                            " class="col-xs-12 item-max-width" ng-repeat="item in value | toArray:false  | filter: {rootVendor: row.vendorCode}">
                        <div class="list__item list__item--beige">
                            <div class="list__item__pattern"
                                 style="background-image: url('http://www.gallery.kg/image/кыргызстан/{{item.image}}/174/231')"></div>
                            <div class="list__item__info__code" style="cursor:pointer;">{{item.vendorCode}} &nbsp;</div>
                            <div class="list__item__info__code" style="cursor:pointer;">{{item.style}} &nbsp;</div>
                            <div class="list__item__info__code" style="cursor:pointer;">{{item.texture}} &nbsp;</div>
                            <div class="list__item__info__code" style="cursor:pointer;">{{item.property}} &nbsp;</div>
                            <div class="list__item__info__code" style="cursor:pointer;">{{item.propertySecond}} &nbsp;</div>
                            <div class="list__item__info__code" style="cursor:pointer;">{{item.notebook}} &nbsp;</div>
                            <div class="list__item__info__code" style="padding-bottom: 10px">
                                <a style="width: 100%" href="http://www.gallery.kg/image/кыргызстан/{{row.image}}" target="_blank">
                                    Изображение
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="product__info product__info--edit" style="width:100px; margin: 0 auto;" ng-click="triggerChangeComplect(key)" >Редактировать</div>
                </div>
            </div>
        </div>
    </div>

</div>