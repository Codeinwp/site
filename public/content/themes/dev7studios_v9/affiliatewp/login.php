<?php
global $affwp_login_redirect;
affiliate_wp()->login->print_errors();
?>

<form id="affwp-login-form" class="affwp-form col-sm-6" action="" method="post">
	<?php do_action( 'affwp_affiliate_login_form_top' ); ?>

	<fieldset>
		<legend><?php _e( 'Log into Your Account', 'affiliate-wp' ); ?></legend>

		<?php do_action( 'affwp_login_fields_before' ); ?>

		<div class="form-group">
			<label for="affwp-user-login"><?php _e( 'Username', 'affiliate-wp' ); ?></label>
			<input id="affwp-user-login" class="required form-control" type="text" name="affwp_user_login" title="<?php esc_attr_e( 'Username', 'affiliate-wp' ); ?>" />
		</div>

		<div class="form-group">
			<label for="affwp-user-pass"><?php _e( 'Password', 'affiliate-wp' ); ?></label>
			<input id="affwp-user-pass" class="password required form-control" type="password" name="affwp_user_pass" />
		</div>

		<div class="checkbox">
			<label class="affwp-user-remember" for="affwp-user-remember">
				<input id="affwp-user-remember" type="checkbox" name="affwp_user_remember" value="1" /><?php _e( 'Remember Me', 'affiliate-wp' ); ?>
			</label>
		</div>

		<div class="form-group">
			<input type="hidden" name="affwp_redirect" value="<?php echo esc_url( $affwp_login_redirect ); ?>"/>
			<input type="hidden" name="affwp_login_nonce" value="<?php echo wp_create_nonce( 'affwp-login-nonce' ); ?>" />
			<input type="hidden" name="affwp_action" value="user_login" />
			<input type="submit" class="btn btn-primary" value="<?php esc_attr_e( 'Login', 'affiliate-wp' ); ?>" />
		</div>

		<p class="affwp-lost-password">
			<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php _e( 'Lost Password?', 'affiliate-wp' ); ?></a>
		</p>

		<?php do_action( 'affwp_login_fields_after' ); ?>
	</fieldset>

	<?php do_action( 'affwp_affiliate_login_form_bottom' ); ?>
</form>
