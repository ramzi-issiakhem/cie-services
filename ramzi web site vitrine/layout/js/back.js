$(function(){
    'use strict';
    
    // flikiyt carousell 
    $('.main-carousel').flickity({
        // options
        cellAlign: 'left',
        wrapArround: true,
        freeScroll: true,
        contain: true
    });

    // scroll to the top
    $(".back-to-top-btn").click(function() {
        // $(this).hide();
        window.scrollTo({top: 0, behavior: 'smooth'});
    });

    // focus item in nav bar
    $("a.nav-link").click(function() {
        $("a.nav-link").removeClass('active');
        $(this).addClass("active");
    });

    // change focus side bar and nav bar
    $(window).scroll(function() {
        var scroll = $(window).scrollTop();

        var sec1 = $('.home-sb').offset().top - 300;
        var sec2 = $('.about-sb').offset().top - 300;
        var sec3 = $('.service-sb').offset().top - 300;
        var sec4 = $('.contact-sb').offset().top - 300;

        if(scroll > sec4){
            $(".side-bar ul li p").removeClass('active');
            $(".sb4").addClass("active");
            $("a.nav-link").removeClass('active');
            $(".nb4").addClass("active");
        }
        else if(scroll > sec3){
            $(".side-bar ul li p").removeClass('active');
            $(".sb3").addClass("active");
            $("a.nav-link").removeClass('active');
            $(".nb3").addClass("active");
        }
        else if(scroll > sec2){
            console.log(scroll >= sec2);
            $(".side-bar ul li p").removeClass('active');
            $(".sb2").addClass("active");
            $("a.nav-link").removeClass('active');
            $(".nb2").addClass("active");
        }
        else if(scroll > sec1){
            $(".side-bar ul li p").removeClass('active');
            $(".sb1").addClass("active");
            $("a.nav-link").removeClass('active');
            $(".nb1").addClass("active");
        }
    });
});



