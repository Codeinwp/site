	<article <?php post_class('edd_download col-sm-6'); ?>>
		<?php
		edd_get_template_part( 'shortcode', 'content-image' );
		edd_get_template_part( 'shortcode', 'content-title' );
		edd_get_template_part( 'shortcode', 'content-excerpt' );
		?>
		<a href="<?php the_permalink(); ?>" class="btn btn-success">Get <?php the_title(); ?></a>
	</article>