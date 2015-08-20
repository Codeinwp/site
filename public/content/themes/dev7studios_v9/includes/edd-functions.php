<?php

define( 'EDD_SLUG', 'products' );

function dev7_payment_receipt_before( $payment, $edd_receipt_args ) {
	?>
	<tr>
		<th style="vertical-align:bottom">
			<img src="https://cdn.dev7studios.com/dev7studios.png" alt="" style="width:300px" />
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