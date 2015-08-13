<?php get_template_part( 'parts/header' ); ?>

	<section class="main" role="main">
		<?php if ( !have_posts() ) { ?>
			<?php get_template_part( 'parts/no-posts' ); ?>
		<?php } ?>

		<?php while (have_posts()) : the_post(); ?>
			<?php get_template_part( 'content', 'download' ); ?>
		<?php endwhile; ?>

		<div class="col-sm-12">
			<?php get_template_part( 'parts/pagination' ); ?>
		</div>
	</section><!-- /.main -->

<?php get_template_part( 'parts/footer' ); ?>