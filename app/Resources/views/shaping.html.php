<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Домосфера</title>

    <link href="/fonts/fonts.css" rel="stylesheet">
    <link href="/bootstrap/css/bootstrap-theme.min.css"  rel="stylesheet">
    <link href="/bootstrap/css/bootstrap.min.css"  rel="stylesheet">

    <?php $view['slots']->output('meta', '') ?>

</head>
<body>
<main class="list row">
    <div ng-app="wallpaper" ng-controller="mainCtrl" >
        <nav class="navbar navbar-default menu-left sideNav">
            <div class="container-fluid">
                <div class="col-sm-6 col-md-10 text-center" style="margin: 0 25px;">
                    <select class="form-control"  id="shaping-type">
                        <option value="/shaping/1">Формирование компаньонов</option>
                        <option value="/shaping/2">Формирование комплектов </option>
                        <option value="/shaping/3">Формирование тетрадей</option>
                        <option value="/shaping/4">Формирование коллекций</option>
                    </select>

                </div>
                <table class="table">
                    <tr>
                        <td class="col-md-7" colspan="2">Режим просмотра:</td>
                        <td class="col-md-4">
                            <div class="onoffswitch"  ng-mouseup="changeViewMode()">
                                <input type="checkbox"  name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" checked>
                                <label class="onoffswitch-label" for="myonoffswitch">
                                    <span class="onoffswitch-inner"></span>
                                    <span class="onoffswitch-switch"></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                </table>
                <table class="table">
                    <tr>
                        <td class="col-md-4">Всего:</td>
                        <td class="col-md-6">
                            <span style="color:red">{{counters.total[0].not_handled}}</span>
                            <span style="color:green">{{counters.total[0].handled}}</span>
                        </td>
                        <td class="col-md-2"></td>
                    </tr>
                    <tr>
                        <td class="col-md-4">Страна</td>
                        <td class="col-md-6">
                            <select id="countries" class="form-control" ng-change="changeCountry()" ng-model="country">
                                <?php foreach ($countries as $country): ?>
                                    <option
                                            value="<?php echo $country['name'][1];?>"
                                            style="color:<?php echo $country['not_handled'] > 0 ? 'darkred' : 'darkgreen';?>"
                                    >
                                        <?php echo $country['name'][1];?>
                                    </option>
                                <?php endforeach;?>
                            </select></td>
                        <td class="col-md-2">
                            <span style="color:red">{{counters.country[0].not_handled}}</span>
                            <span style="color:green">{{counters.country[0].handled}}</span>
                        </td>
                    </tr>
                    <tr ng-hide="manufacturers == ''">
                        <td class="col-md-4"><div>Производитель</div></td>
                        <td class="col-md-6">
                            <select class="form-control"
                                    ng-change="changeManufacturer()"
                                    ng-model="manufacturer"
                                    style="display:none"
                                    id="manufacturer-id"
                            >
                                <option value=""></option>
                                <option ng-repeat="item in manufacturers" style="color:{{item.color}}" value="{{item.label}}">{{item.label}}</option>
                            </select>
                        </td>
                        <td class="col-md-2">
                            <span style="color:red">{{counters.manufacturer[0].not_handled}}</span>
                            <span style="color:green">{{counters.manufacturer[0].handled}}</span>
                        </td>
                    </tr>
                    <tr ng-hide="catalogs == ''">
                        <td class="col-md-4">
                            <div>Каталог</div>
                        </td>
                        <td class="col-md-6">
                            <select class="form-control"
                                    ng-change="changeCatalog()"
                                    ng-model="catalog"
                                    style="display:none"
                                    id="catalog"
                            >
                                <option value=""></option>
                                <option ng-repeat="item in catalogs" style="color:{{item.color}}" value="{{item.label}}">{{item.label}}</option>
                            </select>
                        </td>
                        <td class="col-md-2">
                            <span style="color:red">{{counters.catalog[0].not_handled}}</span>
                            <span style="color:green">{{counters.catalog[0].handled}}</span>
                        </td>
                    </tr>
                    <?php $view['slots']->output('component') ?>
                </table>
            </div>

            <?php $view['slots']->output('single_vendor') ?>
        </nav>
        <?php $view['slots']->output('_content') ?>
        <?php $view['slots']->output('stylesheets') ?>
    </div>
</main>

<script src="/js/jquery-3.1.1.min.js"></script>
<script src="/bootstrap/js/bootstrap.min.js"></script>

<script src="/js/jquery-ui.js"></script>
<script src="/js/favorite.js"></script>
<?php $view['slots']->output('scripts', '') ?>

<script>
    $(document).ready(function() {
        var location = window.location.href.split('/');
        var currUrl = '/' + location[location.length - 2] + '/' + location[location.length - 1];
        $('#shaping-type').change(function() {
            if($(this).val() == currUrl) {
                return true;
            } else {
                window.location = $(this).val();
            }
        })
        $('#shaping-type').val(currUrl).change();
    });

</script>
</body>
</html>