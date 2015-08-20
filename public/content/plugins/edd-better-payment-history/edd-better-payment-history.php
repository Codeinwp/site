<?php
/*
Plugin Name: Easy Digital Downloads - Better Payment History
Plugin URI: http://dev7studios.com
Description: Better payment history for Easy Digital Downloads
Version: 1.0
Author: Dev7studios
Author URI: http://dev7studios.com
License: GPL2
*/

class EDDBetterPaymentHistory {

    private $plugin_path;
    private $plugin_url;

    function __construct()
    {
        $this->plugin_path = plugin_dir_path( __FILE__ );
        $this->plugin_url = plugin_dir_url( __FILE__ );

		add_action( 'admin_init', array(&$this, 'admin_init') );
		add_action( 'admin_print_styles', array(&$this, 'admin_print_styles') );
        add_action( 'edd_payment_row_actions', array(&$this, 'row_actions'), 10, 2 );
        add_filter( 'edd_payments_table_column', array(&$this, 'table_column'), 10, 3 );
        add_filter( 'edd_view_receipt_bph', array(&$this, 'view_receipt_bph'), 10, 3 );
    }

    function admin_init()
    {
	    wp_register_style( 'edd-better-payment-history', plugins_url('edd-better-payment-history.css', __FILE__) );
    }

    function admin_print_styles()
    {
    	if( isset($_GET['post_type']) && isset($_GET['page']) && $_GET['post_type'] == 'download' && $_GET['page'] == 'edd-payment-history' )
	    	wp_enqueue_style( 'edd-better-payment-history' );
    }

    function row_actions( $row_actions, $payment )
    {
    	$payment_data = edd_get_payment_meta( $payment->ID );
    	$receipt_id = $payment_data['key'];
		$row_actions['bph_view'] = '<a href="' . add_query_arg( array ( 'payment_key' => $receipt_id, 'edd_action' => 'view_receipt_bph' ), home_url() ) . '" target="_blank" class="bph-view">' . __( 'View Receipt', 'edd' ) . '</a>';

		return $row_actions;
    }

    function table_column( $value, $item_id, $column_name )
    {
    	if( $column_name == 'email' ){
    		list($email, $rest) = explode('<', $value);
	    	$value = get_avatar( trim($email), 20 ) .' &nbsp;'. $value;
    	}
    	if( $column_name == 'status' ){
	    	$value = '<span class="bph-status-'. strtolower($value) .'">'. $value .'</span>';
    	}

	    return $value;
    }

    /* Duplicate edd_render_receipt_in_browser() to allow any user to see receipt */
    function view_receipt_bph()
    {
		if ( ! isset( $_GET['payment_key'] ) )
			wp_die( __( 'Missing purchase key.', 'edd' ), __( 'Error', 'edd' ) );

		if ( !current_user_can('edit_posts') )
			wp_die( __( 'Unauthorized.', 'edd' ), __( 'Error', 'edd' ) );

		$key = urlencode( $_GET['payment_key'] );

		error_reporting(0);
		ob_start();
		?>
		<!DOCTYPE html>
		<html lang="en">
			<title><?php _e( 'Receipt', 'edd' ); ?></title>
			<meta charset="utf-8" />
			<?php wp_head(); ?>
		</html>
		<body class="<?php echo apply_filters('edd_receipt_page_body_class', 'edd_receipt_page' ); ?>">
			<div id="edd_receipt_wrapper">
				<?php do_action( 'edd_render_receipt_in_browser_before' ); ?>
				<?php
				global $edd_receipt_args;

				$edd_receipt_args = shortcode_atts( array(
					'error'           => __( 'Sorry, trouble retrieving payment receipt.', 'edd' ),
					'price'           => true,
					'discount'        => true,
					'products'        => true,
					'date'            => true,
					'notes'           => true,
					'payment_key'     => true,
					'payment_method'  => true,
					'payment_id'      => true
				), $atts, 'edd_receipt' );

				$session = edd_get_purchase_session();
				if ( isset( $_GET[ 'payment_key' ] ) ) {
					$payment_key = urldecode( $_GET[ 'payment_key' ] );
				} else if ( $session ) {
					$payment_key = $session[ 'purchase_key' ];
				}

				// No key found
				if ( ! isset( $payment_key ) )
					return $edd_receipt_args[ 'error' ];

				$edd_receipt_args[ 'id' ] = edd_get_purchase_id_by_key( $payment_key );

				edd_get_template_part( 'shortcode', 'receipt' );
				?>
				<?php do_action( 'edd_render_receipt_in_browser_after' ); ?>
			</div>
		<?php wp_footer(); ?>
		</body>
		<?php
		echo ob_get_clean();
		die();
	}

}
new EDDBetterPaymentHistory();