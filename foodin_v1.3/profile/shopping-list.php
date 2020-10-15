<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/*
* Register new endpoint to use inside My Account page.
*/

function shopping_list_endpoints() {
    add_rewrite_endpoint( 'shopping-list', EP_ROOT | EP_PAGES );
}

add_action( 'init', 'shopping_list_endpoints' );

/**
* Add new query var.
* @param array $vars
* @return array
*/

function shopping_list_query_vars( $vars ) {
    $vars[] = 'shopping-list';

    return $vars;
}

add_filter( 'query_vars', 'shopping_list_query_vars', 0 );


/*
     * Change endpoint title.
     *
     * @param string $title
     * @return string
     */

function shopping_list_endpoint_title( $title ) {
    global $wp_query;

    $is_endpoint = isset( $wp_query->query_vars['shopping-list'] );

    if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
        // New page title.
        $title = __( 'Shopping List', 'woocommerce' );

        remove_filter( 'the_title', 'shopping_list_endpoint_title' );
    }

    return $title;
}

add_filter( 'the_title', 'shopping_list_endpoint_title' );


//Content

function shopping_list_endpoint_content() {

	$user = wp_get_current_user();
	$user_id = get_current_user_id();

	$custom_diet = get_user_meta($user_id, 'custom_diet', true);
	$assinged_diet_id = get_user_meta($user_id, 'assigned_diet', true);
	$date = getdate();
	$day = $date['weekday'];
	$weekdays = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

	$date = time(); //Current date
	$weekDay = date('w', strtotime('+1 day',$date));

	$tomorrow = strtolower($weekdays[$weekDay]);
	$tomorrow_custom_meals = get_user_meta($user_id, $tomorrow, true);

	if($custom_diet=='Yes'){
		$tomorrows_meals = $tomorrow_custom_meals;
	} else {
		$tomorrows_meals = assigned_diet_meals($assinged_diet_id, $tomorrow);
	}

	//array = assigned_diet_meals($id, $today)
	echo '<div class="col-lg-12 fridge"><a class="btn-white button" href="javascript:history.go(-1)"><ion-icon class="nav__icon" name="arrow-back-outline"></ion-icon>  Go back</a></div>';
	echo shopping_list($tomorrows_meals, 'Tomorrow&apos;s meals', 'There are no items in your shopping list for tomorrow.');
	session_start();
	if (! $_POST['meals']){
		$meal_list = $_SESSION['meals'];
		$meal_list = explode(',',$_COOKIE['mealss']);
	} else {
		$meal_list = $_POST['meals'];
		$_SESSION['meals'] = $_POST['meals'];
		setcookie('mealss', implode(',',$meal_list), time() + (86400 * 30), "/");
	}
	
	echo shopping_list($meal_list, 'My Shopping list', 'You haven&apos;t selected any meals in the <a class="blue" href="/profile/schedule">Schedule</a>.');
}

add_action( 'woocommerce_account_shopping-list_endpoint', 'shopping_list_endpoint_content' );


/**
     * Custom help to add new items into an array after a selected item.
     *
     * @param array $items
     * @param array $new_items
     * @param string $after
     * @return array
     */
function shopping_list_insert_after_helper( $items, $new_items, $after ) {
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
function shopping_list_menu_items( $items ) {
    $new_items = array();
    $new_items['shopping-list'] = __( 'Shopping List', 'woocommerce' );

    // Add the new item after `orders`.
    return shopping_list_insert_after_helper( $items, $new_items, 'schedule' );
}

add_filter( 'woocommerce_account_menu_items', 'shopping_list_menu_items' );



?>