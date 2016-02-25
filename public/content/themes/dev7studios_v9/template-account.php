<?php
/*
Template Name: Account
*/
get_template_part( 'parts/header' ); ?>

	<section class="main col-sm-12" role="main">
		<?php while (have_posts()) { the_post(); ?>
			<div class="entry-content">
				<?php the_content(); ?>
				<?php wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>')); ?>
			</div>
		<?php } ?>
		<?php if ( is_user_logged_in() ){ ?>
			<div id="account-tabs">
				<ul>
					<li><a href="#tab-purchase-history"><?php _e( 'Purchase History', 'dev7' ); ?></a></li>
					<li><a href="#tab-profile"><?php _e( 'Profile', 'dev7' ); ?></a></li>
					<li><a href="#tab-subscriptions"><?php _e( 'Subscriptions', 'dev7' ); ?></a></li>
					<li><a href="#tab-affiliates"><?php _e( 'Affiliates', 'dev7' ); ?></a></li>
				</ul>
				<div id="tab-purchase-history">
					<?php echo do_shortcode( '[purchase_history]' ); ?>
				</div>
				<div id="tab-profile">
					<?php echo do_shortcode( '[edd_profile_editor]' ); ?>
				</div>
				<div id="tab-subscriptions">
					<?php echo do_shortcode( '[edd_subscriptions]' ); ?>
				</div>
				<div id="tab-affiliates">
					Manage your affiliate account on the <a href="<?php echo home_url( 'affiliates' ); ?>">Affiliates page</a>.
				</div>
			</div>
		<?php } else { ?>
			<?php echo do_shortcode( '[edd_login redirect="'. home_url( 'account' ) .'"]' ); ?>
		<?php } ?>
	</section><!-- /.main -->

<?php get_template_part( 'parts/footer' ); ?>