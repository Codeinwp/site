<?php get_template_part( 'parts/header' ); ?>

	<section class="main col-sm-7" role="main">
		<?php while (have_posts()) { the_post(); ?>
			<div class="entry-content">
				<?php the_content(); ?>
				<?php wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>')); ?>
			</div>
		<?php } ?>
	</section><!-- /.main -->

<?php get_sidebar( 'page' ); ?>

<?php get_template_part( 'parts/footer' ); ?>