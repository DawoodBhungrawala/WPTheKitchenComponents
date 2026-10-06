<?php
$footer_now = current_datetime();
$footer_day = (int) $footer_now->format('N');
$footer_minutes = ((int) $footer_now->format('G') * 60) + (int) $footer_now->format('i');
$footer_schedule = [
    1 => ['open' => 540, 'close' => 1020, 'open_label' => '9am', 'close_label' => '5pm'],
    2 => ['open' => 540, 'close' => 1020, 'open_label' => '9am', 'close_label' => '5pm'],
    3 => ['open' => 540, 'close' => 1020, 'open_label' => '9am', 'close_label' => '5pm'],
    4 => ['open' => 540, 'close' => 1020, 'open_label' => '9am', 'close_label' => '5pm'],
    5 => ['open' => 540, 'close' => 1020, 'open_label' => '9am', 'close_label' => '5pm'],
    6 => ['open' => 660, 'close' => 960, 'open_label' => '11am', 'close_label' => '4pm'],
];
$footer_day_names = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];
$footer_is_open = false;
$footer_status = 'Closed';

if (isset($footer_schedule[$footer_day])) {
    $today_hours = $footer_schedule[$footer_day];
    if ($footer_minutes >= $today_hours['open'] && $footer_minutes < $today_hours['close']) {
        $footer_is_open = true;
        $footer_status = 'Open now · Until ' . $today_hours['close_label'];
    }
}

if (!$footer_is_open) {
    for ($offset = 0; $offset <= 7; $offset++) {
        $candidate_day = (($footer_day - 1 + $offset) % 7) + 1;
        if (!isset($footer_schedule[$candidate_day])) {
            continue;
        }
        $candidate = $footer_schedule[$candidate_day];
        if ($offset === 0 && $footer_minutes < $candidate['open']) {
            $footer_status = 'Closed · Opens today ' . $candidate['open_label'];
            break;
        }
        if ($offset > 0) {
            $when = $offset === 1 ? 'tomorrow' : $footer_day_names[$candidate_day];
            $footer_status = 'Closed · Opens ' . $when . ' ' . $candidate['open_label'];
            break;
        }
    }
}

