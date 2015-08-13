	<?php
	$page_title = get_the_title();
	$page_subtitle = get_the_excerpt();
	$demo_url = get_post_meta( $post->ID, '_dev7_demo_url', true );
	$demo_text = get_post_meta( $post->ID, '_dev7_demo_text', true );
	$video_embed = get_post_meta( $post->ID, '_dev7_video_embed', true );

	if( $page_title || $page_subtitle ) { ?>
	<div class="jumbotron page-top">
		<div class="container">
			<?php if( $page_title ) { ?>
			<h1 class="page-title"><?php echo $page_title; ?></h1>
			<?php } ?>
			<?php if( $page_subtitle ) { ?>
			<p class="page-subtitle"><?php echo $page_subtitle; ?></p>
			<?php } ?>
			<?php if( $video_embed ) { ?>
				<div class="video-thumb">
					<?php echo $video_embed; ?>
				</div>
			<?php } else if( has_post_thumbnail() ) { ?>
				<div class="post-thumb">
					<?php the_post_thumbnail( 'product-full' ); ?>
					<?php if( $demo_url && $demo_text ) { ?>
					<div class="overlay">
						<a href="<?php echo esc_url( $demo_url ); ?>" target="_blank" class="btn btn-success btn-lg"><?php echo esc_attr( $demo_text ); ?></a>
					</div>
					<?php } ?>
				</div>
			<?php } ?>
		</div>
	</div>
	<?php } ?>