const $ = require('jquery');
import "@popperjs/core";
import "bootstrap";
import aos from 'aos';


$(document).ready(function () {

    /* AOS library */
    aos.init({
        disable: 'mobile', // accepts following values: 'phone', 'tablet', 'mobile', boolean, expression or function
        startEvent: 'DOMContentLoaded', // name of the event dispatched on the document, that AOS should initialize on
        initClassName: 'aos-init', // class applied after initialization
        animatedClassName: 'aos-animate', // class applied on animation
        useClassNames: false, // if true, will add content of `data-aos` as classes on scroll
        disableMutationObserver: false, // disables automatic mutations' detections (advanced)
        debounceDelay: 50, // the delay on debounce used while resizing window (advanced)
        throttleDelay: 99, // the delay on throttle used while scrolling the page (advanced)
        offset: 120, // offset (in px) from the original trigger point
        delay: 160, // values from 0 to 3000, with step 50ms
        duration: 400, // values from 0 to 3000, with step 50ms
        easing: 'ease', // default easing for AOS animations
        once: false, // whether animation should happen only once - while scrolling down
        mirror: false, // whether elements should animate out while scrolling past them
        anchorPlacement: 'top-bottom', // defines which position of the element regarding to window should trigger the animation
    });
});


$(document).ready(function () {
    $('.thumbnail-image').on('click', function () {
        var fullImageUrl = $(this).data('full');
        $('#mainProductImage').attr('src', fullImageUrl);
        // Add active border
        $('.thumbnail-image').removeClass('active-thumb');
        $(this).addClass('active-thumb');
    });
});


$(document).ready(function () {
    var $mainImage = $('#mainProductImage');
    var $container = $('.main-image');

    $container.on('mousemove', function (e) {
        var rect = this.getBoundingClientRect();
        var x = ((e.clientX - rect.left) / rect.width) * 100;
        var y = ((e.clientY - rect.top) / rect.height) * 100;
        $mainImage.css('transform-origin', x + '% ' + y + '%');
    });

    $container.on('mouseleave', function () {
        $mainImage.css({
            'transform-origin': 'center center',
            'transform': 'scale(1)'
        });
    });

    $container.on('mouseenter', function () {
        $mainImage.css('transform', 'scale(2)');
    });
});

