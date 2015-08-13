<?php if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( get_the_ID() ) ) : ?>
	<div class="edd_download_image">
		<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
			<?php
			if( is_tax() ){
				echo get_the_post_thumbnail( get_the_ID(), 'product-medium' );
			} else {
				echo get_the_post_thumbnail( get_the_ID(), 'product-thumb' );
			}
			?>
		</a>
	</div>
<?php endif; ?>