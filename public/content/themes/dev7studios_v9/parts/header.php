<!doctype html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" href="<?php echo get_template_directory_uri() .'/assets/img/favicon.png'; ?>">

	<?php wp_head(); ?>

	<script src="//use.typekit.net/ybp7qlj.js"></script>
	<script>try{Typekit.load();}catch(e){}</script>
</head>
<body <?php body_class(); ?>>

	<div class="header-wrap">

		<header class="banner navbar navbar-default navbar-static-top" role="banner">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<img src="<?php echo get_template_directory_uri() .'/assets/img/logo.png'; ?>" alt="<?php bloginfo( 'name' ); ?>">
					</a>
				</div>

				<nav class="collapse navbar-collapse" role="navigation">
					<?php
					if ( has_nav_menu( 'primary-navigation' ) ) {
						wp_nav_menu( array( 'theme_location' => 'primary-navigation', 'menu_class' => 'nav navbar-nav navbar-right' ) );
					}
					?>
				</nav>
			</div>
		</header>

		<?php
		global $post;
		if( is_single() ){
			get_template_part( 'parts/page-top', get_post_type( $post->ID ) );
		} else {
	    	get_template_part( 'parts/page-top', $post->post_name );
		}
		?>

	</div>

	<div class="wrap container" role="document">
		<?php
		global $ignore_content_row;
		if( !isset( $ignore_content_row ) || !$ignore_content_row ){ ?>
		<div class="content row">
		<?php } ?>