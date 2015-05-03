<?php
/*
Plugin Name: Pods Caldera Forms Processor
Plugin URI: http://pods.io/
Description: Integration with Caldera Forms (http://wordpress.org/plugins/caldera-forms/); Provides a UI for mapping a Form's submissions into a Pod
Version: 1.0.0
Author: Pods Framework Team
Author URI: http://pods.io/about/
Text Domain: pods-caldera-forms
Domain Path: /languages/

Copyright 2014-2015  Pods Foundation, Inc  (email : contact@podsfoundation.org)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * @package Pods\Caldera Forms
 */

define( 'PODS_CF_VERSION', '1.0.0' );
define( 'PODS_CF_FILE', __FILE__ );
define( 'PODS_CF_DIR', plugin_dir_path( PODS_CF_FILE ) );
define( 'PODS_CF_URL', plugin_dir_url( PODS_CF_FILE ) );


/**
 * Include main functions and initiate
 */
function pods_cf_init () {

	if ( ! function_exists( 'pods' ) ) {
		return false;
	}

	/**
	 * Register the Pods CF Processor
	 */
	add_filter('caldera_forms_get_form_processors', 'pods_cf_register_processor');
	function pods_cf_register_processor($processors){

		$processors['pods'] = array(
			"name"				=>	__('Pods', 'pods-caldera-forms'),
			"description"		=>	__("Process submission to a Pod", 'pods-caldera-forms'),
			"icon"				=>	PODS_CF_URL . "assets/images/pods-icon.png",
			"template"			=>	PODS_CF_DIR . "ui/setup.php",
			"pre_processor"		=>	'pods_cf_verify_entry_id',
			"post_processor"	=>	'pods_cf_capture_entry',
			"magic_tags"		=>	array(
				'pod_id'
			)
			
		);
		return $processors;

	}

	/**
	 * Pre-populate Selects,Radios & Checkboxes
	 */
	add_filter( 'caldera_forms_render_get_field_type-dropdown', 'pods_cf_populate_options');
	add_filter( 'caldera_forms_render_get_field_type-radio', 'pods_cf_populate_options');
	add_filter( 'caldera_forms_render_get_field_type-checkbox', 'pods_cf_populate_options');

	if( is_admin() ){
		/**
		 * Ajax Controls for building Field Binding
		 */
		add_action("wp_ajax_pods_cf_load_fields", 'pods_cf_load_fields' );
	}

	// Include main functions
	require_once( PODS_CF_DIR . 'includes/functions.php' );


}

add_action( 'init', 'pods_cf_init' );

/**
 * Admin nag if Pods or CF isn't activated.
 */
add_action( 'admin_notices', 'pods_cf_admin_nag' );

function pods_cf_admin_nag () {
	if ( is_admin() && ( ! class_exists( 'Caldera_Forms' ) || ! defined( 'PODS_VERSION' ) ) ) {
		echo sprintf( '<div id="message" class="error"><p>%s</p></div>',
					  __( 'Pods Caldera Forms Processor requires that the Pods and Caldera Forms core plugins be installed and activated.', 'pods-caldera-forms' )
		);
	}

}





add_filter( 'caldera_forms_render_pre_get_entry', 'pods_populate_edit_data', 10, 2 );
function pods_populate_edit_data( $data, $form ){

	$processors = Caldera_Forms::get_processor_by_type( 'pods', $form );
	
	if( !empty( $processors ) ){
		foreach( $processors as $processor ){
			if( empty( $processor['config']['pod_id'] ) ){
				continue;
			}
			$pod = pods( $processor['config']['pod'], Caldera_Forms::do_magic_tags( $processor['config']['pod_id'] ) );

			foreach( $processor['config']['fields'] as $field=>$field_id ){
				if( empty( $field_id ) ){
					continue;
				}
				$line = $pod->field( $field );

				if( is_array( $line ) ){
					foreach( $line as $line_item ){						
						if( isset( $line_item['ID'] ) ){
							$data[ $field_id ] = $line_item['ID'];
						}elseif( isset( $line_item['id'] ) ){
							$data[ $field_id ] = $line_item['id'];
						}else{
							$data[ $field_id ] = $line_item;
						}
					}	
				}else{
					$data[ $field_id ] = $line;
				}
			}

		}
	}

	return $data;
}





















