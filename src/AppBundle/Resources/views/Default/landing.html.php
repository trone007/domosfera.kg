<?php $prefix = explode('/', $_SERVER['REQUEST_URI'])[1] ?>
<?php switch($prefix): ?>
<?php case 'new': $view->extend('::smallBase.html.php'); break;?>
    <?php case 'other-new': $view->extend('::largeBase.html.php'); break;?>
    <?php default : $view->extend('::base.html.php'); break;?>
    <?php endswitch;?>
<?php $view['slots']->start('meta')?>
    <link rel="stylesheet" href="/styles.css">
<?php $view['slots']->stop()?>
<?php $view['slots']->start('scripts')?>

<script src="/js/sly.min.js"></script>
<script src="/js/sidebar.js"></script>

<script src="/js/carousel.js"></script>

<script>
    $(document).ready(carousel);
</script>
<?php $view['slots']->stop()?>

<section class="section-hero" style="background-image: url(/img/hero-bg.png)">
    <div class="col-md-5 col-md-offset-1">
        <h1 class="title-1">Идеальные обои для ваших стен</h1>
        <p class="lead">Самый обширный ассортимент обоев: цвета, фактуры и дизайнерские решения от надежных и качественных обойных брендов со всего мира.</p>
    </div>
    <div class="square"></div>
</section>

<section class="section-text row">
    <div class="col-md-6">
        <h2 class="title-2 section-text__title">Самый большой<br/> ассортимент обоев</h2>
        <p>Более 30 000 рулонов обоев в наличии и на заказ—
            мы постоянно привозим новые коллекции и заботимся о поддержании ассортимента.
            В торговом доме «Галерея» вы всегда найдете именно то, что вам нужно.
            Просто расскажите консультантам, чего вам хочется или покажите дизайн-проект.
            И они с радостью помогут подобрать лучшие варианты.</p>
        <a href="/new/catalog" class="button-with-arrow">
            <span class="arrow-link">Перейти в каталог</span>
        </a>
    </div>
    <div class="col-md-6 goods-demo" style="background-image: url(/img/goods-demo.png);margin-top: 30px;">
        <div class="goods-demo__content">
            <div class="goods-demo__content__title">Muralto Florian 2</div>
            <div class="goods-demo__content__country">Италия, Sirpi</div>
            <div class="goods-demo__content__price"><span>от</span> 620</div>
        </div>
    </div>
</section>

<section class="section-carousel">
    <h2 class="section-carousel__title title-2">Для всего дома</h2>
    <p class="section-carousel__text">Более 30 000 рулонов обоев в наличии и на заказ— мы постоянно привозим<br> новые коллекции и заботимся о поддержании ассортимента</p>
    <ul class="section-carousel__room-list">
        <li class="section-carousel__room-list__item active" data-room="livingroom">
            <div class="icon"></div>
            <div class="name">Гостинная</div>
        </li>
        <li class="section-carousel__room-list__item" data-room="bedroom">
            <div class="icon"></div>
            <div class="name">Спальня</div>
        </li>
        <li class="section-carousel__room-list__item" data-room="childroom">
            <div class="icon"></div>
            <div class="name">Детская</div>
        </li>
    </ul>
    <div class="wallpaper-carousel">
        <div class="wallpaper-carousel__frame">
            <ul class="slide">
                <?php foreach($wallpapers as $wallpaper):?>
                <li class="wallpaper-carousel__item wallpaper-carousel__item--aqua">
                    <div class="wallpaper-carousel__item__info">
                        <div class="wallpaper-carousel__item__price"><?php echo $wallpaper->getPrice()?></div>
                        <div class="wallpaper-carousel__item__code">Артикул <?php echo $wallpaper->getVendorCode()?></div>
                        <div class="wallpaper-carousel__item__size"><?php echo $wallpaper->getSize() . 'x' .  round(1/$wallpaper->getMarketPlan() ?: 1/$wallpaper->getSize(), 2);?> м</div>
                        <a href="/new/wallpaper/<?php echo $wallpaper->getVendorCode()?>"
                           target="_blank" class="wallpaper-carousel__item__title"><?php echo $wallpaper->getCatalog()?></a>
                        <div class="wallpaper-carousel__item__like"></div>
                    </div>
                    <div class="wallpaper-carousel__item__pattern" style="background-image: url(/image?id=<?php echo $wallpaper->getImage()?>&width=205&height=500"></div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="wallpaper-carousel__overlay wallpaper-carousel__overlay__text active" data-room="livingroom">
            <div class="title-3">Более <mark>300 видов</mark> обоев для роскошной гостиной</div>
            <p>Богатый вензель, дамаск и узоры — <br>впечатлите гостей от  <span class="price">400</span> за рулон</p>
        </div>
        <div class="wallpaper-carousel__overlay wallpaper-carousel__overlay__figure active" data-room="livingroom"></div>
        <div class="wallpaper-carousel__overlay wallpaper-carousel__overlay__text" data-room="bedroom">
            <div class="title-2">Более <mark>100 видов</mark> обоев для нежной спальни</div>
            <p>Богатый вензель, дамаск и узоры — <br>впечатлите гостей от  <span class="price">400</span> за рулон</p>
        </div>
        <div class="wallpaper-carousel__overlay wallpaper-carousel__overlay__figure" data-room="bedroom"></div>
        <div class="wallpaper-carousel__overlay wallpaper-carousel__overlay__text" data-room="childroom">
            <div class="title-2">Более <mark>50 видов</mark> обоев для милой детской</div>
            <p>Богатый вензель, дамаск и узоры — <br>впечатлите гостей от  <span class="price">400</span> за рулон</p>
        </div>
        <div class="wallpaper-carousel__overlay wallpaper-carousel__overlay__figure" data-room="childroom"></div>
    </div>
