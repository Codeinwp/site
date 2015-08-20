<?php
/*
Plugin Name: Easy Digital Downloads - Better Reports
Plugin URI: http://dev7studios.com
Description: Better reporting for Easy Digital Downloads
Version: 1.0
Author: Dev7studios
Author URI: http://dev7studios.com
License: GPL2
*/

class EDDBetterReports {

    private $plugin_path;
    private $plugin_url;

    function __construct()
    {
        $this->plugin_path = plugin_dir_path( __FILE__ );
        $this->plugin_url = plugin_dir_url( __FILE__ );

		add_action( 'admin_init', array(&$this, 'admin_init') );
		add_action( 'admin_print_scripts', array(&$this, 'admin_print_scripts') );
        add_action( 'edd_reports_tabs', array(&$this, 'edd_reports_tabs') );
        add_action( 'edd_reports_tab_better-reports', array(&$this, 'better_reports') );
    }

    function admin_init()
    {
    	wp_register_style( 'morris', plugins_url('scripts/morris-0.4.3.min.css', __FILE__), array(), '0.4.3' );
	    wp_register_script( 'raphael', plugins_url('scripts/raphael-min.js', __FILE__), array(), '2.1.0' );
	    wp_register_script( 'morris', plugins_url('scripts/morris-0.4.3.min.js', __FILE__), array('raphael'), '0.4.3' );
	    wp_register_script( 'dateFormat', plugins_url('scripts/date.format.js', __FILE__), array(), '1.2.3' );
    }

    function admin_print_scripts()
    {
	    if( isset($_GET['post_type']) && isset($_GET['page']) && isset($_GET['tab']) &&
	    	$_GET['post_type'] == 'download' && $_GET['page'] == 'edd-reports' && $_GET['tab'] == 'better-reports' ){
		    wp_enqueue_style( 'morris' );
		    wp_enqueue_script( 'raphael' );
		    wp_enqueue_script( 'morris' );
		    wp_enqueue_script( 'dateFormat' );
	    }
    }

    function edd_reports_tabs()
    {
    	$current_page = admin_url( 'edit.php?post_type=download&page=edd-reports' );
    	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'reports';
	    ?>
	    <a href="<?php echo add_query_arg( array( 'tab' => 'better-reports', 'settings-updated' => false ), $current_page ); ?>" class="nav-tab <?php echo $active_tab == 'better-reports' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Better Reports', 'edd' ); ?></a>
	    <?php
    }

