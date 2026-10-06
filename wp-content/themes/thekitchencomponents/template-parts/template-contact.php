<?php
/*
Template Name: Contact Page Template
*/

get_header();

$contact = get_field('contact');
$hero_background = get_template_directory_uri() . '/images/stock/showroom-main.jpg';
$hero_title = !empty($contact['hero_title']) ? $contact['hero_title'] : get_the_title();
$intro_title = !empty($contact['introduction_title']) ? $contact['introduction_title'] : 'Visit or Contact The Kitchen Components';
$intro_content = !empty($contact['introduction_content']) ? $contact['introduction_content'] : '';
$phone_number = get_field('phone_number', 'option');
$email_address = get_field('email_address', 'option');
$address = get_field('address', 'option');
$whatsapp = get_field('whatsapp', 'option');
?>

<section
        class="contact-page-hero"
    <?php if ($hero_background): ?>
        style="background-image: linear-gradient(135deg, rgba(20, 20, 20, 0.76), rgba(20, 20, 20, 0.48)), url('<?php echo esc_url($hero_background); ?>');"
    <?php endif; ?>
>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="contact-page-hero__panel">
                    <div class="row g-4 align-items-center">
                        <div class="col-12 col-lg-8">
                            <span class="contact-page-hero__eyebrow">Contact Us</span>
                            <h1 class="contact-page-hero__title"><?php echo esc_html($hero_title); ?></h1>
                            <p class="contact-page-hero__copy">
                                Get in touch with our Bradford team for product advice, showroom visits, pricing
                                support,
                                and help planning your next kitchen, bedroom, or appliance project.
                            </p>
                            <div class="contact-page-hero__actions">
                                <?php if ($phone_number): ?>
                                    <a href="tel:<?php echo esc_attr($phone_number); ?>"
                                       class="btn btn-primary px-4 py-3">
                                        Call Us
                                    </a>
                                <?php endif; ?>
                                <?php if ($whatsapp): ?>
                                    <a href="<?php echo esc_url($whatsapp); ?>" class="btn btn-outline-light px-4 py-3"
                                       target="_blank" rel="noopener noreferrer">
                                        WhatsApp Us
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="contact-page-hero__stat">
                                <strong>Friendly Team</strong>
                                <span>Speak to us about products, pricing, lead times, and showroom appointments.</span>
                            </div>
                            <div class="contact-page-hero__stat">
                                <strong>Showroom Visit</strong>
                                <span>Visit our Bradford location to compare finishes, appliances, and inspiration in
                                    person.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="contact-page-details">
    <div class="container">
        <div class="row g-4 g-xl-5 align-items-start">
            <div class="col-12 col-xl-6">
                <div class="contact-page-details__content">
                    <span class="contact-page-details__eyebrow">Get In Touch</span>
                    <h2><?php echo esc_html($intro_title); ?></h2>
                    <?php if ($intro_content): ?>
                        <div class="contact-page-details__copy">
                            <?php echo wp_kses_post(wpautop($intro_content)); ?>
                        </div>
                    <?php endif; ?>

                    <div class="contact-page-details__cards">
                        <?php if ($phone_number): ?>
                            <a href="tel:<?php echo esc_attr($phone_number); ?>"
                               class="contact-detail-card text-decoration-none">
                                <span class="contact-detail-card__icon" aria-hidden="true">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <span class="contact-detail-card__content">
                                    <span class="contact-detail-card__label">Phone Number</span>
                                    <strong><?php echo esc_html($phone_number); ?></strong>
                                </span>
                            </a>
                        <?php endif; ?>

                        <?php if ($email_address): ?>
                            <a href="mailto:<?php echo esc_attr($email_address); ?>"
                               class="contact-detail-card text-decoration-none">
                                <span class="contact-detail-card__icon" aria-hidden="true">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <span class="contact-detail-card__content">
                                    <span class="contact-detail-card__label">Email Address</span>
                                    <strong><?php echo esc_html($email_address); ?></strong>
                                </span>
                            </a>
                        <?php endif; ?>

                        <?php if ($address): ?>
                            <div class="contact-detail-card">
                                <span class="contact-detail-card__icon" aria-hidden="true">
                                    <i class="fas fa-location-dot"></i>
                                </span>
                                <span class="contact-detail-card__content">
                                    <span class="contact-detail-card__label">Showroom Address</span>
                                    <strong><?php echo wp_kses_post($address); ?></strong>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="contact-page-form">
                    <div class="contact-page-form__intro">
                        <span class="contact-page-form__eyebrow">Send an Enquiry</span>
                        <h2>Tell us what you need and we will get back to you shortly</h2>
                    </div>

                    <div class="contact-page-form__body">
                        <?php echo do_shortcode('[contact-form-7 id="00cb1fb" title="Contact Form"]'); ?>
                    </div>
                </div>

                <div class="contact-page-hours">
                    <span class="contact-page-hours__eyebrow">Opening Hours</span>
                    <ul class="contact-page-hours__list">
                        <li><strong>Monday - Friday</strong><span>9am - 5pm</span></li>
                        <li><strong>Saturday</strong><span>11am - 4pm</span></li>
                        <li><strong>Sunday</strong><span>Closed</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="contact-page-showroom">
    <div class="container">
        <div class="row g-4 g-xl-5 align-items-start">
            <div class="col-12 col-xl-5">
                <div class="contact-page-showroom__content">
                    <span class="contact-page-showroom__eyebrow">Visit Our Showroom</span>
                    <h2>See products in person and speak with our team</h2>
                    <p>
                        Visit our Bradford showroom to compare products, explore finishes, and get practical advice for
                        your project.
                        Whether you are choosing appliances, planning a fitted kitchen, or comparing bedroom options, we
                        are here to help.
                    </p>

                    <div class="contact-page-showroom__actions">
                        <a href="<?php echo esc_url(home_url('/showrooms/')); ?>" class="btn btn-primary px-4 py-3">View
                            Showroom</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-7">
                <div class="contact-page-map">
                    <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2356.4993065792205!2d-1.7757807226318418!3d53.79839847242383!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x487be6b03e506b35%3A0x23a24f3f60b4b0e6!2sThe%20Kitchen%20Gallery%20Bradford%20ltd!5e0!3m2!1sen!2suk!4v1756050669489!5m2!1sen!2suk"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="The Kitchen Components showroom map"
                    ></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
