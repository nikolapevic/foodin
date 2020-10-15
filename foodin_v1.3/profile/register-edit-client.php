<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/*
* Register new endpoint to use inside My Account page.
*/

function edit_client_endpoints() {
    add_rewrite_endpoint( 'edit-client', EP_ROOT | EP_PAGES );
}

add_action( 'init', 'edit_client_endpoints' );

/**
* Add new query var.
* @param array $vars
* @return array
*/

function edit_client_query_vars( $vars ) {
    $vars[] = 'edit-client';

    return $vars;
}

add_filter( 'query_vars', 'edit_client_query_vars', 0 );


/*
     * Change endpoint title.
     *
     * @param string $title
     * @return string
     */

function edit_client_endpoint_title( $title ) {
    global $wp_query;

    $is_endpoint = isset( $wp_query->query_vars['edit-client'] );

    if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
        // New page title.
        $title = __( 'Edit Client', 'woocommerce' );

        remove_filter( 'the_title', 'edit_client_endpoint_title' );
    }

    return $title;
}

add_filter( 'the_title', 'edit_client_endpoint_title' );


//Content

function edit_client_endpoint_content() {

	$client_id = $_GET['client'];
	echo '<div class="col-lg-12 fridge"><a class="btn-white button" href="/profile/nutritionist-dashboard/?client='.$client_id.'"><ion-icon class="nav__icon" name="arrow-back-outline"></ion-icon>  Go back</a></div>';
	echo '<div class="col-lg-12 fridge">'.edit_client().'</div>';
	
}

add_action( 'woocommerce_account_edit-client_endpoint', 'edit_client_endpoint_content' );


/**
     * Custom help to add new items into an array after a selected item.
     *
     * @param array $items
     * @param array $new_items
     * @param string $after
     * @return array
     */
function edit_client_insert_after_helper( $items, $new_items, $after ) {
    // Search for the item position and +1 since is after the selected item key.
    $position = array_search( $after, array_keys( $items ) ) + 2;

    // Insert the new item.
    $array = array_slice( $items, 0, $position, true );
    $array += $new_items;
    $array += array_slice( $items, $position, count( $items ) - $position, true );

    return $array;
}

/**
     * Insert the new endpoint into the My Account menu.
     *
     * @param array $items
     * @return array
     */
function edit_client_menu_items( $items ) {
    $new_items = array();
    $new_items['edit-client'] = __( 'Edit Client', 'woocommerce' );

    // Add the new item after `orders`.
    return edit_client_insert_after_helper( $items, $new_items, 'fridge' );
}

add_filter( 'woocommerce_account_menu_items', 'edit_client_menu_items' );



?>