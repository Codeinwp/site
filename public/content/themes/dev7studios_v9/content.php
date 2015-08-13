	<article <?php post_class(); ?>>
		<header class="entry-header">
			<?php if ( is_single() ) { ?>
				<h1 class="entry-title"><?php the_title(); ?></h1>
			<?php } else { ?>
				<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
			<?php } ?>
			<p class="entry-meta">
				<time class="published" datetime="<?php echo get_the_time( 'c' ); ?>"><?php echo get_the_date(); ?></time>
				<?php echo __( 'in', 'dev7' ) .' '. get_the_category_list( ', ' ); ?>
				<?php if( get_comments_number() ){ ?>
					- <?php comments_popup_link( '', '1 Comment', '% Comments' ); ?>
				<?php } ?>
			</p>
			<?php if( has_post_thumbnail() ){ ?>
				<div class="entry-thumb">
					<?php the_post_thumbnail( 'product-full' ); ?>
				</div>
			<?php } ?>
		</header>

		<div class="entry-content">
			<?php if ( is_single() ) { ?>
				<?php the_content(); ?>
				<?php wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>')); ?>
			<?php } else { ?>
				<?php the_excerpt(); ?>
			<?php } ?>
		</div>

		<?php comments_template(); ?>
	</article>