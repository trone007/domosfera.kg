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
<script src="/js/sidebar.js"></script>
<script src="/js/peppermint.min.js"></script>
<script src="/js/home.js"></script>
<?php $view['slots']->stop()?>


<section class="section-text section-text--bordered">
    <div class="section-text__content">
        <h1 class="title-1 section-text__title">Лучший каталог для ваших идей</h1>
        <div class="section-text__info">
            <p>Торговый дом Галерея — это ваш надежный партнер во всем, что касается интерьера.
                Здесь вы можете купить любые товары для ремонта и дизайна помещений от лучших
                зарубежных производителей, а также различные предметы интерьера,
                <mark>элементы декора</mark>, стильные <mark>светильники</mark>
                и элитные <mark>люстры.</mark></p>
            <p>В нашем каталоге представлены товары из разных стран: Англия, Бельгия,
                Германия, Голландия, Италия, Китай, Россия, США, Украина, Франция.</p>
        </div>
    </div>
</section>

<section class="section-slider">
    <div class="slider-arrow slider-arrow--left hidden">
        <span class="arrow"></span>
        <span class="neighbor-slide-name"></span>
    </div>
    <div class="slider peppermint" id="carousel">
        <div class="slider-item" data-slide-name="Обои">
            <div class="col-sm-8 col-md-8 slider-item__image" style="background-image: url(/img/design/1.png)">
                <div class="slider-item__content">
                    <div class="slider-item__image__content__title">Muralto Florian 2</div>
                    <div class="slider-item__image__content__country">Италия, Sirpi</div>
                    <div class="slider-item__price price">5 320</div>
                    <a href="/new/landing" class="button-with-arrow">
                        <span class="arrow-link">Все обои</span>
                    </a>
                </div>
            </div>
            <div class="col-sm-4 col-md-4 slider-item__info">
                <h2 class="title-2">Широкий выбор обоев</h2>
                <div class="slider-item__info__text">Широкий выбор <mark>обоев</mark> позволит подобрать вам лучший вариант для вашего интерьера. В нашем магазине вы сможете купить обои любого типа:
                    <mark>бумажные, текстильные, виниловые</mark>  и полностью <mark>флизелиновые.</mark> </div>
                <div class="slider-item__info__pages">1/3</div>
            </div>
        </div>
        <div class="slider-item" data-slide-name="Кафель">
            <div class="col-sm-8 col-md-8 slider-item__image" style="background-image: url(/img/collection-intro.jpg)">
                <div class="slider-item__content">
                    <div class="slider-item__image__content__title">Muralto Florian 2</div>
                    <div class="slider-item__image__content__country">Италия, Sirpi</div>
                    <div class="slider-item__price price">5 320</div>
                    <a href=/new/landing" class="button-with-arrow">
                        <span class="arrow-link">Все обои</span>
                    </a>
                </div>
            </div>
            <div class="col-sm-4 col-md-4 slider-item__info">
                <h2 class="title-2">Широкий выбор обоев</h2>
                <div class="slider-item__info__text">Широкий выбор <mark>обоев</mark> позволит подобрать вам лучший вариант для вашего интерьера. В нашем магазине вы сможете купить обои любого типа:
                    <mark>бумажные, текстильные, виниловые</mark>  и полностью <mark>флизелиновые.</mark> </div>
                <div class="slider-item__info__pages">2/3</div>
            </div>
        </div>
        <div class="slider-item" data-slide-name="Ещё">
            <div class="col-sm-8 col-md-8 slider-item__image" style="background-image: url(/img/collection-intro-2.jpg)">
                <div class="slider-item__content">
                    <div class="slider-item__image__content__title">Muralto Florian 2</div>
                    <div class="slider-item__image__content__country">Италия, Sirpi</div>
                    <div class="slider-item__price price">5 320</div>
                    <a href="/new/landing" class="button-with-arrow">
                        <span class="arrow-link">Все обои</span>
                    </a>
                </div>
            </div>
            <div class="col-sm-4 col-md-4 slider-item__info">
                <h2 class="title-2">Широкий выбор обоев</h2>
                <div class="slider-item__info__text">Широкий выбор <mark>обоев</mark> позволит подобрать вам лучший вариант
                    для вашего интерьера. В нашем магазине вы сможете купить обои любого типа: <mark>бумажные,
                        текстильные, виниловые</mark>  и полностью <mark>флизелиновые.</mark> </div>
                <div class="slider-item__info__pages">3/3</div>
            </div>
        </div>
    </div>
    <div class="slider-arrow slider-arrow--right">
        <span class="neighbor-slide-name"></span>
        <span class="arrow"></span>
    </div>
</section>