$footer_whatsapp = function_exists('get_field') ? get_field('whatsapp', 'option') : '';
$footer_socials = [
    ['field' => 'facebook', 'icon' => 'fab fa-facebook-f', 'label' => 'Facebook'],
    ['field' => 'twitter', 'icon' => 'fab fa-twitter', 'label' => 'Twitter'],
    ['field' => 'instagram', 'icon' => 'fab fa-instagram', 'label' => 'Instagram'],
    ['field' => 'tiktok', 'icon' => 'fab fa-tiktok', 'label' => 'TikTok'],
    ['field' => 'whatsapp', 'icon' => 'fab fa-whatsapp', 'label' => 'WhatsApp'],
];
?>
<footer class="site-footer footer-premium" id="siteFooter">
    <section class="footer-cta" style="--footer-cta-image: url('<?php echo esc_url(get_template_directory_uri() . '/images/homepage-hero.jpg'); ?>');">
        <div class="container">
            <div class="footer-cta__inner">
                <div class="footer-cta__copy">
                    <span class="footer-eyebrow">Let’s build something better</span>
                    <h2>Planning your next kitchen upgrade?</h2>
                    <p>Visit our Bradford showroom or speak with the team for straightforward product advice.</p>
                </div>
                <div class="footer-cta__actions">
                    <a class="footer-btn footer-btn--primary" href="<?php echo esc_url(home_url('/showrooms/')); ?>">
                        <i class="fa-solid fa-store" aria-hidden="true"></i>
                        <span>Visit Showroom</span>
                    </a>
                    <a class="footer-btn footer-btn--ghost" href="<?php echo esc_url(home_url('/contact-us/')); ?>">
                        <i class="fa-regular fa-envelope" aria-hidden="true"></i>
                        <span>Contact Us</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="footer-trust" aria-label="Service highlights">
        <div class="container">
            <div class="footer-trust__grid">
                <div class="footer-trust__item"><i class="fa-solid fa-award" aria-hidden="true"></i><span>Trusted Brands</span></div>
                <div class="footer-trust__item"><i class="fa-solid fa-truck-fast" aria-hidden="true"></i><span>Nationwide Delivery</span></div>
                <div class="footer-trust__item"><i class="fa-solid fa-headset" aria-hidden="true"></i><span>Expert Support</span></div>
                <div class="footer-trust__item"><i class="fa-solid fa-location-dot" aria-hidden="true"></i><span>Bradford Showroom</span></div>
            </div>
        </div>
    </section>

    <div class="footer-main">
        <div class="container">
            <div class="footer-main__grid">
                <section class="footer-brand-panel" aria-label="The Kitchen Components">
                    <div class="footer-brand-panel__logo-wrap">
                        <img class="footer-brand-panel__logo" src="<?php echo esc_url(get_template_directory_uri() . '/images/footer-logo.png'); ?>" alt="The Kitchen Components">
                    </div>
                    <p>Premium kitchens, appliances and expert guidance for every project.</p>
                    <div class="footer-brand-panel__mini-actions">
                        <a href="<?php echo esc_url(home_url('/shop/')); ?>">Shop products <span aria-hidden="true">→</span></a>
                        <a href="<?php echo esc_url(home_url('/showrooms/')); ?>">Our showroom <span aria-hidden="true">→</span></a>
                    </div>
                </section>

                <section class="footer-accordion footer-contact-panel">
                    <button class="footer-accordion__toggle" type="button" aria-expanded="true" aria-controls="footerContactPanel">
                        <span>Contact</span><i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                    </button>
                    <div class="footer-accordion__content" id="footerContactPanel">
                        <div class="footer-status <?php echo $footer_is_open ? 'is-open' : 'is-closed'; ?>">
                            <span class="footer-status__dot" aria-hidden="true"></span>
                            <span><?php echo esc_html($footer_status); ?></span>
                        </div>

                        <div class="footer-contact-group footer-contact-group--showroom">
                            <div class="footer-contact-group__label">
                                <i class="fa-solid fa-store" aria-hidden="true"></i>
                                <span>Showroom</span>
                            </div>
                            <address class="footer-address">
                                <strong>The Kitchen Gallery</strong><br>
                                296 Thornton Road<br>
                                Bradford<br>
                                BD8 8JZ
                            </address>
                            <div class="footer-contact-links">
                                <a href="mailto:thekitchengallery296@gmail.com"><i class="fa-regular fa-envelope" aria-hidden="true"></i><span>thekitchengallery296@gmail.com</span></a>
                                <a href="tel:01274955354"><i class="fa-solid fa-phone" aria-hidden="true"></i><span>01274 955354</span></a>
                                <a target="_blank" rel="noopener noreferrer" href="https://www.google.com/maps/search/?api=1&amp;query=The+Kitchen+Gallery+296+Thornton+Road+Bradford+BD8+8JZ"><i class="fa-solid fa-location-arrow" aria-hidden="true"></i><span>Get directions</span></a>
                            </div>
                        </div>

                        <div class="footer-contact-group footer-contact-group--online">
                            <div class="footer-contact-group__label">
                                <i class="fa-solid fa-globe" aria-hidden="true"></i>
                                <span>Online</span>
                            </div>
                            <address class="footer-address">
                                <strong>The Kitchen Components</strong><br>
                                Unit 2, 296 Thornton Road<br>
                                Bradford<br>
                                BD8 8JZ
                            </address>
                            <div class="footer-contact-links">
                                <a href="mailto:sales@thekitchencomponents.com"><i class="fa-regular fa-envelope" aria-hidden="true"></i><span>sales@thekitchencomponents.com</span></a>
                                <a href="tel:08001455253"><i class="fa-solid fa-phone" aria-hidden="true"></i><span>0800 145 5253</span></a>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="footer-accordion footer-menu-panel menu-products">
                    <button class="footer-accordion__toggle" type="button" aria-expanded="true" aria-controls="footerProductsPanel">
                        <span>Products</span><i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                    </button>
                    <div class="footer-accordion__content" id="footerProductsPanel">
                        <?php
                        wp_nav_menu([
                            'theme_location' => 'products',
                            'container' => false,
                            'menu_class' => 'footer-link-list',
                            'fallback_cb' => false,
                        ]);
                        ?>
                    </div>
                </section>

                <section class="footer-accordion footer-menu-panel footer-quick-link">
                    <button class="footer-accordion__toggle" type="button" aria-expanded="true" aria-controls="footerQuickPanel">
                        <span>Quick Links</span><i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                    </button>
                    <div class="footer-accordion__content" id="footerQuickPanel">
                        <?php
                        wp_nav_menu([
                            'theme_location' => 'footer-quick-links',
                            'container' => false,
                            'menu_class' => 'footer-link-list',
                            'fallback_cb' => false,
                        ]);
                        ?>
                    </div>
                </section>
            </div>

            <div class="footer-info-grid">
                <section class="footer-accordion footer-hours-panel">
                    <button class="footer-accordion__toggle" type="button" aria-expanded="true" aria-controls="footerHoursPanel">
                        <span>Opening Hours</span><i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                    </button>
                    <div class="footer-accordion__content" id="footerHoursPanel">
                        <div class="footer-hours-list">
                            <div><span>Monday – Friday</span><strong>9am – 5pm</strong></div>
                            <div><span>Saturday</span><strong>11am – 4pm</strong></div>
                            <div><span>Sunday</span><strong>Closed</strong></div>
                        </div>
                    </div>
                </section>

                <section class="footer-social-panel">
                    <div class="footer-social-panel__copy">
                        <span class="footer-section-label">Stay connected</span>
                        <p>Follow The Kitchen Components for products, inspiration and showroom updates.</p>
                    </div>
                    <ul class="footer-social-links" aria-label="Social media">
                        <?php foreach ($footer_socials as $social) : ?>
                            <?php $social_url = function_exists('get_field') ? get_field($social['field'], 'option') : ''; ?>
                            <?php if ($social_url) : ?>
                                <li><a href="<?php echo esc_url($social_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($social['label']); ?>"><i class="<?php echo esc_attr($social['icon']); ?>" aria-hidden="true"></i></a></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </section>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <div class="footer-bottom__inner">
                <p>Copyright &copy; <?php echo esc_html(wp_date('Y')); ?> The Kitchen Components. All rights reserved.</p>
                <nav class="footer-legal" aria-label="Legal links">
                    <?php if ($privacy_url = get_privacy_policy_url()) : ?>
                        <a href="<?php echo esc_url($privacy_url); ?>">Privacy Policy</a>
                    <?php endif; ?>
                    <a href="<?php echo esc_url(home_url('/refund-and-returns-policy/')); ?>">Refund &amp; Returns</a>
                </nav>
                <button class="footer-back-top" id="tkcFooterBackTop" type="button" aria-label="Back to top">
                    <span>Back to top</span><i class="fa-solid fa-arrow-up" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>
