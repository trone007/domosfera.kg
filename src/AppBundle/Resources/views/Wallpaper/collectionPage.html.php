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
<script>
    $(document).ready(function() {
        var slideIndex = 1;

        plusSlides = function (n) {
            showSlides(slideIndex += n);
        }

        showSlides = function (n) {
            var i;
            var slides = document.getElementsByClassName("mySlides");
            if (n > slides.length) {slideIndex = 1}
            if (n < 1) {slideIndex = slides.length}
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            slides[slideIndex-1].style.display = "block";
        };

        showSlides(slideIndex);
    });
</script>
<?php $view['slots']->stop()?>

<?php foreach ($catalog as $key => $cat):?>
        <div class="head">
            <h1 class="title-1 head__title"><?php echo $key?></h1>
            <p class="head__subtitle"><?php echo $cat['country'], ', ', $cat['manufacturer']?></p>
            <a href="/new/collections" class="arrow-link arrow-link--invert head__go-back">Ко всему списку</a>
        </div>
        <div class="col-xs-12 col-md-12 slideshow-container" style="height: 500px;">
            <?php foreach ($cat['image'] as $image):?>
            <div class="mySlides fadet">
                    <img src="http://gallery.kg/image/кыргызстан/<?php echo $image['image']?>"  style="width:100%" />
                </div>
            <?php endforeach;?>
            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>
        </div>
    </section>
    <main class="list row">
        <?php foreach ($cat['vendors'] as $vendor):?>
            <?php $vendor['size'] = $vendor['size'] == null ? 1 : $vendor['size'];?>
            <div class="col-sm-6 col-md-2">
                <a href="/new/wallpaper/<?php echo $vendor['vendorCode'];?>" target="_blank" >
                    <div class="list__item list__item--aqua
                            <?php if($vendor['points'] == 1 || $vendor['points'] == 2) echo 'list__item--sale '?>
                            <?php if($vendor['points'] == 7) echo 'list__item--new '?>
                            <?php if($vendor['points'] == 5 || $vendor['points'] == 4) echo 'list__item--hot '?>"
                    >
                        <div class="list__item__pattern
                            " style="background: url('/image?id=<?php echo $vendor['image'];?>&width=300&height=300') no-repeat"></div>
                        <div class="list__item__info">
                            <div class="list__item__info__code"><?php echo $vendor['vendorCode']?></div>
                            <div class="list__item__info__title">
                                <?php echo $vendor['catalog']?>
                            </div>
                            <div class="list__item__info__title"><?php echo $vendor['manufacturer']?></div>
                            <div class="list__item__info__size"><?php echo $vendor['size'] . 'x' .  $vendor['height'];?> м</div>
                            <div class="list__item__info__price"><?php echo $vendor['price']?> </div>
                            <div class="list__item__info__country"><?php echo $vendor['country']?></div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach;?>
    </main>
<?php endforeach;?>
<!--<div class="row">-->
<!--    <div class="col-xs-12 col-sm-3">-->
<!--        <a href="#" class="button-with-arrow button-with-arrow--invert navigate navigate--prev">-->
<!--            <span>Предыдущая коллекция</span>-->
<!--            <div class="navigate__name">Bohemian Burlesque</div>-->
<!--        </a>-->
<!--    </div>-->
<!--    <div class="col-xs-12 col-sm-3 col-sm-offset-6">-->
<!--        <a href="#" class="button-with-arrow navigate navigate--next">-->
<!--            <span>Следующая коллекция</span>-->
<!--            <div class="navigate__name">Ornamenta</div>-->
<!--        </a>-->
<!--    </div>-->
<!--</div>-->