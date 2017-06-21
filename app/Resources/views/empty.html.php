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
<div class="container main-container" id="main-container">
    <!--    <header class="header--new"><div class="header--new__wrap">-->
    <!--            <div class="header--new__mobile-view">-->
    <!--                <div class="mobile-menu__handler mobile-menu__handler--open" data-sidebar-open="mobile-menu"><span>menu open</span></div>-->
    <!--                <a href="/" class="header--new__title"></a>-->
    <!--            </div>-->
    <!--            <div class="header--new__top">-->
    <!--                <a href="/" class="header--new__title"></a>-->
    <!--                <div class="header__region bs__dropdown">-->
    <!--                    <button class="bs__dropdown__toggle" type="button" id="region-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Кыргызстан</button>-->
    <!--                    <ul class="header__navigation__categories__item__menu bs__dropdown__menu" aria-labelledby="region-dropdown">-->
    <!--                        <li>-->
    <!--                            <a href="/landing">Кыргызстан</a>-->
    <!--                            <ul class="second-level">-->
    <!--                                <li>Бишкек</li>-->
    <!--                                <li>Кара-Балта</li>-->
    <!--                                <li>Ош</li>-->
    <!--                                <li>Талас</li>-->
    <!--                                <li>Токмак</li>-->
    <!--                            </ul>-->
    <!--                        </li>-->
    <!--                        <li><a href="/landing">Казахстан</a></li>-->
    <!--                        <li><a href="/landing">Группа компаний</a></li>-->
    <!--                    </ul>-->
    <!--                </div>-->
    <!--                <div class="header--new__block">-->
    <!--                    <a href="/login" class="header--new__link">личный кабинет</a>-->
    <!--                    <a href="/favorites" class="header--new__link header__navigation__more__favorites"><span>0</span>избранное</a>-->
    <!--                </div>-->
    <!--                <div class="header--new__block">-->
    <!--                    <a href="/landing" class="header--new__link">контакты</a>-->
    <!--                </div>-->
    <!--                <div class="header--new__block">-->
    <!--                    <a href="/landing" class="header--new__link">мастера</a>-->
    <!--                    <a href="/landing" class="header--new__link">дизайнеры</a>-->
    <!--                </div>-->
    <!--            </div>-->
    <!---->
    <!---->
    <!--            <div class="header--new__navigation__categories">-->
    <!---->
    <!--                --><?php //foreach ($view['session']->get('nomenclatures') as $nomenclature) : ?>
    <!--                    --><?php //if(count($nomenclature['children']) > 0) :?>
    <!--                        <div class="header--new__navigation__categories__item bs__dropdown">-->
    <!--                            <button class="bs__dropdown__toggle" type="button" id="header-cat1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">--><?php //echo $nomenclature['nomenclature']?><!--</button>-->
    <!--                            <ul class="header--new__navigation__categories__item__menu bs__dropdown__menu" aria-labelledby="header-cat1">-->
    <!--                                --><?php //foreach ($nomenclature['children'] as $child):?>
    <!--                                    <li><a href="/--><?php //echo $child['href']?><!--">--><?php //echo $child['nomenclature']?><!--</a></li>-->
    <!--                                --><?php //endforeach; ?>
    <!--                            </ul>-->
    <!--                        </div>-->
    <!--                    --><?php //else: ?>
    <!--                        <a href="/--><?php //echo $nomenclature['href']?><!--" class="header--new__navigation__categories__item">--><?php //echo $nomenclature['nomenclature']?><!--</a>-->
    <!--                    --><?php //endif;?>
    <!--                --><?php //endforeach; ?>
    <!--            </div>-->
    <!--        </div></header>-->
    <!---->
    <!--    <div class="mobile-menu closed" id="mobile-menu">-->
    <!--        <div class="mobile-menu__container">-->
    <!--            <div class="mobile-menu__head clearfix">-->
    <!--                <div class="select-container">-->
    <!--                    <select name="citySelector" id="citySelector">-->
    <!--                        <option value="bsk">Бишкек</option>-->
    <!--                        <option value="krb">Кара-Балта</option>-->
    <!--                        <option value="osh">Ош</option>-->
    <!--                        <option value="tls">Талас</option>-->
    <!--                        <option value="tmk">Токмак</option>-->
    <!--                        <option value="kzh">Казахстан</option>-->
    <!--                        <option value="oth">Другие</option>-->
    <!--                    </select>-->
    <!--                </div>-->
    <!--                <div class="mobile-menu__handler mobile-menu__handler--close" data-sidebar-close="mobile-menu"><span>menu close</span></div>-->
    <!--            </div>-->
    <!--            <ul class="mobile-menu__list">-->
    <!--                <li><a href="/catalog">Обои</a></li>-->
    <!--                <li><a href="/landing">Кафель</a></li>-->
    <!--                <li><a href="/landing">Освещение</a></li>-->
    <!--                <li><a href="/landing">Текстиль</a></li>-->
    <!--                <li><a href="/landing">Лепнина</a></li>-->
    <!--                <li><a href="/landing">Напольные покрытия</a></li>-->
    <!--                <li><a href="/landing">Ковры</a></li>-->
    <!--                <li><a href="/landing">Мебель</a></li>-->
    <!--            </ul>-->
    <!--            <ul class="mobile-menu__small-list">-->
    <!--                <li><a href="/landing">мастера</a></li>-->
    <!--                <li><a href="/landing">дизайнеры</a></li>-->
    <!--            </ul>-->
    <!--            <ul class="mobile-menu__small-list">-->
    <!--                <li><a href="/landing">избранное</a></li>-->
    <!--                <li><a href="/landing">вход в личный кабинет</a></li>-->
    <!--            </ul>-->
    <!--        </div>-->
    <!--        <div class="blackout mobile-menu__handler--close"></div>-->
    <!--    </div>-->
    <?php $view['slots']->output('_content') ?>
    <?php $view['slots']->output('stylesheets') ?>
    <!---->
    <!--    <footer class="footer row">-->
    <!--        <div class="col-xs-12 footer__main-menu">-->
    <!--            <a href="/landing">Обои</a>-->
    <!--            <a href="/landing">Кафель</a>-->
    <!--            <a href="/landing">Освещение</a>-->
    <!--            <a href="/landing">Текстиль</a>-->
    <!--            <a href="/landing">Лепнина</a>-->
    <!--            <a href="/landing">Краска</a>-->
    <!--            <a href="/landing">Напольные покрытия</a>-->
    <!--            <a href="/landing">Ковры</a>-->
    <!--            <a href="/landing">Мебель</a>-->
    <!--        </div>-->
    <!--        <div class="col-xs-12 footer__more-menu footer__more-menu--mobile"><div class="row">-->
    <!--                <div class="col-xs-5 footer__more-menu__nav">-->
    <!--                    <a href="/landing">МАСТЕРА</a>-->
    <!--                    <a href="/landing">ДИЗАЙНЕРЫ</a>-->
    <!--                    <a href="/landing">О КОМПАНИИ</a>-->
    <!--                    <a href="/landing">МАГАЗИНЫ</a>-->
    <!--                    <a href="/landing">КОНТАКТЫ</a>-->
    <!--                </div>-->
    <!--                <div class="col-xs-7">-->
    <!--                    <a href="/" class="footer__more-menu__logo"></a>-->
    <!--                    <div class="footer__more-menu__socials">-->
    <!--                        <a href="/landing" target="_blank" class="vk"></a>-->
    <!--                        <a href="/landing" target="_blank" class="fb"></a>-->
    <!--                        <a href="/landing" target="_blank" class="tw"></a>-->
    <!--                    </div>-->
    <!--                    <div class="footer__more-menu__copyright">© 2010-2016 Торговый дом Домосфера. Все права защищены.</div>-->
    <!--                </div>-->
    <!--            </div></div>-->
    <!--        <div class="col-xs-12 footer__more-menu footer__more-menu--desktop"><div class="row">-->
    <!--                <div class="col-md-2">-->
    <!--                    <a href="/" class="footer__more-menu__logo"></a>-->
    <!--                </div>-->
    <!--                <div class="col-md-7 footer__more-menu__nav">-->
    <!--                    <a href="/landing">МАСТЕРА</a>-->
    <!--                    <a href="/landing">ДИЗАЙНЕРЫ</a>-->
    <!--                    <a href="/landing">О КОМПАНИИ</a>-->
    <!--                    <a href="/landing">МАГАЗИНЫ</a>-->
    <!--                    <a href="/landing">КОНТАКТЫ</a>-->
    <!--                    <div class="footer__more-menu__copyright">© 2010-2016 Торговый дом Домосфера. Все права защищены.</div>-->
    <!--                </div>-->
    <!--                <div class="col-md-3">-->
    <!--                    <div class="footer__more-menu__socials">-->
    <!--                        <a href="/landing" target="_blank" class="vk"></a>-->
    <!--                        <a href="/landing" target="_blank" class="fb"></a>-->
    <!--                        <a href="/landing" target="_blank" class="tw"></a>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div></div>-->
    <!--    </footer>-->

</div>

<script src="/js/jquery-3.1.1.min.js"></script>
<script src="/bootstrap/js/bootstrap.min.js"></script>

<script src="/js/sidebar.js"></script>
<script src="/js/dogNails.js"></script>
<script src="/js/jquery-ui.js"></script>
<script src="/js/favorite.js"></script>
<?php $view['slots']->output('scripts', '') ?>


</body>
</html>