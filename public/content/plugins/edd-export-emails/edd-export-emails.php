<?php
/*
Plugin Name: Easy Digital Downloads - Export Emails
Plugin URI: http://dev7studios.com
Description: Email exporting for Easy Digital Downloads
Version: 1.0
Author: Dev7studios
Author URI: http://dev7studios.com
License: GPL2
*/

class EDDExportEmails {

    private $plugin_path;
    private $plugin_url;

    function __construct()
    {
        $this->plugin_path = plugin_dir_path( __FILE__ );
        $this->plugin_url = plugin_dir_url( __FILE__ );

        add_action( 'edd_better_email_export', array(&$this, 'export_emails') );
        add_action( 'edd_reports_tab_export_content_bottom', array(&$this, 'export_emails_settings') );
    }

    function export_emails() {
        require_once $this->plugin_path . 'class-export-emails.php';

        $emails_export = new EDD_Emails_Export();
        $emails_export->export();
    }

    function export_emails_settings()
    {
		?>
    	<div class="postbox">
            <h3><span><?php _e('Export Customer Emails', 'edd'); ?></span></h3>
            <div class="inside">
                <p><?php _e( 'Download a CSV of customer emails.', 'edd' ); ?></p>
                <p>
                    <form method="post">
                        <select name="edd_export_download">
                            <option value="0"><?php printf( __( 'All %s', 'edd' ), edd_get_label_plural() ); ?></option>
                            <?php
                            $downloads = get_posts( array( 'post_type' => 'download', 'posts_per_page' => -1 ) );
                            if( $downloads ) {
                                foreach( $downloads as $download ) {
                                    echo '<option value="' . $download->ID . '">' . get_the_title( $download->ID ) . '</option>';
                                }
                            }
                            ?>
                        </select>
                        <select name="edd_export_option">
                            <option value="emails"><?php _e( 'Emails', 'edd' ); ?></option>
                            <option value="emails_and_names"><?php _e( 'Emails and Names', 'edd' ); ?></option>
                        </select>
                        <input type="hidden" name="edd-action" value="better_email_export"/>
                        <input type="submit" value="<?php _e( 'Generate CSV', 'edd' ); ?>" class="button-secondary"/>
                    </form>
                </p>
            </div>
        </div>
		<?php
    }

}
new EDDExportEmails();
