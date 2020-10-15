<?php
/**
     * Plugin Name: Foodin Plugin
     * Plugin URI: http://www.foodin.io/foodin-plugin
     * Description: Plugin for Foodin Website... Needs Woocommerce to work
     * Version: 1.0.0
     * Author: Nikola Pevic
     * Author URI: http://www.foodin.io
     */
    
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


include('functions.php');
include('profile/schedule.php');
include('profile/nutritionist-dashboard.php');
include('profile/fridge.php');
include('profile/shopping-list.php');
include('profile/register-edit-client.php');

include('admin/edit-user.php');
include('admin/remove-capabilities.php');
include('woocommerce/diet-product.php');
include('woocommerce/landing.php');
include('woocommerce/home-page.php');
include('schedule/register.php');
include('schedule/pick-diets.php');
include('nutrition/nutritionist.php');
include('nutrition/client.php');
include('nutrition/clients.php');
include('nutrition/edit-client.php');



    

function GetSubscriberUserData(){
$DBRecord = array();
$args = array(
    'role'    => 'Subscriber',
    'orderby' => 'last_name',
    'order'   => 'ASC'
);
$users = get_users( $args );
foreach ( $users as $user ){
    $user_data = get_user_meta( $user->ID );
    array_push($DBRecord, array(
        'role' => "Subscriber",
        'id' => $user->ID,
        'firstname' => $user->first_name,
        'height' => $user_data['height'][0],
    ));
  }
return $DBRecord;
}

function foodin_script() {
    wp_enqueue_script( 'script', plugin_dir_url( __FILE__) . '/js/jquery.easypiechart.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'cancel', plugin_dir_url( __FILE__) . '/js/cancellation.js', array( 'jquery' ), '1.3.14.21', true );
	wp_enqueue_script( 'modify', plugin_dir_url( __FILE__) . '/js/modify.js', array( 'jquery' ), '0.0.1.1', true );
	
	wp_localize_script('modify','magicalData', array(
		'nonce' => wp_create_nonce('wp_rest'),
		'siteURL' => get_site_url()
	));
	
	wp_enqueue_script( 'progressbar', plugin_dir_url( __FILE__) . '/js/progressbar.min.js', array( 'jquery' ), '1.0.0', true );
	wp_deregister_script('jquery');
	wp_enqueue_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js', array(), null, true);
}
add_action( 'wp_enqueue_scripts', 'foodin_script' );