</footer>

<div class="site-quick-links" aria-label="Quick links">
    <a class="site-quick-link site-quick-link--cart xoo-wsc-cart-trigger text-decoration-none"
       href="<?php echo esc_url(function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/')); ?>"
       aria-label="Open shopping basket">
        <span class="site-quick-link__icon" aria-hidden="true">
            <i class="fa-solid fa-basket-shopping"></i>
            <?php if (function_exists('WC') && WC()->cart) : ?>
                <span class="site-quick-link__count" aria-live="polite"><?php echo esc_html(WC()->cart->get_cart_contents_count()); ?></span>
            <?php endif; ?>
        </span>
        <span class="site-quick-link__content">
            <span class="site-quick-link__title">Shopping Basket</span>
            <span class="site-quick-link__subtitle">Open your side cart</span>
        </span>
    </a>

    <a target="_blank"
       rel="noopener noreferrer"
       href="https://www.google.com/local/place/fid/0x487be6b03e506b35:0x23a24f3f60b4b0e6/photosphere?iu=https://lh3.googleusercontent.com/gps-cs-s/AG0ilSyUDRzKi1DuVo8_QusYYaL6q2WnTIEWeflxUVSbaXISkgRceESuyLLEW_r4xrD4l-2NniVqbLB9P4RM_zL6jbLU-Qe_cx7oYk2F63BqQtKoK3bQ2Rn_GLIBallsTDuuifvUln2w%3Dw160-h106-k-no-pi-0-ya199.55576-ro-0-fo100&ik=CAoSF0NJSE0wb2dLRUlDQWdJRGx3SjNJMXdF"
       class="site-quick-link site-quick-link--showroom text-decoration-none">
        <span class="site-quick-link__icon" aria-hidden="true">
            <i class="fa-solid fa-cube"></i>
        </span>
        <span class="site-quick-link__content">
            <span class="site-quick-link__title">360 Showroom</span>
            <span class="site-quick-link__subtitle">Take the virtual tour</span>
        </span>
    </a>

    <a class="site-quick-link site-quick-link--whatsapp text-decoration-none"
       href="<?php echo esc_url(get_field('whatsapp', 'option') ?: 'https://wa.me/448001455253?text=Hello%20The%20Kitchen%20Components,%20I%20am%20interested%20in%20some%20appliances.%20Could%20you%20please%20share%20pricing,%20availability,%20and%20delivery%20details?%20Thank%20you.'); ?>"
       target="_blank"
       rel="noopener noreferrer">
        <span class="site-quick-link__icon" aria-hidden="true">
            <i class="fab fa-whatsapp"></i>
        </span>
        <span class="site-quick-link__content">
            <span class="site-quick-link__title">WhatsApp</span>
            <span class="site-quick-link__subtitle">Chat with our team</span>
        </span>
    </a>
