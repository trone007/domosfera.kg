$(document).ready(function () {
    var productImageHeight = $('.product__info').outerHeight();
	$('.product__image').height(productImageHeight).find('img').blowup({'cursor': false});
})