<?php

get_header(); ?>

<section class="checkout-page">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-12 col-xl-10">
				<div class="checkout-page__header text-center">
					<span class="checkout-page__eyebrow">Secure Checkout</span>
					<h1>Complete your order with confidence</h1>
					<p class="mb-0">
						Review your details, choose your delivery options, and place your order through a cleaner checkout experience.
					</p>
				</div>
			</div>
		</div>

		<div class="checkout-page__content">
			<?php
			echo do_shortcode('[woocommerce_checkout]');
			?>
		</div>
	</div>
</section>


<?php get_footer(); ?>
