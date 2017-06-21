<?php $prefix = explode('/', $_SERVER['REQUEST_URI'])[1]; ?>
<?php $prefix = $prefix != 'new' && $prefix != 'other-new' ? '': $prefix; ?>
<?php switch($prefix): ?>
<?php case 'new': $view->extend('::smallBase.html.php'); break;?>
    <?php case 'other-new': $view->extend('::largeBase.html.php'); break;?>
    <?php default : $view->extend('::base.html.php'); break;?>
    <?php endswitch;?>
<?php $view['slots']->start('meta')?>
<link rel="stylesheet" href="/styles.css">
<?php $view['slots']->stop()?>
<script>
</script>
<div ng-app="wallpaper" ng-controller="mainCtrl" >

    <main class = "list row">
        <div class="container">
            <?php if($region == 'бишкек'):?>
                <div class="col-md-6 col-md-offset-3">
                    <div class="col-md-12 text-center">
                        <div class="row">
                            <div class="col-md-12">
                                Бишкек - ТД Галерея, Ортосайский рынок
                            </div>
                        </div>
                        <div class="row"  style="margin-bottom: 20px">
                            Время работы:<br/>
                            Летнее время: Пн-Вс 09:00 - 19:00<br/>
                            Зимнее время: Пн-Вс 09:00 - 18:00
                        </div>
                        <div class="row" style="margin-bottom: 20px">
                            <div class="col-md-12">
                                <div class="col-md-6 text-left">Megacom</div>
                                <div class="col-md-6 text-right">+996 (555) 57-64-61</div>
                                <div class="col-md-6 text-left">Beeline</div>
                                <div class="col-md-6 text-right">+996 (772) 57-64-61</div>
                                <div class="col-md-6 text-left">O!</div>
                                <div class="col-md-6 text-right">+996 (700) 57-64-61</div>
                                <div class="col-md-6 text-left">Общий</div>
                                <div class="col-md-6 text-right">+996 (312) 57-64-61</div>
                            </div>
                        </div>
                        <div class="row">г. Бишкек, Ортосайский рынок. ул. Жукеева-Пудовкина / ул. Донецкая</div>

                    </div>
                </div>
            <?php elseif($region == 'ош'):?>
            <div class="col-md-6 col-md-offset-3">
                <div class="col-md-12 text-center">
                    <div class="row">
                        <div class="col-md-12">
                            Ош, Тд Редеко
                        </div>
                    </div>
                    <div class="row"  style="margin-bottom: 20px">
                        Время работы:<br/>
                        Летнее время: Пн-Вс 09:00 - 19:00<br/>
                        Зимнее время: Пн-Вс 09:00 - 18:00
                    </div>
                    <div class="row" style="margin-bottom: 20px">
                        <div class="col-md-12">
                            <div class="col-md-6 text-left">Общий</div>
                            <div class="col-md-6 text-right">+996 (559) 08-73-78</div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6 text-left">Общий</div>
                            <div class="col-md-6 text-right">+996 (777) 08-73-78</div>
                        </div>
                    </div>
                    <div class="row">г.Ош, ул.Шакирова д.108а, Магазин «REdeco», ориентир ТД «Ош Таатан»</div>

                </div>
            </div>
            <?php elseif($region == 'токмак'):?>
            <div class="col-md-6 col-md-offset-3">
                <div class="col-md-12 text-center">
                    <div class="row">
                        <div class="col-md-12">
                            Токмак, ТД Галерея
                        </div>
                    </div>
                    <div class="row"  style="margin-bottom: 20px">
                        Время работы:<br/>
                        Пн-Сб 09:00 - 18.00<br/>
                        Вс 09:00 - 16:00
                    </div>
                    <div class="row" style="margin-bottom: 20px">
                        <div class="col-md-12">
                            <div class="col-md-6 text-left">Общий</div>
                            <div class="col-md-6 text-right">+996 (3138) 6-77-94</div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6 text-left">Общий</div>
                            <div class="col-md-6 text-right">+996 (700) 06-77-94</div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6 text-left">Общий</div>
                            <div class="col-md-6 text-right">+996 (550) 06-77-94</div>
                        </div>
                    </div>
                    <div class="row">г.Токмок, ул.Дунларова д.128</div>

                </div>
            </div>
            <?php elseif($region == 'кара-балта'):?>
            <div class="col-md-6 col-md-offset-3">
                <div class="col-md-12 text-center">
                    <div class="row">
                        <div class="col-md-12">
                            Кара-Балта, ТД Галерея
                        </div>
                    </div>
                    <div class="row"  style="margin-bottom: 20px">
                        Время работы:<br/>
                        Пн-Сб 09:00 - 18.00<br/>
                        Вс 09:00 - 16:00
                    </div>
                    <div class="row" style="margin-bottom: 20px">
                        <div class="col-md-12">
                            <div class="col-md-6 text-left">Общий</div>
                            <div class="col-md-6 text-right">+996 (3133) 7-05-14</div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6 text-left">Общий</div>
                            <div class="col-md-6 text-right">+996 (706) 89-88-24</div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6 text-left">Общий</div>
                            <div class="col-md-6 text-right">+996 (312) 89-88-24</div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6 text-left">Общий</div>
                            <div class="col-md-6 text-right">+996 (559) 89-88-24</div>
                        </div>
                    </div>
                    <div class="row">г.Кара-Балта, ул.8 Марта 46</div>

                </div>
            </div>
            <?php elseif($region == 'актобе'):?>
            <div class="col-md-6 col-md-offset-3">
                <div class="col-md-12 text-center">
                    <div class="row">
                        <div class="col-md-12">
                            Актобе - ТД Галерея
                        </div>
                    </div>
                    <div class="row"  style="margin-bottom: 20px">
                        Время работы:<br/>
                        Пн-Вс 09:00 - 19.00<br/>
                    </div>
                    <div class="row" style="margin-bottom: 20px">
                        <div class="col-md-12">
                            <div class="col-md-6 text-left">Общий</div>
                            <div class="col-md-6 text-right">+7 (7132) 90-76-03</div>
                        </div>
                    </div>
                    <div class="row">г.Актобе, ул. М. Маметовой д.16 Г, ТОО «Галерея Запад»</div>

                </div>
            </div>
            <?php elseif($region == 'шымкент'):?>
            <div class="col-md-6 col-md-offset-3">
                <div class="col-md-12 text-center">
                    <div class="row">
                        <div class="col-md-12">
                            Шымкент - ТД Галерея
                        </div>
                    </div>
                    <div class="row"  style="margin-bottom: 20px">
                        Время работы:<br/>
                        Пн-Сб 09:00 - 19.00<br/>
                        Вс 09:00 - 18:00
                    </div>
                    <div class="row" style="margin-bottom: 20px">
                        <div class="col-md-12">
                            <div class="col-md-6 text-left">Общий</div>
                            <div class="col-md-6 text-right">+7 (7252) 32-22-34</div>
                        </div>
                    </div>
                    <div class="row">г.Шымкент, ул.Турара Рыскулова д.30, Магазин «Галерея», напротив рынка «Евразия»</div>

                </div>
            </div>
            <?php elseif($region == 'актау'):?>
            <div class="col-md-6 col-md-offset-3">
                <div class="col-md-12 text-center">
                    <div class="row">
                        <div class="col-md-12">
                            Актау - ТД Галерея, Шыгыс
                        </div>
                    </div>
                    <div class="row"  style="margin-bottom: 20px">
                        Время работы:<br/>
                        Пн-Сб 10:00 - 19.00<br/>
                        Вс 10:00 - 18:00
                    </div>
                    <div class="row" style="margin-bottom: 20px">
                        <div class="col-md-12">
                            <div class="col-md-6 text-left">Общий</div>
                            <div class="col-md-6 text-right">+7 (707) 734-46-14</div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6 text-left">Магазин</div>
                            <div class="col-md-6 text-right">+7 (7292) 34-46-14</div>
                        </div>
                    </div>
                    <div class="row">г.Актау, 29а мкр Бизнес-Центр «Шыгыс», ТОО «Art Comfort», магазин «Галерея»</div>

                </div>
            </div>
            <?php elseif($region == 'тараз'):?>
            <div class="col-md-6 col-md-offset-3">
                <div class="col-md-12 text-center">
                    <div class="row">
                        <div class="col-md-12">
                            Тараз - ТД Галерея
                        </div>
                    </div>
                    <div class="row"  style="margin-bottom: 20px">
                        Время работы:<br/>
                        Пн-Сб 10:00 - 19.00<br/>
                        Вс 10:00 - 18:00
                    </div>
                    <div class="row" style="margin-bottom: 20px">
                        <div class="col-md-12">
                            <div class="col-md-6 text-left">Обои, Лепнина, Фотообои, Люстры, Шторы, Тестиль</div>
                            <div class="col-md-6 text-right">+7 (7262) 51-14-69</div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6 text-left">Краски, Напольные покрытия, Плитка</div>
                            <div class="col-md-6 text-right">+7 (7262) 51-94-93</div>
                        </div>
                    </div>
                    <div class="row">г.Тараз, ул.Ташкентская д.82 (пересечение пр.Абая), ТД «Галерея»</div>

                </div>
            </div>
            <?php else:?>
            <div class="col-md-6 col-md-offset-3">
                <div class="col-md-12 text-center">
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo ucfirst($region)?> - <?php echo $shop->getName();?>
                        </div>
                    </div>
                    <div class="row"  style="margin-bottom: 20px">
                        Время работы:<br/>
                        Летнее время: Пн-Вс 09:00 - 19:00<br/>
                        Зимнее время: Пн-Вс 09:00 - 18:00
                    </div>
                    <div class="row" style="margin-bottom: 20px">
                        <div class="col-md-12">
                            <div class="col-md-6 text-left">Общий</div>
                            <div class="col-md-6 text-right"><?php echo $shop->getPhoneNumber()?></div>
                        </div>
                    </div>
                    <div class="row">г. <?php echo ucfirst($region)?></div>

                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>


