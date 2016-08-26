<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
// Register Custom Post Type
function inex_ticket_cpt() {

	$labels = array(
		'name'                => _x( 'Tickets', 'Post Type General Name', 'inex-ticket' ),
		'singular_name'       => _x( 'Ticket', 'Post Type Singular Name', 'inex-ticket' ),
		'menu_name'           => __( 'Ticket', 'inex-ticket' ),
		'name_admin_bar'      => __( 'Ticket', 'inex-ticket' ),
		'parent_item_colon'   => __( 'Parent Ticket:', 'inex-ticket' ),
		'all_items'           => __( 'All Tickets', 'inex-ticket' ),
		'add_new_item'        => __( 'Add New Ticket', 'inex-ticket' ),
		'add_new'             => __( 'Add New', 'inex-ticket' ),
		'new_item'            => __( 'New Ticket', 'inex-ticket' ),
		'edit_item'           => __( 'Edit Ticket', 'inex-ticket' ),
		'update_item'         => __( 'Update Ticket', 'inex-ticket' ),
		'view_item'           => __( 'View Ticket', 'inex-ticket' ),
		'search_items'        => __( 'Search Ticket', 'inex-ticket' ),
		'not_found'           => __( 'Not found', 'inex-ticket' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'inex-ticket' ),
	);
	$args = array(
		'label'               => __( 'Ticket', 'inex-ticket' ),
		'description'         => __( 'Ticket system', 'inex-ticket' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'custom-fields', ),
		'taxonomies'          => array( 'inex-ticket', 'inex_ticket_type', 'inex_ticket_status', 'inex_ticket_priority' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
	);
	register_post_type( 'inex-ticket', $args );

}
add_action( 'init', 'inex_ticket_cpt', 0 );

// Register Custom Taxonomy
function inex_ticket_tax() {

	$labels = array(
		'name'                       => _x( 'Tickets', 'Taxonomy General Name', 'inex-ticket' ),
		'singular_name'              => _x( 'Ticket', 'Taxonomy Singular Name', 'inex-ticket' ),
		'menu_name'                  => __( 'Ticket category', 'inex-ticket' ),
		'all_items'                  => __( 'All Items', 'inex-ticket' ),
		'parent_item'                => __( 'Parent Item', 'inex-ticket' ),
		'parent_item_colon'          => __( 'Parent Item:', 'inex-ticket' ),
		'new_item_name'              => __( 'New Item Name', 'inex-ticket' ),
		'add_new_item'               => __( 'Add New Item', 'inex-ticket' ),
		'edit_item'                  => __( 'Edit Item', 'inex-ticket' ),
		'update_item'                => __( 'Update Item', 'inex-ticket' ),
		'view_item'                  => __( 'View Item', 'inex-ticket' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'inex-ticket' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'inex-ticket' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'inex-ticket' ),
		'popular_items'              => __( 'Popular Items', 'inex-ticket' ),
		'search_items'               => __( 'Search Items', 'inex-ticket' ),
		'not_found'                  => __( 'Not Found', 'inex-ticket' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'inex-ticket', array( 'inex-ticket' ), $args );

}
add_action( 'init', 'inex_ticket_tax', 0 );

// Register Custom Taxonomy
function inex_ticket_type_tax() {

	$labels = array(
		'name'                       => _x( 'Types', 'Taxonomy General Name', 'inex-ticket' ),
		'singular_name'              => _x( 'Type', 'Taxonomy Singular Name', 'inex-ticket' ),
		'menu_name'                  => __( 'Ticket type', 'inex-ticket' ),
		'all_items'                  => __( 'All Items', 'inex-ticket' ),
		'parent_item'                => __( 'Parent Item', 'inex-ticket' ),
		'parent_item_colon'          => __( 'Parent Item:', 'inex-ticket' ),
		'new_item_name'              => __( 'New Item Name', 'inex-ticket' ),
		'add_new_item'               => __( 'Add New Item', 'inex-ticket' ),
		'edit_item'                  => __( 'Edit Item', 'inex-ticket' ),
		'update_item'                => __( 'Update Item', 'inex-ticket' ),
		'view_item'                  => __( 'View Item', 'inex-ticket' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'inex-ticket' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'inex-ticket' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'inex-ticket' ),
		'popular_items'              => __( 'Popular Items', 'inex-ticket' ),
		'search_items'               => __( 'Search Items', 'inex-ticket' ),
		'not_found'                  => __( 'Not Found', 'inex-ticket' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'inex_ticket_type', array( 'inex-ticket' ), $args );

}
add_action( 'init', 'inex_ticket_type_tax', 0 );

// Register Custom Taxonomy
function inex_ticket_status_tax() {

	$labels = array(
		'name'                       => _x( 'Status', 'Taxonomy General Name', 'inex-ticket' ),
		'singular_name'              => _x( 'Status', 'Taxonomy Singular Name', 'inex-ticket' ),
		'menu_name'                  => __( 'Ticket status', 'inex-ticket' ),
		'all_items'                  => __( 'All Items', 'inex-ticket' ),
		'parent_item'                => __( 'Parent Item', 'inex-ticket' ),
		'parent_item_colon'          => __( 'Parent Item:', 'inex-ticket' ),
		'new_item_name'              => __( 'New Item Name', 'inex-ticket' ),
		'add_new_item'               => __( 'Add New Item', 'inex-ticket' ),
		'edit_item'                  => __( 'Edit Item', 'inex-ticket' ),
		'update_item'                => __( 'Update Item', 'inex-ticket' ),
		'view_item'                  => __( 'View Item', 'inex-ticket' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'inex-ticket' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'inex-ticket' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'inex-ticket' ),
		'popular_items'              => __( 'Popular Items', 'inex-ticket' ),
		'search_items'               => __( 'Search Items', 'inex-ticket' ),
		'not_found'                  => __( 'Not Found', 'inex-ticket' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'inex_ticket_status', array( 'inex-ticket' ), $args );

}
add_action( 'init', 'inex_ticket_status_tax', 0 );

// Register Custom Taxonomy
function inex_ticket_priority_tax() {

	$labels = array(
		'name'                       => _x( 'Priorities', 'Taxonomy General Name', 'inex-ticket' ),
		'singular_name'              => _x( 'Priority', 'Taxonomy Singular Name', 'inex-ticket' ),
		'menu_name'                  => __( 'Ticket priority', 'inex-ticket' ),
		'all_items'                  => __( 'All Items', 'inex-ticket' ),
		'parent_item'                => __( 'Parent Item', 'inex-ticket' ),
		'parent_item_colon'          => __( 'Parent Item:', 'inex-ticket' ),
		'new_item_name'              => __( 'New Item Name', 'inex-ticket' ),
		'add_new_item'               => __( 'Add New Item', 'inex-ticket' ),
		'edit_item'                  => __( 'Edit Item', 'inex-ticket' ),
		'update_item'                => __( 'Update Item', 'inex-ticket' ),
		'view_item'                  => __( 'View Item', 'inex-ticket' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'inex-ticket' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'inex-ticket' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'inex-ticket' ),
		'popular_items'              => __( 'Popular Items', 'inex-ticket' ),
		'search_items'               => __( 'Search Items', 'inex-ticket' ),
		'not_found'                  => __( 'Not Found', 'inex-ticket' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'inex_ticket_priority', array( 'inex-ticket' ), $args );

}
add_action( 'init', 'inex_ticket_priority_tax', 0 );

// Register Custom Post Type
function inex_ticket_reply_cpt() {

	$labels = array(
		'name'                => _x( 'Tickets Reply', 'Post Type General Name', 'inex-ticket' ),
		'singular_name'       => _x( 'Ticket Reply', 'Post Type Singular Name', 'inex-ticket' ),
		'menu_name'           => __( 'Ticket Reply', 'inex-ticket' ),
		'name_admin_bar'      => __( 'Ticket Reply', 'inex-ticket' ),
		'parent_item_colon'   => __( 'Parent Item:', 'inex-ticket' ),
		'all_items'           => __( 'All Tickets Reply', 'inex-ticket' ),
		'add_new_item'        => __( 'Add Ticket Reply', 'inex-ticket' ),
		'add_new'             => __( 'Add New', 'inex-ticket' ),
		'new_item'            => __( 'New Ticket Reply', 'inex-ticket' ),
		'edit_item'           => __( 'Edit Ticket Reply', 'inex-ticket' ),
		'update_item'         => __( 'Update Ticket Reply', 'inex-ticket' ),
		'view_item'           => __( 'View Ticket Reply', 'inex-ticket' ),
		'search_items'        => __( 'Search Tickets Reply', 'inex-ticket' ),
		'not_found'           => __( 'Not found', 'inex-ticket' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'inex-ticket' ),
	);
	$args = array(
		'label'               => __( 'Ticket Reply', 'inex-ticket' ),
		'description'         => __( 'Ticket Reply system', 'inex-ticket' ),
		'labels'              => $labels,
		'supports'            => array( 'editor', 'excerpt', 'author', 'custom-fields', ),
		'taxonomies'          => array(),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => 'edit.php?post_type=inex-ticket',
		//'menu_position'       => 5,
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
	);
	register_post_type( 'inex-ticket-reply', $args );

}
add_action( 'init', 'inex_ticket_reply_cpt', 0 );


