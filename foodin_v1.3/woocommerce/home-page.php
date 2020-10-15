<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



function meal_array($name,$no_posts){
	$array = wc_get_products(array(
		'category' => array($name),
		'posts_per_page' => 20,
		'status' => 'publish'
	));
	
	shuffle($array);
	
	$arrayArr = [];
	foreach($array as $k => $v){
		if($k<$no_posts){
			array_push($arrayArr,$v->get_ID());
		}
	}
	$array = $arrayArr;
	
	return $array;
}

add_shortcode('home_landing','home_landing');

function home_landing(){

	$user = wp_get_current_user();
	$output .= '<div class="nutritionist-dashboard woocommerce">';
	if (!is_user_logged_in()){
		$output .= '<div class="white-container box-shadow recommended-plans mt-5">';
		$output .= '<div class="schedule-left">Don&apos;t have an account?</div><div class="schedule-right"><a class="blue" href="/profile"><button class="btn button btn-primary">Sign up</button></a></div>';
		$output .= '</div>';
	}
	$output .= '<div class="col-lg-12 col-sm-12 recommended-plans">';
	$output .= recommended_meals(meal_array('healthy',8),$user,'Healthy meals','/product-category/recipes/healthy');
	$output .= recommended_meals(meal_array('italian',8),$user,'Italian meals','/product-category/recipes/italian');
	//$output .= recommended_meals(meal_array('breakfast-recipes',8),$user,'Breakfast','/product-category/recipes/breakfast-recipes');
	//$output .= recommended_meals(meal_array('dairy-free',8),$user,'Dairy-free meals','/product-category/recipes/dairy-free');
	$output .= recommended_meals(meal_array('gluten-free',8),$user,'Gluten-free meals','/product-category/recipes/gluten-free');
	$output .= recommended_meals(meal_array('snacks-recipes',8),$user,'Snacks','/product-category/recipes/snacks-recipes');
	$output .= '</div>';

	$output .= '<div class="col-lg-12 col-sm-12 recommended-plans">';

	$output .= recommended_diets(meal_array('plans',3),$user,'Trending plans','/product-category/plans');
	$output .= '</div>';
	$output .= '</div>';
	return $output;
}



?>