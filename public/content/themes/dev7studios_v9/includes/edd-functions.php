<?php

define( 'EDD_SLUG', 'products' );

function dev7_payment_receipt_before( $payment, $edd_receipt_args ) {
	?>
	<tr>
		<th style="vertical-align:bottom">
			<?php if( is_ssl() ){ ?>
			<h3>Dev7studios</h3>
			<?php } else { ?>
			<img src="http://cdn.dev7studios.com/dev7studios.png" alt="" style="width:300px" />
			<?php } ?>
		</th>
		<th style="text-align:right">Dev7studios Ltd<br />
		3 Duffus Place<br />
		Elgin<br />
		IV30 5PB
		</th>
	</tr>
	<?php
}
add_action( 'edd_payment_receipt_before', 'dev7_payment_receipt_before', 10, 2 );

function dev7_payment_receipt_after( $payment, $edd_receipt_args ) {
	$user = edd_get_payment_meta_user_info( $payment->ID );
	?>
	<tr>
		<td><strong>Customer:</strong></td>
		<td>
			<?php if(isset($user['first_name']) && $user['first_name']) echo $user['first_name']; ?>
			<?php if(isset($user['last_name']) && $user['last_name']) echo ' '. $user['last_name']; ?>
			<?php
			if(isset($user['address']) && !empty($user['address'])){
				echo '<br />';
				if(isset($user['address']['line1']) && $user['address']['line1']) echo $user['address']['line1'] .'<br />';
				if(isset($user['address']['line2']) && $user['address']['line2']) echo $user['address']['line2'] .'<br />';
				if(isset($user['address']['city']) && $user['address']['city']) echo $user['address']['city'] .'<br />';
				if(isset($user['address']['state']) && $user['address']['state']) echo $user['address']['state'] .'<br />';
				if(isset($user['address']['country']) && $user['address']['country']) echo $user['address']['country'] .'<br />';
				if(isset($user['address']['zip']) && $user['address']['zip']) echo $user['address']['zip'] .'<br />';
			}
			?>
		</td>
	</tr>
	<?php
}
add_action( 'edd_payment_receipt_after', 'dev7_payment_receipt_after', 1, 2 );

function dev7_user_can_view_receipt( $user_can_view, $edd_receipt_args ) {
	// Everyone can view receipts
	return true;
}
add_filter( 'edd_user_can_view_receipt', 'dev7_user_can_view_receipt', 10, 2 );

// Bundle licene limit activation
function dev7_get_license_limit( $limit, $download_id, $license_id, $price_id ) {

	$payment_id   = get_post_meta( $license_id, '_edd_sl_payment_id', true );
	$is_bundle    = false;
	$bundle_items = array();
	$downloads    = edd_get_payment_meta_downloads( $payment_id );
	if( $downloads ) {
		foreach( $downloads as $download ) {
			if( 'bundle' == edd_get_download_type( $download['id'] ) ) {
				$is_bundle    = true;
				$bundle_items = edd_get_bundled_products( $download['id'] );
				break;
			}
		}
	}

	if( $is_bundle && in_array( $download_id, $bundle_items ) ) {
		if( $price_id !== false ) {
			switch( $price_id ) {
				case 0:
					$limit = 1; // single site license
					break;
				case 1:
					$limit = 5; // up to 5 sites
					break;
				case 2:
					$limit = 0; // unlimited
					break;
			}
		}
	}

	return $limit;

}
add_filter( 'edd_get_license_limit', 'dev7_get_license_limit', 10, 4 );

/*function dev7_accepted_payment_image( $card ) {
	$image = edd_locate_template( 'images' . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR . $card . '.gif', false );
	$content_dir = WP_CONTENT_DIR;
	$content_dir = str_replace( 'public/content', 'public/shared/content', $content_dir );

	if( function_exists( 'wp_normalize_path' ) ) {

		// Replaces backslashes with forward slashes for Windows systems
		$image = wp_normalize_path( $image );
		$content_dir = wp_normalize_path( $content_dir );

	}

	$image = str_replace( $content_dir, WP_CONTENT_URL, $image );
	return $image;
}
function dev7_accepted_payment_mastercard_image() {
	return dev7_accepted_payment_image( 'mastercard' );
}
add_filter( 'edd_accepted_payment_mastercard_image', 'dev7_accepted_payment_mastercard_image' );
function dev7_accepted_payment_visa_image() {
	return dev7_accepted_payment_image( 'visa' );
}
add_filter( 'edd_accepted_payment_visa_image', 'dev7_accepted_payment_visa_image' );
function dev7_accepted_payment_americanexpress_image() {
	return dev7_accepted_payment_image( 'americanexpress' );
}
add_filter( 'edd_accepted_payment_americanexpress_image', 'dev7_accepted_payment_americanexpress_image' );
function dev7_accepted_payment_discover_image() {
	return dev7_accepted_payment_image( 'discover' );
}
add_filter( 'edd_accepted_payment_discover_image', 'dev7_accepted_payment_discover_image' );
function dev7_accepted_payment_paypal_image() {
	return dev7_accepted_payment_image( 'paypal' );
}
add_filter( 'edd_accepted_payment_paypal_image', 'dev7_accepted_payment_paypal_image' );*/

/**
 * EDD Cross-sell & Upsell - Removing the excerpt
 * https://easydigitaldownloads.com/extensions/cross-sell-and-upsell/?ref=166
 */
function dev7_edd_csau_show_excerpt() {
	return false;
}
add_filter( 'edd_csau_show_excerpt', 'dev7_edd_csau_show_excerpt' );

/**
 * EDD Xero
 */
function dev7_edd_xero_invoice_number( $invoice_number, $payment_id, $payment ) {
	return 'EDD-' . $payment_id;
}
add_filter( 'edd_xero_invoice_number', 'dev7_edd_xero_invoice_number', 10, 3 );

function dev7_edd_xero_reference( $reference, $payment_id, $payment ) {
	return edd_get_payment_transaction_id( $payment_id );
}
add_filter( 'edd_xero_reference', 'dev7_edd_xero_reference', 10, 3 );