<section class="section-collection">
    <div class="section-collection__head">
        <h3 class="title-3 section-collection__head__title">Дизайнеры</h3>
        <span>Лучшие дизайнеры интерьеров Киргизии</span>
    </div>
    <div class="row list">
        <div class="col-sm-6 col-md-3"><div class="list__item">
                <div class="list__item__image" style="background-image: url(/img/design/1.png)"></div>
                <div class="list__item__info">
                    <img class="list__item__info__logo" alt="Designer logo" src="http://shilkinstudio.ru/wp-content/uploads/2015/11/shilkinlogo.png"/>
                    <a href="#" target="_blank" class="list__item__info__title">Студия Алексея Шилкина</a>
                </div>
            </div></div>
        <div class="col-sm-6 col-md-3"><div class="list__item">
                <div class="list__item__image" style="background-image: url(/img/design/1.png)"></div>
                <div class="list__item__info">
                    <img class="list__item__info__logo" alt="Designer logo" src="http://shilkinstudio.ru/wp-content/uploads/2015/11/shilkinlogo.png"/>
                    <a href="#" target="_blank" class="list__item__info__title">Студия Алексея Шилкина</a>
                </div>
            </div></div>
        <div class="col-sm-6 col-md-3"><div class="list__item">
                <div class="list__item__image" style="background-image: url(/img/design/1.png)"></div>
                <div class="list__item__info">
                    <img class="list__item__info__logo" alt="Designer logo" src="http://shilkinstudio.ru/wp-content/uploads/2015/11/shilkinlogo.png"/>
                    <a href="#" target="_blank" class="list__item__info__title">Студия Алексея Шилкина</a>
                </div>
            </div></div>
        <a href="/new/collections" class="col-sm-6 col-md-3 list__item--next">
            <span class="arrow-link">Все дизайнеры</span>
            <div class="list__item--next__count">91<small>дизайнер</small></div>
        </a>
    </div>
</section>

<section class="section-collection">
    <div class="section-collection__head">
        <h3 class="title-3 section-collection__head__title">Мастера</h3>
        <span>Самый большой выбор мастеров по строительству и ремонту в Киргизии</span>
    </div>
    <div class="row list">
        <div class="col-sm-6 col-md-2"><div class="list__item">
                <div class="list__item__image" style="background-image: url(/img/userpic.png)"></div>
                <div class="list__item__info">
                    <a href="#" target="_blank" class="list__item__info__title">Карцев<br>Денис</a>
                    <div class="list__item__info__text">Сантехник, отопление, бытовая техника</div>
                    <div class="list__item__info__text">Стаж: 5 лет</div>
                </div>
            </div></div>
        <div class="col-sm-6 col-md-2"><div class="list__item">
                <div class="list__item__image" style="background-image: url(/img/userpic.png)"></div>
                <div class="list__item__info">
                    <a href="#" target="_blank" class="list__item__info__title">Карцев<br>Денис</a>
                    <div class="list__item__info__text">Сантехник, отопление, бытовая техника</div>
                    <div class="list__item__info__text">Стаж: 5 лет</div>
                </div>
            </div></div>
        <div class="col-sm-6 col-md-2"><div class="list__item">
                <div class="list__item__image" style="background-image: url(/img/userpic.png)"></div>
                <div class="list__item__info">
                    <a href="#" target="_blank" class="list__item__info__title">Карцев<br>Денис</a>
                    <div class="list__item__info__text">Сантехник, отопление, бытовая техника</div>
                    <div class="list__item__info__text">Стаж: 5 лет</div>
                </div>
            </div></div>
        <div class="col-sm-6 col-md-2"><div class="list__item">
                <div class="list__item__image" style="background-image: url(/img/userpic.png)"></div>
                <div class="list__item__info">
                    <a href="#" target="_blank" class="list__item__info__title">Карцев<br>Денис</a>
                    <div class="list__item__info__text">Сантехник, отопление, бытовая техника</div>
                    <div class="list__item__info__text">Стаж: 5 лет</div>
                </div>
            </div></div>
        <div class="col-sm-6 col-md-2"><div class="list__item">
                <div class="list__item__image" style="background-image: url(/img/userpic.png)"></div>
                <div class="list__item__info">
                    <a href="#" target="_blank" class="list__item__info__title">Карцев<br>Денис</a>
                    <div class="list__item__info__text">Сантехник, отопление, бытовая техника</div>
                    <div class="list__item__info__text">Стаж: 5 лет</div>
                </div>
            </div></div>
        <a href="/new/collections" class="col-sm-6 col-md-2 list__item--next">
            <span class="arrow-link">Все мастера</span>
            <div class="list__item--next__count">128<small>мастеров</small></div>
        </a>
    </div>
</section>