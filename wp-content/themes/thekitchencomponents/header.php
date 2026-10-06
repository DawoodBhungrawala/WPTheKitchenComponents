<!DOCTYPE html>
<html <?php language_attributes(); ?> lang="en">

<head>
    <meta charset="<?php bloginfo('charset'); ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php wp_title(); ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11"/>
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>"/>

    <script src="https://kit.fontawesome.com/3f282bb758.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://use.typekit.net/ykn7luy.css">

    <?php include 'favicon.php'; ?>
    <script>
    (function () {
        try {
            var forceSplash = new URLSearchParams(window.location.search).get('tkc_splash') === '1';
            var splashSeen = sessionStorage.getItem('tkcWelcomeSplashSeen') === '1';
            document.documentElement.classList.add((forceSplash || !splashSeen) ? 'tkc-splash-ready' : 'tkc-splash-skip');
        } catch (e) {
            document.documentElement.classList.add('tkc-splash-ready');
        }
    }());
    </script>

    <style id="tkc-welcome-splash-critical">
        .tkc-welcome-splash{position:fixed;inset:0;z-index:2147483000;display:none;align-items:center;justify-content:center;padding:24px;background:#fff;opacity:1;visibility:visible;transition:opacity .45s ease,visibility .45s ease;cursor:pointer}
        .tkc-splash-ready body{overflow:hidden}
        .tkc-splash-ready .tkc-welcome-splash{display:flex}
        .tkc-splash-skip .tkc-welcome-splash,.tkc-welcome-splash[hidden]{display:none!important}
        .tkc-welcome-splash.is-leaving{opacity:0;visibility:hidden}
        .tkc-welcome-splash__inner{width:min(520px,86vw);display:flex;flex-direction:column;align-items:center;text-align:center;transform:translateY(8px) scale(.985);opacity:0;animation:tkcSplashEnter .7s cubic-bezier(.22,.61,.36,1) .08s forwards}
        .tkc-welcome-splash__logo{width:min(280px,66vw);height:auto;display:block;margin:0 auto 22px}
        .tkc-welcome-splash__tagline{color:#171717;font-size:13px;line-height:1.3;font-weight:700;letter-spacing:.19em;text-transform:uppercase}
        .tkc-welcome-splash__line{position:relative;width:min(230px,54vw);height:3px;margin-top:18px;overflow:hidden;border-radius:999px;background:#f0f0f0}
        .tkc-welcome-splash__line::after{content:'';position:absolute;inset:0;border-radius:inherit;background:#ff1018;transform:translateX(-100%);animation:tkcSplashLine 1.45s cubic-bezier(.22,.61,.36,1) .22s forwards}
        @keyframes tkcSplashEnter{to{opacity:1;transform:translateY(0) scale(1)}}
        @keyframes tkcSplashLine{to{transform:translateX(0)}}
        @media(max-width:767.98px){.tkc-welcome-splash__logo{width:min(230px,64vw);margin-bottom:18px}.tkc-welcome-splash__tagline{font-size:11px;letter-spacing:.16em}.tkc-welcome-splash__line{width:min(190px,52vw);margin-top:15px}}
        @media(prefers-reduced-motion:reduce){.tkc-welcome-splash,.tkc-welcome-splash__inner,.tkc-welcome-splash__line::after{animation:none!important;transition-duration:.08s!important}.tkc-welcome-splash__inner{opacity:1;transform:none}.tkc-welcome-splash__line::after{transform:none}}
    </style>

    <?php wp_head(); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css"
    />

    <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="tkc-welcome-splash" id="tkcWelcomeSplash" aria-hidden="true">
    <div class="tkc-welcome-splash__inner">
        <img class="tkc-welcome-splash__logo"
             src="<?php echo esc_url(get_template_directory_uri() . '/images/the-kitchen-components-logo.png'); ?>"
             alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
        <span class="tkc-welcome-splash__tagline">Premium Kitchen Components</span>
        <span class="tkc-welcome-splash__line" aria-hidden="true"></span>
    </div>
</div>


<header class="border-5 border-top border-primary site-header">
    <div class="container-fluid site-header-main">
        <div class="site-header-brand">
            <a class="navbar-brand me-0" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(get_bloginfo('name')); ?>">
                <img class="img-fluid"
                     src="<?php echo get_template_directory_uri() ?>/images/the-kitchen-components-logo.png"
                     alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
            </a>
        </div>

        <div class="site-header-search">
            <?php echo do_shortcode('[fibosearch]'); ?>
        </div>

        <button class="mobile-search-fab" type="button" aria-label="Open product search" aria-controls="mobileSearchOverlay" aria-expanded="false">
            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
        </button>

        <div class="mobile-search-overlay" id="mobileSearchOverlay" aria-hidden="true">
            <div class="mobile-search-overlay__backdrop" data-search-close></div>
            <div class="mobile-search-overlay__dialog" role="dialog" aria-modal="true" aria-labelledby="mobileSearchTitle">
                <button class="mobile-search-overlay__close" type="button" aria-label="Close product search" data-search-close>
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
                <span class="mobile-search-overlay__eyebrow">Search products</span>
                <h2 class="mobile-search-overlay__title" id="mobileSearchTitle">What are you looking for?</h2>
                <div class="mobile-search-overlay__search">
                    <?php echo do_shortcode('[fibosearch]'); ?>
                </div>
            </div>
        </div>

        <div class="site-header-actions">
            <a class="site-header-action site-header-action--contact"
               href="<?php echo esc_url(home_url('/contact-us/')); ?>"
               aria-label="Contact us">
                <i class="fa-solid fa-envelope"></i>
                <span class="site-header-action__label">Contact Us</span>
            </a>

            <a class="site-header-action site-header-action--cart xoo-wsc-cart-trigger"
               href="<?php echo esc_url(function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/')); ?>"
               aria-label="Shopping basket">
                <i class="fa-solid fa-basket-shopping"></i>
                <?php if (function_exists('WC') && WC()->cart) : ?>
                    <span class="site-header-action__count" aria-live="polite"><?php echo esc_html(WC()->cart->get_cart_contents_count()); ?></span>
                <?php endif; ?>
            </a>

            <button class="border-0 d-block d-xl-none navbar-toggler shadow-none site-header-action site-header-action--menu"
                    type="button"
                    data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasNavbar"
                    aria-controls="offcanvasNavbar"
                    aria-expanded="false"
                    aria-label="Open menu">
                <span class="site-header-action__menu-bars" aria-hidden="true">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </button>
        </div>
    </div>
    <nav class="navbar navbar-expand-xl navbar-dark bg-primary py-xl-0 py-0">
        <div class="container-fluid">
            <button class="border-0 d-none navbar-toggler shadow-none w-100 site-mobile-menu-toggle" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-expanded="false"
                    aria-label="Toggle navigation">
                <span class="site-mobile-menu-toggle__icon">
                    <span class="site-mobile-menu-toggle__bars" aria-hidden="true">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </span>
                <span class="site-mobile-menu-toggle__content">
                    <span class="site-mobile-menu-toggle__eyebrow">Navigation</span>
                    <span class="site-mobile-menu-toggle__title">Menu</span>
                </span>
                <span class="site-mobile-menu-toggle__arrow" aria-hidden="true">
                    <i class="fa-solid fa-chevron-right"></i>
                </span>
            </button>

<div class="offcanvas w-100 border-0 bg-primary text-white offcanvas-start" tabindex="-1" id="offcanvasNavbar"
     aria-labelledby="offcanvasNavbarLabel" data-bs-backdrop="true" data-bs-scroll="false">
                <div class="offcanvas-header">
                    <h3 class="offcanvas-title text-white" id="offcanvasNavbarLabel">Menu</h3>
                    <button type="button" class="btn-close bg-white opacity-100"
                            data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                </div>
                <div class="offcanvas-body align-items-center p-0">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'header',
                        'container' => false,
                        'menu_class' => '',
                        'fallback_cb' => '__return_false',
                        'items_wrap' => '<ul id="%1$s" class="navbar-nav mx-auto text-left mb-2 mb-md-0 %2$s flex-wrap">%3$s</ul>',
                        'depth' => 2,
                        'walker' => new bootstrap_5_wp_nav_menu_walker()
                    ));
                    ?>

                </div>
            </div>
        </div>
    </nav>

</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const trigger = document.querySelector('.mobile-search-fab');
    const overlay = document.getElementById('mobileSearchOverlay');
    if (!trigger || !overlay) return;

    const closeButtons = overlay.querySelectorAll('[data-search-close]');
    const searchInput = overlay.querySelector('input[type=\"search\"], input[type=\"text\"]');

    const openSearch = function () {
        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');
        trigger.setAttribute('aria-expanded', 'true');
        document.body.classList.add('mobile-search-open');
        window.setTimeout(function () {
            if (searchInput) searchInput.focus();
        }, 120);
    };

    const closeSearch = function () {
        overlay.classList.remove('is-open');
        overlay.setAttribute('aria-hidden', 'true');
        trigger.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('mobile-search-open');
        trigger.focus();
    };

    trigger.addEventListener('click', openSearch);
    closeButtons.forEach(function (button) {
        button.addEventListener('click', closeSearch);
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && overlay.classList.contains('is-open')) {
            closeSearch();
        }
    });
});
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dropdownToggles = document.querySelectorAll('#offcanvasNavbar .dropdown-toggle, .navbar .navbar-nav .dropdown-toggle');

        if (dropdownToggles.length) {
            const closeDropdown = function (toggle) {
                const parentItem = toggle.closest('.dropdown');
                const dropdownMenu = parentItem ? parentItem.querySelector('.dropdown-menu') : null;

                toggle.classList.remove('show');
                toggle.setAttribute('aria-expanded', 'false');

                if (dropdownMenu) {
                    dropdownMenu.classList.remove('show');
                }
            };

            const closeSiblingDropdowns = function (toggle) {
                const parentList = toggle.closest('ul');

                if (!parentList) {
                    return;
                }

                parentList.querySelectorAll(':scope > .dropdown > .dropdown-toggle').forEach(function (siblingToggle) {
                    if (siblingToggle !== toggle) {
                        closeDropdown(siblingToggle);
                    }
                });
            };

            dropdownToggles.forEach(function (toggle) {
                toggle.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    const dropdownMenu = toggle.nextElementSibling;
                    const isOpen = toggle.classList.contains('show');

                    closeSiblingDropdowns(toggle);

                    if (isOpen) {
                        closeDropdown(toggle);
                        return;
                    }

                    toggle.classList.add('show');
                    toggle.setAttribute('aria-expanded', 'true');

                    if (dropdownMenu) {
                        dropdownMenu.classList.add('show');
                    }
                });
            });

            document.addEventListener('click', function (event) {
                dropdownToggles.forEach(function (toggle) {
                    const parentItem = toggle.closest('.dropdown');

                    if (parentItem && !parentItem.contains(event.target)) {
                        closeDropdown(toggle);
                    }
                });
            });
        }
    });
</script>

<style>
    .offcanvas-backdrop {
    display: block !important;
}
.nav-link{
    font-weight:600;
}
</style>