</div>


<script id="tkc-welcome-splash-script">
(function () {
    function initSplash() {
        var splash = document.getElementById('tkcWelcomeSplash');
        if (!splash) return;

        var root = document.documentElement;
        var forceSplash = false;
        var splashSeen = false;

        try {
            forceSplash = new URLSearchParams(window.location.search).get('tkc_splash') === '1';
            splashSeen = sessionStorage.getItem('tkcWelcomeSplashSeen') === '1';
        } catch (e) {}

        // Do not show again in the same browsing session unless explicitly forced.
        if (!forceSplash && splashSeen) {
            splash.hidden = true;
            splash.setAttribute('aria-hidden', 'true');
            root.classList.remove('tkc-splash-ready');
            root.classList.add('tkc-splash-finished');
            document.body.style.overflow = '';
            return;
        }

        root.classList.remove('tkc-splash-skip', 'tkc-splash-finished');
        root.classList.add('tkc-splash-ready');
        splash.hidden = false;
        splash.setAttribute('aria-hidden', 'false');

        var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        var visibleFor = reducedMotion ? 500 : 1800;
        var fadeFor = reducedMotion ? 60 : 420;
        var finished = false;
        var mainTimer;
        var hardTimer;

        function finishSplash() {
            if (finished) return;
            finished = true;

            if (mainTimer) window.clearTimeout(mainTimer);
            if (hardTimer) window.clearTimeout(hardTimer);

            splash.classList.add('is-leaving');
            splash.style.pointerEvents = 'none';

            try {
                sessionStorage.setItem('tkcWelcomeSplashSeen', '1');
            } catch (e) {}

            window.setTimeout(function () {
                splash.hidden = true;
                splash.style.display = 'none';
                splash.setAttribute('aria-hidden', 'true');
                root.classList.remove('tkc-splash-ready');
                root.classList.add('tkc-splash-finished');
                document.body.style.overflow = '';
            }, fadeFor);
        }

        mainTimer = window.setTimeout(finishSplash, visibleFor);
        hardTimer = window.setTimeout(finishSplash, 3200);

        splash.addEventListener('click', finishSplash, { once: true });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') finishSplash();
        }, { once: true });

        // If the page becomes fully loaded but timers were throttled, retry once.
        window.addEventListener('load', function () {
            window.setTimeout(finishSplash, visibleFor);
        }, { once: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSplash, { once: true });
    } else {
        initSplash();
    }
}());
</script>


