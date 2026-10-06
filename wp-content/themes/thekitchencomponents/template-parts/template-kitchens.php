<?php
/*
Template Name: Kitchens Page Template
*/

get_header();
?>

<section class="kitchen-page-hero">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="kitchen-page-hero__panel">
                    <div class="row align-items-center g-4">
                        <div class="col-12 col-lg-8">
                            <span class="kitchen-page-hero__eyebrow">Kitchen Showroom Bradford</span>
                            <h1 class="kitchen-page-hero__title">Design, supply, and installation for your next kitchen project</h1>
                            <p class="kitchen-page-hero__copy">
                                Visit The Kitchen Components showroom in Bradford to explore modern kitchens, integrated appliances,
                                worktops, cabinets, and flooring with a team that can guide the whole journey from concept to completion.
                            </p>
                            <div class="kitchen-page-hero__actions">
                                <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-primary px-4 py-3">Start Your Kitchen Project</a>
                                <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-outline-light px-4 py-3">Book a Showroom Visit</a>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="kitchen-page-hero__stat">
                                <strong>Full Service</strong>
                                <span>Design, product supply, and installation support all under one roof.</span>
                            </div>
                            <div class="kitchen-page-hero__stat">
                                <strong>Showroom Ready</strong>
                                <span>See appliances, finishes, and kitchen inspiration in person in Bradford.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="kitchen-showroom-overview">
    <div class="container">
        <div class="row g-4 g-xl-5 align-items-stretch">
            <div class="col-12 col-xl-6">
                <div class="kitchen-showroom-overview__image-wrap h-100">
                    <img
                        class="img-fluid w-100 kitchen-showroom-overview__image"
                        src="/wp-content/uploads/homepage-hero.jpg"
                        alt="The Kitchen Components Bradford Showroom"
                    >
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="kitchen-showroom-overview__content h-100">
                    <div class="kitchen-info-card">
                        <span class="kitchen-info-card__kicker">Opening Times</span>
                        <h2>Visit our kitchen showroom</h2>
                        <ul class="kitchen-info-list">
                            <li><strong>Monday - Friday</strong><span>9am - 5pm</span></li>
                            <li><strong>Saturday</strong><span>11am - 4pm</span></li>
                            <li><strong>Sunday</strong><span>Closed</span></li>
                            <li><strong>Online Store</strong><span>Open 24/7</span></li>
                        </ul>
                    </div>

                    <div class="row g-3 kitchen-contact-grid">
                        <div class="col-12 col-md-6">
                            <div class="kitchen-contact-card h-100">
                                <span class="kitchen-contact-card__label">The Kitchen Components</span>
                                <h3>Online</h3>
                                <p>Unit 2, 296 Thornton Road<br>Bradford, BD8 8JZ</p>
                                <p><a href="mailto:sales@thekitchencomponents.com">sales@thekitchencomponents.com</a></p>
                                <p><a href="tel:08001455253">0800 145 5253</a></p>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="kitchen-contact-card h-100">
                                <span class="kitchen-contact-card__label">Showroom</span>
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

<section class="kitchen-page-story">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-9">
                <div class="kitchen-page-story__panel text-center">
                    <span class="kitchen-page-story__eyebrow">Fitted Kitchens in Bradford</span>
                    <h2>Beautiful kitchens, practical planning, and expert guidance</h2>
                    <p>
                        If you are searching for beautifully crafted fitted kitchens in Bradford, The Kitchen Components team can help shape
                        the full journey. We use design-led planning to create kitchens that feel refined, functional, and tailored to your space.
                    </p>
                    <p>
                        From sleek contemporary layouts to more timeless kitchen styles, we supply cabinets, worktops, sinks, taps, flooring,
                        and appliances to help complete the whole room with one joined-up service.
                    </p>
                    <p class="mb-0">
                        Visit our showroom for inspiration, compare products in person, and speak to us about a kitchen that works for your
                        home, budget, and lifestyle.
                    </p>

                    <div class="kitchen-page-story__actions">
                        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-primary px-4 py-3">Speak to Our Kitchen Team</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
