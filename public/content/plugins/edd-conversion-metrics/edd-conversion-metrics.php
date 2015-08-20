<?php
/*
Plugin Name: Easy Digital Downloads - Conversion Metrics
Plugin URI: http://dev7studios.com
Description: Metrics reporting to help you increase retention and conversions
Version: 1.0.0
Author: Dev7studios
Author URI: http://dev7studios.com
License: GPL2
*/

class EDDConversionMetrics {

    private $version;
    private $plugin_path;
    private $plugin_url;

    public function __construct()
    {
        $this->version = '1.0.0';
        $this->plugin_path = plugin_dir_path( __FILE__ );
        $this->plugin_url = plugin_dir_url( __FILE__ );

        add_action( 'init', array(&$this, 'init'), 0 );
        add_action( 'admin_init', array(&$this, 'admin_init') );
		add_action( 'admin_print_scripts', array(&$this, 'admin_print_scripts') );
        add_action( 'edd_reports_tabs', array(&$this, 'edd_reports_tabs') );
        add_action( 'edd_reports_tab_conversion-metrics', array(&$this, 'conversion_metrics') );
        add_action( 'edd_complete_purchase', array(&$this, 'complete_purchase') );

        load_plugin_textdomain( 'edd-conversion-metrics', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
    }

    public function init()
    {
        if( !session_id() ) session_start();

        if( !is_admin() ){
            if( !isset($_SESSION['edd_cm_data']) ){
                $_SESSION['edd_cm_data'] = array(
                    'request' => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '',
                    'uri' => $this->url_origin($_SERVER) . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''),
                    'referrer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
                    'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
                    'ip' => $this->get_user_ip(),
                    'unique' => $this->get_cookie('_edd_cm_unique') ? 0 : 1,
                    'page_views' => 0,
                    'start_time' => time()
                );
            } else {
                $_SESSION['edd_cm_data']['page_views']++;
            }

            if( !$this->get_cookie('_edd_cm_unique') ){
                $this->set_cookie('_edd_cm_unique', 1, 60*60*24*365*10);
            }
        }
    }

    public function complete_purchase( $payment_id )
    {
        if( isset($_SESSION['edd_cm_data']) ){
            add_post_meta( $payment_id, '_edd_cm_request', $_SESSION['edd_cm_data']['request'] );
            add_post_meta( $payment_id, '_edd_cm_uri', $_SESSION['edd_cm_data']['uri'] );
            add_post_meta( $payment_id, '_edd_cm_referrer', $_SESSION['edd_cm_data']['referrer'] );
            add_post_meta( $payment_id, '_edd_cm_user_agent', $_SESSION['edd_cm_data']['user_agent'] );
            add_post_meta( $payment_id, '_edd_cm_ip', $_SESSION['edd_cm_data']['ip'] );
            add_post_meta( $payment_id, '_edd_cm_unique', $_SESSION['edd_cm_data']['unique'] );
            add_post_meta( $payment_id, '_edd_cm_page_views', $_SESSION['edd_cm_data']['page_views'] );
            add_post_meta( $payment_id, '_edd_cm_time_spent', (time() - $_SESSION['edd_cm_data']['start_time']) );
            unset($_SESSION['edd_cm_data']);
        }
    }

    public function admin_init()
    {
        wp_register_style( 'edd-cm', plugins_url('styles/edd-conversion-metrics.css', __FILE__), array(), $this->version );
        wp_register_script( 'jquery-flot-pie', plugins_url('scripts/jquery.flot.pie.js', __FILE__), array('jquery-flot') );
    }

    public function admin_print_scripts()
    {
	    if( isset($_GET['post_type']) && isset($_GET['page']) && isset($_GET['tab']) &&
	    	$_GET['post_type'] == 'download' && $_GET['page'] == 'edd-reports' && $_GET['tab'] == 'conversion-metrics' ){
		    wp_enqueue_style( 'edd-cm' );
            wp_enqueue_script( 'jquery-flot' ); // From EDD
            wp_enqueue_script( 'jquery-flot-pie' );
            wp_enqueue_script( 'jquery-ui-datepicker' );
	    }
    }

