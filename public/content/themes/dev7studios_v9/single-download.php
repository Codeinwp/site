<?php get_template_part( 'parts/header' ); ?>

	<section class="main col-sm-7" role="main">
		<article <?php post_class(); ?>>
			<div class="entry-content">
				<?php the_content(); ?>
				<?php wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>')); ?>
			</div>
		</article>
	</section><!-- /.main -->

<?php get_sidebar( 'download' ); ?>

<?php get_template_part( 'parts/footer' ); ?>