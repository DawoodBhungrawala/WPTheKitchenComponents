<?php
/*
Template Name: Bedrooms Page Template
*/

get_header();

$bedroom_image_url = get_template_directory_uri() . '/images/stock/bedroom-main.jpg';
$bedroom_showroom_image_url = get_template_directory_uri() . '/images/stock/bedroom-showroom.jpg';
?>

<section class="bedroom-page-hero">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="bedroom-page-hero__panel">
                    <div class="row align-items-center g-4">
                        <div class="col-12 col-lg-8">
                            <span class="bedroom-page-hero__eyebrow">Bedroom Showroom Bradford</span>
                            <h1 class="bedroom-page-hero__title">Fitted bedrooms, storage solutions, and design support in one place</h1>
                            <p class="bedroom-page-hero__copy">
                                Visit our Bradford bedroom showroom to explore wardrobes, beds, storage, and bedroom furniture with expert help
                                to turn your ideas into a finished space that feels both stylish and practical.
                            </p>
                            <div class="bedroom-page-hero__actions">
                                <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-primary px-4 py-3">Plan Your Bedroom Project</a>
                                <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-outline-dark px-4 py-3">Book a Visit</a>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="bedroom-page-hero__stat">
                                <strong>Bedroom Inspiration</strong>
                                <span>See fitted wardrobes, beds, and storage ideas in a real showroom setting.</span>
                            </div>
                            <div class="bedroom-page-hero__stat">
                                <strong>Complete Service</strong>
                                <span>Design, supply, and fitting support to keep your bedroom project running smoothly.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bedroom-page-overview">
    <div class="container">
        <div class="row g-4 g-xl-5 align-items-stretch">
            <div class="col-12 col-xl-6">
                <div class="bedroom-page-overview__image-wrap h-100">
                    <img class="img-fluid w-100 bedroom-page-overview__image" src="<?php echo esc_url($bedroom_image_url); ?>" alt="Modern fitted bedroom inspiration at The Kitchen Components">
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="bedroom-page-overview__content h-100">
                    <div class="bedroom-info-card">
                        <span class="bedroom-info-card__kicker">Why Visit</span>
                        <h2>Bedroom design made easier</h2>
                        <p>
                            Our bedroom showroom gives you the chance to compare furniture, fitted wardrobes, storage solutions, and layout ideas with a team that understands both design and installation.
                        </p>
                        <p class="mb-0">
                            Whether you prefer a modern minimalist bedroom or something more classic and elegant, we can help you shape a space that works beautifully for everyday life.
                        </p>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="bedroom-feature-card h-100">
                                <span class="bedroom-feature-card__label">Service</span>
                                <h3>Design to Fit</h3>
                                <p>We offer a complete design-supply-fit service to keep your bedroom project simple from start to finish.</p>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="bedroom-feature-card h-100">
                                <span class="bedroom-feature-card__label">Showroom</span>
                                <h3>Ideas in Person</h3>
                                <p>Explore displays for inspiration and compare practical options before making final choices.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bedroom-page-details">
    <div class="container">
        <div class="row g-4 g-xl-5 align-items-stretch">
            <div class="col-12 col-xl-6">
                <div class="bedroom-page-details__content h-100">
                    <div class="bedroom-hours-card">
                        <span class="bedroom-hours-card__kicker">Opening Times</span>
                        <h2>Visit the showroom</h2>
                        <ul class="bedroom-hours-list">
                            <li><strong>Monday - Friday</strong><span>9am - 5pm</span></li>
                            <li><strong>Saturday</strong><span>11am - 4pm</span></li>
                            <li><strong>Sunday</strong><span>Closed</span></li>
                            <li><strong>Online Store</strong><span>Open 24/7</span></li>
                        </ul>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="bedroom-contact-card h-100">
                                <span class="bedroom-contact-card__label">The Kitchen Components</span>
                                <h3>Online</h3>
                                <p>Unit 2, 296 Thornton Road<br>Bradford, BD8 8JZ</p>
                                <p><a href="mailto:sales@thekitchencomponents.com">sales@thekitchencomponents.com</a></p>
                                <p><a href="tel:08001455253">0800 145 5253</a></p>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="bedroom-contact-card h-100">
                                <span class="bedroom-contact-card__label">Showroom</span>
                                <h3>The Kitchen Gallery</h3>
                                <p>296 Thornton Road<br>Bradford, BD8 8JZ</p>
                                <p><a href="mailto:thekitchengallery296@gmail.com">thekitchengallery296@gmail.com</a></p>
                                <p><a href="tel:01274955354">01274 955354</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="bedroom-page-details__image-wrap h-100">
                    <img class="img-fluid w-100 bedroom-page-details__image" src="<?php echo esc_url($bedroom_showroom_image_url); ?>" alt="Bedroom showroom inspiration with fitted storage and modern styling">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bedroom-page-story">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-9">
                <div class="bedroom-page-story__panel text-center">
                    <span class="bedroom-page-story__eyebrow">Bedroom Shop Bradford</span>
                    <h2>Fitted bedrooms with quality, value, and expert guidance</h2>
                    <p>
                        We offer fitted wardrobes, bed frames, storage solutions, and complete bedroom furniture with a strong focus on quality and value.
                    </p>
                    <p>
                        Our team can manage your bedroom project from design to installation, helping you create a finished room that feels calm, practical, and tailored to your home.
                    </p>
                    <div class="bedroom-page-story__actions">
                        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-primary px-4 py-3">Get Bedroom Advice</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
