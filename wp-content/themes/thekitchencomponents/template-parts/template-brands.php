<?php
/*
Template Name: Brand Page Template
*/

get_header();

$brand_logo_base = trailingslashit(get_template_directory_uri() . '/images/brands');
$brands = [
    ['name' => 'AEG', 'file' => 'aeg-logo.jpg'],
    ['name' => 'Airone', 'file' => 'airone-logo.png'],
    ['name' => 'Amica', 'file' => 'amica-logo.jpg'],
    ['name' => 'Astracast', 'file' => 'astracast-logo.png'],
    ['name' => 'Avelis', 'file' => 'avelis-logo.jpg'],
    ['name' => 'Amtico', 'file' => 'amtico-logo.png'],
    ['name' => 'Beaufort', 'file' => 'beaufort-logo.jpg'],
    ['name' => 'Bidbury Co', 'file' => 'bidbury-co-logo.png'],
    ['name' => 'Bosch', 'file' => 'bosch-logo.jpg'],
    ['name' => 'Candy', 'file' => 'candy-logo.jpg'],
    ['name' => 'Carron', 'file' => 'carron-logo.jpg'],
    ['name' => 'Cavecool', 'file' => 'cavecool-logo.jpg'],
    ['name' => 'CDA', 'file' => 'cda-logo.jpg'],
    ['name' => 'Scudo', 'file' => 'scudo-logo.png'],
    ['name' => 'Eastbrook', 'file' => 'eastbrook-logo.jpg'],
    ['name' => 'Elica', 'file' => 'elica-logo.jpg'],
    ['name' => 'Franke', 'file' => 'franke-logo.jpg'],
    ['name' => 'Harmony', 'file' => 'harmony-brand-image.webp'],
    ['name' => 'Hoover', 'file' => 'hoover-logo.jpg'],
    ['name' => 'Hotpoint', 'file' => 'hotpoint-logo.jpg'],
    ['name' => 'Miro', 'file' => 'miro-logo.png'],
    ['name' => 'Neff', 'file' => 'neff-logo.jpg'],
    ['name' => 'Pevino', 'file' => 'pevino-logo.jpg'],
    ['name' => 'Phoenix Bathrooms', 'file' => 'phoenix-logo.jpg'],
    ['name' => 'Prima', 'file' => 'prima-logo.jpg'],
    ['name' => 'Siena', 'file' => 'siena-logo.jpg'],
    ['name' => 'ViandPro', 'file' => 'viandpro-logo.jpg'],
    ['name' => 'Whirlpool', 'file' => 'whirlpool-logo.jpg'],
];
?>

<section class="brand-hero">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-9">
                <div class="brand-hero__panel text-center text-xl-start">
                    <span class="brand-hero__eyebrow">Trusted Brand Partners</span>
                    <div class="row align-items-center g-4">
                        <div class="col-12 col-xl-8">
                            <h1 class="brand-hero__title">Brands for Kitchen and Bathroom Appliances</h1>
                            <p class="brand-hero__copy mb-0">
                                Explore a hand-picked collection of appliance and interior brands we regularly supply
                                across kitchens, bathrooms, sinks, taps, heating, and flooring.
                            </p>
                        </div>
                        <div class="col-12 col-xl-4">
                            <div class="brand-hero__stat">
                                <strong><?php echo esc_html(count($brands)); ?>+</strong>
                                <span>popular brands available across our range</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="brand-showcase">
    <div class="container">
        <div class="brand-showcase__intro text-center">
            <span class="brand-showcase__kicker">Brand Directory</span>
        </div>

        <div class="row g-3 g-lg-4">
            <?php foreach ($brands as $brand) : ?>
                <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                    <article class="brand-card h-100">
                        <div class="brand-card__media">
                            <img
                                    src="<?php echo esc_url($brand_logo_base . $brand['file']); ?>"
                                    alt="<?php echo esc_attr($brand['name']); ?>"
                                    class="img-fluid brand-logo"
                            >
                        </div>
                        <h3 class="brand-card__title"><?php echo esc_html($brand['name']); ?></h3>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="brand-showcase__cta text-center">
            <p class="mb-3">Need a brand you do not see here?</p>
            <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="btn btn-primary px-4 py-3">Contact Us</a>
        </div>
    </div>
</section>

<?php get_footer(); ?>
