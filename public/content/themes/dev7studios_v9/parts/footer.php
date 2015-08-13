		<?php
		global $ignore_content_row;
		if( !isset( $ignore_content_row ) || !$ignore_content_row ){ ?>
		</div><!-- /.content -->
		<?php } ?>
	</div><!-- /.wrap -->

	<footer class="footer">
		<div class="container">
			<div class="row footer-cols">
				<div class="col-sm-3"><?php dynamic_sidebar('footer-1'); ?></div>
				<div class="col-sm-3"><?php dynamic_sidebar('footer-2'); ?></div>
				<div class="col-sm-3"><?php dynamic_sidebar('footer-3'); ?></div>
				<div class="col-sm-3"><?php dynamic_sidebar('footer-4'); ?></div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="footer-content">
						Copyright &copy; <?php echo date( 'Y' ); ?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
					</div>
				</div>
			</div>
		</div>
	</footer>

	<?php wp_footer(); ?>

</body>
</html>