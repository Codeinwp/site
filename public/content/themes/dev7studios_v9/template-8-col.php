<?php
/*
Template Name: Full Width (8 col)
*/
get_template_part( 'parts/header' ); ?>

	<section class="main col-sm-8 col-sm-offset-2" role="main">
		<?php while (have_posts()) { the_post(); ?>
			<div class="entry-content">
				<?php the_content(); ?>
				<?php wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>')); ?>
			</div>
		<?php } ?>
	</section><!-- /.main -->

<?php get_template_part( 'parts/footer' ); ?>