    public function edd_reports_tabs()
    {
    	$current_page = admin_url( 'edit.php?post_type=download&page=edd-reports' );
    	$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'reports';
	    ?>
	    <a href="<?php echo add_query_arg( array( 'tab' => 'conversion-metrics', 'settings-updated' => false ), $current_page ); ?>" class="nav-tab <?php echo $active_tab == 'conversion-metrics' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Conversion Metrics', 'edd' ); ?></a>
	    <?php
    }

    public function conversion_metrics()
    {
        $date_range_after = date('j M Y', strtotime('-30 days'));
        $date_range_before = date('j M Y');
        if( isset($_GET['date_from']) && $_GET['date_from'] ) $date_range_after = esc_attr( $_GET['date_from'] );
        if( isset($_GET['date_to']) && $_GET['date_to'] ) $date_range_before = esc_attr( $_GET['date_to'] );
        $date_range_before .= ' 23:59:59';

        $traffic_sources = array(
            'direct' => array(),
            'search' => array(),
            'referral' => array()
        );
        $new_vs_returning = array(
            'new' => array(),
            'returning' => array()
        );
        $avg_page_views_purchases = array(
            'total_page_views' => 0,
            'total_purchases' => 0,
            'total_purchase_value' => 0
        );
        $avg_time_spent = array(
            'total_time_spent' => 0,
            'total_purchases' => 0
        );
        $top_referrals = array();
        $top_landing_pages = array();

        $edd_payments = new WP_Query(array(
            'post_type' => 'edd_payment',
            'meta_query' => array(
                array(
                    'key' => '_edd_cm_request',
                    'compare' => 'EXISTS'
                )
            ),
            'date_query' => array(
        		array(
        			'after' => $date_range_after,
                    'before' => $date_range_before,
                    'inclusive' => true
        		)
        	),
        	'posts_per_page' => -1
        ));
        if( $edd_payments->have_posts() ){
            while( $edd_payments->have_posts() ){
                $edd_payments->the_post();
                $meta = get_post_meta( get_the_ID() );
                $data = array(
                    'request' => $meta['_edd_cm_request'][0],
                    'uri' => $meta['_edd_cm_uri'][0],
                    'referrer' => $meta['_edd_cm_referrer'][0],
                    'user_agent' => $meta['_edd_cm_user_agent'][0],
                    'ip' => $meta['_edd_cm_ip'][0],
                    'unique' => $meta['_edd_cm_unique'][0],
                    'page_views' => $meta['_edd_cm_page_views'][0],
                    'time_spent' => $meta['_edd_cm_time_spent'][0],
                    'payment_meta' => @unserialize($meta['_edd_payment_meta'][0])
                );
                $cart_details = $data['payment_meta']['cart_details'];

                // Traffic Sources
                if($data['referrer'] == ''){
                    $traffic_sources['direct'][] = $data;
                } else {
                    $parsed = parse_url( $data['referrer'], PHP_URL_QUERY );
                    parse_str( $parsed, $query );
                    if( isset($query['q']) ){
                        $traffic_sources['search'][] = $data;
                    } else {
                        $parsed = parse_url( get_site_url() );
                        if( strpos($data['referrer'], $parsed['host']) === false ){
                            $traffic_sources['referral'][] = $data;

                            // Top Referrals
                            if( !isset($top_referrals[$data['referrer']]) ) $top_referrals[$data['referrer']] = 0;
                            $top_referrals[$data['referrer']]++;
                        } else {
                            $traffic_sources['direct'][] = $data;
                        }
                    }
                }

                // New vs Returning
                if($data['unique']){
                    $new_vs_returning['new'][] = $data;
                } else {
                    $new_vs_returning['returning'][] = $data;
                }

                // Avg. Page Views per Purchase & Avg. Value per Page View
                $avg_page_views_purchases['total_page_views'] += $data['page_views'];
                $avg_page_views_purchases['total_purchases']++;
                if(!empty($cart_details)){
					foreach($cart_details as $details){
						$avg_page_views_purchases['total_purchase_value'] += $details['price'] - $details['discount'];
					}
				}

                // Avg. Time Spent on Site
                $avg_time_spent['total_time_spent'] += $data['time_spent'];
                $avg_time_spent['total_purchases']++;

                // Top Landing Pages
                if( !isset($top_landing_pages[$data['request']]) ) $top_landing_pages[$data['request']] = 0;
                $top_landing_pages[$data['request']]++;
            }
            wp_reset_postdata();

            arsort($top_referrals);
            arsort($top_landing_pages);
            $top_referrals = array_slice($top_referrals, 0, 10, true);
            $top_landing_pages = array_slice($top_landing_pages, 0, 10, true);
        }
        ?>
        <br>
        <form id="edd-cm-date-select-form" method="get">
            <input type="hidden" name="post_type" value="download">
            <input type="hidden" name="page" value="edd-reports">
            <input type="hidden" name="tab" value="conversion-metrics">
            <input type="text" name="date_from" value="<?php echo date('j M Y', strtotime($date_range_after)); ?>" placeholder="From">
            <input type="text" name="date_to" value="<?php echo date('j M Y', strtotime($date_range_before)); ?>" placeholder="To">
            <input type="submit" value="Show Data" class="button">
        </form>
        <script type="text/javascript">
        (function($) {
            $(document).ready(function(){
                var options = { dateFormat: 'd M yy' };
                $('#edd-cm-date-select-form input[name="date_from"]').datepicker(options);
                $('#edd-cm-date-select-form input[name="date_to"]').datepicker(options);
            });
        }(jQuery));
        </script>
        <br>

        <?php if( !empty($traffic_sources['direct']) || !empty($traffic_sources['search']) || !empty($traffic_sources['referral']) ){ ?>
        <div id="edd-cm-traffic-conversions" class="metabox-holder">
			<div class="postbox">
				<h3><span><?php _e('Traffic Conversions', 'edd-conversion-metrics'); ?></span></h3>

				<div class="inside edd-cm-clearfix">
					<div id="edd-cm-traffic-sources-graph"></div>
                    <div id="edd-cm-new-vs-returning-graph"></div>
                    <p class="explanation">Based on <?php echo number_format(count($new_vs_returning['new']) + count($new_vs_returning['returning'])); ?> sales
                    between <?php echo date('j M Y', strtotime($date_range_after)); ?> and <?php echo date('j M Y', strtotime($date_range_before)); ?></p>
				</div>
			</div>
		</div>
        <script type="text/javascript">
        (function($) {
            $(document).ready(function(){

                function labelFormatter(label, series) {
                    return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
                }

                $.plot('#edd-cm-traffic-sources-graph', [
                        { label: 'Direct', color: '#1993c1', data: <?php echo round(count($traffic_sources['direct'])); ?> },
                        { label: 'Search', color: '#df4f4f', data: <?php echo round(count($traffic_sources['search'])); ?> },
                        { label: 'Referral', color: '#5e486d', data: <?php echo round(count($traffic_sources['referral'])); ?> }
                    ], {
                    series: {
                        pie: {
                            show: true,
                            radius: 1,
                            label: {
                                show: true,
                                radius: 0.6,
                                formatter: labelFormatter,
                                background: {
                                    opacity: 0.5
                                }
                            }
                        }
                    },
                    legend: {
                        show: false
                    }
                });

                $.plot('#edd-cm-new-vs-returning-graph', [
                        { label: 'New visitors', color: '#1993c1', data: <?php echo round(count($new_vs_returning['new'])); ?> },
                        { label: 'Returning visitors', color: '#df4f4f', data: <?php echo round(count($new_vs_returning['returning'])); ?> }
                    ], {
                    series: {
                        pie: {
                            show: true,
                            radius: 1,
                            label: {
                                show: true,
                                radius: 0.6,
                                formatter: labelFormatter,
                                background: {
                                    opacity: 0.5
                                }
                            }
                        }
                    },
                    legend: {
                        show: false
                    }
                });

            });
        }(jQuery));
        </script>
        <?php } else { ?>
        <p class="no-data">There is currently no data available for the given time period.</p>
        <?php } ?>

        <?php if( $avg_page_views_purchases['total_purchases'] ){ ?>
        <div id="edd-cm-interactions-per-visit" class="metabox-holder">
            <div class="postbox">
                <h3><span><?php _e('Interactions Per Visit', 'edd-conversion-metrics'); ?></span></h3>

                <div class="inside edd-cm-clearfix">
                    <table style="width:100%">
						<thead>
							<tr>
								<th style="width:33.33%">Avg. Page Views per Sale</th>
                                <th style="width:33.33%">Avg. Value per Page View</th>
								<th style="width:33.33%">Avg. Time Spent per Sale</th>
							</tr>
						</thead>
						<tbody>
                            <tr>
                                <td style="text-align:center"><?php echo round($avg_page_views_purchases['total_page_views'] / $avg_page_views_purchases['total_purchases']); ?></td>
                                <td style="text-align:center"><?php echo edd_currency_filter( edd_format_amount( $avg_page_views_purchases['total_purchase_value'] / $avg_page_views_purchases['total_page_views'] ) ); ?></td>
                                <td style="text-align:center"><?php echo $this->seconds_to_time( $avg_time_spent['total_time_spent'] / $avg_time_spent['total_purchases'] ); ?></td>
                            </tr>
						</tbody>
					</table>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if( !empty($top_referrals) ){ ?>
        <div id="edd-cm-top-referrals" class="metabox-holder">
            <div class="postbox">
                <h3><span><?php _e('Top Referrals', 'edd-conversion-metrics'); ?></span></h3>

                <div class="inside edd-cm-clearfix">
                    <table style="width:100%">
                        <thead>
                            <tr>
                                <th style="text-align:left;width:70%">Referral</th>
                                <th style="text-align:left">Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach($top_referrals as $top_referral_key=>$top_referral_val){
                                echo '<tr><td><a href="'. esc_url( $top_referral_key ) .'" target="_blank">'. esc_url( $top_referral_key ) .'</a></td>';
                                echo '<td>'. $top_referral_val .'</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if( !empty($top_landing_pages) ){ ?>
        <div id="edd-cm-top-landing-pages" class="metabox-holder">
            <div class="postbox">
                <h3><span><?php _e('Top Landing Pages', 'edd-conversion-metrics'); ?></span></h3>

                <div class="inside edd-cm-clearfix">
                    <table style="width:100%">
                        <thead>
                            <tr>
                                <th style="text-align:left;width:70%">Landing Page</th>
                                <th style="text-align:left">Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach($top_landing_pages as $top_landing_page_key=>$top_landing_page_val){
                                echo '<tr><td><a href="'. get_site_url() . $top_landing_page_key .'" target="_blank">'. $top_landing_page_key .'</a></td>';
                                echo '<td>'. $top_landing_page_val .'</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php }
    }

    private function get_user_ip()
    {
        $ip = '';
        $client  = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : '';
        $forward = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '';
        $remote  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

        if( filter_var($client, FILTER_VALIDATE_IP) ){
            $ip = $client;
        }
        elseif( filter_var($forward, FILTER_VALIDATE_IP) ){
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        return $ip;
    }

    private function url_origin( $s, $use_forwarded_host = false )
    {
        $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true : false;
        $sp = strtolower($s['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port = isset($s['SERVER_PORT']) ? $s['SERVER_PORT'] : 80;
        $port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
        $host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
        $host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
        return $protocol . '://' . $host;
    }

    private function seconds_to_time($secs)
    {
        $hours = floor($secs / 3600);
        $mins = floor(($secs - ($hours*3600)) / 60);
        $seconds = floor($secs % 60);

        $output = $seconds .' secs';
        if($mins) $output = $mins .' mins, '. $output;
        if($hours) $output = $hours .' hours, '. $output;
        return $output;
    }

    private function get_cookie( $name )
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : 0;
    }

    private function set_cookie( $name, $value, $expire_secs )
    {
        return setcookie( $name, $value , time() + $expire_secs );
    }

}
new EDDConversionMetrics();
