<?php $prefix = explode('/', $_SERVER['REQUEST_URI'])[1]; ?>
<?php $prefix = $prefix != 'new' && $prefix != 'other-new' ? '': $prefix; ?>
<?php switch($prefix): ?>
<?php case 'new': $view->extend('::smallBase.html.php'); break;?>
<?php case 'other-new': $view->extend('::largeBase.html.php'); break;?>
<?php default : $view->extend('::empty.html.php'); break;?>
<?php endswitch;?>

<?php $view['slots']->start('meta')?>
    <link rel="stylesheet" href="/styles.css">
<?php $view['slots']->stop()?>

<?php $view['slots']->start('scripts')?>
<script src="/js/blowup.min.js"></script>
<script src="/js/product.js"></script>
    <script>
    (function(){
        var vendors = localStorage.getItem('vendors');
        var vendor = $('.article').html();
        if(vendors !== null) {
            vendors = vendors.split(",");
            $.each(vendors, function(e, val) {
                if(val == vendor) {
                    $('.js-like').removeClass('js-like--false');
                    $('.js-like').addClass('js-like--true');
                    $('.js-like').html('Убрать из избранного');
                }

                $('.list__item__info__code').each(function(e, el) {
                    if($(el).html() == val) {
                        $(el).parent().find('.list__item__info__like').addClass('list__item__info__like--active');
                    }
                });
            });
        }
    }());

    $(document).ready(function() {
        $('.list__item__info__like').click(function(){
            if($(this).hasClass('list__item__info__like--active')) {
                $(this).removeClass('list__item__info__like--active');
            } else {
                $(this).addClass('list__item__info__like--active');
            }
        });

        $('.form-input').hide();

        $('.head-block').click(function(){
            if($('.form-input').is(':visible')) {
                $('.form-input').hide();
            } else {
                $('.form-input').show();
            }

        });

        $('#message-form').submit(function(event){
            event.preventDefault();

            var post = $.post(
                '/save-message',
                $('#message-form').serialize()
            );

            post.done(function(data) {
                $('#text-area').val('Сохранено');

                setTimeout(function(){
                    $('.form-input').hide();

                    $('#text-area').val('');
                }, 2000)
            });

            post.fail(function(){
                alert('Произошла ошибка');
            });
        });

    });
</script>
<?php $view['slots']->stop()?>

<section class="section-product">
    <div class="breadcrumps">
    </div>
    <div class="row">
        <div class="col-sm-5 col-md-4 product__info
            <?php
                if($wallpaper->getPriceOld() - $wallpaper->getPrice() > 0) {echo 'product__info--sale';}
                else if($wallpaper->getPoints() == 7) {echo 'product__info--new';}
                else if($wallpaper->getPoints() == 1) {echo 'product__info--hot';}
                ?>">
            <div class="product__info__title"><?php echo $wallpaper->getCatalog()?></div>
            <table class="product__info__table">
                <tr>
                    <td>Артикул</td>
                    <td class="article"><?php echo $wallpaper->getVendorCode()?></td>
                </tr>
                <tr>
                    <td>Группа</td>
                    <td><a href="#"><?php echo $wallpaper->getCatalog()?></a></td>
                </tr>
                <tr>
                    <td>Страна</td>
                    <td><a href="#"><?php echo $wallpaper->getCountry()?></a></td>
                </tr>
                <tr>
                    <td>Фабрика</td>
                    <td><a href="#"><?php echo $wallpaper->getManufacturer()?></a></td>
                </tr>
                <tr>
                    <td>Вид обоев</td>
                    <td><a href="#"><?php echo $wallpaper->getBasis()?></a></td>
                </tr>
                <tr>
                    <td>Рулон</td>
                    <td class="size"><?php echo $wallpaper->getSize() . 'x' .  round(1/$wallpaper->getMarketPlan()/$wallpaper->getSize(), 2);?> м</td>
                </tr>
                <tr>
                    <td>Остатки</td>
                    <td class="remains remains--green"></td>
                </tr>
            </table>
            <div class="product__info__addition">
                <div class="product__info__addition__like js-like js-like--false" onclick="toFavorite('<?php echo $wallpaper->getVendorCode()?>')">В&nbsp;избранное</div>
                <div class="product__info__addition__price">
                    <span><?php echo $wallpaper->getPrice()?></span>
                    <p>За&nbsp;1&nbsp;рулон</p>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-7 col-md-8 product__image">
            <img src="/image?id=<?php echo $wallpaper->getImage()?>&width=1000&height=1000" alt="Product image">
        </div>
    </div>
</section>
<?php if($complectsData): ?>
    <div class="row list">
        <div class="list__head">
            <h3 class="title-3">Компаньоны</h3>
        </div>
            <?php foreach($complectsData as $data):?>
                <div class="col-sm-6 col-md-2">
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
                                <div class="list__item__info__country"><?php echo $data->getCountry(), "({$shops['kgb']['count']})";?></div>

                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach;?>
    </div>
<?php endif;?>
    <div class="row list" style="margin-bottom: 40px; margin-top:20px">
        <div class="list__head">
            <h3 class="title-3">Отстатки на складах</h3>
        </div>
        <table class="table">
            <thead>
                <tr class="text-center">
                    <th>Подразделение</th>
                    <th>Партия</th>
                    <th>Резерв</th>
                    <th>Рулон</th>
                </tr>
            </thead>
            <?php foreach($shops as $shop):?>
                <tr>
                    <td><?php echo $shop['name'] ?></td>
                    <td><?php echo $shop['lot'] ?></td>
                    <td><?php echo Round($shop['reserve']/$wallpaper->getMarketPlan(),2) ?></td>
                    <td><?php echo Round($shop['count'],2)  ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

<style>
footer.container {
    position: fixed;
    bottom: 0;
    z-index: 3;
}

@media(max-width: 768px) {
    footer.container {
        width: 100%;
    }
}

.head-block {
    background: url('/img/pattern.png') no-repeat center center/cover;
    height: 40px;
    color: white;
    padding: 10px;
}
.head-block:hover {
    cursor: pointer;
}

.container .form-input textarea {
    height: 200px;
}
.container .form-input .btn {
    width: 100%;
}

.list {
    margin: 60px 0 0 0;
}

</style>
<?php if($prefix == ''):?>
<footer class="container">
    <div class="col-lg-offset-4 col-lg-4 col-sm-12 col-xs-12">
        <div class="lg-col-12 head-block text-center">
            <h2>Оставить сообщение</h2>
        </div>
        <div class="row form-input">
            <form id="message-form">
                <div class="col-lg-12">
                    <input type="text" name="login" class="form-control" disabled value="<?php echo !empty($user) ? $user->getUsername() : ''?>"/>
                    <input type="hidden" name="vendorCode" value="<?php echo $wallpaper->getVendorCode()?>"/>
                </div>
                <div class="col-lg-12">
                    <textarea name="data" id="text-area" class="form-control"></textarea>
                </div>
                <?php if(!empty($user)):?>
                    <div class="col-lg-12">
                        <button id="send-message" class="btn btn-info">Отправить</button>
                    </div>
                <?php else:?>
                    <div class="col-lg-12">
                        <a href="/login" target="_blank" class="btn btn-info">Войти в систему</a>
                    </div>
                <?php endif;?>
            </form>
        </div>
    </div>
</footer>
<?php endif;?>