    function better_reports()
    {
		?>
    	<br />
    	<div class="metabox-holder" style="padding-top: 0;">
    		<?php
			$day     = strtotime('-30 days');
			$day_end = strtotime('tomorrow');
			$data = '';
			$total_sales = 0;
			$total_earnings = 0;
			while ( $day <= $day_end ) :
				$sales = edd_get_sales_by_date( date('d', $day), date('m', $day), date('Y', $day) );
				$earnings = edd_get_earnings_by_date( date('d', $day), date('m', $day), date('Y', $day) );
				$total_sales += $sales;
				$total_earnings += $earnings;
				$date = mktime( 0, 0, 0, date('m', $day), date('d', $day), date('Y', $day) ) * 1000;
				$data .= '{date:'. $date .', earnings:'. $earnings .', sales:'. $sales .'},' . "\n";
				$day += (60 * 60 * 24);
			endwhile;
			$data = rtrim($data, ',');
			?>
			<div class="postbox">
				<h3><span><?php _e('Recent Earnings', 'edd'); ?></span></h3>

				<div class="inside">
					<div id="better-reports-earnings-graph" style="height:300px"></div>
					<p class="totals-for-period"><strong>Total earnings for period:</strong> <?php echo edd_currency_filter( edd_format_amount( $total_earnings ) ); ?><br />
					<strong>Total sales for period:</strong> <?php echo number_format( $total_sales ); ?></p>
				</div>
			</div>
	    	<script type="text/javascript">
			new Morris.Line({
				element: 'better-reports-earnings-graph',
				data: [<?php echo $data; ?>],
				xkey: 'date',
				ykeys: [ 'earnings' ],
				labels: [ 'Earnings' ],
				lineColors: [
					'#1993c1',
					'#df4f4f',
					'#5e486d',
					'#DD6904',
					'#7DB9E8',
					'#167f39'
				],
				ymin: 'auto 0',
				lineWidth: 2,
				dateFormat: function(x){ return dateFormat(x, 'd mmm, yyyy'); },
				xLabelFormat: function(x){ return dateFormat(x, 'd mmm yyyy'); },
				yLabelFormat: function (y){
					if( edd_vars.currency_pos == 'before' ) {
						return edd_vars.currency_sign + y.toFixed(2);
	            	} else {
						return y.toFixed(2) + edd_vars.currency_sign;
	            	}
				}
			});
			</script>
    	</div>

	    <div class="metabox-holder" style="padding-top: 0;">
			<div class="postbox">
				<h3><span><?php _e('Recent Checkouts', 'edd'); ?></span></h3>

				<div class="inside">
					<table style="width:100%">
						<thead>
							<tr>
								<th style="text-align:left;">Customer</th>
								<th style="text-align:left;">Product(s)</th>
								<th style="text-align:left;">Date</th>
								<th style="text-align:left;">Total</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$args = array(
								'post_type'      => 'edd_payment',
								'meta_key'       => '_edd_payment_mode',
								'meta_value'     => 'live',
								'post_status'    => array( 'publish', 'revoked' ),
								'posts_per_page' => 10,
								'update_post_term_cache' => false
							);
							$recent_sales = new WP_Query( $args );
							while( $recent_sales->have_posts() ) {
								$recent_sales->next_post();
								$meta = get_post_meta($recent_sales->post->ID, '_edd_payment_meta', true);
                                $cart_details = @unserialize($meta['cart_details']);
                                if($cart_details === false) $cart_details = $meta['cart_details'];
								$user = @unserialize($meta['user_info']);
                                if($user === false) $user = $meta['user_info'];
								$product_name = '';
								$total_price = 0;
								if(!empty($cart_details)){
									foreach($cart_details as $details){
										$product_name .= $details['name'] .', ';
										$total_price += $details['price'] - $details['discount'];
									}
								}
								$product_name = rtrim($product_name, ', ');
								echo '<tr><td>'. get_avatar( trim($user['email']), 20 ) .' &nbsp;'. $user['email'] .'</td><td>'. $product_name .'</td><td>'. get_the_time('D jS M, h:ia', $recent_sales->post) .'</td><td>'. edd_currency_filter( edd_format_amount( $total_price ) ) .'</td></tr>';
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="metabox-holder" style="padding-top: 0;">
			<div class="postbox">
				<h3><span><?php _e('Overall Performance', 'edd'); ?></span></h3>

				<div class="inside">
					<table style="width:100%">
						<thead>
							<tr>
								<th style="text-align:left;width:70%;">Product</th>
								<th style="text-align:left;">Total</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$args = array(
								'post_type'              => 'edd_payment',
								'nopaging'               => true,
								'meta_key'               => '_edd_payment_mode',
								'meta_value'             => 'live',
								'post_status'            => array( 'publish', 'revoked' ),
								'update_post_meta_cache' => false,
								'update_post_term_cache' => false
							);
							$overall_performance = new WP_Query( $args );
							$results = array();
							while( $overall_performance->have_posts() ) {
								$overall_performance->next_post();
								$meta = get_post_meta($overall_performance->post->ID, '_edd_payment_meta', true);
								$cart_details = @unserialize($meta['cart_details']);
                                if($cart_details === false) $cart_details = $meta['cart_details'];
								if(!empty($cart_details)){
									foreach($cart_details as $details){
										if( !isset($results[$details['name']]) ){
											$results[$details['name']] = $details['price'];
										} else {
											$results[$details['name']] += $details['price'];
										}
									}
								}
							}
							arsort($results);
							foreach($results as $product_name=>$total_earnings){
								echo '<tr><td>'. $product_name .'</td><td>'. edd_currency_filter( edd_format_amount( $total_earnings ) ) .'</td></tr>';
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php
    }

}
new EDDBetterReports();
