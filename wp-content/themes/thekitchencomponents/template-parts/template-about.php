<?php
/*
Template Name: About Page Template
*/

get_header();

$about = get_field('about');
$hero_background = get_template_directory_uri() . '/images/stock/showroom-main.jpg';
$hero_title = !empty($about['hero_title']) ? $about['hero_title'] : get_the_title();
$intro_title = !empty($about['introduction_title']) ? $about['introduction_title'] : 'About The Kitchen Components';
$intro_content = !empty($about['introduction_content']) ? $about['introduction_content'] : '';
$intro_image = array(
    'url' => get_template_directory_uri() . '/images/stock/showroom-details.jpg',
    'alt' => 'The Kitchen Components showroom interior'
);
$services = !empty($about['services']) ? $about['services'] : array();
?>

<section
    class="about-page-hero"
    <?php if ($hero_background): ?>
        style="background-image: linear-gradient(135deg, rgba(20, 20, 20, 0.74), rgba(20, 20, 20, 0.46)), url('<?php echo esc_url($hero_background); ?>');"
    <?php endif; ?>
>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="about-page-hero__panel">
                    <div class="row g-4 align-items-center">
                        <div class="col-12 col-lg-8">
                            <span class="about-page-hero__eyebrow">About Us</span>
                            <h1 class="about-page-hero__title"><?php echo esc_html($hero_title); ?></h1>
                            <p class="about-page-hero__copy">
                                Discover the people, values, and experience behind The Kitchen Components,
                                from our Bradford showroom to the service that supports every order and project.
                            </p>
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="about-page-hero__stat">
                                <strong>Family Business</strong>
                                <span>Trusted advice, practical help, and a hands-on approach to every customer journey.</span>
                            </div>
                            <div class="about-page-hero__stat">
                                <strong>Showroom & Online</strong>
                                <span>Shop online or visit us in Bradford to explore kitchens, bedrooms, and appliances in person.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-page-intro">
    <div class="container">
        <div class="row g-4 g-xl-5 align-items-stretch">
            <div class="col-12 col-xl-6">
                <div class="about-page-intro__content h-100">
                    <span class="about-page-intro__eyebrow">Who We Are</span>
                    <h2><?php echo esc_html($intro_title); ?></h2>
                    <?php if ($intro_content): ?>
                        <div class="about-page-intro__copy">
                            <?php echo wp_kses_post($intro_content); ?>
                        </div>
                    <?php endif; ?>

                    <div class="about-page-intro__actions">
                        <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="btn btn-primary px-4 py-3">Speak to Our Team</a>
                        <a href="<?php echo esc_url(home_url('/showrooms/')); ?>" class="btn btn-outline-secondary px-4 py-3">Visit the Showroom</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="about-page-intro__media h-100">
                    <?php if ($intro_image): ?>
                        <img
                            class="img-fluid w-100 about-page-intro__image"
                            src="<?php echo esc_url($intro_image['url']); ?>"
                            alt="<?php echo esc_attr($intro_image['alt'] ?: $intro_title); ?>"
                        >
                    <?php else: ?>
                        <div class="about-page-intro__placeholder">
                            <span>Visit our Bradford showroom to explore kitchens, bedrooms, and appliances with expert guidance.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-page-story">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <div class="about-page-story__header text-center">
                    <span class="about-page-story__eyebrow">Our Story</span>
                    <h2>Built on experience, service, and honest pricing</h2>
                    <p>
                        We combine a family-business approach with a broad range of modern kitchens, bedrooms, and appliances,
                        helping customers shop with confidence online and in person.
                    </p>
                </div>
            </div>
        </div>

        <div class="row g-4 align-items-stretch">
            <div class="col-12 col-lg-4">
                <article class="about-story-card h-100">
                    <span class="about-story-card__kicker">Why Choose Us</span>
                    <p>
                        Welcome to <strong class="text-primary">The Kitchen Components</strong>, where you will find a large
                        selection of high-quality kitchens and bedrooms at competitive prices. By working directly with major manufacturers,
                        we are able to offer design-led products without the designer price tag.
                    </p>
                    <p class="mb-0">
                        We keep pricing clear and straightforward, so shopping with us feels simple, transparent, and secure.
                    </p>
                </article>
            </div>

            <div class="col-12 col-lg-4">
                <article class="about-story-card h-100">
                    <span class="about-story-card__kicker">Our History</span>
                    <p>
                        We are proud to be a family business, with family values at the heart of how we work. Established over 10 years ago,
                        we have grown from our trading roots into a trusted independent kitchen and bedroom specialist in West Yorkshire.
                    </p>
                    <p class="mb-0">
                        Today, our showroom and distribution hub help us support customers with dependable service and practical product knowledge.
                    </p>
                </article>
            </div>

            <div class="col-12 col-lg-4">
                <article class="about-story-card h-100">
                    <span class="about-story-card__kicker">Our Standards</span>
                    <p>
                        Great service matters just as much as competitive pricing. Our team is trained regularly and brings hands-on experience
                        to every enquiry, helping customers choose the right solution for their home, style, and budget.
                    </p>
                    <p class="mb-0">
                        From the first conversation to the final order, customer satisfaction stays at the centre of everything we do.
                    </p>
                </article>
            </div>
        </div>
    </div>
</section>

<?php if ($services): ?>
    <section class="about-page-services">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-xl-8 text-center">
                    <span class="about-page-services__eyebrow">What We Offer</span>
                    <h2 class="about-page-services__title">Our services</h2>
                    <p class="about-page-services__copy">
                        A joined-up service across kitchens, bedrooms, appliances, and showroom support to make planning your next project easier.
                    </p>
                </div>
            </div>

            <div class="row g-3 g-lg-4">
                <?php foreach ($services as $service): ?>
                    <?php
                    $image = !empty($service['icon']) ? $service['icon'] : '';
                    $title = !empty($service['title']) ? $service['title'] : '';
                    if (!$title) {
                        continue;
                    }
                    ?>
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="about-service-card h-100">
                            <?php if ($image): ?>
                                <span class="about-service-card__icon">
                                    <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
                                </span>
                            <?php endif; ?>
                            <h3><?php echo esc_html($title); ?></h3>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php if (have_rows('testimonials', 'option')): ?>
    <section class="py-5 bg-white border-top border-grey border-1 d-none">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="text-center mb-5">What Our Customers Have to Say</h2>
                </div>
                <div class="col-12">
                    <div class="testimonial-slider">
                        <?php while (have_rows('testimonials', 'option')): the_row(); ?>
                            <?php
                            $name = get_sub_field('name');
                            $content = get_sub_field('content');
                            $rating = get_sub_field('rating');
                            ?>

                            <div class="testimonial-slide pb-4 px-3">
                                <div class="card border border-primary shadow overflow-hidden bg-gray h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center justify-content-center mb-3">
                                            <div class="me-3 flex-shrink-0">
                                                <i class="fa-solid fa-user-circle fa-2x text-secondary"></i>
                                            </div>
                                            <div>
                                                <p class="fw-bold text-muted mb-0"><?php echo esc_html($name); ?></p>
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <p class="card-text mb-2"><?php echo esc_html($content); ?></p>
                                            <div class="text-warning">
                                                <?php echo str_repeat('★', (int) $rating) . str_repeat('☆', 5 - (int) $rating); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php get_footer(); ?>
