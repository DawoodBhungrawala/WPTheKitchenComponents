<?php
/*
Template Name: Showrooms Page Template
*/

get_header();

$showroom_main_image_url = get_template_directory_uri() . '/images/stock/showroom-main.jpg';
$showroom_details_image_url = get_template_directory_uri() . '/images/stock/showroom-details.jpg';
?>

<section class="showroom-page-hero">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="showroom-page-hero__panel">
                    <div class="row align-items-center g-4">
                        <div class="col-12 col-lg-8">
                            <span class="showroom-page-hero__eyebrow">Bradford Showroom</span>
                            <h1 class="showroom-page-hero__title">Our kitchen and bedroom showroom in Bradford</h1>
                            <p class="showroom-page-hero__copy">
                                Explore contemporary kitchens, fitted bedrooms, appliances, flooring, and finishing touches in one place,
                                with a team that can help you plan your project from first ideas through to fitting.
                            </p>
                            <div class="showroom-page-hero__actions">
                                <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-primary px-4 py-3">Plan Your Showroom Visit</a>
                                <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-outline-dark px-4 py-3">Speak to Our Team</a>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="showroom-page-hero__stat">
                                <strong>One Roof</strong>
                                <span>Kitchens, bedrooms, appliances, flooring, and more all under one showroom experience.</span>
                            </div>
                            <div class="showroom-page-hero__stat">
                                <strong>Start to Finish</strong>
                                <span>Design guidance, supply, and installation support from an experienced local team.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="showroom-page-overview">
    <div class="container">
        <div class="row g-4 g-xl-5 align-items-stretch">
            <div class="col-12 col-xl-6">
                <div class="showroom-page-overview__content h-100">
                    <span class="showroom-page-overview__kicker">Design Service</span>
                    <h2>Kitchen and bedroom planning with expert support</h2>
                    <p>
                        Our Bradford showroom team can help you shape the perfect kitchen or bedroom for your home with a joined-up design service,
                        free consultation, and practical product guidance.
                    </p>
                    <p>
                        Once your design is approved, our experienced fitting team can install your new kitchen or bedroom and manage the project
                        through to completion.
                    </p>
                    <p class="mb-0">
                        If you only need product supply, we can help with that too, using our experience across kitchens and bedrooms to make the whole process easier.
                    </p>
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="showroom-page-overview__image-wrap h-100">
                    <img src="<?php echo esc_url($showroom_main_image_url); ?>" alt="Modern showroom-style interior for kitchen and home inspiration" class="img-fluid showroom-page-overview__image">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="showroom-page-details">
    <div class="container">
        <div class="row g-4 g-xl-5 align-items-stretch">
            <div class="col-12 col-xl-6">
                <div class="showroom-page-details__image-wrap h-100">
                    <img class="img-fluid w-100 showroom-page-details__image" src="<?php echo esc_url($showroom_details_image_url); ?>" alt="Stylish kitchen showroom inspiration with modern finishes">
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="showroom-page-details__content h-100">
                    <div class="showroom-info-card">
                        <span class="showroom-info-card__kicker">Opening Times</span>
                        <h2>Visit the showroom</h2>
                        <ul class="showroom-info-list">
                            <li><strong>Monday - Friday</strong><span>9am - 5pm</span></li>
                            <li><strong>Saturday</strong><span>11am - 4pm</span></li>
                            <li><strong>Sunday</strong><span>Closed</span></li>
                            <li><strong>Online Store</strong><span>Open 24/7</span></li>
                        </ul>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="showroom-contact-card h-100">
                                <span class="showroom-contact-card__label">The Kitchen Components</span>
                                <h3>Online</h3>
                                <p>Unit 2, 296 Thornton Road<br>Bradford, BD8 8JZ</p>
                                <p><a href="mailto:sales@thekitchencomponents.com">sales@thekitchencomponents.com</a></p>
                                <p><a href="tel:08001455253">0800 145 5253</a></p>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="showroom-contact-card h-100">
                                <span class="showroom-contact-card__label">Showroom</span>
                                <h3>The Kitchen Gallery</h3>
                                <p>296 Thornton Road<br>Bradford, BD8 8JZ</p>
                                <p><a href="mailto:thekitchengallery296@gmail.com">thekitchengallery296@gmail.com</a></p>
                                <p><a href="tel:01274955354">01274 955354</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="showroom-page-story">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-9">
                <div class="showroom-page-story__panel text-center">
                    <span class="showroom-page-story__eyebrow">Why Visit</span>
                    <h2>See ideas in person before you commit</h2>
                    <p>
                        Visiting our Bradford showroom gives you a better feel for layouts, finishes, materials, and product quality than browsing online alone.
                    </p>
                    <p>
                        Whether you are planning a full renovation or just comparing options, we can help you explore practical solutions for your home and budget.
                    </p>
                    <div class="showroom-page-story__actions">
                        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-primary px-4 py-3">Book Your Visit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
