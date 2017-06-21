$(document).ready(function(){
    
    var $mainContainer = $('#main-container');

    var $mobileMenu = $('#mobile-menu');
    var $mobileMenuOpen = $('.mobile-menu__handler--open');
    var $mobileMenuClose = $('.mobile-menu__handler--close');

    var $filterSidebar = $('#filter-sidebar');
    var $filterSidebarOpen = $('.filter-sidebar__handler--open');
    var $filterSidebarClose = $('.filter-sidebar__handler--close');
    var $filterContentContainer = $('.filter__categories__content');

    function openSidebar($sidebar) {
    $mainContainer.addClass('fixed');
    $sidebar.removeClass('closed').addClass('opened');
    }

    function closeSidebar($sidebar) {
    $mainContainer.removeClass('fixed');
    $sidebar.removeClass('opened').addClass('closed');
    }

    $mobileMenuOpen.on("click", function() {
    openSidebar($mobileMenu);
    });

    $mobileMenuClose.on('click', function() {
    closeSidebar($mobileMenu);
    });

    $filterSidebarOpen.on('click', function() {
    openSidebar($filterSidebar);
    });

    $filterSidebarClose.on('click', function() {
    closeSidebar($filterSidebar);
    $filterContentContainer.removeClass('is-visible');
    });

});