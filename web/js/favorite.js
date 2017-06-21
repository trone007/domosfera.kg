var toFavorite = function(vendor) {
    var vendors = localStorage.getItem('vendors');
    if(vendors !== null) {
        vendors = vendors.split(",");
        if(typeof vendors !== 'object') {
            if (vendors == vendor) {
                localStorage.removeItem('vendors');
                return true;
            }
            vendors = [vendors, vendor];
        } else {
            var position = 0;
            var res = true;
            $.each(vendors, function(e, val) {
                console.log(val);
                if(val == vendor) {
                    vendors.splice(position, 1);
                    localStorage.setItem('vendors', vendors);
                    position -= 2;
                    res = false;
                }
                if(val == "") {
                    vendors.splice(position, 1);
                    localStorage.setItem('vendors', vendors);
                    position -= 2;
                }
                position++;
            });

            if(!res) return true;
            vendors.push(vendor);
        }
    } else {
        vendors = [];
        vendors.push(vendor);
    }

    localStorage.setItem('vendors', vendors);
};
var updateFavorite = function() {
    var vendors = localStorage.getItem('vendors').split(",");
    var count = 0;
    if(vendors[0].length == 0) {
        vendors.splice(0,1);
        localStorage.setItem('vendors', vendors);
    }

    if(vendors !== null) {
        count = vendors.length;
    }

    // if(count > 0 && !$('.header__navigation__more__favorites').hasClass('header--new__link--active')) {
    //     $('.header__navigation__more__favorites').addClass('header--new__link--active');
    // } else {
    //     $('.header__navigation__more__favorites').removeClass('header--new__link--active');
    // }

    $('.header__navigation__more__favorites').find('span').html(count);
};

$(document).ready(function(){
    updateFavorite();
    setInterval(updateFavorite, 1000);
});
