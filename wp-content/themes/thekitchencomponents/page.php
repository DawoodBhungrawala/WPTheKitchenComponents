<?php
	
	get_header();
	
	if (have_posts()) {
		while (have_posts()) {
			the_post(); ?>
			<main class="container py-5">
				<?php the_content(); ?>
			</main>
			<?php
		}
	}
	
	get_footer();
