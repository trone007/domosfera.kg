<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <title>Каталог списком</title>
    <link href="/fonts/fonts.css" rel="stylesheet">
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/bootstrap/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="/jquery-ui/jquery-ui.min.css">
    <script src="/js/jquery-3.1.1.min.js"></script>
    <script src="/bootstrap/js/bootstrap.min.js"></script>
    <script src="/js/sidebar.js"></script>
    <script src="/js/filter.js"></script>
    <script src="/js/dogNails.js"></script>
    <script src="/js/jquery-ui-custom.min.js"></script>
    <script src="/js/jquery.ui.touch-punch.min.js"></script>

    <?php $view['slots']->output('meta', '') ?>

</head>
<body>
<?php if(count($view['session']->get('nomenclatures')) == 0 ) { header('Location: /new/'); exit;}?>
<div class="container main-container" id="main-container">
    <header class="header--new"><div class="header--new__wrap">
            <div class="header--new__mobile-view">
                <div class="mobile-menu__handler mobile-menu__handler--open" data-sidebar-open="mobile-menu"><span>menu open</span></div>
                <a href="/new/" class="header--new__title"></a>
            </div>
            <div class="header--new__top">
                <a href="/new/" class="header--new__title"></a>
                <div class="header__region bs__dropdown">
                    <button class="bs__dropdown__toggle" type="button" id="region-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Кыргызстан</button>
                    <ul class="header__navigation__categories__item__menu bs__dropdown__menu" aria-labelledby="region-dropdown">
                        <li>
                            <a href="/other-new/landing">Кыргызстан</a>
                            <ul class="second-level">
                                <li>Бишкек</li>
                                <li>Кара-Балта</li>
                                <li>Ош</li>
                                <li>Талас</li>
                                <li>Токмак</li>
                            </ul>
                        </li>
                        <li><a href="/other-new/landing">Казахстан</a></li>
                        <li><a href="/other-new/landing">Группа компаний</a></li>
                    </ul>
                </div>
                <div class="header--new__block">
                    <a href="/login" class="header--new__link">личный кабинет</a>
                    <a href="/other-new/landing" class="header--new__link header__navigation__more__favorites"><span>138</span>избранное</a>
                </div>
                <div class="header--new__block">
                    <a href="/other-new/landing" class="header--new__link">контакты</a>
                </div>
                <div class="header--new__block">
                    <a href="/other-new/landing" class="header--new__link">мастера</a>
                    <a href="/other-new/landing" class="header--new__link">дизайнеры</a>
                </div>
            </div>


            <div class="header--new__navigation__categories">
                <div class="header--new__navigation__categories__item bs__dropdown">
                    <button class="bs__dropdown__toggle" type="button" id="header-cat1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Обои</button>
                    <ul class="header--new__navigation__categories__item__menu bs__dropdown__menu" aria-labelledby="header-cat1">
                        <li><a href="/other-new/catalog">Cartoon</a></li>
                        <li><a href="/other-new/catalog">Wallpaper</a></li>
                        <li><a href="/other-new/catalog">Walls</a></li>
                    </ul>
                </div>
                <a href="/other-new/landing" class="header--new__navigation__categories__item">Кафель</a>
                <a href="/other-new/landing" class="header--new__navigation__categories__item">Освещение</a>
                <a href="/other-new/landing" class="header--new__navigation__categories__item">Текстиль</a>
                <a href="/other-new/landing" class="header--new__navigation__categories__item">Лепнина</a>
                <a href="/other-new/landing" class="header--new__navigation__categories__item">Краска</a>
                <a href="/other-new/landing" class="header--new__navigation__categories__item">Напольные покрытия</a>
                <a href="/other-new/landing" class="header--new__navigation__categories__item">Ковры</a>
                <a href="/other-new/landing" class="header--new__navigation__categories__item">Мебель</a>
            </div>
        </div></header>
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
                <li><a href="/other-new/landing">Обои</a></li>
                <li><a href="/other-new/landing">Кафель</a></li>
                <li><a href="/other-new/landing">Освещение</a></li>
                <li><a href="/other-new/landing">Текстиль</a></li>
                <li><a href="/other-new/landing">Лепнина</a></li>
                <li><a href="/other-new/landing">Напольные покрытия</a></li>
                <li><a href="/other-new/landing">Ковры</a></li>
                <li><a href="/other-new/landing">Мебель</a></li>
            </ul>
            <ul class="mobile-menu__small-list">
                <li><a href="/other-new/landing">мастера</a></li>
                <li><a href="/other-new/landing">дизайнеры</a></li>
            </ul>
            <ul class="mobile-menu__small-list">
                <li><a href="/other-new/landing">избранное</a></li>
                <li><a href="/other-new/landing">вход в личный кабинет</a></li>
            </ul>
        </div>
        <div class="blackout mobile-menu__handler--close"></div>
    </div>

    <main class="list row">
        <?php $view['slots']->output('_content') ?>
        <?php $view['slots']->output('stylesheets') ?>
    </main>

    <footer class="footer row">
        <div class="col-xs-12 footer__main-menu">
            <a href="/other-new/landing">Обои</a>
            <a href="/other-new/landing">Кафель</a>
            <a href="/other-new/landing">Освещение</a>
            <a href="/other-new/landing">Текстиль</a>
            <a href="/other-new/landing">Лепнина</a>
            <a href="/other-new/landing">Краска</a>
            <a href="/other-new/landing">Напольные покрытия</a>
            <a href="/other-new/landing">Ковры</a>
            <a href="/other-new/landing">Мебель</a>
        </div>
        <div class="col-xs-12 footer__more-menu footer__more-menu--mobile"><div class="row">
                <div class="col-xs-5 footer__more-menu__nav">
                    <a href="/other-new/landing">МАСТЕРА</a>
                    <a href="/other-new/landing">ДИЗАЙНЕРЫ</a>
                    <a href="/other-new/landing">О КОМПАНИИ</a>
                    <a href="/other-new/landing">МАГАЗИНЫ</a>
                    <a href="/other-new/landing">КОНТАКТЫ</a>
                </div>
                <div class="col-xs-7">
                    <a href="/" class="footer__more-menu__logo"></a>
                    <div class="footer__more-menu__socials">
                        <a href="/other-new/landing" target="_blank" class="vk"></a>
                        <a href="/other-new/landing" target="_blank" class="fb"></a>
                        <a href="/other-new/landing" target="_blank" class="tw"></a>
                    </div>
                    <div class="footer__more-menu__copyright">© 2010-2016 Торговый дом Домосфера. Все права защищены.</div>
                </div>
            </div></div>
        <div class="col-xs-12 footer__more-menu footer__more-menu--desktop"><div class="row">
                <div class="col-md-2">
                    <a href="/" class="footer__more-menu__logo"></a>
                </div>
                <div class="col-md-7 footer__more-menu__nav">
                    <a href="/other-new/landing">МАСТЕРА</a>
                    <a href="/other-new/landing">ДИЗАЙНЕРЫ</a>
                    <a href="/other-new/landing">О КОМПАНИИ</a>
                    <a href="/other-new/landing">МАГАЗИНЫ</a>
                    <a href="/other-new/landing">КОНТАКТЫ</a>
                    <div class="footer__more-menu__copyright">© 2010-2016 Торговый дом Домосфера. Все права защищены.</div>
                </div>
                <div class="col-md-3">
                    <div class="footer__more-menu__socials">
                        <a href="/other-new/landing" target="_blank" class="vk"></a>
                        <a href="/other-new/landing" target="_blank" class="fb"></a>
                        <a href="/other-new/landing" target="_blank" class="tw"></a>
                    </div>
                </div>
            </div></div>
    </footer>

</div></body>
<?php $view['slots']->output('scripts', '') ?>


</body>
</html>