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
    $('.group').resize(function(){
        alert('changed');
    });

</script>
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

        $scope.showBtn = false;

    }
    angular
        .module('wallpaper', ['ngRoute', 'angular.filter'])
        .factory('PagerService', PagerService)
        .controller('mainCtrl', function($scope, PagerService, $http, $timeout) {
            initScopeVariables($scope);

            $scope.changeViewMode = function(returnity = false) {
                if($('.onoffswitch-inner').css('margin-left') == '0px') {
                    $scope.viewMode = false;
                    $('#single-vendor').show();
                } else {
                    $scope.viewMode = true;
                    $('#single-vendor').hide();
                }

                if(returnity) {
                    return returnity;
                }

                if($scope.collection.length > 0){
                    $scope.changeCollection();
                } else {
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
                    url: '/collection-made/get-counts',
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
                $scope.collectionShortName = root.split("/").slice(0, root.split("/").length - 1).join("/");

                $('#single-vendor').show();

                $scope.collectionChecked = root;

                $('.onoffswitch-inner').click();
                $scope.changeViewMode(true);

                $.each($scope.complects, function(event, complect) {
                    $.each(complect, function(ev, el){
                        if(el.complectCode == root) {
                            $scope.collectionType = el.property;
                            $scope.showName = true;
                            changeCheckedWallpapers(el);
                        }
                    });
                });

               $scope.showBtn = false;
            };

            $scope.unCheckFn = function($event, vendor) {
                if($scope.viewMode) {
                    return true;
                }

                $('.vendor-items').each(function (ev, el){
                    var element = JSON.parse($(el).attr('vendor'));

                    if(element.vendorCode == vendor) {
                        $(el).removeClass('bordered-div');
                    }
                });

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

                $scope.showBtn = true;
            };

            $scope.checkFn = function($event, vendor, notMain = false) {
                if($event.target != null && $($event.target).get(0).tagName == 'A') {
                    return false;
                }

                if($scope.viewMode) {
                    return false;
                }

                $scope.showName = true;
                if($($event.currentTarget).hasClass('bordered-div') && !notMain) {
                    $scope.unCheckFn($event, vendor);
                    return true;
                } else {
                    $($event.currentTarget).addClass('bordered-div');
                }

                if(!notMain) {
//                    if($scope.checkedWallpapers.length < 1) {
                    $http({
                        method: 'POST',
                        url: '/collection-made/get-companion',
                        data:
                        'query=' + encodeURIComponent(JSON.stringify({vendorCode: Base64.encode(vendor)})),
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    }).then(function successCallback(response) {
                        var data = response.data.group;
                        $.each(data, function(key, val) {
                            $('.vendor-items').each(function(ev, el) {
                                var vendorCode = JSON.parse($(el).attr('vendor')).vendorCode;
                                if(vendorCode == vendor) {
                                    return true;
                                }
                                if(vendorCode == val.vendorCode) {
                                    if(!$(el).hasClass('bordered-div')) {
                                        var event = {
                                            target: null,
                                            currentTarget: $(el)
                                        }
                                        $scope.checkFn(event, vendorCode, true);
                                    }
                                }
                            })
                        });
                    }, function errorCallback(response){

                    });
//                    }

                }
                var exit = false;
                $.each($scope.checkedWallpapers, function(ev, el) {
                    if(el.vendorCode == vendor) {
                        $scope.unCheckFn($event, vendor);
                        exit = true;
                        return true;
                    }
                });

                if (exit) {
                    return true;
                }

                $scope.showBtn = true;

                var position = 0;
                $scope.vendors.node = [];

                $.each($scope.wallpapers, function(ev, el) {
                    if(el.vendorCode == vendor) {
                        el.handled = 1;

                        $scope.checkedWallpapers.push({
                            'vendorCode': el.vendorCode,
                            'picture': el.picture,
                            'property': el.property,
                            'texture': el.texture,
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
                $scope.collection = "";
                $scope.catalog = "";
                $scope.wallpapers = [];
                $scope.complects = {};

                $http({
                    method: 'POST',
                    url: '/collection-made/get-manufacturer',
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
                    url: '/collection-made/get-catalog',
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
            $scope.updateCollections = function(){
                $http({
                    method: 'POST',
                    url: '/collection-made/get-collections',
                    data:
                    'query=' + encodeURIComponent(JSON.stringify({catalog: Base64.encode($scope.catalog)})),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }).then(function successCallback(response) {
                    var data = response.data;
                    var i = 0;

                    $scope.complects = data.vendors.complects.complects;
                    var collection = $scope.collection;
                    $scope.collections = [];

                    $.each(data.collections, function(e, value) {
                        $scope.collections.push({
                            'id': i++,
                            'label': value.name,
                            'color': value.not_handled == 0 ? 'darkgreen' : 'darkred'
                        });
                    });

                    $('#collection').show();
                    $scope.collection = collection;

                    $scope.wallpapers = data.vendors.vendors;

                    $scope.recalcCounters();

                    $scope.wallpaperVendors = $scope.wallpapers;
                }, function errorCallback(response){
                })
            }

            $scope.changeCatalog = function() {
//                if($scope.checkedWallpapers.length == 0 || $scope.catalog != $scope.checkedWallpapers[0].catalog) {
//                    $scope.collection = "";
//                    $('#collection').hide();
//                    $('#single-vendor').hide();
//                    $scope.checkedWallpapers = [];
//                }
                $scope.showBtn = false;
                $scope.wallpapers = [];
                $scope.checkedWallpapers = [];
                $scope.complects = {};
                $http({
                    method: 'POST',
                    url: $scope.viewMode ? '/collection-made/get-collections' : '/collection-made/get-vendors',
                    data:
                    'query=' + encodeURIComponent(JSON.stringify({
                        catalog: Base64.encode($scope.catalog),
                        manufacturer: Base64.encode($scope.manufacturer)
                    })),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }).then(function successCallback(response) {
                    var data = response.data;
                    var i = 0;

                    switch($scope.viewMode) {
                        case true:
                            $scope.complects = data.vendors.complects.complects;
                            var collection = $scope.collection;
                            $scope.collections = [];

                            $.each(data.collections, function(e, value) {
                                $scope.collections.push({
                                    'id': i++,
                                    'label': value.name,
                                    'color': value.not_handled == 0 ? 'darkgreen' : 'darkred'
                                });
                            });

                            $('#collection').show();
                            $scope.collection = collection;

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
                    $scope.wallpaperVendors = $scope.wallpapers;
                }, function errorCallback(response){
                })
            };

            $scope.changeCollection= function() {
                $('#single-vendor').hide();

                $scope.wallpapers = [];
                $scope.complects = {};
                $scope.checkedWallpapers = [];
                $scope.showBtn = false;

                $http({
                    method: 'POST',
                    url: '/collection-made/get-collection-vendors',
                    data:
                    'query=' + encodeURIComponent(JSON.stringify({
                        collection: Base64.encode($scope.collection),
                        catalog: Base64.encode($scope.catalog)
                    })),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }).then(function successCallback(response) {
                    var data = response.data;

                    if(!$scope.viewMode) {
                        $scope.wallpapers = $scope.wallpaperVendors;
                        $('#single-vendor').show();

                    }else {
                        $scope.wallpapers = data.vendors;
                    }

                    $scope.vendors = {
                        'node' : []
                    };

                    $.each($scope.wallpapers, function(ev, wallpaper) {
                        wallpaper.bordered = false;
                    });
                    $scope.recalcCounters();
                    $scope.$applyAsync();


                    $.each(data.vendors, function(event, el) {
                        changeCheckedWallpapers(el);
                    });
                }, function errorCallback(response){
                })
            };

            $scope.saveComplect = function() {
                $scope.checkedWallpapers = [];
                $http({
                    method: 'POST',
                    url: '/collection-made/save',
                    data:
                    'query=' +encodeURIComponent(JSON.stringify({
                        collection: $scope.collectionChecked ? $scope.collectionChecked : $scope.collection,
                        newName: $scope.checkedName,
                        collectionType: $scope.collectionType,
                        catalog: $scope.catalog,
                        manufacturer: $scope.manufacturer,
                        vendors: $scope.vendors
                    })),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }).then(function successCallback(response) {
                    var data = response.data;
                    $.each($scope.wallpapers, function(ev, wallpaper) {
                        wallpaper.bordered = false;
                    });

                    $scope.wallpapers = [];
                    $scope.wallpapers = data.complects.vendors;

                    $scope.complects = data.complects.complects.complects;

                    $scope.collectionChecked = "";
                    $scope.showBtn = false;
                    $scope.showName = false;
                    $scope.updateCollections();
                    $scope.recalcCounters();
                    $scope.$applyAsync();

                    if($scope.collection.length > 0) {
                        $scope.changeCollection();
                    }

                    $scope.collection = "";
                    $scope.collectionChecked = "";
                    $scope.collectionShortName = "";
                    $scope.nameChanged = "";

                    $('#single-vendor').hide();

                }, function errorCallback(response){
                    alert("Ошибка при сохранении, возможно имя уже занято");
                });
            };

            var changeCheckedWallpapers = function (vendor) {
                $scope.vendors.node.push(vendor.vendorCode);

                $scope.checkedWallpapers.push({
                    'vendorCode': vendor.vendorCode,
                    'picture': vendor.picture,
                    'texture': vendor.texture,
                    'notebook': vendor.notebook,
                    'catalog': vendor.catalog,
                    'image' : vendor.image
                });

                if($scope.checkedWallpapers.length == 0) {
                    $('#single-vendor').hide();
                } else {
                    $.each($scope.wallpapers, function(ev, wallpaper) {
                        if(wallpaper.vendorCode == vendor.vendorCode) {
                            wallpaper.bordered = true;
                        }
                    })
                }

                $scope.$applyAsync();
            };

            $scope.updateComplect = function() {

                $scope.vendors = {
                    'node' : []
                };


                $.each($scope.wallpapers, function(ev, wallpaper) {
                    wallpaper.bordered = false;
                });

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

                if($scope.collectionChecked == $scope.collectionShortName + "/" + $scope.checkedName || $scope.checkedName == '') {
                    $scope.showBtn = false;
                } else {
                    $scope.showBtn = true;
                }
            }

            $scope.changeCollectionType = function () {
                $scope.showBtn = true;
            }
        })
</script>

<?php $view['slots']->stop()?>
<?php $view['slots']->start('component')?>
<tr ng-hide="collections == ''">
    <td class="col-md-4">
        <div>Коллекция</div>
    </td>
    <td class="col-md-6">
        <select class="form-control"
                ng-change="changeCollection()"
                ng-model="collection"
                style="display:none;"
                id="collection"
        >
            <option value=""></option>
            <option ng-repeat="item in collections" ng-selected="{{item.label == collection}}" style="color:{{item.color}}" value="{{item.label}}">{{item.label}}</option>
        </select>
    </td>
    <td class="col-md-2">
        <span style="color:red">{{counters.collection[0].not_handled}}</span>
        <span style="color:green">{{counters.collection[0].handled}}</span>
    </td>
</tr>
<?php $view['slots']->stop()?>

<?php $view['slots']->start('single_vendor')?>
<div id='single-vendor' style="display: none;  border:none;">
    <div ng-hide="viewMode">
        <div class="list__item list__item--beige" ng-if="showBtn == true">
            <button class="btn btn-success" style="width: 100%;" ng-click="saveComplect()">Сохранить</button>
        </div>
        <div class="" style="margin-bottom: 40px" ng-hide="!showName">
            <div class="col-md-6 text-right">
                {{collectionShortName}}/
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" style="width: 100%;" ng-model="checkedName" ng-change="changeName()"/>
            </div>
        </div>
        <div class="" style="margin-bottom: 40px" ng-hide="!showName">
            <select class="form-control col-md-6 " ng-model="collectionType" ng-change="changeCollectionType()">
                <option value="Статус">Статус</option>
                <option value="Романтика">Романтика</option>
                <option value="Современный">Современный</option>
                <option value="Экзотика">Экзотика</option>
                <option value="Национальный">Национальный</option>
            </select>
         </div>
    <div class="col-sm-12 col-md-12 col-lg-12 vendor-children">
        <div ng-repeat="row in checkedWallpapers" style="margin-bottom:5px;">
            <div class="list__item--beige"
                 ng-click="unCheckFn($event, row.vendorCode)"
                 style="cursor:pointer;" vendor="">
                <div class="list__item__info" style="padding: 5px 0px 15px; width:55%; float:left">
                    <div class="list__item__info__code">{{row.vendorCode}}  &nbsp;</div>
                    <div class="list__item__info__code">{{row.property}}  &nbsp;</div>
                    <div class="list__item__info__code">{{row.texture}}  &nbsp;</div>
                    <div class="list__item__info__code">{{row.picture}}  &nbsp;</div>
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
        <div class="row" ng-if="!viewMode">
            <div class="col-sm-4 col-lg-2 col-md-3 col-xs-12" ng-repeat="row in wallpapers">
                <div ng-class="{'bordered-div': row.bordered,
                                'red-border': row.complectCode == null,
                                'green-border': row.complectCode != null }"
                     class="list__item list__item--beige vendor-items collection-element"
                     ng-click="checkFn($event, row.vendorCode)"
                     vendor="{{row}}"
                     image="{{row.image}}"
                >
                    <div class="list__item__info">
                        <div class="list__item__pattern"
                             style="background-image: url('http://www.gallery.kg/image/кыргызстан/{{row.image}}/174/231')"></div>
                        <div class="list__item__info__code" style="cursor:pointer;">{{row.vendorCode}} &nbsp;</div>
                        <div class="list__item__info__code" style="cursor:pointer;">{{row.property}} &nbsp;</div>
                        <div class="list__item__info__code" style="cursor:pointer;">{{row.texture}} &nbsp;</div>
                        <div class="list__item__info__code" style="cursor:pointer;">{{row.picture}} &nbsp;</div>
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
            <div ng-repeat="(key, value) in complects" class="complect-catalog-block section-product group col-lg-auto col-xs-auto col-sm-auto col-md-auto"  collection="{{key}}">
                    <div class="row">
                        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                            {{key}}
                        </div>
                    </div>

                    <div class="row data" style="margin: 0">
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
                                <div class="list__item__info__code" style="cursor:pointer;">{{item.property}} &nbsp;</div>
                                <div class="list__item__info__code" style="cursor:pointer;">{{item.texture}} &nbsp;</div>
                                <div class="list__item__info__code" style="cursor:pointer;">{{item.picture}} &nbsp;</div>
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