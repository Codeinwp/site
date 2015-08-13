	<?php
	$page_title = get_post_meta( $post->ID, '_dev7_page_title', true );
	$page_subtitle = get_post_meta( $post->ID, '_dev7_page_subtitle', true );

	if( is_tax( 'download_category', 'wordpress-plugin' ) ){
		$page_title = 'WordPress Plugins';
		$page_subtitle = 'Hand crafted premium WordPress plugins';
	}
	if( is_home() ){
		$page_title = 'Blog';
		$page_subtitle = 'Web Development &amp; Design News &amp; Updates';
	}

	if( $page_title || $page_subtitle ) { ?>
	<div class="jumbotron page-top">
		<div class="container">
			<?php if( $page_title ) { ?>
			<h1 class="page-title"><?php echo $page_title; ?></h1>
			<?php } ?>
			<?php if( $page_subtitle ) { ?>
			<p class="page-subtitle"><?php echo $page_subtitle; ?></p>
			<?php } ?>
			<?php if( is_singular() && has_post_thumbnail() ){ ?>
				<?php the_post_thumbnail( 'product-full' ); ?>
			<?php } ?>
		</div>
	</div>
	<?php } ?>