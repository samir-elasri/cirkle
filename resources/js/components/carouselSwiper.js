import $ from 'jquery';

export default ($component) => {

    const autoPlaySpeed = Math.round($component.data('slideshow-auto-play-speed') * 1000);
    const $slides = $('.slides', $component);
    let timeoutId = 0;


    function init(ev, slick) {
        if (slick.$dots) {
            const $items = slick.$dots.find('li');

            if ($items.length > 0) {
                $items.addClass('on-desktop');
            }
        }

        if ($('.slide', $component).length > 1) {
            setTimeout(() => beforeChange(ev, slick, -1, 0), 100);
        }
    }

    function beforeChange(ev, slick, curr, next) {

        // Aucun changement de diapositive
        if (curr == next) return;

        let id;

        const $curr = slick.$prev = $(slick.$slides[curr]);
        const $next = $(slick.$slides[next]);

        clearTimeout(timeoutId);

        timeoutId = setTimeout(() => $slides.slick('next'), autoPlaySpeed);
    }

    function afterChange(ev, slick) {

        // Réinitialise le vidéo précédent
        const $video = slick.$prev ? slick.$prev.children('video').get(0) : null;
        if ($video) {
            $video.currentTime = 0;
        }

    }


    let slidesNumber = Math.floor(($component.width() / $('.mini-slide img', $component).width()));

    const slickSettings = {
        slidesToShow: 2,
        slidesToScroll:2,
        centerPadding: '0',
        centerMode: false,
        arrows: true,
        autoplay: false,
        autoplaySpeed: 0,
        dots: false,
        adaptiveHeight: false,
        prevArrow: '.slick-prev',
        nextArrow: '.slick-next',
        useCSS: false,
        rows: 0,
        infinite: true,
        responsive: [
            {
                breakpoint: 9999,
                settings: {
                    slidesToShow: slidesNumber,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 1300,
                settings: {
                    slidesToShow: slidesNumber,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: slidesNumber,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 900,
                settings: {
                    slidesToShow: slidesNumber,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 500,
                settings: {
                    slidesToShow: slidesNumber,
                    slidesToScroll: 2
                }
            },
            {
                breakpoint: 300,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }
        ]
    };

    $slides
        .on('init', init)
        .on('beforeChange', beforeChange)
        .on('afterChange', afterChange)
        .slick(slickSettings)
        .show();

    const $navigation = $('ul.slick-dots', $component);
    $navigation.append('<li class="on-desktop"><button aria-label="play - pause button" class="play playing" type="button"></button></li>');
};
