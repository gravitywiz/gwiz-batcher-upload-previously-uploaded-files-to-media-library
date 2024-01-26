<?php
/*
 * Plugin Name:  Gravity Wiz Batcher: Media Library Upload Previously Uploaded Files
 * Plugin URI:   http://gravitywiz.com
 * Description:  Batcher to import all media files uploaded before the GP Media Library Perk was activated.
 * Author:       Gravity Wiz
 * Version:      0.1
 * Author URI:   http://gravitywiz.com
 */

add_action( 'init', 'gwiz_batcher' );

function gwiz_batcher() {

	if ( ! is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		return;
	}

	require_once( plugin_dir_path( __FILE__ ) . 'class-gwiz-batcher.php' );

	new Gwiz_Batcher( array(
		'title'              => 'GPML Importer',
		'id'                 => 'gpml-importer',
		'size'               => 100,
		'show_form_selector' => true,
		'get_items'          => function ( $size, $offset, $form_id = null ) {

			$paging  = array(
				'offset'    => $offset,
				'page_size' => $size,
			);
			$search_criteria = array(
				'status' => 'active',
			);

			$entries = GFAPI::get_entries( $form_id, $search_criteria, null, $paging, $total );

			return array(
				'items' => $entries,
				'total' => $total,
			);
		},
		'process_item'       => function ( $entry ) {
			$form = GFAPI::get_form( $entry['form_id'] );
			gp_media_library()->maybe_upload_to_media_library( $entry, $form );
		},
	) );

}
