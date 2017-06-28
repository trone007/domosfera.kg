<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Домосфера</title>

    <link href="/fonts/fonts.css" rel="stylesheet">
<!--    <link href="/bootstrap/css/bootstrap-theme.min.css"  rel="stylesheet">-->
    <link href="/bootstrap/css/bootstrap.css"  rel="stylesheet">
    <?php $view['slots']->output('meta', '') ?>




</head>
<body>
<?php if(count($view['session']->get('nomenclatures')) == 0 ) { header('Location: /new/'); exit;}?>
<div class="container main-container" id="main-container">

    <header class="header">
        <div class="mobile-menu__handler mobile-menu__handler--open" data-sidebar-open="mobile-menu">
            <span class="btn-hamburger">menu open</span>
        </div>
        <div class="container--flex box--light desktop-only--flex">
                <a href="/new" class="header__title">
                    <img src="/img/logo-old.svg" width="200" alt="Logo">
                </a>
                <div class="navigation">
                    <div class="navigation__top">
                        <div  class="navigation__item navigation__item--right bs__dropdown">
                            <button class="bs__dropdown__toggle" style="text-transform: uppercase" type="button" id="region-dropdown"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <?php echo $view['session']->get('region') ?? 'Кыргызстан';?>
                            </button>
                            <ul class="header__navigation__categories__item__menu bs__dropdown__menu" aria-labelledby="region-dropdown">
                                <?php foreach($view['session']->get('shops') as $key => $values):?>
                                    <li>
                                       <?php echo $key ?>
                                        <?php foreach ($values as $k => $val):?>
                                            <ul class="second-level">
                                                <li><a href="/change-region/<?php echo $k;?>"><?php echo $k;?></a></li>
                                            </ul>
                                        <?php endforeach;?>
                                    </li>
                                <?php endforeach;?>
                            </ul>
                        </div>
                        <a class="navigation__item" href="/new/shops">контакты</a>
                        <a class="navigation__item" href="/new/favorites"><span class="favourite">0</span>избранное</a>


    <!--                    <a href="/login" class="col-md-2">личный кабинет</a>-->
                    </div>
                    <div class="navigation__menu">
                        <a href="/new/landing">Обои</a>
                        <a href="/new/landing">Фотообои</a>
                        <a href="/new/landing">Лепнина</a>
                        <a href="/new/landing">Кафель</a>
                    </div>
                </div>
            </div>
    </header>
    <div class="mobile-menu closed" id="mobile-menu">
        <div class="mobile-menu__container">
            <div class="mobile-menu__head clearfix">
                <div class="select-container">
                    <select name="citySelector" id="citySelector">
                        <?php foreach($view['session']->get('shops') as $key => $values):?>
                                <?php foreach ($values as $k => $val):?>
                                    <option value="bsk"><?php echo $k;?></option>
                                <?php endforeach;?>
                        <?php endforeach;?>
                    </select>
                </div>
                <div class="mobile-menu__handler mobile-menu__handler--close" data-sidebar-close="mobile-menu">
                    <span class="btn-cross">menu close</span>
                </div>
            </div>
            <ul class="mobile-menu__list">
                <li><a href="/new/catalog">Обои</a></li>
                <li><a href="/new/catalog">Фотообои</a></li>
                <li><a href="/new/landing">Лепнина</a></li>
                <li><a href="/new/landing">Кафель</a></li>
            </ul>
            <ul class="mobile-menu__small-list">
                <li><a href="/new/favorites">избранное</a></li>
                <li><a href="/login">вход в личный кабинет</a></li>
            </ul>
        </div>
        <div class="blackout mobile-menu__handler--close"></div>
    </div>
        <?php $view['slots']->output('_content') ?>
        <?php $view['slots']->output('stylesheets') ?>


    <footer class="footer row">
        <div class="col-xs-12 footer__main-menu">
            <a href="/new/catalog">Обои</a>
            <a href="/new/catalog">Фотообои</a>
            <a href="/new/landing">Лепнина</a>
            <a href="/new/landing">Кафель</a>
        </div>
        <div class="col-xs-12 footer__more-menu footer__more-menu--mobile"><div class="row">
                <div class="col-xs-5 footer__more-menu__nav">

                    <a href="/new/landing">О КОМПАНИИ</a>
                    <a href="/new/landing">МАГАЗИНЫ</a>
                    <a href="/new/landing">КОНТАКТЫ</a>
                </div>
                <div class="col-xs-7">
                    <a href="/" class="footer__more-menu__logo"></a>
                    <div class="footer__more-menu__socials">
                        <a href="/new/landing" target="_blank" class="vk"></a>
                        <a href="/new/landing" target="_blank" class="fb"></a>
                        <a href="/new/landing" target="_blank" class="tw"></a>
                    </div>
                    <div class="footer__more-menu__copyright">© 2010-2016 Торговый дом Домосфера. Все права защищены.</div>
                </div>
            </div></div>
        <div class="col-xs-12 footer__more-menu footer__more-menu--desktop"><div class="row">
                <div class="col-md-2">
                    <a href="/" class="footer__more-menu__logo"></a>
                </div>
                <div class="col-md-7 footer__more-menu__nav">

                    <a href="/new/landing">О КОМПАНИИ</a>
                    <a href="/new/landing">МАГАЗИНЫ</a>
                    <a href="/new/landing">КОНТАКТЫ</a>
                    <div class="footer__more-menu__copyright">© 2010-2017 Торговый дом Домосфера. Все права защищены.</div>
                </div>
                <div class="col-md-3">
                    <div class="footer__more-menu__socials">
                        <a href="/new/landing" target="_blank" class="vk"></a>
                        <a href="/new/landing" target="_blank" class="fb"></a>
                        <a href="/new/landing" target="_blank" class="tw"></a>
                    </div>
                </div>
            </div></div>
    </footer>

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