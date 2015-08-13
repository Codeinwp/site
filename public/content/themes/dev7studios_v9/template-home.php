<?php
/*
Template Name: Home
*/

$ignore_content_row = true;
get_template_part( 'parts/header' ); ?>

	<div class="row recent-wp-plugins section">
		<h2 class="section-title">Premium WordPress Plugins</h2>
		<p class="section-intro">Our range of Premium WordPress Plugins offers some awesome functionality for
		enhancing your website. Below are some of our recent plugins.</p>
		<?php echo do_shortcode( '[downloads category="wordpress-plugin" number="3" columns="3" buy_button="no"]' ); ?>
		<a href="<?php echo home_url( 'products/category/wordpress-plugin' ); ?>" class="btn btn-primary btn-lg">View All Plugins</a>
	</div>

	<div class="row dedicated-support section section-dark">
		<div class="container">
			<div class="row">
				<div class="col-sm-6">
					<h2 class="dedicated">Dedicated Support</h2>
					<p>We hope you never need us but when you run into an issue, or just want a helping hand, our
					support team is there to help in whatever way we can.</p>
				</div>
				<div class="col-sm-6">
					<h2 class="guarantee">30 Day Guarantee</h2>
					<p>We put a lot of effort into our products and aim to put out the highest quality software. So
					we offer a 30 day money back guarantee as proof of our sincerity.</p>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<img src="<?php echo get_template_directory_uri() .'/assets/img/customers.png' ?>" alt="">
				</div>
			</div>
		</div>
	</div>

<?php get_template_part( 'parts/footer' ); ?>