</section>

<section class="section-text section-text--no-margins row">
    <div class="col-md-6">
        <h2 class="lead">Более 30 000 рулонов обоев в наличии и на заказ— мы постоянно привозим новые коллекции и заботимся об ассортименте</h2>
        <div class="list row">

            <?php $i=0; foreach($wallpapers as $data):?>
                <div class="col-sm-6 col-md-4">
                    <a href="/<?php echo $prefix != '' ? $prefix . '/' : ''?>wallpaper/<?php echo $data->getVendorCode();?>"
                       target="_blank">
                        <div class="list__item list__item--beige">
                            <div class="list__item__pattern" style="background-image: url('/image?id=<?php echo $data->getImage()?>&width=300&height=300'"></div>
                            <div class="list__item__info">
                                <div class="list__item__info__code"><?php echo $data->getVendorCode();?></div>
                                <div class="list__item__info__like" onclick="toFavorite('<?php echo $data->getVendorCode()?>')"></div>
                                <a href="/<?php echo $prefix != '' ? $prefix . '/' : ''?>wallpaper/<?php echo $data->getVendorCode();?>" target="_blank" class="list__item__info__title"><?php echo $data->getCatalog();?></a>
                                <div class="list__item__info__title">
                                    <?php echo $data->getManufacturer()?>
                                </div>
                                <div class="list__item__info__size">0.53x10 м</div>
                                <div class="list__item__info__price">
                                    <?php echo $data->getPrice()?>
                                </div>
                                <div class="list__item__info__country"><?php echo $data->getCountry()?></div>

                            </div>
                        </div>
                    </a>
                </div>
                <?php if($i++ == 2 ) break;?>
            <?php endforeach;?>
        </div>
    </div>
    <div class="col-md-6 goods-demo" style="background-image: url(/img/goods-demo-2.png)">
        <div class="goods-demo__content">
            <div class="goods-demo__content__title">Grandeko</div>
            <div class="goods-demo__content__country">Германия, Sirpi</div>
            <div class="goods-demo__content__price"><span>от</span> 700</div>
            <a href="/new/catalog" class="button-with-arrow">
                <span class="arrow-link">Перейти в каталог</span>
            </a>
        </div>
    </div>
</section>

<section class="section-text section-text--grey section-text--with-map row">
    <div class="col-lg-5 col-lg-offset-1">
        <h2 class="title-2 section-text__title">Всегда рядом с вами</h2>
        <p>Сюда приятно зайти и здесь удобно выбирать. Все товары распределены по категориям и аккуратно расставлены. Обои, фрески, трафареты, шторы и лепнина — все находится на своем заранее подготовленном месте.</p>
        <a href="/new/shops" class="button-with-arrow">
            <span class="arrow-link">Найти ближайший магазин</span>
        </a>
    </div>
    <div class="map">
        <img src="/img/map.png" alt="map">
    </div>
</section>
