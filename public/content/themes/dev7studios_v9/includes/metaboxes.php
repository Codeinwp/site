<?php

add_filter( 'cmb_meta_boxes', 'dev7_metaboxes' );
/**
 * Define the metabox and field configurations.
 *
 * @param  array $meta_boxes
 * @return array
 */
function dev7_metaboxes( array $meta_boxes ) {

	$prefix = '_dev7_';

	$meta_boxes['page_settings'] = array(
		'id'         => 'page_settings',
		'title'      => __( 'Page Settings', 'dev7' ),
		'pages'      => array( 'page' ),
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'name' => __( 'Page Title', 'dev7' ),
				'desc' => __( 'Optional page title', 'dev7' ),
				'id'   => $prefix .'page_title',
				'type' => 'text'
			),
			array(
				'name' => __( 'Page Subtitle', 'dev7' ),
				'desc' => __( 'Optional page subtitle', 'dev7' ),
				'id'   => $prefix .'page_subtitle',
				'type' => 'text'
			)
		)
	);

	$meta_boxes['download_settings'] = array(
		'id'         => 'download_settings',
		'title'      => __( 'Download Settings', 'dev7' ),
		'pages'      => array( 'download' ),
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			array(
				'name' => __( 'Demo URL', 'dev7' ),
				'desc' => __( 'URL to demo page', 'dev7' ),
				'id'   => $prefix .'demo_url',
				'type' => 'text'
			),
			array(
				'name' => __( 'Demo Text', 'dev7' ),
				'desc' => __( 'Text for demo button (e.g. Plugin Demo)', 'dev7' ),
				'id'   => $prefix .'demo_text',
				'type' => 'text'
			),
			array(
				'name' => __( 'Video Embed', 'dev7' ),
				'desc' => __( 'Video Embed code (will replace featured image)', 'dev7' ),
				'id'   => $prefix .'video_embed',
				'type' => 'textarea_code'
			)
		)
	);

	return $meta_boxes;
}

add_action( 'init', 'cmb_initialize_cmb_meta_boxes', 9999 );
/**
 * Initialize the metabox class.
 */
function cmb_initialize_cmb_meta_boxes() {

	if ( ! class_exists( 'cmb_Meta_Box' ) )
		require_once 'custom-metaboxes/init.php';

}