<script id="tkc-premium-footer-script">
(function () {
    function initPremiumFooter() {
        var footer = document.querySelector('.footer-premium');
        if (!footer) return;

        var mobileQuery = window.matchMedia('(max-width: 767.98px)');
        var toggles = Array.prototype.slice.call(footer.querySelectorAll('.footer-accordion__toggle'));

        function syncAccordions() {
            var mobile = mobileQuery.matches;
            footer.classList.toggle('is-accordion-ready', mobile);
            toggles.forEach(function (toggle) {
                var panel = document.getElementById(toggle.getAttribute('aria-controls'));
                if (!panel) return;
                if (mobile) {
                    if (!toggle.dataset.mobileInit) {
                        toggle.setAttribute('aria-expanded', 'false');
                        panel.hidden = true;
                        toggle.dataset.mobileInit = '1';
                    }
                    toggle.disabled = false;
                } else {
                    toggle.disabled = true;
                    toggle.setAttribute('aria-expanded', 'true');
                    panel.hidden = false;
                    delete toggle.dataset.mobileInit;
                }
            });
        }

        toggles.forEach(function (toggle) {
            toggle.addEventListener('click', function () {
                if (!mobileQuery.matches) return;
                var panel = document.getElementById(toggle.getAttribute('aria-controls'));
                if (!panel) return;
                var open = toggle.getAttribute('aria-expanded') === 'true';
                toggle.setAttribute('aria-expanded', open ? 'false' : 'true');
                panel.hidden = open;
            });
        });

        if (mobileQuery.addEventListener) mobileQuery.addEventListener('change', syncAccordions);
        else if (mobileQuery.addListener) mobileQuery.addListener(syncAccordions);
        syncAccordions();

        var backTop = document.getElementById('tkcFooterBackTop');
        if (backTop) {
            backTop.addEventListener('click', function () {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        // Keep the floating quick actions/search above the footer rather than covering footer content.
        var quickLinks = document.querySelector('.site-quick-links');
        var mobileSearch = document.querySelector('.mobile-search-fab');
        var footerLifted = false;
        function positionFloatingActions() {
            var footerTop = footer.getBoundingClientRect().top;
            var overlap = Math.max(0, window.innerHeight - footerTop);
            var extra = overlap > 0 ? overlap : 0;
            var shouldLift = overlap > 8;

            document.documentElement.style.setProperty('--tkc-footer-float-offset', extra + 'px');

            if (shouldLift !== footerLifted) {
                footerLifted = shouldLift;
                if (quickLinks) quickLinks.classList.toggle('is-footer-lifted', shouldLift);
                if (mobileSearch) mobileSearch.classList.toggle('is-footer-lifted', shouldLift);
            }
        }
        if (quickLinks || mobileSearch) {
            window.addEventListener('scroll', positionFloatingActions, { passive: true });
            window.addEventListener('resize', positionFloatingActions);
            positionFloatingActions();
        }
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initPremiumFooter, { once: true });
    else initPremiumFooter();
}());
</script>

<?php wp_footer(); ?>


</body>

</html>
